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
/* ルートディレクトリの取得 */
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100160001/validator.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-104001");

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

    $table = new TableControlAgent('B_ANS_COMVRAS_USLIST','ROW_ID', 'No', 'B_ANS_COMVRAS_USLIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('B_ANS_COMVRAS_USLIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANS_COMVRAS_USLIST_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel('AnsibleCommonVarsUsedList');
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile','AnsibleCommonVarsUsedList');

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----

    $c = new IDColumn('FILE_ID','File Type','B_ANS_COMVRAS_USLIST_F_ID','ROW_ID','NAME','',array('OrderByThirdColumn'=>'ROW_ID'));
    $c->setDescription("File Type\nPlaybook: Legacy playbook\nDialog file: Pioneer dialog file\nRole package: LegacyRole role package\nTemplate file: Ansible common template file");
    $table->addColumn($c);

    $c = new IDColumn('VRA_ID','Variable Type','B_ANS_COMVRAS_USLIST_V_ID','ROW_ID','NAME','',array('OrderByThirdColumn'=>'ROW_ID'));
    $c->setDescription("Variable Type\nGBL:Global Variable\nCPF:File Variable\nTPF:Template Variable");
    $table->addColumn($c);

    $c = new NumColumn('CONTENTS_ID','Primary key');
    $c->setDescription('Primary key of target database');
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('VAR_NAME','Variable name');
    $c->setDescription('Variable name');
    $table->addColumn($c);

    $c = new TextColumn('REVIVAL_FLAG','Revival Flag');
    $c->setDescription('Revival Flag');
    $table->addColumn($c);

//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
