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

    function getFileNameAndPath($strSearchDirPath, $strFilePrefix='', $strFilePostFix=''){
        $boolExecuteContinue = true;
        $aryFileList = array();
        $target_file_path = "";
        if( is_string($strSearchDirPath)===false ){
            $boolExecuteContinue = false;
        }
        if( is_string($strFilePrefix)===false ){
            $boolExecuteContinue = false;
        }
        if( is_string($strFilePostFix)===false ){
            $boolExecuteContinue = false;
        }
        if( $boolExecuteContinue===true ){
            $aryFile = scandir($strSearchDirPath);
            foreach($aryFile as $strFileObjectName){
                if( 0 < strlen($strFilePrefix) ){
                    if( strpos($strFileObjectName,$strFilePrefix)!==0 ){
                        //----見つからなかった、または、先頭ではなかった
                        continue;
                        //見つからなかった、または、先頭ではなかった----
                    }
                }
                if( 0 < strlen($strFilePostFix) ){
                    if( strpos(strrev($strFileObjectName),strrev($strFilePostFix))!==0 ){
                        //----見つからなかった、または、末尾ではなかった
                        continue;
                        //見つからなかった、または、末尾ではなかった----
                    }
                }
                $strCheckPath = $strSearchDirPath."/".$strFileObjectName;
                if ( is_file($strCheckPath)===true ){
                    $aryFileList[] = $strCheckPath;
                }
            }
        }
        return $aryFileList;
    }

    function getMircotime($mode=0){
        //----$mode[0:Unixtimestamp/1:YmdHis/2:Y/m/d H:i:s]
        $strFormat = "";
        $arrayStr = explode(" ", microtime());
        if( $mode == 2 ){
            $strFormat = "Y/m/d H:i:s";    //$strFormat = "Y/m/d H:i:";
        }else if( $mode == 1 ){
            $strFormat = "YmdHis";        //$strFormat = "YmdHi";
        }
        if( $strFormat == "" ){
            $ret = $arrayStr[1].".".substr(str_replace("0.","",$arrayStr[0]),0,6);
        }else{
            $ret = sprintf("%s.%06d",date($strFormat, $arrayStr[1]) ,($arrayStr[0]*1000000));
        }
        return $ret;
    }

    function convFromStrDateToUnixtime($str,$boolPlusMirco=false){
        //----$str[YYYYMMDDNNSS(.000000)||YYYY/MM/DD HH:NN:SS(.000000)]
        //----$boolPlusMirco:マイクロ秒付記モード
        $array = explode(".", $str);
        $intTime = strtotime($array[0]);
        $decTime = "";
        if($boolPlusMirco === true){
            if( isset($array[1]) === true ){
                $decTime = ".".sprintf('%06d', $array[1]);
            }else{
                $decTime = ".000000";
            }
        }
        $ret = $intTime.$decTime;
        return $ret;
    }

    function convFromUnixtimeToStrDate($str,$boolPlusMirco=false,$mode=0){
        //----$str[unixtimestamp(.000000)]
        //----$boolPlusMirco:マイクロ秒付記モード
        //----$mode[0:Unixtimestamp||1:YmdHis||2:Y/m/d H:i:s]
        $strFormat = "";
        if($boolPlusMirco === true){
            $array = explode(".", $str);
            $intTime = date($array[0]);
            $decTime = ".".sprintf('%06d', $array[1]);
        }else{
            $intTime = $str;
            $decTime = ".000000";
        }
        if( $mode == 2 ){
            $strFormat = "Y/m/d H:i:";
        }else if( $mode == 1 ){
            $strFormat = "YmdHi";
        }
        if( $strFormat == "" ){
            $ret = $intTime.$decTime;
        }else{
            $sec = date("s",$intTime).$decTime;
            $ret = date($strFormat, $intTime).$sec;
        }
        return $ret;
    }
    
    // ---- pgrepによるansibleプロセス実行チェック
    // 【引数】
    // $strPlaybookPath : playbookフルパス
    // 【戻り値】 
    // true  : 実行中
    // false : 未実行
    function chkAnsibleRunning($strPlaybookPath){
        $rtn = false;

        // #1055 2016/09/28 Update start centos7ではpgrepで自分を検出している模様
        // pgrep => psに変更
        $strBuildCommand     = "ps -efw|grep '{$strPlaybookPath}' |grep -v grep|wc -l";

        for($i = 0; $i < 3; $i++){
            $strPidListBody = shell_exec($strBuildCommand);

            // 余計な改行を取り除く
            $count = sprintf('%d',$strPidListBody);

            if ( $count != '0' ){
                //----プロセスIDが発見された
                $rtn = true;
                break;
            }
            if($i < 2) {
                usleep(500000);
            }

        }
        return $rtn;
    }
?>
