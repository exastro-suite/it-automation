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
    //  【処理概要】
    //    ・WebDBCore機能を用いたWebページの、動的再描画などを行う。
    //
    //////////////////////////////////////////////////////////////////////
    
    require_once( dirname(__FILE__) ."/81_conductor_ins_control.php" );
    if($strCommand == "CANCEL" || $strCommand == "SCRAM" || $strCommand == "RELEASE"){
      if( is_array($objJSONOfReceptedData) !== true ){
        $intResultStatusCode = 400;
        $arrayRetBody = $g['requestByREST']['preResponsContents']['errorInfo'];
        $arrayRetBody['Error'] = array();
        $arrayRetBody['Error'][] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-312");
        $arrayRetBody['Error'][] = array(0 => $g['objMTS']->getSomeMessage("ITAWDCH-ERR-317"));
        $aryForResultData[0] = array('ResultStatusCode'=>$intResultStatusCode,
                                     'ResultData'=>$arrayRetBody);
        $aryForResultData[1] = null;
      }else{
        $aryForResultData = conductorInstanceControlFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData);
      }
    }else{
      $aryForResultData = conductorInstanceControlFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData);
    }
?>