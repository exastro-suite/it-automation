<?php
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

//    /* ルートディレクトリの取得 */
//    if ( empty($root_dir_path) ){
//        $root_dir_temp = array();
//        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
//        $root_dir_path = $root_dir_temp[0] . "ita-root";
//    }

//    //リリースファイル読み込み
//    $releaseFile=array();
//    foreach(glob($root_dir_path . "/libs/release/*") as $file) {
//        array_push($releaseFile,file_get_contents($file));
//    }
    
    
//    $releaseFile = "";
//    $break = '<br/>';
//    foreach(glob($root_dir_path . "/libs/release/*") as $file) {
//        $releaseFile = $releaseFile . file_get_contents($file) . $break;
//    }
//    $arrayWebSetting = array();
//    $arrayWebSetting['page_info'] = $releaseFile;

$arrayWebSetting['page_info'] = "";

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

    $table = new TableControlAgent('A_MENU_LIST','MENU_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-910001"), 'A_MENU_JNL', $tmpAry);
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
