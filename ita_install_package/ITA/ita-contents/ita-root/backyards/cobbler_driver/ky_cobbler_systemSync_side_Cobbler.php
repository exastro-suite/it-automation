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
    //   cobblerのシステムリストを、ファイルから更新する。
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
    $file_name          = 'cobbler_system_List';

    $cobbler_sys_command_head         = 'cobbler system ';
    $cobbler_sys_list_command         = $cobbler_sys_command_head.'list';    //cobblerコマンド:システム名のリストを取得する。
    $cobbler_sys_edit_command_head    = $cobbler_sys_command_head.'edit ';   //cobblerコマンド:システムを更新する。
    $cobbler_sys_add_command_head     = $cobbler_sys_command_head.'add ';    //cobblerコマンド:システムを追加する。
    $cobbler_sys_remove_command_head  = $cobbler_sys_command_head.'remove '; //cobblerコマンド:システムを削除する。
    $cobbler_sys_report_command_head  = $cobbler_sys_command_head.'report '; //cobblerコマンド:システムの詳細を取得する。
    $cobbler_prof_list_command        = 'cobbler profile list';              //cobblerコマンド:プロファイル名のリストを取得する。

    //ファイルのカラム名とcobblerの項目名の組み合わせ
    $file_key_name_array = array(
        'SYSTEM_NAME'           => 'name',
        'HOSTNAME'              => 'hostname',
        'IP_ADDRESS'            => 'ip-address',
        'COBBLER_PROFILE_NAME'  => 'profile',
        'INTERFACE_TYPE'        => 'interface',
        'MAC_ADDRESS'           => 'mac-address',
        'NETMASK'               => 'netmask',
        'GATEWAY'               => 'gateway',
        'STATIC'                => 'static'
    );

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $file_dir                   = '';       // システム一覧連携ファイルのディレクトリ（後で取得）
    $exec_sys_array             = array();  //コマンド実行結果格納配列(システム)
    $exec_prof_array            = array();  //コマンド実行結果格納配列(プロファイル)
    $sys_array                  = array();  //システム名の配列
    $profile_array              = array();  //プロファイル名の配列
    $exec_result                = '';       //コマンド実行ステータス格納
    $system_file_arrays         = array();  //ファイルから読み込んだシステムのリスト

    ////////////////////////////////
    // ファンクション宣言         //
    ////////////////////////////////
    //ファイルのシステム情報一件分を、コブラコマンドに入れ込む文字列に変換。
    function GetCobblerCommandBody($sys_file_array){
        $command_body = "";
        foreach($sys_file_array as $key => $value){
            $command_body .= "--$key=$value ";
        }
        return $command_body;
    }


    ////////////////////////////////
    // 処理開始                   //
    ////////////////////////////////
    try{
        //メッセージ用クラスインスタンス作成
        require ( $common_function_php );
        require ( $mess_template_php );

        $objMTS = new MessageTemplateStorage();

        // トレースメッセージ 開始
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-4001", array($key, $command));
            require ($log_output_php );
        }

        //データリレーストレージのファイルのディレクトリを取得
        $file_dir = trim(file($file_dir_dir)[0]);
        if(!$file_dir){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-6001") );
        }
        $file_dir .= "/" . $file_name;

        //ファイル全体を取得 ITA側で一時ファイルと入れ替えているタイミングで消えていることがあるため、スパンを取る。
        $file_in_one = false;//読み込み結果が来ない間はループ
        $roop_time = 0;
        while($file_in_one == false){
            $file_in_one = file($file_dir);
            if($file_in_one == false){
                // ファイルが存在し、かつそのファイルサイズが0の場合はデータが空として以降の処理を継続
                if(is_file($file_dir) && (filesize($file_dir) === 0)){
                    break;
                }

                sleep(3);
                if($roop_time > 9)    //3秒おきに10回ループしても読み込めなかったら処理スキップ
                {
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-4001", array(__LINE__)) );
                }
                $roop_time++;
            }
        }

        // トレースメッセージ システムファイル取得成功
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-4002", array($key, $command));
            require ($log_output_php );
        }

        //ファイルのレコードをcobbler用のkey名の連想配列に詰め替え
        foreach($file_in_one as $record){
            //ファイル内のjson形式のデータ一件を配列にデコード
            $record_array = json_decode(trim($record), true);

            $key_array = array();
            $err_arr_flg = 0;
            foreach($file_key_name_array as $key => $value){
                if(array_key_exists($key, $record_array)){
                    $key_array[$value] = $record_array[$key];
                }else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4002", array($record));
                    require ($log_output_php );
                    $err_arr_flg = 1;
                    break;//行の構造が想定ではなさそうな場合はスルー
                }
            }
            if($err_arr_flg === 1)continue;

            $system_file_arrays[$record_array['SYSTEM_NAME']] = $key_array;
        }

        //cobblerから、システムのリストを取得
        exec($cobbler_sys_list_command, $exec_sys_array, $exec_result);
        if ($exec_result != 0){
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-4003", array(__LINE__)) . "\n" . $exec_sys_array[0] );
        }
        //システムのリストをトリム
        $sys_array = array_map('trim', $exec_sys_array);

        //cobblerから、プロファイルのリストを取得
        exec($cobbler_prof_list_command, $exec_prof_array, $exec_result);
        if ($exec_result != 0){
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-4004", array(__LINE__)) . "\n" . $exec_prof_array[0] );
        }
        //プロファイルのリストをトリム
        $prof_array = array_map('trim', $exec_prof_array);

        //ファイルのリストの配列をループし、処理を決定、実行する。
        foreach($system_file_arrays as $key => $sys_file_array){
            //プロファイルの名称が登録されていないものであれば、更新、登録はしない。
            if(!in_array($sys_file_array['profile'], $prof_array)){
                if($log_level === 'DEBUG'){
                    $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4005", array($key));
                    require ($log_output_php );
                }
                continue;
            }

            $is_edit = 0;
            if(in_array($key, $sys_array))//すでにあるシステム名の場合はedit
            {
                $is_edit = 1;
                $command = $cobbler_sys_edit_command_head.GetCobblerCommandBody($sys_file_array);
            }
            else//新しいシステム名の場合はadd
            {
                $is_edit = 0;
                $command = $cobbler_sys_add_command_head.GetCobblerCommandBody($sys_file_array);
            }

            //ここでコマンドを実行
            exec($command, $exec_array, $exec_result);
            if ($exec_result != 0){
                if($log_level === 'DEBUG'){
                    $FREE_LOG = '';
                    if($is_edit === 1){
                        $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4006", array($key, $command)) . "\n" . $exec_array[0];
                    }else{
                        $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4007", array($key, $command)) . "\n" . $exec_array[0];
                    }
                    require ($log_output_php );
                }
                continue;//例外処理にはいかずに無視
            }

            // editの場合インターフェースがaddされるので、今回追加したもの以外は削除する
            if($is_edit === 1){
                // systemのname指定でreport出力
                $command = $cobbler_sys_report_command_head.GetCobblerCommandBody(array('name' => $key));
                exec($command, $exec_sys_report_array, $exec_result);
                if ($exec_result != 0){
                    $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4009", array($key)) . "\n" . $exec_sys_report_array[0];
                    require ($log_output_php);
                    continue;
                }

                // インターフェース名のリストアップ
                $interface_array = array();
                foreach($exec_sys_report_array as $sys_report_data){
                    $pos = strpos($sys_report_data, "Interface =====");
                    if($pos !== false && $pos == 0){
                        $interface_array[] = trim(explode(':', $sys_report_data)[1]);
                    }
                }

                // 削除処理
                foreach($interface_array as $interface_name){
                    if($interface_name == $sys_file_array['interface']){
                        continue; // ITAの情報と同一名であれば残す
                    }

                    // 異なるインターフェースは削除する
                    $target_param = array('name' => $key, 'interface' => $interface_name);
                    $command = $cobbler_sys_edit_command_head.GetCobblerCommandBody($target_param)."--delete-interface";
                    exec($command, $exec_array, $exec_result);
                    if ($exec_result != 0){
                        $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4010", array($key, $interface_name)) . "\n" . $exec_array[0];
                        require ($log_output_php);
                    }
                }
            }
        }

        //システムのリストをループし、ファイルと突き合わせて削除するものを探す。
        foreach($sys_array as $sys_name){
            if(!array_key_exists($sys_name, $system_file_arrays)){
                //ファイルに存在していない場合、目標の名前を持つシステムを削除する。
                $command = $cobbler_sys_remove_command_head."--name=$sys_name";
                //ここでコマンドを実行
                exec($command, $exec_array, $exec_result);
                if ($exec_result != 0){
                    $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-4008", array($key, $command)) . "\n" . $exec_array[0];
                    require ($log_output_php );
                    continue;//例外処理にはいかずに無視
                }
            }
        }

        // トレースメッセージ システムリスト同期成功
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-4003", array($key, $command));
            require ($log_output_php );
        }
    }
    catch (Exception $e){
        if($log_level === 'DEBUG' || $error_flag   != 0 ||$warning_flag != 0){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($log_output_php );
        }
    }
?>
