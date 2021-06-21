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

    require_once( dirname(__FILE__) ."/82_conductor_no_register.php" );

    if($strCommand == "EDIT"){
        $aryForResultData = conductorRegisterFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData,true);

    }else if ($strCommand == "FILTER" || $strCommand == "FILTER_DATAONLY"){
         $filterArray = array();
         foreach ($objJSONOfReceptedData as $key => $value) {
           if($key == "conductor_name"){
             $filterArray['3'] = $value;
           }elseif ($key == "id") {
             $filterArray['2'] = $value;
           }elseif ($key == "note") {
             $filterArray['4'] = $value;
           }elseif ($key == "LUT4U") {
             $filterArray['8'] = $value;
           }elseif ($key == "ACCESS_AUTH") {
             $filterArray['5'] = $value;
           }
         }

         $subStrCommand = $strCommand;
         $strCommand = "FILTER";
         $aryForResultData = ReSTCommandFilterExecute($strCommand,$filterArray,$objTable,true);
         if($aryForResultData[0]['ResultStatusCode'] == 200){
             $intConductorClassId = 0;
             $strDdisuse = "";
             $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'][0] = conductorJsonGetTitle();
             if($subStrCommand == "FILTER_DATAONLY"){
               unset($aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'][0]);
               array_values($aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY']);
             }
             for ($i=0; $i < $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['RECORD_LENGTH']; $i++) {
               $intConductorClassId = $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'][$i+1][2];
               $strDdisuse = $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'][$i+1][1];
               $tmpAry = convertConductorClassJson($intConductorClassId,$strDdisuse,1);
               $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'][$i+1] = $tmpAry;
               $firstTimeFlg = 1;
             }
         }
    }else if ($strCommand == "INFO" ){
         $aryForResultData = ReSTCommandFilterExecute($strCommand,$objJSONOfReceptedData,$objTable);
         $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['INFO'] = conductorJsonGetTitle();
    }

?>
