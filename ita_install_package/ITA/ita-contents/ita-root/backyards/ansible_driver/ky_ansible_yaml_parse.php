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
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // 起動パラメータで受け取る
    $in_yaml_file                      = $argv[1];  // input YAML file
    $ouy_yaml_analys_result_json_file  = $argv[2];  // Json format file that saves YAML analysis results

    // ドライバに対応した変数の読み込み
    require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php");

    ////////////////////////////////
    // $log_output_dirを設定      //
    ////////////////////////////////
    // UIとbackyardから呼ばれるのでディレクトリは固定
    $log_output_dir = $root_dir_path . "/" . "logs/backyardlogs";

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    // PHP エラー時のログ出力先を設定
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php                  = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php                = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                  = '/libs/commonlibs/common_db_connect.php';
    
    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $error_flag               = 0;          // 異常フラグ(1：異常発生)

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        
        //////////////////////////////////////////
        // DBコネクト(共通オブジェクト生成の為) //
        //////////////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        
        //////////////////////////////////////////
        // yaml parse                           //
        //////////////////////////////////////////
        // エラー出力は抑止
        $analys = @yaml_parse_file($in_yaml_file);
        if($analys === false) {
            // parseに失敗し場合、エラーメッセージを標準出力に表示。呼出元で、この情報を取得。
            $FREE_LOG = "yaml parse error";
            $pares_error = error_get_last();
            if(is_array($pares_error)) {
                if(isset($pares_error["message"])) {
                    $FREE_LOG = sprintf("yaml parse error: %s\n",$pares_error["message"]);
                }
            }
            echo $FREE_LOG;
            $error_flag = 1; 
            throw new Exception( "" );

//            // "YAML解析で想定外のエラーが発生しました。"
//            $msg .= $objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000109');
//            $this->SetLastError($msg);
//            return false;

        } else {
            // yaml定義がない場合にnullが帰るので、空配列に設定
            if($analys === null) {
                $analys = array();
            }
            $json = json_encode($analys);
            // エラー出力は抑止
            $ret = @file_put_contents($ouy_yaml_analys_result_json_file,$json);
            if($ret === false) {
                // エラーメッセージを標準出力に表示。呼出元で、この情報を取得。
                $FREE_LOG = "file_put_contents error";
                $pares_error = error_get_last();
                if(is_array($pares_error)) {
                    if(isset($pares_error["message"])) {
                        $FREE_LOG = sprintf("%s\n",$pares_error["message"]);
                    }
                }
                // 一時ファイルの作成に失敗しました。
                $FREE_LOG .= $objMTS->getSomeMessage('ITAANSIBLEH-ERR-6000018');
                echo $FREE_LOG;
                $error_flag = 1; 
                throw new Exception( "" );
            }
        }
    }
    catch (Exception $e){
        $error_flag = 1;
        $FREE_LOG = $e->getMessage();
        if( $FREE_LOG != "") {
            echo $FREE_LOG;
            // メッセージ出力
            require ($root_dir_path . $log_output_php );
        }
    }
    // 処理結果コードを判定
    if( $error_flag != 0 ) {
        exit(200);
    } else {
        exit(0);
    }
?>
