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
    //  【機能】
    //   cobblerのプロファイル名のリストを取得し、ファイルに記録する。
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
    $log_level = getenv('LOG_LEVEL');

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php     = $root_dir_path.'/libs/backyardlibs/backyard_log_output.php';
    $mess_template_php  = $root_dir_path.'/libs/commonlibs/common_php_classes.php';
    $common_function_php= $root_dir_path.'/libs/commonlibs/common_php_functions.php';
    $file_dir_dir       = $root_dir_path.'/confs/backyardconfs/cobbler_driver/path_DATA_RELAY_STRAGE_side_Cobbler';
    $file_name          = 'cobbler_profile_List';
    $file_name_tamp     = 'cobbler_profile_List_temp';

    $cobbler_profile_command = 'cobbler profile list';//cobblerコマンド:プロファイル名のリストを取得する。

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $exec_array                 = array();  //コマンド実行結果格納配列
    $exec_result                = '';       //コマンド実行ステータス格納
    $file_folder                = '';
    $file_dir                   = '';
    $temp_file_dir              = '';

    try{
        //メッセージ用クラスインスタンス作成
        require ( $common_function_php );
        require ( $mess_template_php );

        $objMTS = new MessageTemplateStorage();

        // トレースメッセージ 開始
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-3001", array(__LINE__));
            require ($log_output_php );
        }

        //データリレーストレージのファイルのディレクトリを取得
        $file_folder = trim(file($file_dir_dir)[0]);
        if(!$file_folder){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-6001") );
        }
        $file_dir = $file_folder . "/" . $file_name;
        $temp_file_dir = $file_folder . "/" . $file_name_tamp;


        //cobblerから、プロファイルのリストを取得
        exec($cobbler_profile_command, $exec_array, $exec_result);
        if ($exec_result != 0){
            // 異常フラグON
            // $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3001", array(__LINE__)) );
        }

        // トレースメッセージ　プロファイルリスト取得成功
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-3002", array(__LINE__));
            require ($log_output_php );
        }

        //念のため一時ファイルを消去
        if(file_exists($temp_file_dir)){
            if(!unlink($temp_file_dir)){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3002", array(__LINE__)) );
            }
        }

        if(!file_exists($file_folder)){
            if(!mkdir($file_folder)){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3003", array(__LINE__)) );
            }
        }

        //一時ファイルオープン
        $open_resource = fopen($temp_file_dir, 'a');
        if (!$open_resource){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3004", array(__LINE__)) );
        }

        //一時ファイルをロック
        flock($open_resource, LOCK_EX);

        //一時ファイル書き込み
        foreach($exec_array as $profile_name){
            $str_prfile_name = trim($profile_name)."\n";
            $write_result = fwrite($open_resource, $str_prfile_name);
            if (!$write_result){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3005", array(__LINE__)) );
            }
        }

        //ファイルクローズ
        $close_result = fclose($open_resource);
        if (!$close_result){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3006", array(__LINE__)) );
        }

        //////////////////////////////////////////////////////////
        //  旧ファイルを消去し、一時ファイルをリネームする。    //
        //////////////////////////////////////////////////////////
        //旧ファイルを消去 ITA側で見ているタイミングで消去できない可能性があるのでスパンを取る。
        if(file_exists($file_dir)){
            $unlink_result = false;
            $roop_time = 0;
            while(!$unlink_result){
                $unlink_result = unlink($file_dir);
                if(!$unlink_result){
                    sleep(3);
                    if($roop_time > 9)//3秒おきに10回ループしても消去できなかったらエラー
                    {
                        // 異常フラグON
                        $error_flag = 1;
                        // 例外処理へ
                        throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-3007", array(__LINE__)) );
                    }
                    $roop_time++;
                }
            }
        }

        //一時ファイルをリネーム
        if(!rename($temp_file_dir, $file_dir)){
            // メッセージ出力 旧ファイルが消えているはずのため、一時ファイルを消去するエラー処理にはいかない。
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-3008", array(__LINE__));
            require ($log_output_php );
        }

        // トレースメッセージ　プロファイルリストファイル作成成功
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-3003", array(__LINE__));
            require ($log_output_php );
        }
    }
    catch (Exception $e){
        if($log_level === 'DEBUG' || $error_flag != 0 || $warning_flag != 0){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($log_output_php );
        }

        //エラー発生時、一時ファイルを消去する。
        if(file_exists($temp_file_dir)){
            if(!unlink($temp_file_dir)){
                // メッセージ出力
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-3002", array(__LINE__));
                require ($log_output_php );
            }
        }
    }
?>
