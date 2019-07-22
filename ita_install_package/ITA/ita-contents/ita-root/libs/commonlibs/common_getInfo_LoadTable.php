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
//  【概要】
//      メニューIDに紐付くテーブル及びカラム情報を取得
//
//  【特記事項】
//         getInfoOfLTUsingIdOfMenuForDBtoDBLink($intCheckTgtMenuId,$objDBCA)
//         $intCheckTgtMenuId: メニューID
//         $objDBCA:           $objDBCA   DBアクセスクラスオブジェクト
//
//////////////////////////////////////////////////////////////////////

    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    try{
        //おきまり
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");

        //getRepresentativeFilenameByMenuIDが実装されているので必要
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_functions_for_menu_info.php");

        // web_logとdev_logが実装されているので必要
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_php_functions.php");

        //TableControlAgentが実装されているので必要
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_request_init.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    function getInfoOfLTUsingIdOfMenuForDBtoDBLink($strMenuIdNumeric,$objDBCA,&$aryVariant=array(), &$arySetting=array()){
        //$strMenuIdNumeric[CMDB用メニュー一覧にあるものであることが前提]
        
        $aryValues = array();
        $intErrorType = null;
        $strErrMsg = "";
        
        $strFxName = __FUNCTION__;
        
        try{
            $tmpAryRetBody = getInfoOfLoadTable(sprintf("%010d", $strMenuIdNumeric),$aryVariant,$arySetting);
            if( $tmpAryRetBody[1] !== null ){
                $intErrorType = $tmpAryRetBody[1];
                
                // 例外処理へ
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . basename(__FILE__) . ',[LINE]' . __LINE__ . ')\n' .
                'Error info\n' .
                'RetBody:(' .   print_r($tmpAryRetBody[0],true) . ')\n' .
                'ErrorType:(' . print_r($tmpAryRetBody[1],true) . ')\n' . 
                'strErrMsg:(' . print_r($tmpAryRetBody[2],true) . ')');
            }
            $aryValues = $tmpAryRetBody[0];
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $strErrMsg = $tmpErrMsgBody;
        }
        return array($aryValues,$intErrorType,$strErrMsg);
    }
    
    function getInfoOfLoadTable($strMenuIdNumeric,&$aryVariant=array(), &$arySetting=array()){
        global $g;

        $aryValues = array();
        $intErrorType = null;
        $strErrMsg = "";

        $strFxName = __FUNCTION__;

        $registeredKey = "";
        $strLoadTableFullname = "";

        $objTable = null;

        $strHiddenTableMode = false;
        
        $aryInfoOfTable = array();
        $strPageType = "";
        
        $strUTNTableId = "";
        $strJNLTableId = "";

        $strUTNRIColumnId = "";
        $strJNLRIColumnId = "";

        $aryColumnInfo01 = array();
        $aryColumnInfo02 = array();

        try{
            $systemDir = "systems/{$strMenuIdNumeric}";
            $userDir = "users/{$strMenuIdNumeric}";
            if(file_exists("{$g['root_dir_path']}/webconfs/systems/{$strMenuIdNumeric}_loadTable.php")){
                $strLrWebRootToThisPageDir = "/systems/{$strMenuIdNumeric}";
            }
            else if(file_exists("{$g['root_dir_path']}/webconfs/users/{$strMenuIdNumeric}_loadTable.php")){
                $strLrWebRootToThisPageDir = "/users/{$strMenuIdNumeric}";
            }
            else{
                // 例外処理へ
                throw new Exception( '([FUNCTION]' . $strFxName . ',[FILE]' . basename(__FILE__) . ',[LINE]' . __LINE__ . ')\n' .
                'loadTable with menuId[' . $strMenuIdNumeric . '] does not exists.');
            }

            if( array_key_exists('aryTCABuildFunction',$g)===true ){
                $aryFunctions = $g['aryTCABuildFunction'];
                if( array_key_exists($strMenuIdNumeric,$aryFunctions) === true){
                    $registeredKey = $strMenuIdNumeric;
                    $strLoadTableFullname = "{$g['root_dir_path']}/webconfs{$strLrWebRootToThisPageDir}_loadTable.php";
                }
            }
            if( strlen($registeredKey) == 0 ){
                //----まだ登録されていない
                $strLoadTableFullname = "{$g['root_dir_path']}/webconfs{$strLrWebRootToThisPageDir}_loadTable.php";
                if( file_exists($strLoadTableFullname)===true ){
                    require_once($strLoadTableFullname);
                    $registeredKey = $strMenuIdNumeric;
                }
                else{
                    // 00_loadTable.phpが存在しない場合 
                    $intErrorType = 100;
                    throw new Exception( '([FUNCTION]' . $strFxName . ',[FILE]' . basename(__FILE__) . ',[LINE]' . __LINE__ . ')\n' .
                    "[" . $strLoadTableFullname . "] not exists");
                }
                //まだ登録されていない----
            }
            if( 0 < strlen($registeredKey) ){
                $objTable = loadTable($registeredKey,$aryVariant,$arySetting);
                if($objTable === null){
                    // 00_loadTable.phpの読込失敗
                    $intErrorType = 101;
                    $strErrMsg = "[" . $strLoadTableFullname . "] Analysis Error";
                }
            }
            if( $objTable !== null ){
                $aryColumns = $objTable->getColumns();
                
                if( is_a($objTable,"TemplateTableForReview")=== true ){
                    //----ReView用テーブル
                    $strPageType = $objTable->getPageType();
                    
                    $tmpStrRIColumn = "";
                    $tmpStrLockTargetColumn = "";
                    foreach($aryColumns as $strColumnId=>$objColumn){
                        if( is_a($objColumn,"RowIdentifyColumn") === true ){
                            $tmpStrRIColumn = $objColumn->getID();
                            continue;
                        }
                        if( is_a($objColumn,"LockTargetColumn") === true ){
                            $tmpStrLockTargetColumn = $objColumn->getID();
                            continue;
                        }
                    }
                    $strUTNRIColumnId = $tmpStrRIColumn;
                    $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();
                    
                    $strLockTargetColumnId = $tmpStrLockTargetColumn;
                    unset($tmpStrRIColumn);
                    unset($tmpStrLockTargetColumn);
                    
                    $aryRequiredColumnId = array(
                        "RowIdentify"    =>$strUTNRIColumnId
                        
                        ,"LockTarget"    =>$strLockTargetColumnId
                        ,"EditStatus"    =>$objTable->getEditStatusColumnID()
                        
                        ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                        ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                        ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                        
                        ,"Note"          =>$objTable->getRequiredNoteColumnID()
                        
                        ,"ApplyUpdate"   =>$objTable->getApplyUpdateColumnID()
                        ,"ApplyUser"     =>$objTable->getApplyUserColumnID()
                        ,"ConfirmUpdate" =>$objTable->getConfirmUpdateColumnID()
                        ,"ConfirmUser"   =>$objTable->getConfirmUserColumnID()
                        
                        ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                        ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                        ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                        ,"JnlSeqNo"      =>$strJNLRIColumnId
                        ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                        ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                    );
                    
                    if( $strPageType == "apply" || $strPageType == "confirm" ){
                        $strUTNTableId = $objTable->getDBMainTableHiddenID();
                        $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                        if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                            $strHiddenTableMode = true;
                        }
                        else{
                            $strUTNTableId = $objTable->getDBMainTableBody();
                            $strJNLTableId = $objTable->getDBJournalTableBody();
                        }
                    }
                    else{
                        $strUTNTableId = $objTable->getDBResultTableHiddenID();
                        $strJNLTableId = $objTable->getDBResultJournalTableHiddenID();
                        if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                            $strHiddenTableMode = true;
                        }
                        else{
                            $strUTNTableId = $objTable->getDBResultTableBody();
                            $strJNLTableId = $objTable->getDBResultJournalTableBody();
                        }
                    }
                    
                    $aryInfoOfTable = array("PAGE_TYPE"        =>$strPageType
                                           ,"UTN"              =>array("OBJECT_ID"           =>$strUTNTableId
                                                                      ,"ROW_INDENTIFY_COLUMN"=>$strUTNRIColumnId
                                                                       )
                                           ,"JNL"              =>array("OBJECT_ID"           =>$strJNLTableId
                                                                      ,"ROW_INDENTIFY_COLUMN"=>$strJNLRIColumnId
                                                                       )
                                           ,"UTN_ROW_INDENTIFY"=>$strUTNRIColumnId
                                           ,"JNL_SEQ_NO"       =>$strJNLRIColumnId
                                           ,"REQUIRED_COLUMNS" =>$aryRequiredColumnId
                                            );
                    //ReView用テーブル----
                }
                else{
                    //----標準テーブル
                    $strUTNRIColumnId = $objTable->getRowIdentifyColumnID();
                    $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();
                    
                    $aryRequiredColumnId = array(
                        "RowIdentify"    =>$strUTNRIColumnId
                        ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                        ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                        ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                        
                        ,"Note"          =>$objTable->getRequiredNoteColumnID()

                        ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                        ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                        ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                        ,"JnlSeqNo"      =>$strJNLRIColumnId
                        ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                        ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                    
                    );
                    
                    $strUTNTableId = $objTable->getDBMainTableHiddenID();
                    $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                    if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                        $strHiddenTableMode = true;
                    }
                    else{
                        $strUTNTableId = $objTable->getDBMainTableBody();
                        $strJNLTableId = $objTable->getDBJournalTableBody();
                    }
                    $aryInfoOfTable = array("PAGE_TYPE"        =>$strPageType
                                           ,"UTN"              =>array("OBJECT_ID"           =>$strUTNTableId
                                                                      ,"ROW_INDENTIFY_COLUMN"=>$strUTNRIColumnId
                                                                       )
                                           ,"JNL"              =>array("OBJECT_ID"           =>$strJNLTableId
                                                                      ,"ROW_INDENTIFY_COLUMN"=>$strJNLRIColumnId
                                                                       )
                                           ,"UTN_ROW_INDENTIFY"=>$strUTNRIColumnId
                                           ,"JNL_SEQ_NO"       =>$strJNLRIColumnId
                                           ,"REQUIRED_COLUMNS" =>$aryRequiredColumnId
                                            );
                    //標準テーブル----
                }
                
                //必須カラムのID----
                
                //----カラムインスタンスの取得
                
                
                foreach($aryColumns as $strColumnId=>$objColumn){
                    $boolAddInfo = false;
                    if( in_array($strColumnId,$aryRequiredColumnId) === false ){
                        //----必須カラムではない任意カラム
                        if( $strHiddenTableMode === true ){
                            //----VIEWを表示、TABLEを更新させる設定の場合
                            if( $objColumn->isDBColumn() === true && $objColumn->isHiddenMainTableColumn() ){
                                $boolAddInfo = true;
                            }
                            //VIEWを表示、TABLEを更新させる設定の場合----
                        }
                        else{
                            //----TABLEを表示/更新させる設定の場合
                            if( $objColumn->isDBColumn() === true ){
                                $boolAddInfo = true;
                            }
                            //----TABLEを表示/更新させる設定の場合
                        }
                        if( $boolAddInfo === true ){
                            if("IDColumn" === get_class($objColumn)){
                                $aryColumnInfo01[] = array($strColumnId,$objColumn->getColLabel(true),$objColumn->getMasterTableIDForFilter(),$objColumn->getKeyColumnIDOfMaster(),$objColumn->getDispColumnIDOfMaster());
                            }
                            else{
                                $aryColumnInfo01[] = array($strColumnId,$objColumn->getColLabel(true),"","","");
                            }
                        }
                        else{
                            $aryColumnInfo02[] = array($strColumnId,$objColumn->getColLabel(true));
                        }
                        //必須カラムではない任意カラム----
                    }
                }
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array("TABLE_INFO"       =>$aryInfoOfTable
                          ,"TABLE_IUD_COLUMNS"=>$aryColumnInfo01
                          ,"OTHER_COLUMNS"    =>$aryColumnInfo02                         
                           );
        return array($aryValues,$intErrorType,$strErrMsg);
    }

define('LOCAL_DEBUG',FALSE);
if(LOCAL_DEBUG){
    $intCheckTgtMenuId = '2100040711';

    list($aryTemp,$intErrorType,$strErrMsg)  = getInfoOfLTUsingIdOfMenuForDBtoDBLink($intCheckTgtMenuId,$objDBCA);

    // テーブル名
    echo "テーブル名:" . $aryTemp['TABLE_INFO']['UTN']['OBJECT_ID'] . "\n";
    echo "ID:" . $aryTemp['TABLE_INFO']['UTN']['ROW_INDENTIFY_COLUMN'] . "\n";
    $idx=1;
    foreach($aryTemp['TABLE_IUD_COLUMNS'] as $no=>$list){
       // カラム名,カラムタイトル
       echo sprintf("[%02d]%40s    :%s\n",$idx,$list[0],$list[1]);
       $idx++;
    }
}
    
?>
