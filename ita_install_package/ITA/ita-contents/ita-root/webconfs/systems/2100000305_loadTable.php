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
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){

    global $g;
    $root_dir_path = $g['root_dir_path'];
    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-107070");

    require_once ($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
    $dbobj = new CommonDBAccessCoreClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);

    $sqlBody   = "select ANSIBLE_EXEC_MODE from B_ANSIBLE_IF_INFO where DISUSE_FLAG='0'";
    $arrayBind = array();
    $objQuery  = "";
    $ansible_exec_mode = 0;
    $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
    if($ret === false) {
        web_log($dbobj->GetLastErrorMsg());
    } else {
        if($objQuery->effectedRowCount() == 0) {
            $message = sprintf("Recode not found. (Table:B_ANSIBLE_IF_INFO");
            web_log(basename(__FILE__),__LINE__,$message);
        } else {
            $row = $objQuery->resultFetch();
            // ANSIBLE_EXEC_MODE=2 ansible tower
            $ansible_exec_mode = $row['ANSIBLE_EXEC_MODE'];
        }
    }
/*
作業パターン
*/
    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('C_PATTERN_PER_ORCH','PATTERN_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-107080"), 'C_PATTERN_PER_ORCH_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['PATTERN_ID']->setSequenceID('C_PATTERN_PER_ORCH_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_PATTERN_PER_ORCH_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);
    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-107090"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITABASEH-MNU-108010"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $table->addUniqueColumnSet(array('ITA_EXT_STM_ID','PATTERN_NAME'));

    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('PATTERN_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108020"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108030"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    $c = new IDColumn('ITA_EXT_STM_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108040"),'B_ITA_EXT_STM_MASTER','ITA_EXT_STM_ID','ITA_EXT_STM_NAME','B_ITA_EXT_STM_MASTER');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108050"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_ITA_EXT_STM_MASTER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('ITA_EXT_STM_ID');
    $c->setJournalDispIDOfMaster('ITA_EXT_STM_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new NumColumn('TIME_LIMIT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108060"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108070"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
	$c->setValidator(new IntNumValidator(null,null));
    $table->addColumn($c);

    $wanted_filename = "ita_ansible-driver";
    if( file_exists($root_dir_path."/libs/release/".$wanted_filename) ) {
        $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-108075") );

            $c = new IDColumn('ANS_HOST_DESIGNATE_TYPE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108080"),'    B_HOST_DESIGNATE_TYPE_LIST','HOST_DESIGNATE_TYPE_ID','HOST_DESIGNATE_TYPE_NAME','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108090"));//エクセル・ヘッダでの説明
            $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true    )であることも必要。
            $c->setJournalTableOfMaster('B_HOST_DESIGNATE_TYPE_LIST_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('HOST_DESIGNATE_TYPE_ID');
            $c->setJournalDispIDOfMaster('HOST_DESIGNATE_TYPE_NAME');
            $cg->addColumn($c);

            $c = new IDColumn('ANS_WINRM_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108100"),'D_FLAG_LIST_01','    FLAG_ID','FLAG_NAME','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108110"));//エクセル・ヘッダでの説明
            $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true    )であることも必要。
            $cg->addColumn($c);
            
            /* 親Playbookのヘッダーセクション */
            $objVldt = new MultiTextValidator(0,512,false);
            $c = new MultiTextColumn('ANS_PLAYBOOK_HED_DEF',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000008"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000009"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $c->setRequired(false);
            $cg->addColumn($c);

            /* Ansible-Playbook実行時のMovement固有オプションパラメータ */
            $objVldt = new SingleTextValidator(0,512,false);
            $c = new TextColumn('ANS_EXEC_OPTIONS',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000010"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000011"));
            $c->setValidator($objVldt);
            $c->setRequired(false);
            $cg->addColumn($c);

        $table->addColumn($cg);

        // ANSIBLEインターフェース情報の実行エンジンがTowerの場合にTower利用情報を表示
        if($ansible_exec_mode == 2) {

            // Tower利用情報
            $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-108241") );

                // virtualenv
                $c = new IDColumn('ANS_VIRTUALENV_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108242"),'B_ANS_TWR_VIRTUALENV','VIRTUALENV_NAME','VIRTUALENV_NAME','');
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108243")); //エクセル・ヘッダでの説明
                $c->setHiddenMainTableColumn(true); //コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
            $cg->addColumn($c);

            $table->addColumn($cg);
        }
    }

    // OpenStack利用情報
    $wanted_filename = "ita_openstack-driver";
    if( file_exists($root_dir_path."/libs/release/".$wanted_filename) ) {
        $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-108200") );

            // Heatテンプレート
            $c = new FileUploadColumn('OPENST_TEMPLATE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108210"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108220"));//エクセル・ヘッダでの説明
            $cg->addColumn($c);

            // 環境設定ファイル
            $c = new FileUploadColumn('OPENST_ENVIRONMENT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108230"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108240"));//エクセル・ヘッダでの説明
            $cg->addColumn($c);

        $table->addColumn($cg);
    }

    // DSC利用情報
    $wanted_filename = "ita_dsc-driver";
    if( file_exists($root_dir_path."/libs/release/".$wanted_filename) ) {
        $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-108300") );

            // エラーリトライタイムアウト
            $c = new FileUploadColumn('DSC_RETRY_TIMEOUT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-108310"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-108320"));//エクセル・ヘッダでの説明
            $cg->addColumn($c);

        $table->addColumn($cg);
    }


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
