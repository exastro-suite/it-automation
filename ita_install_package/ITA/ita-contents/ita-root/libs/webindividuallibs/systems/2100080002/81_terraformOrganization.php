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

class  terraformOrganization {
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
    //  Organizationが既に存在するかどうかをチェックする //
    ////////////////////////////////////////////////
    function checkOrganization($registerData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
            false, //[2]Organizationの存在判定
            null,  //[3]指定IDのOrganizationのData
        );

        $hostName = $registerData['hostName'];
        $token = $registerData['token'];
        $organizationName = $registerData['organizationName'];
        $proxySetting = $registerData['proxySetting'];

        //Organization一覧取得APIを実行
        $apiResponse = get_organizations_list($hostName, $token, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];

        //APIの結果を判定
        $existFlg = false;
        $organizationData = null;
        if($statusCode == 200){
            $ResponsContents = $apiResponse['ResponsContents'];
            foreach($ResponsContents['data'] as $data){
                if($data['id'] == $organizationName){
                    $existFlg = true;
                    $organizationData = $data;
                }
            }

            //返却値をセット
            $ret[0] = true;
            $ret[1] = $ResponsContents;
            $ret[2] = $existFlg;
            $ret[3] = $organizationData;
        }

        return $ret;
    }

    ////////////////////////////
    //  Organizationを作成する //
    ////////////////////////////
    function createOrganization($registerData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
        );

        $hostName = $registerData['hostName'];
        $token = $registerData['token'];
        $organizationName = $registerData['organizationName'];
        $emailAddress = $registerData['emailAddress'];
        $proxySetting = $registerData['proxySetting'];

        //Organization登録APIを実行
        $apiResponse = create_organization($hostName, $token, $organizationName, $emailAddress, $proxySetting);
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
    //  Organizationを更新する //
    ////////////////////////////
    function updateOrganization($registerData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
        );

        $hostName = $registerData['hostName'];
        $token = $registerData['token'];
        $organizationName = $registerData['organizationName'];
        $emailAddress = $registerData['emailAddress'];
        $proxySetting = $registerData['proxySetting'];

        //Organization登録APIを実行
        $apiResponse = update_organization($hostName, $token, $organizationName, $emailAddress, $proxySetting);
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
    //  Organizationを削除する //
    ////////////////////////////
    function deleteOrganization($deleteData){
        $ret = array(
            false, //[0]APIの判定
            null,  //[1]APIのResponsContents中身
        );

        $hostName = $deleteData['hostName'];
        $token = $deleteData['token'];
        $organizationName = $deleteData['organizationName'];
        $proxySetting = $deleteData['proxySetting'];

        //Organization登録APIを実行
        $apiResponse = delete_organization($hostName, $token, $organizationName, $proxySetting);
        $statusCode = $apiResponse['StatusCode'];

        //APIの結果を判定
        if($statusCode == 204){
            //返却値をセット
            $ret[0] = true;
            $ret[1] = $apiResponse['ResponsContents'];
        }

        return $ret;
    }

}



?>