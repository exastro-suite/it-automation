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
	//$do:INSERT、UPDATE等、SQLで行う処理
	//$arrayConfig:
	//$tbl_name:対象のテーブル名
	//$tbl_key:対象テーブルのキー
	//$tbl_name_jnl:対象のテーブルの履歴テーブル
	//$cln_execution_row:登録の内容
	//$objMTS:ログメッセージ系クラス  $objDBCA:データベース系クラスが必要
	//シーケンスはロックしておく
        
    ////////////////////////////////////////////////////////////////
    // テーブルに登録                                             //
    ////////////////////////////////////////////////////////////////
    // SQL作成＋バインド用変数準備
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         $do,
                                         $tbl_key,
                                         $tbl_name,
                                         $tbl_name_jnl,
                                         $arrayConfig,
                                         $cln_execution_row );
 	
    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];
    
    $sqlJnlBody = $retArray[3];
    $arrayJnlBind = $retArray[4];
    
    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
    $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);
    
    if( $objQueryUtn->getStatus()===false || 
        $objQueryJnl->getStatus()===false ){
        // 異常フラグON
        $error_flag = 1;
        
        // 例外処理へ "Error occured([FILE]｛｝[LINE]｛｝[ETC-Code]｛｝)"
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103110") . ":" . $tbl_name . ":" . $do);
    }
    
    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
        $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
        // 異常フラグON
        $error_flag = 1;
        
        // 例外処理へ "Error occured([FILE]｛｝[LINE]｛｝[ETC-Code]｛｝)"
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103120") . ":" . $tbl_name . ":" . $do);
    }

    $rUtn = $objQueryUtn->sqlExecute();
    if($rUtn!=true){
        // 異常フラグON
        $error_flag = 1;
        
        $GetError = $objQueryUtn->getLastError();
        
        // 例外処理へ "Error occured([FILE]｛｝[LINE]｛｝[ETC-Code]｛｝)"
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103130") . ":" . $tbl_name . ":" . $sqlUtnBody . ":" . $GetError);
    }
    
    $rJnl = $objQueryJnl->sqlExecute();
    if($rJnl!=true){
        // 異常フラグON
        $error_flag = 1;
        
        // 例外処理へ "Error occured([FILE]｛｝[LINE]｛｝[ETC-Code]｛｝)"
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103130") . ":" . $tbl_name . "[JNL]:" . $sqlUtnBody);
    }
    
    // DBアクセス事後処理
    unset($objQueryUtn);
    unset($objQueryJnl);
?>
