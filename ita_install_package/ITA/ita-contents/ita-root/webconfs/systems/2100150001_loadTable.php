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

require_once ( $root_dir_path . "/libs/webindividuallibs/systems/2100150001/validator.php");
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100701");

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

    $table = new TableControlAgent('F_MATERIAL_IF_INFO','ROW_ID', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100702"), 'F_MATERIAL_IF_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_MATERIAL_IF_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_MATERIAL_IF_INFO_JSQ');
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setResultCount(array('update'  =>array('name'=>$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100703"),'ct'=>0),
                                                            'error'   =>array('name'=>$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100704"),'ct'=>0)
                                                           )
                                                     );
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setCommandArrayForEdit(array(2=>$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100703"),
                                                                   )
                                                             );
    $outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
    $tmpAryColumn['DISUSE_FLAG']->setOutputType("print_table", $outputType);
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100705"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100706"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----



    // リモートリポジトリURL
    $c = new TextColumn('REMORT_REPO_URL',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100707"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100708"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $objVldt = new RemortRepoUrlValidator(1, 256, false);
	$c->setValidator($objVldt);
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // ブランチ
    $c = new TextColumn('BRANCH',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100715"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100716"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $objVldt = new RemortRepoUrlValidator(0, 256, false);
	$c->setValidator($objVldt);
    $table->addColumn($c);

    // クローンリポジトリ
    $c = new TextColumn('CLONE_REPO_DIR',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100709"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100710"));//エクセル・ヘッダでの説明
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $objVldt = new CloneRepoDirValidator(1, 256, false);
	$c->setValidator($objVldt);
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // パスワード
	$objVldt = new SingleTextValidator(0,64,false);
    $c = new PasswordColumn('PASSWORD',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100711"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100712"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setUpdateRequireExcept(1);//1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setEncodeFunctionName("ky_encrypt");
    $table->addColumn($c);


    //----リンクボタン
    $c = new LinkButtonColumn('SYNCHRONIZATION_BUTTON',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100713"), $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-100714"), 'initial_sync',array(':REMORT_REPO_URL', ':BRANCH', ':CLONE_REPO_DIR', ':PASSWORD', 'this')); 
    $table->addColumn($c);
    //リンクボタン----


//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
