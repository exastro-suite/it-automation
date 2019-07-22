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
    //  【概要】
    //      10分に一度、OpenStackのマスタデータをRESTAPI経由で取得し、DBに格納する。
    //      本APIはジャーナルが存在せず、特段変更履歴等の考慮は必要無し。
    //
    //////////////////////////////////////////////////////////////////////

    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL'); // 'DEBUG';

    ////////////////////////////////
    // PHPエラー時のログ出力先設定//
    ////////////////////////////////
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";
    ini_set('display_errors',2);
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    require_once($root_dir_path . "/libs/backyardlibs/openstack_driver/openStack_RESTCallLib.php");

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php      = '/libs/commonlibs/common_db_connect.php';
    $db_access_user_id   = -100903;  // LEG(-100009):::PIO(-100010)

    //----変数名テーブル関連
    $openStackMasterTbl = "B_OPENST_SYNC_MASTER";

    //作業パターン変数名紐付テーブル関連----
    // REST取得

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    try {
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////

        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        // 開始メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55003");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // RESTAPI アクセス情報を取得する   //
        ////////////////////////////////

        if(1) {
            $sqlUtnBody = "SELECT * " 
                         ."FROM B_OPENST_IF_INFO TAB_1 "
                         ."WHERE TAB_1.DISUSE_FLAG = '0'";
            $arrayUtnBind = array();

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
            }
            $r = $objQueryUtn->sqlExecute();
            if(!$r) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
            }

            // FETCH行数を取得
            $num_of_rows = $objQueryUtn->effectedRowCount();

            // レコード無しの場合
            if( $num_of_rows === 0 ) {
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-10101") );
            }
            // 重複登録の場合
            else if( $num_of_rows > 1 ) {
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-10102") );
            }

            $openstack_if_info = $objQueryUtn->resultFetch();

        }

        ////////////////////////////////
        // 取得したIF情報を元に、認証を実施し、トークンを取得する //
        ////////////////////////////////
        //認証リクエスト
        $content=[
            "auth"=>[
                "passwordCredentials"=>[
                    "username"=>$openstack_if_info['OPENST_USER'],
                    "password"=>ky_decrypt($openstack_if_info['OPENST_PASSWORD'])
                ],
            ]
        ];
        $content=json_encode($content,true);

        $aryResult       = array();
        $ret = openstack_rest_call("TOKENS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", "", $content, $aryResult);

        if($ret != '200') {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));

            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($FREE_LOG);
        } else {
            //通信成功時（正常系）
            $ret=json_decode($aryResult['ResponsContents'],true);

            $token=$ret['access']['token']['id'];
            $response['general']['token']=$aryResult['ResponsContents'];
        }

        if(!(isset($token))) {
            //トークンが取得できなかったらエラー
        } else {
            //正常系（トークンが取得できた

            ////////////////////////////////
            // テナント一覧
            ////////////////////////////////
            $aryResult = array();
            $ret = openstack_rest_call("PROJECTS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", $token, "", $aryResult);
            if($ret != '200') {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));

                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($FREE_LOG);
            } else {
                //通信成功時（正常系）
                $response['general']['project']=$aryResult['ResponsContents'];

            }
        }

        $tenantArray=json_decode($response['general']['project'],JSON_UNESCAPED_UNICODE);

        ////////////////////////////////
        //プロジェクト一覧テーブルを作成し、格納する。
        ////////////////////////////////
        if(1) {

            ////////////////////////////////
            // トランザクション開始
            ////////////////////////////////
            if( $objDBCA->transactionStart()===false ) {
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55004");
                require ($root_dir_path . $log_output_php );
            }

            //一度全て消す
            $sqlUtnBody = "UPDATE B_OPENST_PROJECT_INFO SET DISUSE_FLAG = 1";
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }
            $r = $objQueryUtn->sqlExecute();
            if(!$r) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
            }

            for ($i=0; $i <count($tenantArray['tenants']) ; $i++) {

                $node=$tenantArray['tenants'][$i];

                //INSERT
                $sqlUtnBody = "INSERT INTO B_OPENST_PROJECT_INFO " 
                             ."VALUES(:OPENST_PROJECT_ID,:OPENST_PROJECT_NAME,0,0,0,:DATE,:DB_ACCESS_USER_ID)";
                $arrayUtnBind = array(
                    "OPENST_PROJECT_ID"=>$node['id'],
                    "OPENST_PROJECT_NAME"=>$node['name'],
                    "DATE"=>date("Y-m-d H:i:s"),
                    "DB_ACCESS_USER_ID"=>$db_access_user_id
                );

                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                }
                $r = $objQueryUtn->sqlExecute();
                if (!$r) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
            };

            //フラグが立ててあるものを削除する
            $sqlUtnBody = "DELETE FROM B_OPENST_PROJECT_INFO WHERE DISUSE_FLAG = 1";
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }
            $r = $objQueryUtn->sqlExecute();
            if(!$r) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
            }

            ////////////////////////////////
            // コミット
            ////////////////////////////////
            $r = $objDBCA->transactionCommit();
            if(!$r) {
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55015");
                require ($root_dir_path . $log_output_php );
            }

            ////////////////////////////////
            // トランザクション終了       //
            ////////////////////////////////
            $objDBCA->transactionExit();

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55005");
                require ($root_dir_path . $log_output_php );
            }
        }

        if(isset($tenantArray['tenants'])) {
            //テナントの数だけAPIを取得する。

            for ($i=0; $i <count($tenantArray['tenants']) ; $i++) { 

                $tenant_id=$tenantArray['tenants'][$i]['id'];

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ) {
                    $FREE_LOG = $tenant_id." start";
                    require ($root_dir_path . $log_output_php );
                }

                if(true) {

                    //認証リクエスト（テナントID付）
                    $content=[
                        "auth"=>[
                            "passwordCredentials"=>[
                                "username"=>$openstack_if_info['OPENST_USER'],
                                "password"=>ky_decrypt($openstack_if_info['OPENST_PASSWORD'])
                            ],
                            "tenantId"=>$tenant_id
                        ]
                    ];
                    $content=json_encode($content,true);

                    $aryResult       = array();
                    $ret = openstack_rest_call("TOKENS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", "", $content, $aryResult);

                    if($ret != '200') {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));

                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception($FREE_LOG);
                    } else {
                        //通信成功時（正常系）
                        $resData=json_decode($aryResult['ResponsContents'],true);

                        $token=$resData['access']['token']['id'];
                        $response[$tenant_id]['token']=$aryResult['ResponsContents'];
                    }

                    // コールするAPI一覧
                    $apiArray=[];
                    for ($j=0; $j < count($resData['access']['serviceCatalog']); $j++) { 
                        $node=$resData['access']['serviceCatalog'][$j];
                        $name=$node['type'];
                        $value=$node['endpoints'][0]['publicURL'];
                        $apiArray[$value]=$name;
                    }

                    $apiUrl=getApiUrl("Compute",$apiArray);
                    if($apiUrl!=null) {

                        //アベイル一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("AVAIL", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl,$token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "AVAIL   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        }else{
                            //通信成功時（正常系）
                            $response[$tenant_id]['avail']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("Image",$apiArray);
                    if($apiUrl!=null) {

                        //イメージ一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("IMAGE", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "IMAGE   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        }else{
                            //通信成功時（正常系）
                            $response[$tenant_id]['image']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("Compute",$apiArray);
                        if($apiUrl!=null) {
                        //サーバ一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("SERVER", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "SERVER   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['server']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("Compute",$apiArray);
                    if($apiUrl!=null) {
                        //フレーバ一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("FLAVOR", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "FLAVOR   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['flavor']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("network",$apiArray);
                    if($apiUrl!=null) {
                        //IP一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("IP", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "IP   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['ip']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("network",$apiArray);
                    if($apiUrl!=null) {

                        //セキュリティグループ一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("SECURITYGROUP", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "SECURITYGROUP   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）

                            //セキュリティグループは、全てのテナントIDに属するものが返却されてしまう。
                            //RESTへのリクエストパラメータで絞り込みもできない。
                            //そのため、レスポンスをテナントIDでフィルタして格納する。
                            $array=json_decode($aryResult['ResponsContents'],true);
                            $targetArray['security_groups']=array();
                            for ($k=0; $k <count($array['security_groups']) ; $k++) { 

                                $node=$array['security_groups'][$k];
                                if($node['tenant_id']==$tenant_id){
                                    $targetArray['security_groups'][]=$node;
                                }
                            }
                            $response[$tenant_id]['securityGroup']=json_encode($targetArray,JSON_UNESCAPED_UNICODE);
                        }
                    }

                    $apiUrl=getApiUrl("Compute",$apiArray);
                    if($apiUrl!=null) {
                        //キーペア一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("KEYPAIR", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "KEYPAIR   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['keypair']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("network",$apiArray);
                    if($apiUrl!=null) {
                        //ネットワーク一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("NETWORKS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "NETWORKS   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['networks']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("network",$apiArray);
                    if($apiUrl!=null) {

                        //ルータ一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("ROUTERS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "ROUTERS   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['routers']=$aryResult['ResponsContents'];
                        }
                    }

                    $apiUrl=getApiUrl("Volume",$apiArray);
                    if($apiUrl!=null) {
                        //volume一覧
                        $aryResult = array();
                        $ret = openstack_rest_call("VOLUME", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ) {
                            $FREE_LOG = "VOLUME   Response:".$ret."  URL:".$apiUrl;
                            require ($root_dir_path . $log_output_php );
                        }

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $response[$tenant_id]['volume']=$aryResult['ResponsContents'];
                        }
                    }
                }
            }
        }

        ////////////////////////////////
        // トランザクション開始
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55004");
            require ($root_dir_path . $log_output_php );
        }

        //一度全て消す
        $sqlUtnBody = "UPDATE B_OPENST_MASTER_SYNC SET DISUSE_FLAG = 1";
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
        }

        $r = $objQueryUtn->sqlExecute();
        if(!$r) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
        }

        //削除成功時
        //取得したAPIを格納する
        foreach ($response as $key => $value) {

            $node=$value;

            foreach ($node as $key2 => $value2) {

                //INSERT
                $sqlUtnBody = "INSERT INTO B_OPENST_MASTER_SYNC " 
                             ."VALUES(:TENANT_ID,:NAME,:VALUE,0,:DATE,:DB_ACCESS_USER_ID)";
                $arrayUtnBind = array(
                    "TENANT_ID"=>$key,
                    "NAME"=>$key2,
                    "VALUE"=>$value2,
                    "DATE"=>date("Y-m-d H:i:s"),
                    "DB_ACCESS_USER_ID"=>$db_access_user_id
                    );    

                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                }
                $r = $objQueryUtn->sqlExecute();
                if (!$r) {            
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
            };
        };

        //フラグが立ててあるものを削除する
        $sqlUtnBody = "DELETE FROM B_OPENST_MASTER_SYNC WHERE DISUSE_FLAG = 1";
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if(!$r) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
        }

        ////////////////////////////////
        // コミット
        ////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if(!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55015");
            require ($root_dir_path . $log_output_php );
        }
        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55005");
            require ($root_dir_path . $log_output_php );
        }
    }
    catch (Exception $e){
        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55272");
        require ($root_dir_path . $log_output_php );

        // 例外メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );
        
        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ) {
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55016");
            } else {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50045");
            }
            require ($root_dir_path . $log_output_php );
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-50047");
            } else {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50049");
            }
            require ($root_dir_path . $log_output_php );
        }
    }

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ) {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            //$FREE_LOG = 'プロシージャ終了(異常)';
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55267");
            require ($root_dir_path . $log_output_php );
        }
        exit(2);
    }
    elseif( $warning_flag != 0 ) {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55268");
            require ($root_dir_path . $log_output_php );
        }
        exit(2);
    } else {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55002");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }

    function getApiUrl($category,$apiArray){

        $category=mb_strtolower($category);
        foreach($apiArray as $key => $value){

            if(preg_match("/$category/",$value)){
                return $key;
            }
        }
        return null;
    }
