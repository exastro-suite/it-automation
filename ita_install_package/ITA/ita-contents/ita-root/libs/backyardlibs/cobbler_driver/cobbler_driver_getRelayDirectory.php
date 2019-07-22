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
	/////////////////////////////////////////////////////////////////
	//DBからデータリレーストレージのディレクトリを取得             //
	/////////////////////////////////////////////////////////////////
	//$objMTS:ログメッセージ系クラス  $objDBCA:データベース系クラスが必要

    // SQL作成
    $arrayConfig = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "COBBLER_STORAGE_PATH_LNX"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    //すべての有効なレコードを取得
    $temp_array = array('WHERE'=>" DISUSE_FLAG = '0' ORDER BY DISP_SEQ ");
    
    $arrayValue = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "COBBLER_STORAGE_PATH_LNX"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                         'SELECT',
                                         'COBBLER_IF_INFO_ID',
                                         'B_COBBLER_IF_INFO',
                                         'B_COBBLER_IF_INFO_JNL',
                                         $arrayConfig,
                                         $arrayValue,
                                         $temp_array );
    
    $sqlUtnBody = $retArray[1];
    $arrayUtnBind = $retArray[2];
    
    $proObjQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
    
    if( $proObjQueryUtn->getStatus()===false ){
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ 
        throw new Exception( $objMTS->getSomeMessage("ITACBLC-ERR-2007", array(__LINE__)) );
    }
    
    $r = $proObjQueryUtn->sqlExecute();
    if (!$r){
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ 
        throw new Exception( $objMTS->getSomeMessage("ITACBLC-ERR-2009", array($sqlUtnBody,__LINE__)) );
    }
    
    if($proObjQueryUtn->effectedRowCount() <= 0){
    	// 異常フラグON
        $error_flag = 1;
        // 例外処理へ 
        throw new Exception( $objMTS->getSomeMessage("ITACBLC-ERR-5002") );
    }
    
    // レコードFETCH1件のみ
    $result_row = $proObjQueryUtn->resultFetch();
    
	$file_folder = $result_row['COBBLER_STORAGE_PATH_LNX'];
    
    // DBアクセス事後処理
    unset($objQueryUtn);
?>
