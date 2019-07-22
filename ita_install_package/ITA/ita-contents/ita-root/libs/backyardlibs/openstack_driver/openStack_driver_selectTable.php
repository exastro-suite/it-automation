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
	//$do:SELECT、SELECTFORUPDATE等、SQLで行う処理
	//$arrayConfig:
	//$tbl_name:対象のテーブル名
	//$tbl_key:対象テーブルのキー
	//$tbl_name_jnl:対象のテーブルの履歴テーブル
	//$cln_execution_row:SELECTの条件、登録の内容
	//$bindArray:バインド条件の連想配列 
	//$objMTS:ログメッセージ系クラス  $objDBCA:データベース系クラスが必要
        
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
                                         $arrayValue,
                                         $temp_array );
    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];

    //バインド配列を適用
    foreach($bindArray as $key => $value){
    	$arrayUtnBind[$key] = $value;
    }
    
    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
    
    //生成したクエリをチェックする。
	if( $objQueryUtn->getStatus()===false ){
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103110") . ":" . $tbl_name . ":" . $do);
    }
    
    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103120") . ":" . $tbl_name . ":" . $do);
    }
    
    $r = $objQueryUtn->sqlExecute();
    if (!$r){
        throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-103130") . ":" . $tbl_name . ":" . $sqlUtnBody);
    }

    $tgt_execution_row = array();
    // レコードFETCH
    while ( $row = $objQueryUtn->resultFetch() ){
    	$tgt_execution_row[] = $row;
    }
    // DBアクセス事後処理
    unset($objQueryUtn);
?>
