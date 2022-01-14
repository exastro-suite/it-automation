<?php
//   Copyright 2021 NEC Corporation
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
    //  【処理概要】
    //    ・WebDBCore機能を用いたWebページの、動的再描画などを行う。
    //
    //////////////////////////////////////////////////////////////////////

    if($strCommand == "INFO"){
      $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
      
      $aryForResultData['resultdata'] = array('CONTENTS'=>array('BODY'=>array()));
                                                              
      //リリースファイル読み込み
      $releaseFile=array();
      foreach(glob($g['root_dir_path'] . "/libs/release/*") as $file) {
        if( $file==$g['root_dir_path'] . '/libs/release/ita_base' ){
            $releaseBase=file_get_contents($file);
        }else{
          array_push($releaseFile,file_get_contents($file));
        }
      }
      
      //バージョン取得
      $strVersion = str_replace('Exastro IT Automation Base functions version ', '',$releaseBase);
      $strVersion = str_replace(PHP_EOL, '', $strVersion);
      
      //インストール済ドライバ取得
      $driverName = array();
      $tmpAry = array();
      foreach($releaseFile as $release) {
        $tmpAry = explode(" ", $release);
        $driverName[] = $tmpAry[3];
      }
      
      $tmpAry = array();
      $tmpAry[] = $releaseBase;
      $tmpAry = array_merge($tmpAry,$releaseFile);
      $aryForResultData['resultdata']['CONTENTS']['BODY']['version'] = $strVersion;
      $aryForResultData['resultdata']['CONTENTS']['BODY']['installed_driver'] = $driverName;
    }else{
      //----不正な要求（内容が不正）
      if( isset($expandRestCommandPerMenu) === false ){
        // WARNING:ILLEGAL_ACCESS, DETAIL:UNEXPECTED X-COMMAND SENT FOR REST CONTENT.
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-115008"));
        webRequestForceQuitFromEveryWhere(400,11510807);
        //不正な要求（内容が不正）----
      }
    }

?>