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
    //  【特記事項】
    //      オーケストレータ別の設定記述あり
    //
    //////////////////////////////////////////////////////////////////////
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    //----オーケストレータ別の設定記述
    
    $varOrchestratorId = 3;
    $strOrchRPath = "ansible_driver/legacy/ns";
    
    //オーケストレータ別の設定記述----
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    ////////////////////////////////////////////////////////////////
    //  パラメータチェック(ガードロジック)                        //
    ////////////////////////////////////////////////////////////////
    
    $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
    if( $objIntNumVali->isValid($target_execution_no) === false ){
        // エラー箇所をメモ
        $error_info = '[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ',[MESSAGE]' . $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-102020");
    }
    else{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA'],$g);
        $objOLA->addFuncionsPerOrchestrator($varOrchestratorId, $strOrchRPath);
        $aryRetBody = $objOLA->srcamExecute($varOrchestratorId, $target_execution_no);
        
        //----返し値の解析
        $output_str = $aryRetBody[4][0];
        
        if( $aryRetBody[1] !== null ){
            $error_info = $aryRetBody[3];
            $warning_info = $aryRetBody[4][2];
        }
        else{
            $output_str = $aryRetBody[4][0];
            $info = $aryRetBody[4][1];
        }
        //返し値の解析----
    }

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
