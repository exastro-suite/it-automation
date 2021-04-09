<?php
//   Copyright 2020 NEC Corporation
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

    // メニューID設定
    if(!array_key_exists('no', $_GET)){
        $_GET['no'] = "2100080017";
    }

    if(array_key_exists("purl",$_GET)===true && array_key_exists("policyName",$_GET)===true){
        try{
            require_once ( $root_dir_path . "/libs/commonlibs/common_terraform_function.php");
            require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_05_preupload.php");
            $purl = htmlspecialchars($_GET['purl'], ENT_QUOTES, "UTF-8");
            $policyName = htmlspecialchars($_GET['policyName'], ENT_QUOTES, "UTF-8");

            //インターフェース情報を取得
            $retInterfaceInfo = getInterfaceInfo();
            if($retInterfaceInfo[0] == false){
                //処理を停止
                throw new Exception();
            }
            $hostname = $retInterfaceInfo[1]['TERRAFORM_HOSTNAME'];
            $token = ky_decrypt($retInterfaceInfo[1]['TERRAFORM_TOKEN']);
            $proxySetting = array();
            $proxySetting['address'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_ADDRESS'];
            $proxySetting['port'] = $retInterfaceInfo[1]['TERRAFORM_PROXY_PORT'];

            //一時保存ディレクトリを作成
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
            $temp_dir = $root_dir_path . "/temp/terraform_download_temp/";
            if(!file_exists($temp_dir)){
                if(!mkdir($temp_dir, 0777, true)){
                    //処理を停止
                    throw new Exception();
                }else{
                    if(!chmod($temp_dir, 0777)){
                        //処理を停止
                        throw new Exception();
                    }
                }
            }

            //Policyコードファイルダウンロード用のHTTPコンテキスト作成
            $Header = array( "Authorization: Bearer ".$token,
                             "Content-Type: application/vnd.api+json");
            $HttpContext = array( "http" => array( "method"     => 'GET',
                                                   "header"     => implode("\r\n", $Header),
                                                   "ignore_errors" => true));
            $HttpContext['ssl']['verify_peer']=false;
            $HttpContext['ssl']['verify_peer_name']=false;

            //proxy設定
            if($proxySetting['address'] != ""){
                $address = $proxySetting['address'];
                if($proxySetting['port'] != ""){
                    $address = $address . ":" . $proxySetting['port'];
                }
                $HttpContext['http']['proxy'] = $address;
                $HttpContext['http']['request_fulluri'] = true;
            }

            //Policyコードファイルを取得
            $downloadUrl = "https://" . $hostname . $purl;
            $policyCode = @file_get_contents($downloadUrl, false, stream_context_create($HttpContext));
            if($policyCode == ""){
                //処理を停止
                throw new Exception();
            }

            //policyファイルを生成
            $fileName = $policyName . ".sentinel";
            $policyFile = $temp_dir . "/" . $fileName;
            if(!file_exists($policyFile)){
                if(!touch($policyFile)){
                    //処理を停止
                    throw new Exception();
                }else{
                    if(!chmod($policyFile, 0777)){
                        //処理を停止
                        throw new Exception();
                    }
                }
            }

            //ファイルに中身を追記
            file_put_contents($policyFile, $policyCode);

            //ファイルをダウンロード
            header('Content-Type: application/force-download');
            header('Content-Length: '.filesize($policyFile));
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
            while (ob_get_level()) { ob_end_clean(); }
            readfile($policyFile);

            //ファイル削除
            unlink($policyFile);
        }catch(Exception $e){
            exit();
        }


    }else{
        //処理を停止
        exit();
    }

    // ----アクセスログ出力
    web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-603"));
    // アクセスログ出力----

?>

