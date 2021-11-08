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

class  terraformWorkspace {
    private $root_dir_path;
    function __construct() {
        // ルートディレクトリを取得
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $this->root_dir_path = $root_dir_temp[0] . "ita-root";

        //REST APIリクエストfunction定義
        $terraform_api_request_php = '/libs/commonlibs/common_terraform_restapi.php';
        require_once ($this->root_dir_path . $terraform_api_request_php );
    }

    ////////////////////////////////////////////////
    //  Workspaceが既に存在するかどうかをチェックする //
    ////////////////////////////////////////////////
    function checkWorkspace($registerData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
            false,  //[2]Workspaceの存在判定
            null,  //[3]指定IDのWorkspaceのData
        );

        $hostName = $registerData['hostName'];
        $token = $registerData['token'];
        $organizationName = $registerData['organizationName'];
        $workspaceName = $registerData['workspaceName'];
        $proxySetting = $registerData['proxySetting'];

        //Workspace一覧取得APIを実行
        $apiResponse = get_workspaces_list($hostName, $token, $organizationName, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];

        //APIの結果を判定
        $existFlg = false;
        $workspaceData = null;
        if($statusCode == 200){
            $ResponsContents = $apiResponse['ResponsContents'];
            foreach($ResponsContents['data'] as $data){
                if($data['attributes']['name'] == $workspaceName){
                    $existFlg = true;
                    $workspaceData = $data;
                }
            }

            //返却値をセット
            $ret[0] = true;
            $ret[1] = $ResponsContents;
            $ret[2] = $existFlg;
            $ret[3] = $workspaceData;
        }

        return $ret;
    }

    ////////////////////////////
    //  Workspaceを作成する //
    ////////////////////////////
    function createWorkspace($registerData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
        );

        $hostName = $registerData['hostName'];
        $token = $registerData['token'];
        $organizationName = $registerData['organizationName'];
        $workspaceName = $registerData['workspaceName'];
        $option = array(
            'terraformVersion' => $registerData['terraformVersion']
        );
        $proxySetting = $registerData['proxySetting'];

        //Workspace作成APIを実行
        $apiResponse = create_workspace($hostName, $token, $organizationName, $workspaceName, $option, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];

        //APIの結果を判定
        if($statusCode == 201){
            //返却値をセット
            $ret[0] = true;
            $ret[1] = $apiResponse['ResponsContents'];
        }

        return $ret;
    }

    ////////////////////////////
    //  Workspaceを更新する //
    ////////////////////////////
    function updateWorkspace($registerData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
        );

        $hostName = $registerData['hostName'];
        $token = $registerData['token'];
        $organizationName = $registerData['organizationName'];
        $workspaceName = $registerData['workspaceName'];
        /*
        $option = array(
            'applyMethod' => $registerData['applyMethod'],
            'terraformVersion' => $registerData['terraformVersion'],
        );
        */
        $option = array(
            'terraformVersion' => $registerData['terraformVersion']
        );
        $proxySetting = $registerData['proxySetting'];

        //Workspace作成APIを実行
        $apiResponse = update_workspace($hostName, $token, $organizationName, $workspaceName, $option, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];

        //APIの結果を判定
        if($statusCode == 200){
            //返却値をセット
            $ret[0] = true;
            $ret[1] = $apiResponse['ResponsContents'];
        }

        return $ret;
    }

    ////////////////////////////
    //  Workspaceを削除する //
    ////////////////////////////
    function deleteWorkspace($deleteData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
        );

        $hostName = $deleteData['hostName'];
        $token = $deleteData['token'];
        $organizationName = $deleteData['organizationName'];
        $workspaceName = $deleteData['workspaceName'];
        $proxySetting = $deleteData['proxySetting'];

        //Workspace作成APIを実行
        $apiResponse = delete_workspace($hostName, $token, $organizationName, $workspaceName, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];

        //APIの結果を判定
        if($statusCode == 200){
            //返却値をセット
            $ret[0] = true;
            $ret[1] = $apiResponse['ResponsContents'];
        }

        return $ret;
    }
}

?>