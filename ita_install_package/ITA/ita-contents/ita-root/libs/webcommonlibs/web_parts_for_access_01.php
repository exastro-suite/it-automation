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

    // リファラ取得(リダイレクト判定のため)
    if(isset($_SERVER["HTTP_REFERER"])){
        $host = $_SERVER['HTTP_REFERER'];
    }
    else{
        $host = "";
    }
    
    // 一般動作
    if( isset($array_except_referer) === false ){
        $array_except_referer = array($ACRCM_representative_file_name);
    }
    else{
        $tmpBool=true;
        foreach($array_except_referer as $tmpValue1){
            if(is_array($tmpValue1)===true){
                foreach($tmpValue1 as $tmpValue2){
                    if( is_bool($tmpValue2) === true ){
                        $tmpBool=false;
                        break;
                    }
                }
            }
        }
        if( $tmpBool===true ){
            $array_except_referer[] = $ACRCM_representative_file_name;
        }
        unset($tmpValue1);
        unset($tmpValue2);
        unset($tmpBool);
    }
    
    $boolRefererCheck = false;
    foreach($array_except_referer as $value){
        if( strstr($host, $value) !== false ){
            $boolRefererCheck = true;
            break;
        }
    }
    
    // 代表PHPファイルからのリダイレクトでない場合はNG
    if( $boolRefererCheck === false ){
        // アクセスログ出力(リダイレクト判定NG)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-17",$host));
        
        // 不正操作によるアクセス警告画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,10810201);
        exit();
    }
    
    // browse系、access系、reg_n_up系の共有パーツを読み込み
    require_once ($root_dir_path . "/libs/webcommonlibs/web_parts_for_common.php");
?>
