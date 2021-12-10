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

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_02_access.php");
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_common.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    class Db_Access extends Db_Access_Core {
        //-- サイト個別PHP要素、ここから--

        /////////////////////
        // 新規登録
        /////////////////////
        function registerTable($menuData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $tranStartFlg = false;
            $arrayResult = array();

            
            try{

                require_once ( $g["root_dir_path"] . "/libs/webcommonlibs/table_control_agent/03_registerTable.php");
                require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
                
                // トランザクション開始
                $varTrzStart = $g["objDBCA"]->transactionStart();
                if ($varTrzStart === false) {
                    web_log($g["objMTS"]->getSomeMessage("ITABASEH-ERR-900015", array(basename(__FILE__), __LINE__)));
                    $msg = $g["objMTS"]->getSomeMessage("ITABASEH-ERR-900015", array(basename(__FILE__), __LINE__));
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $tranStartFlg = true;

                $aryVariant = array("TCA_PRESERVED" => array("TCA_ACTION" => array("ACTION_MODE" => "DTUP_singleRecRegister")));
                
                // jsonからPHP配列に変換
                $menuData = json_decode($menuData,true);
                if(!array_key_exists("MENUGROUP_FOR_INPUT",$menuData['menu']))  $menuData['menu']['MENUGROUP_FOR_INPUT'] = "";
                if(!array_key_exists("MENUGROUP_FOR_SUBST",$menuData['menu']))  $menuData['menu']['MENUGROUP_FOR_SUBST'] = "";
                if(!array_key_exists("MENUGROUP_FOR_VIEW",$menuData['menu']))   $menuData['menu']['MENUGROUP_FOR_VIEW'] = "";
                if(!array_key_exists("VERTICAL",$menuData['menu']))             $menuData['menu']['VERTICAL'] = "";

                // 縦メニュー利用とリピートのチェック
                if($menuData['menu']['VERTICAL'] == 1 && !array_key_exists('r1',$menuData['repeat'])){
                    $arrayResult[0] = "002";
                    $arrayResult[1] = "";
                    $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1024');
                    throw new Exception();
                }

                // リピート数のチェック
                if($menuData['menu']['VERTICAL'] == 1 && array_key_exists('r1',$menuData['repeat'])){
                    if($menuData['repeat']['r1']['REPEAT_CNT'] < 2 || $menuData['repeat']['r1']['REPEAT_CNT'] > 99) {
                        $arrayResult[0] = "002";
                        $arrayResult[1] = "";
                        $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1025');
                        throw new Exception();
                    }
                }

                //////////////////////////
                // メニュー作成情報を登録
                //////////////////////////

                $arrayRegisterData = array("MENU_NAME"              => $menuData['menu']['MENU_NAME'],
                                           "TARGET"                 => $menuData['menu']['TARGET'],
                                           "DISP_SEQ"               => $menuData['menu']['DISP_SEQ'],
                                           "PURPOSE"                => $menuData['menu']['PURPOSE'],
                                           "VERTICAL"               => $menuData['menu']['VERTICAL'],
                                           "MENUGROUP_FOR_INPUT"    => $menuData['menu']['MENUGROUP_FOR_INPUT'],
                                           "MENUGROUP_FOR_SUBST"    => $menuData['menu']['MENUGROUP_FOR_SUBST'],
                                           "MENUGROUP_FOR_VIEW"     => $menuData['menu']['MENUGROUP_FOR_VIEW'],
                                           "DESCRIPTION"            => $menuData['menu']['DESCRIPTION'],
                                           "ACCESS_AUTH"            => $menuData['menu']['ACCESS_AUTH'],
                                           "NOTE"                   => $menuData['menu']['NOTE'],
                                          );
                
                $g["page_dir"] = "2100160001";

                // 登録処理
                $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160001", 4);
                if($arrayResult[0] !== "000"){
                    throw new Exception();
                }
                // 登録したメニューを記録
                $menuData['menu']['CREATE_MENU_ID'] = json_decode($arrayResult[2],true)['CREATE_MENU_ID'];

                //////////////////////////
                // カラムグループ情報を登録
                //////////////////////////
                
                // カラムグループ情報テーブルを取得
                $columnGroupTable = new ColumnGroupTable($g["objDBCA"], $g["db_model_ch"]);
                $sql = $columnGroupTable->createSselect("WHERE DISUSE_FLAG = '0'");
                $result = $columnGroupTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg);
                    throw new Exception();
                }
                $cgArray = $result;
                
                foreach($menuData['group'] as &$groupData){
                    if(array_key_exists('REPEAT_GROUP',$groupData) && true === $groupData['REPEAT_GROUP']){
                        continue;
                    }
                    $groupData['PA_COL_GROUP_ID'] = "";
                    $skipFlag = false;
                    if($groupData['PARENT'] == "")
                        $fullPath = $groupData['COL_GROUP_NAME'];
                    else{
                        $fullPath = $groupData['PARENT'] . '/' .  $groupData['COL_GROUP_NAME'];
                    }
                    foreach($cgArray as $cgData){
                        if($fullPath == $cgData['FULL_COL_GROUP_NAME']){
                            $skipFlag = true;
                            break;
                        }
                        if($cgData['FULL_COL_GROUP_NAME'] == $groupData['PARENT']){
                            $groupData['PA_COL_GROUP_ID'] = $cgData['COL_GROUP_ID'];
                        }
                    }
                    // 既存データがあった場合スキップ
                    if(true == $skipFlag){
                        continue;
                    }
                    $arrayRegisterData = array("PA_COL_GROUP_ID" => $groupData['PA_COL_GROUP_ID'],
                                               "COL_GROUP_NAME" => $groupData['COL_GROUP_NAME'],
                                               "DESCRIPTION" => ""
                                              );

                    $g["page_dir"] = "2100160008";

                    // 登録処理
                    $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160008", 4);

                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                    $cgArray[] = json_decode($arrayResult[2],true);
                }
                unset($groupData);
                
                //////////////////////////
                // メニュー作成項目情報を登録
                //////////////////////////
                $repeatCount = 0;
                $columnIdConvArray = array(); //一意制約の項目ID変換用
                foreach($menuData['item'] as $key => &$itemData){
                    //作成対象「データシート」で入力方式「パラメータシート参照」(ID:11)を利用している場合はエラー
                    if($menuData['menu']['TARGET'] == 2 && $itemData['INPUT_METHOD_ID'] == 11){
                        $arrayResult[0] = "002";
                        $arrayResult[1] = "";
                        $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5026');
                        throw new Exception();
                    }

                    if($itemData['REPEAT_ITEM'] == true){
                        $repeatCount += 1;
                    }
                    if($itemData['REQUIRED'] === true){
                        $required = "1";
                    }
                    else{
                        $required = "";
                    }
                    if($itemData['UNIQUED'] === true){
                        $uniqued= "1";
                    }
                    else{
                        $uniqued = "";
                    }
                    if($itemData['COL_GROUP_ID'] != ""){
                        foreach($cgArray as $cgData){
                            if($itemData['COL_GROUP_ID'] == $cgData['FULL_COL_GROUP_NAME']){
                                $itemData['COL_GROUP_ID'] = $cgData['COL_GROUP_ID'];
                                break;
                            }
                        }
                    }
                    if(!array_key_exists("MAX_LENGTH",$itemData))           $itemData["MAX_LENGTH"] = "";
                    if(!array_key_exists("PREG_MATCH",$itemData))           $itemData["PREG_MATCH"] = "";
                    if(!array_key_exists("MULTI_MAX_LENGTH",$itemData))     $itemData["MULTI_MAX_LENGTH"] = "";
                    if(!array_key_exists("MULTI_PREG_MATCH",$itemData))     $itemData["MULTI_PREG_MATCH"] = "";
                    if(!array_key_exists("INT_MIN",$itemData))              $itemData["INT_MIN"] = "";
                    if(!array_key_exists("INT_MAX",$itemData))              $itemData["INT_MAX"] = "";
                    if(!array_key_exists("FLOAT_MIN",$itemData))            $itemData["FLOAT_MIN"] = "";
                    if(!array_key_exists("FLOAT_MAX",$itemData))            $itemData["FLOAT_MAX"] = "";
                    if(!array_key_exists("FLOAT_DIGIT",$itemData))          $itemData["FLOAT_DIGIT"] = "";
                    if(!array_key_exists("OTHER_MENU_LINK_ID",$itemData))   $itemData["OTHER_MENU_LINK_ID"] = "";
                    if(!array_key_exists("PW_MAX_LENGTH",$itemData))        $itemData["PW_MAX_LENGTH"] = "";
                    if(!array_key_exists("UPLOAD_MAX_SIZE",$itemData))      $itemData["UPLOAD_MAX_SIZE"] = "";
                    if(!array_key_exists("LINK_LENGTH",$itemData))          $itemData["LINK_LENGTH"] = "";
                    if(!array_key_exists("REFERENCE_ITEM",$itemData))       $itemData["REFERENCE_ITEM"] = "";
                    if(!array_key_exists("TYPE3_REFERENCE",$itemData))      $itemData["TYPE3_REFERENCE"] = "";
                    if(!array_key_exists("SINGLE_DEFAULT_VALUE",$itemData)) $itemData["SINGLE_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("MULTI_DEFAULT_VALUE",$itemData))  $itemData["MULTI_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("INT_DEFAULT_VALUE",$itemData))    $itemData["INT_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("FLOAT_DEFAULT_VALUE",$itemData))  $itemData["FLOAT_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("DATETIME_DEFAULT_VALUE",$itemData)) $itemData["DATETIME_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("DATE_DEFAULT_VALUE",$itemData))   $itemData["DATE_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("PULLDOWN_DEFAULT_VALUE",$itemData)) $itemData["PULLDOWN_DEFAULT_VALUE"] = "";
                    if(!array_key_exists("LINK_DEFAULT_VALUE",$itemData))   $itemData["LINK_DEFAULT_VALUE"] = "";
                    
                    $arrayRegisterData = array("CREATE_MENU_ID"     => $menuData['menu']['CREATE_MENU_ID'],
                                               "ITEM_NAME"          => $itemData['ITEM_NAME'],
                                               "DISP_SEQ"           => $itemData['DISP_SEQ'],
                                               "REQUIRED"           => $required,
                                               "UNIQUED"            => $uniqued,
                                               "COL_GROUP_ID"       => $itemData['COL_GROUP_ID'],
                                               "INPUT_METHOD_ID"    => $itemData['INPUT_METHOD_ID'],
                                               "MAX_LENGTH"         => $itemData['MAX_LENGTH'],
                                               "PREG_MATCH"         => $itemData['PREG_MATCH'],
                                               "MULTI_MAX_LENGTH"   => $itemData['MULTI_MAX_LENGTH'],
                                               "MULTI_PREG_MATCH"   => $itemData['MULTI_PREG_MATCH'],
                                               "INT_MIN"            => $itemData['INT_MIN'],
                                               "INT_MAX"            => $itemData['INT_MAX'],
                                               "FLOAT_MIN"          => $itemData['FLOAT_MIN'],
                                               "FLOAT_MAX"          => $itemData['FLOAT_MAX'],
                                               "FLOAT_DIGIT"        => $itemData['FLOAT_DIGIT'],
                                               "OTHER_MENU_LINK_ID" => $itemData['OTHER_MENU_LINK_ID'],
                                               "PW_MAX_LENGTH"      => $itemData['PW_MAX_LENGTH'],
                                               "UPLOAD_MAX_SIZE"    => $itemData['UPLOAD_MAX_SIZE'],
                                               "LINK_LENGTH"        => $itemData['LINK_LENGTH'],
                                               "REFERENCE_ITEM"     => $itemData['REFERENCE_ITEM'],
                                               "TYPE3_REFERENCE"    => $itemData['TYPE3_REFERENCE'],
                                               "SINGLE_DEFAULT_VALUE"   => $itemData['SINGLE_DEFAULT_VALUE'],
                                               "MULTI_DEFAULT_VALUE"    => $itemData['MULTI_DEFAULT_VALUE'],
                                               "INT_DEFAULT_VALUE"      => $itemData['INT_DEFAULT_VALUE'],
                                               "FLOAT_DEFAULT_VALUE"    => $itemData['FLOAT_DEFAULT_VALUE'],
                                               "DATETIME_DEFAULT_VALUE" => $itemData['DATETIME_DEFAULT_VALUE'],
                                               "DATE_DEFAULT_VALUE"     => $itemData['DATE_DEFAULT_VALUE'],
                                               "PULLDOWN_DEFAULT_VALUE" => $itemData['PULLDOWN_DEFAULT_VALUE'],
                                               "LINK_DEFAULT_VALUE"     => $itemData['LINK_DEFAULT_VALUE'],
                                               "DESCRIPTION"        => $itemData['DESCRIPTION'],
                                               "ACCESS_AUTH"        => $menuData['menu']['ACCESS_AUTH'],
                                               "NOTE"               => $itemData['NOTE']
                                              );

                    $g["page_dir"] = "2100160002";

                    // 登録処理
                    $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160002", 4);

                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                    
                    $itemData['CREATE_ITEM_ID'] = json_decode($arrayResult[2],true)['CREATE_ITEM_ID'];

                    //columnIdとITEM_IDを紐つける
                    $columnIdConvArray[$key] = $itemData['CREATE_ITEM_ID'];
                }
                unset($itemData);
                
                //////////////////////////
                // 縦メニュー情報を登録
                //////////////////////////
                
                if(array_key_exists('r1',$menuData['repeat'])){
                    if($menuData['repeat']['r1']['COLUMNS'][0][0] == 'i'){
                        $createItemID = $menuData['item'][$menuData['repeat']['r1']['COLUMNS'][0]]['CREATE_ITEM_ID'];
                    }
                    else{
                        $curGroup = $menuData['repeat']['r1']['COLUMNS'][0];
                        while($curGroup[0] == 'g'){
                            $curGroup = $menuData['group'][$curGroup]['COLUMNS'][0];
                        }
                        $createItemID = $menuData['item'][$curGroup]['CREATE_ITEM_ID'];
                    }
                    $arrayRegisterData = array("CREATE_ITEM_ID" => $createItemID,
                                               "COL_CNT"        => $repeatCount / ($menuData['repeat']['r1']['REPEAT_CNT'] - 1),
                                               "REPEAT_CNT"     => $menuData['repeat']['r1']['REPEAT_CNT'],
                                               "ACCESS_AUTH"    => $menuData['menu']['ACCESS_AUTH'],
                                              );

                    $g["page_dir"] = "2100160009";

                    // 登録処理
                    $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160009", 4);

                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                }

                //////////////////////////
                // 一意制約(複数項目)を登録
                //////////////////////////
                if($menuData['menu']['UNIQUE_CONSTRAINT'] != ""){
                    $uniqueConstraintArray = explode(",", $menuData['menu']['UNIQUE_CONSTRAINT']);
                    foreach($uniqueConstraintArray as $idPattern){
                        //columnId「i1」をITEM_IDに置換する。
                        $idPatternArray = explode("-", $idPattern);
                        $idPatternConv = "";
                        foreach($idPatternArray as $id){
                            if(isset($columnIdConvArray[$id])){
                                $convId = $columnIdConvArray[$id];
                                if($idPatternConv == ""){
                                    $idPatternConv = $convId;
                                }else{
                                    $idPatternConv = $idPatternConv . "," . $convId;
                                }  
                            }else{
                                continue;
                            }
                        }

                        $arrayRegisterData = array("CREATE_MENU_ID" => $menuData['menu']['CREATE_MENU_ID'],
                                                   "UNIQUE_CONSTRAINT_ITEM" => $idPatternConv,
                                                   "ACCESS_AUTH"    => $menuData['menu']['ACCESS_AUTH'],
                                                  );

                        $g["page_dir"] = "2100160018";

                        // 登録処理
                        $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160018", 4);

                        if($arrayResult[0] !== "000"){
                            throw new Exception();
                        }
                    }
                }

                // メニュー作成実行
                $createMenuStatusTable = new CreateMenuStatusTable($g["objDBCA"], $g["db_model_ch"]);
                $insertData = array();
                $insertData['CREATE_MENU_ID'] = $menuData['menu']['CREATE_MENU_ID'];
                $insertData['STATUS_ID'] = "1";
                $insertData['MENU_CREATE_TYPE_ID'] = "1"; //新規作成
                $insertData['FILE_NAME'] = "";
                $insertData['ACCESS_AUTH'] = $menuData['menu']['ACCESS_AUTH'];
                $insertData['NOTE'] = "";
                $insertData['DISUSE_FLAG'] = "0";
                $insertData['LAST_UPDATE_USER'] = $g['login_id'];
                
                //////////////////////////
                // メニュー作成管理テーブルに登録
                //////////////////////////
                $result = $createMenuStatusTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg);
                    throw new Exception();
                }
                $createResult = array("MM_STATUS_ID" => $seqNo,"CREATE_MENU_ID" => $menuData['menu']['CREATE_MENU_ID']);
                $arrayResult = array("000","",json_encode($createResult));

                // コミット
                $res = $g["objDBCA"]->transactionCommit();
                if ($res === false) {
                    web_log($g["objMTS"]->getSomeMessage("ITABASEH-ERR-900036", array(basename(__FILE__), __LINE__)));
                    $msg = $g["objMTS"]->getSomeMessage("ITABASEH-ERR-900036", array(basename(__FILE__), __LINE__));
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $tranStartFlg = false;

            }
            catch (Exception $e){
                if($tranStartFlg === true){
                    // ロールバック
                    $g["objDBCA"]->transactionRollback();
                }
            }

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                web_log( $arrayResult[2]);
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                web_log( $arrayResult[2]);
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // 更新
        /////////////////////
        function updateTable($menuData){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            try{
                require_once ( $g["root_dir_path"] . "/libs/webcommonlibs/table_control_agent/03_registerTable.php");
                require_once ( $g["root_dir_path"] . "/libs/webcommonlibs/table_control_agent/04_updateTable.php");
                require_once ( $g["root_dir_path"] . "/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
                require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
                
                // トランザクション開始
                $varTrzStart = $g["objDBCA"]->transactionStart();
                if ($varTrzStart === false) {
                    web_log($g["objMTS"]->getSomeMessage("ITABASEH-ERR-900015", array(basename(__FILE__), __LINE__)));
                    $msg = $g["objMTS"]->getSomeMessage("ITABASEH-ERR-900015", array(basename(__FILE__), __LINE__));
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $tranStartFlg = true;
                
                $aryVariant = array("TCA_PRESERVED" => array("TCA_ACTION" => array("ACTION_MODE" => "DTUP_singleRecRegister")));
                
                $menuData = json_decode($menuData,true);
                if(!array_key_exists("MENUGROUP_FOR_INPUT",$menuData['menu']))  $menuData['menu']['MENUGROUP_FOR_INPUT'] = "";
                if(!array_key_exists("MENUGROUP_FOR_SUBST",$menuData['menu']))  $menuData['menu']['MENUGROUP_FOR_SUBST'] = "";
                if(!array_key_exists("MENUGROUP_FOR_VIEW",$menuData['menu']))   $menuData['menu']['MENUGROUP_FOR_VIEW'] = "";
                if(!array_key_exists("VERTICAL",$menuData['menu']))             $menuData['menu']['VERTICAL'] = "";

                // 縦メニュー利用とリピートのチェック
                if($menuData['menu']['VERTICAL'] == 1 && !array_key_exists('r1',$menuData['repeat'])){
                    $arrayResult[0] = "002";
                    $arrayResult[1] = "";
                    $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1024');
                    throw new Exception();
                }

                // リピート数のチェック
                if($menuData['menu']['VERTICAL'] == 1 && array_key_exists('r1',$menuData['repeat'])){
                    if($menuData['repeat']['r1']['REPEAT_CNT'] < 2 || $menuData['repeat']['r1']['REPEAT_CNT'] > 99) {
                        $arrayResult[0] = "002";
                        $arrayResult[1] = "";
                        $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1025');
                        throw new Exception();
                    }
                }

                //////////////////////////
                // メニュー作成情報を取得
                //////////////////////////
                $createMenuInfoTable = new CreateMenuInfoTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $createMenuInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $createMenuInfoTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $createMenuInfoArray = $result;
                
                //////////////////////////
                // メニュー項目作成情報を取得
                //////////////////////////
                $createItemInfoTable = new CreateItemInfoTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $createItemInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $createItemInfoTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $createItemInfoArray = $result;
                
                //////////////////////////
                // カラムグループ管理テーブルを検索
                //////////////////////////
                $columnGroupTable = new ColumnGroupTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $columnGroupTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $columnGroupTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $columnGroupArray = $result;
                
                //////////////////////////
                // メニュー(縦)作成情報を取得
                //////////////////////////
                $convertParamInfoTable = new ConvertParamInfoTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $convertParamInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $convertParamInfoTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $convertParamInfoArray = $result;

                //////////////////////////
                // 一意制約(複数項目)情報を取得
                //////////////////////////
                $uniqueConstraintTable = new UniqueConstraintTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $uniqueConstraintTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $uniqueConstraintTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $uniqueConstraintArray = $result;

                //////////////////////////
                // メニュー作成情報を更新
                //////////////////////////
                $arrayUpdateData = NULL;
                $menuCreateFlag = NULL;
                foreach($createMenuInfoArray as $createMenuInfoData){
                    if($createMenuInfoData['CREATE_MENU_ID'] == $menuData['menu']['CREATE_MENU_ID']){
                         //メニュー作成履歴のMENU_CREATE_TYPE判定のためMENU_CREATE_STATUSを取得
                         $menuCreateFlag = $createMenuInfoData['MENU_CREATE_STATUS'];

                         //「編集」の場合は「作業対象」「縦メニュー利用」「ホストグループ利用」に変更がないことをチェック
                         if($menuData['type'] === 'update'){
                            if($createMenuInfoData['TARGET'] != $menuData['menu']['TARGET']){
                                $arrayResult[0] = "002";
                                $arrayResult[1] = "";
                                $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1156');
                                throw new Exception();
                            }

                            if($createMenuInfoData['VERTICAL'] != $menuData['menu']['VERTICAL']){
                                $arrayResult[0] = "002";
                                $arrayResult[1] = "";
                                $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1157');
                                throw new Exception();
                            }

                            if($createMenuInfoData['PURPOSE'] != $menuData['menu']['PURPOSE']){
                                $arrayResult[0] = "002";
                                $arrayResult[1] = "";
                                $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1158');
                                throw new Exception();
                            }
                         }

                         $strNumberForRI = $menuData['menu']['CREATE_MENU_ID'];
                         $arrayUpdateData = array("MENU_NAME"               => $menuData['menu']['MENU_NAME'],
                                                  "TARGET"                  => $menuData['menu']['TARGET'],
                                                  "DISP_SEQ"                => $menuData['menu']['DISP_SEQ'],
                                                  "PURPOSE"                 => $menuData['menu']['PURPOSE'],
                                                  "VERTICAL"                => $menuData['menu']['VERTICAL'],
                                                  "MENUGROUP_FOR_INPUT"     => $menuData['menu']['MENUGROUP_FOR_INPUT'],
                                                  "MENUGROUP_FOR_SUBST"     => $menuData['menu']['MENUGROUP_FOR_SUBST'],
                                                  "MENUGROUP_FOR_VIEW"      => $menuData['menu']['MENUGROUP_FOR_VIEW'],
                                                  "DESCRIPTION"             => $menuData['menu']['DESCRIPTION'],
                                                  "ACCESS_AUTH"             => $menuData['menu']['ACCESS_AUTH'],
                                                  "NOTE"                    => $menuData['menu']['NOTE'],
                                                  "UPD_UPDATE_TIMESTAMP"    => "T_" . preg_replace("/[^a-zA-Z0-9]/", "", $menuData['menu']['LAST_UPDATE_TIMESTAMP'])
                                          );
                         break;
                    }
                }
                if($arrayUpdateData === NULL){
                    // 更新するメニューIDがいない場合、エラー
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5005', $result);
                    $arrayResult = array("999","",$msg);
                    throw new Exception();
                }
                $g["page_dir"] = "2100160001";

                // 更新処理
                $arrayResult = updateTableMain(3, $strNumberForRI, $arrayUpdateData, "2100160001", 4);
                if($arrayResult[0] !== "000"){
                    throw new Exception();
                }
                
                //////////////////////////
                // カラムグループ情報を更新
                ////////////////////////// 
                foreach($menuData['group'] as &$groupData){
                    $groupData['PA_COL_GROUP_ID'] = "";
                    $skipFlag = false;
                    if($groupData['PARENT'] == "")
                        $fullPath = $groupData['COL_GROUP_NAME'];
                    else{
                        $fullPath = $groupData['PARENT'] . '/' .  $groupData['COL_GROUP_NAME'];
                    }
                    foreach($columnGroupArray as $columnGroupData){
                        if($fullPath == $columnGroupData['FULL_COL_GROUP_NAME']){
                            $skipFlag = true;
                            break;
                        }
                        if($columnGroupData['FULL_COL_GROUP_NAME'] == $groupData['PARENT']){
                            $groupData['PA_COL_GROUP_ID'] = $columnGroupData['COL_GROUP_ID'];
                        }
                    }
                    // 既存データがあった場合スキップ
                    if(true == $skipFlag){
                        continue;
                    }
                    $arrayRegisterData = array("PA_COL_GROUP_ID" => $groupData['PA_COL_GROUP_ID'],
                                               "COL_GROUP_NAME" => $groupData['COL_GROUP_NAME'],
                                               "DESCRIPTION" => ""
                                              );

                    $g["page_dir"] = "2100160008";

                    // 登録処理
                    $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160008", 4);

                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                    $columnGroupArray[] = json_decode($arrayResult[2],true);
                }
                unset($groupData);

                //////////////////////////
                // メニュー項目情報を更新
                ////////////////////////// 
                
                // 更新したカラムグループ管理テーブルを検索
                $sql = $columnGroupTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $columnGroupTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    throw new Exception($msg);
                }
                $columnGroupArray = $result;

                // 既存、使えなくなった項目を廃止
                foreach($createItemInfoArray as $createItemInfoData){
                    if($createItemInfoData['CREATE_MENU_ID'] == $menuData['menu']['CREATE_MENU_ID']){
                        $key = array_search($createItemInfoData['CREATE_ITEM_ID'], array_column($menuData['item'], 'CREATE_ITEM_ID'));

                        //「編集」の場合は既存の項目データに変更がないことをチェック
                        if($menuData['type'] === 'update'){
                            if($key !== false){
                                $changedFlg = false;
                                $maxbiteFlg = false;
                                $minvalueFlg = false;
                                $maxvalueFlg = false;
                                $maxdigitFlg = false;
                                $changeErrMsg = "";
                                foreach($menuData['item'] as &$itemData){
                                    if($itemData['CREATE_ITEM_ID'] == $createItemInfoData['CREATE_ITEM_ID']){
                                        //項目
                                        if($itemData['INPUT_METHOD_ID'] != $createItemInfoData['INPUT_METHOD_ID']){
                                            $arrayResult[0] = "002";
                                            $arrayResult[1] = "";
                                            $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1159', array($createItemInfoData['ITEM_NAME']));
                                            throw new Exception();
                                        }
                                        //必須チェック
                                        if($itemData['REQUIRED'] != $createItemInfoData['REQUIRED']){
                                            $changedFlg = true;
                                            $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1159', array($createItemInfoData['ITEM_NAME']));
                                        }
                                        //一意制約チェック
                                        if($itemData['UNIQUED'] != $createItemInfoData['UNIQUED']){
                                            $changedFlg = true;
                                            $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1159', array($createItemInfoData['ITEM_NAME']));
                                        }

                                        //文字列(単一行)の場合
                                        if($itemData['INPUT_METHOD_ID'] == 1){
                                            //最大バイト数
                                            if($itemData['MAX_LENGTH'] < $createItemInfoData['MAX_LENGTH']){
                                                $maxbiteFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1294', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //文字列(複数行)の場合
                                        if($itemData['INPUT_METHOD_ID'] == 2){
                                            //最大バイト数
                                            if($itemData['MULTI_MAX_LENGTH'] < $createItemInfoData['MULTI_MAX_LENGTH']){
                                                $maxbiteFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1294', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //整数の場合
                                        if($itemData['INPUT_METHOD_ID'] == 3){
                                            //最小値
                                            if($itemData['INT_MIN'] > $createItemInfoData['INT_MIN']){
                                                $minvalueFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1295', array($createItemInfoData['ITEM_NAME']));
                                            }
                                            //最大値
                                            if($itemData['INT_MAX'] < $createItemInfoData['INT_MAX']){
                                                $maxvalueFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1296', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //小数の場合
                                        if($itemData['INPUT_METHOD_ID'] == 4){
                                            //最小値
                                            if($itemData['FLOAT_MIN'] > $createItemInfoData['FLOAT_MIN']){
                                                $minvalueFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1295', array($createItemInfoData['ITEM_NAME']));
                                            }
                                            //最大値
                                            if($itemData['FLOAT_MAX'] < $createItemInfoData['FLOAT_MAX']){
                                                $maxvalueFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1296', array($createItemInfoData['ITEM_NAME']));
                                            }
                                            //桁数
                                            if($itemData['FLOAT_DIGIT'] < $createItemInfoData['FLOAT_DIGIT']){
                                                if($itemData['FLOAT_DIGIT'] != 14){
                                                    $maxdigitFlg = true;
                                                    $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1297', array($createItemInfoData['ITEM_NAME']));
                                                }
                                            }
                                        }

                                        //プルダウン選択の場合
                                        if($itemData['INPUT_METHOD_ID'] == 7){
                                            //選択項目
                                            if($itemData['OTHER_MENU_LINK_ID'] != $createItemInfoData['OTHER_MENU_LINK_ID']){
                                                $changedFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1159', array($createItemInfoData['ITEM_NAME']));
                                            }

                                            //参照項目
                                            if(isset($itemData['REFERENCE_ITEM'])){
                                                if($itemData['REFERENCE_ITEM'] != $createItemInfoData['REFERENCE_ITEM']){
                                                    $changedFlg = true;
                                                    $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1159', array($createItemInfoData['ITEM_NAME']));
                                                }
                                            }
                                        }

                                        //パスワードの場合
                                        if($itemData['INPUT_METHOD_ID'] == 8){
                                            if($itemData['PW_MAX_LENGTH'] < $createItemInfoData['PW_MAX_LENGTH']){
                                                $maxbiteFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1294', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //ファイルアップロードの場合
                                        if($itemData['INPUT_METHOD_ID'] == 9){
                                            //最大バイト数
                                            if($itemData['UPLOAD_MAX_SIZE'] < $createItemInfoData['UPLOAD_MAX_SIZE']){
                                                $maxbiteFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1294', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //リンクの場合
                                        if($itemData['INPUT_METHOD_ID'] == 10){
                                            //最大バイト数
                                            if($itemData['LINK_LENGTH'] < $createItemInfoData['LINK_LENGTH']){
                                                $maxbiteFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1294', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //パラメータシート参照の場合
                                        if($itemData['INPUT_METHOD_ID'] == 11){
                                            //選択項目
                                            if($itemData['TYPE3_REFERENCE'] != $createItemInfoData['TYPE3_REFERENCE']){
                                                $changedFlg = true;
                                                $changeErrMsg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-1159', array($createItemInfoData['ITEM_NAME']));
                                            }
                                        }

                                        //バリデーションエラーが一つでもあった場合
                                        if($changedFlg == true || $maxbiteFlg == true || $minvalueFlg == true || $maxvalueFlg == true || $maxdigitFlg == true){
                                            $arrayResult[0] = "002";
                                            $arrayResult[1] = "";
                                            $arrayResult[2] = $changeErrMsg;
                                            throw new Exception();
                                        }
                                    }
                                }
                            }
                        }

                        if($key === false){
                            $strNumberForRI = $createItemInfoData['CREATE_ITEM_ID'];       // 主キー
                            $reqDeleteData = array("DISUSE_FLAG"          => "0",
                                                   "UPD_UPDATE_TIMESTAMP" => "T_" . preg_replace("/[^a-zA-Z0-9]/", "", $createItemInfoData['LAST_UPDATE_TIMESTAMP'])
                                                  );

                            $g["page_dir"] = "2100160002";

                            // 廃止処理
                            $intBaseMode = 3;       // 3:廃止、5:復活
                            $arrayResult = deleteTableMain($intBaseMode, $strNumberForRI, $reqDeleteData, "2100160002", 4);
                            if($arrayResult[0] !== "000"){
                                throw new Exception();
                            }
                        }
                    }
                }
                // IDがいる項目を更新
                $repeatCount = 0;
                $columnIdConvArray = array(); //一意制約の項目ID変換用
                foreach($menuData['item'] as $key => &$itemData){
                    //作成対象「データシート」で入力方式「パラメータシート参照」(ID:11)を利用している場合はエラー
                    if($menuData['menu']['TARGET'] == 2 && $itemData['INPUT_METHOD_ID'] == 11){
                        $arrayResult[0] = "002";
                        $arrayResult[1] = "";
                        $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5026');
                        throw new Exception();
                    }
                    if($itemData['REPEAT_ITEM'] === true){
                        $repeatCount += 1;
                    }
                    if(!array_key_exists('CREATE_ITEM_ID',$itemData)){
                        $itemData['CREATE_ITEM_ID'] = "";
                    }
                    if($itemData['CREATE_ITEM_ID'] != ""){
                        if($itemData['REQUIRED'] === true){
                            $required = "1";
                        }
                        else{
                            $required = "";
                        }
                        if($itemData['UNIQUED'] === true){
                            $uniqued= "1";
                        }
                        else{
                            $uniqued = "";
                        }
                        if($itemData['COL_GROUP_ID'] != ""){
                            foreach($columnGroupArray as $columnGroupData){
                                if($itemData['COL_GROUP_ID'] == $columnGroupData['FULL_COL_GROUP_NAME']){
                                    $itemData['COL_GROUP_ID'] = $columnGroupData['COL_GROUP_ID'];
                                    break;
                                }
                            }
                        }
                        if(!array_key_exists("MAX_LENGTH",$itemData))           $itemData["MAX_LENGTH"] = "";
                        if(!array_key_exists("PREG_MATCH",$itemData))           $itemData["PREG_MATCH"] = "";
                        if(!array_key_exists("MULTI_MAX_LENGTH",$itemData))     $itemData["MULTI_MAX_LENGTH"] = "";
                        if(!array_key_exists("MULTI_PREG_MATCH",$itemData))     $itemData["MULTI_PREG_MATCH"] = "";
                        if(!array_key_exists("INT_MIN",$itemData))              $itemData["INT_MIN"] = "";
                        if(!array_key_exists("INT_MAX",$itemData))              $itemData["INT_MAX"] = "";
                        if(!array_key_exists("FLOAT_MIN",$itemData))            $itemData["FLOAT_MIN"] = "";
                        if(!array_key_exists("FLOAT_MAX",$itemData))            $itemData["FLOAT_MAX"] = "";
                        if(!array_key_exists("FLOAT_DIGIT",$itemData))          $itemData["FLOAT_DIGIT"] = "";
                        if(!array_key_exists("OTHER_MENU_LINK_ID",$itemData))   $itemData["OTHER_MENU_LINK_ID"] = "";
                        if(!array_key_exists("PW_MAX_LENGTH",$itemData))        $itemData["PW_MAX_LENGTH"] = "";
                        if(!array_key_exists("UPLOAD_MAX_SIZE",$itemData))      $itemData["UPLOAD_MAX_SIZE"] = "";
                        if(!array_key_exists("LINK_LENGTH",$itemData))          $itemData["LINK_LENGTH"] = "";
                        if(!array_key_exists("REFERENCE_ITEM",$itemData))       $itemData["REFERENCE_ITEM"] = "";
                        if(!array_key_exists("TYPE3_REFERENCE",$itemData))      $itemData["TYPE3_REFERENCE"] = "";
                        if(!array_key_exists("SINGLE_DEFAULT_VALUE",$itemData)) $itemData["SINGLE_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("MULTI_DEFAULT_VALUE",$itemData))  $itemData["MULTI_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("INT_DEFAULT_VALUE",$itemData))    $itemData["INT_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("FLOAT_DEFAULT_VALUE",$itemData))  $itemData["FLOAT_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("DATETIME_DEFAULT_VALUE",$itemData)) $itemData["DATETIME_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("DATE_DEFAULT_VALUE",$itemData))   $itemData["DATE_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("PULLDOWN_DEFAULT_VALUE",$itemData)) $itemData["PULLDOWN_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("LINK_DEFAULT_VALUE",$itemData))   $itemData["LINK_DEFAULT_VALUE"] = "";

                        $strNumberForRI = $itemData['CREATE_ITEM_ID'];
                        $arrayUpdateData = array("CREATE_MENU_ID"       => $menuData['menu']['CREATE_MENU_ID'],
                                                 "ITEM_NAME"            => $itemData['ITEM_NAME'],
                                                 "DISP_SEQ"             => $itemData['DISP_SEQ'],
                                                 "REQUIRED"             => $required,
                                                 "UNIQUED"              => $uniqued,
                                                 "COL_GROUP_ID"         => $itemData['COL_GROUP_ID'],
                                                 "INPUT_METHOD_ID"      => $itemData['INPUT_METHOD_ID'],
                                                 "MAX_LENGTH"           => $itemData['MAX_LENGTH'],
                                                 "PREG_MATCH"           => $itemData['PREG_MATCH'],
                                                 "MULTI_MAX_LENGTH"     => $itemData['MULTI_MAX_LENGTH'],
                                                 "MULTI_PREG_MATCH"     => $itemData['MULTI_PREG_MATCH'],
                                                 "INT_MIN"              => $itemData['INT_MIN'],
                                                 "INT_MAX"              => $itemData['INT_MAX'],
                                                 "FLOAT_MIN"            => $itemData['FLOAT_MIN'],
                                                 "FLOAT_MAX"            => $itemData['FLOAT_MAX'],
                                                 "FLOAT_DIGIT"          => $itemData['FLOAT_DIGIT'],
                                                 "OTHER_MENU_LINK_ID"   => $itemData['OTHER_MENU_LINK_ID'],
                                                 "PW_MAX_LENGTH"        => $itemData['PW_MAX_LENGTH'],
                                                 "UPLOAD_MAX_SIZE"      => $itemData['UPLOAD_MAX_SIZE'],
                                                 "LINK_LENGTH"          => $itemData['LINK_LENGTH'],
                                                 "REFERENCE_ITEM"       => $itemData['REFERENCE_ITEM'],
                                                 "TYPE3_REFERENCE"      => $itemData['TYPE3_REFERENCE'],
                                                 "SINGLE_DEFAULT_VALUE"   => $itemData['SINGLE_DEFAULT_VALUE'],
                                                 "MULTI_DEFAULT_VALUE"    => $itemData['MULTI_DEFAULT_VALUE'],
                                                 "INT_DEFAULT_VALUE"      => $itemData['INT_DEFAULT_VALUE'],
                                                 "FLOAT_DEFAULT_VALUE"    => $itemData['FLOAT_DEFAULT_VALUE'],
                                                 "DATETIME_DEFAULT_VALUE" => $itemData['DATETIME_DEFAULT_VALUE'],
                                                 "DATE_DEFAULT_VALUE"     => $itemData['DATE_DEFAULT_VALUE'],
                                                 "PULLDOWN_DEFAULT_VALUE" => $itemData['PULLDOWN_DEFAULT_VALUE'],
                                                 "LINK_DEFAULT_VALUE"     => $itemData['LINK_DEFAULT_VALUE'],
                                                 "DESCRIPTION"          => $itemData['DESCRIPTION'],
                                                 "ACCESS_AUTH"          => $menuData['menu']['ACCESS_AUTH'],
                                                 "NOTE"                 => $itemData['NOTE'],
                                                 "UPD_UPDATE_TIMESTAMP" => "T_" . preg_replace("/[^a-zA-Z0-9]/", "", $itemData['LAST_UPDATE_TIMESTAMP'])
                                                 );

                        $g["page_dir"] = "2100160002";
                        // 更新処理
                        $arrayResult = updateTableMain(3, $strNumberForRI, $arrayUpdateData, "2100160002", 4);
                        if($arrayResult[0] !== "000"){
                            throw new Exception();
                        }
                        $itemData['CREATE_ITEM_ID'] = json_decode($arrayResult[2],true)['CREATE_ITEM_ID'];

                        //columnIdとITEM_IDを紐つける
                        $columnIdConvArray[$key] = $itemData['CREATE_ITEM_ID'];
                    }
                }
                unset($itemData);

                // IDがいない項目を新規登録
                foreach($menuData['item'] as $key => &$itemData){
                    if($itemData['CREATE_ITEM_ID'] == ""){
                        //作成対象「データシート」で入力方式「パラメータシート参照」(ID:11)を利用している場合はエラー
                        if($menuData['menu']['TARGET'] == 2 && $itemData['INPUT_METHOD_ID'] == 11){
                            $arrayResult[0] = "002";
                            $arrayResult[1] = "";
                            $arrayResult[2] = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5026');
                            throw new Exception();
                        }
                        if($itemData['REQUIRED'] === true){
                            $required = "1";
                        }
                        else{
                            $required = "";
                        }
                        if($itemData['UNIQUED'] === true){
                            $uniqued= "1";
                        }
                        else{
                            $uniqued = "";
                        }
                        if($itemData['COL_GROUP_ID'] != ""){
                            foreach($columnGroupArray as $columnGroupData){
                                if($itemData['COL_GROUP_ID'] == $columnGroupData['FULL_COL_GROUP_NAME']){
                                    $itemData['COL_GROUP_ID'] = $columnGroupData['COL_GROUP_ID'];
                                    break;
                                }
                            }
                        }
                        if(!array_key_exists("MAX_LENGTH",$itemData))           $itemData["MAX_LENGTH"] = "";
                        if(!array_key_exists("PREG_MATCH",$itemData))           $itemData["PREG_MATCH"] = "";
                        if(!array_key_exists("MULTI_MAX_LENGTH",$itemData))     $itemData["MULTI_MAX_LENGTH"] = "";
                        if(!array_key_exists("MULTI_PREG_MATCH",$itemData))     $itemData["MULTI_PREG_MATCH"] = "";
                        if(!array_key_exists("INT_MIN",$itemData))              $itemData["INT_MIN"] = "";
                        if(!array_key_exists("INT_MAX",$itemData))              $itemData["INT_MAX"] = "";
                        if(!array_key_exists("FLOAT_MIN",$itemData))            $itemData["FLOAT_MIN"] = "";
                        if(!array_key_exists("FLOAT_MAX",$itemData))            $itemData["FLOAT_MAX"] = "";
                        if(!array_key_exists("FLOAT_DIGIT",$itemData))          $itemData["FLOAT_DIGIT"] = "";
                        if(!array_key_exists("OTHER_MENU_LINK_ID",$itemData))   $itemData["OTHER_MENU_LINK_ID"] = "";
                        if(!array_key_exists("PW_MAX_LENGTH",$itemData))        $itemData["PW_MAX_LENGTH"] = "";
                        if(!array_key_exists("UPLOAD_MAX_SIZE",$itemData))      $itemData["UPLOAD_MAX_SIZE"] = "";
                        if(!array_key_exists("LINK_LENGTH",$itemData))          $itemData["LINK_LENGTH"] = "";
                        if(!array_key_exists("REFERENCE_ITEM",$itemData))       $itemData["REFERENCE_ITEM"] = "";
                        if(!array_key_exists("TYPE3_REFERENCE",$itemData))      $itemData["TYPE3_REFERENCE"] = "";
                        if(!array_key_exists("SINGLE_DEFAULT_VALUE",$itemData)) $itemData["SINGLE_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("MULTI_DEFAULT_VALUE",$itemData))  $itemData["MULTI_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("INT_DEFAULT_VALUE",$itemData))    $itemData["INT_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("FLOAT_DEFAULT_VALUE",$itemData))  $itemData["FLOAT_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("DATETIME_DEFAULT_VALUE",$itemData)) $itemData["DATETIME_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("DATE_DEFAULT_VALUE",$itemData))   $itemData["DATE_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("PULLDOWN_DEFAULT_VALUE",$itemData)) $itemData["PULLDOWN_DEFAULT_VALUE"] = "";
                        if(!array_key_exists("LINK_DEFAULT_VALUE",$itemData))   $itemData["LINK_DEFAULT_VALUE"] = "";

                        $arrayRegisterData = array("CREATE_MENU_ID"         => $menuData['menu']['CREATE_MENU_ID'],
                                                   "ITEM_NAME"              => $itemData['ITEM_NAME'],
                                                   "DISP_SEQ"               => $itemData['DISP_SEQ'],
                                                   "REQUIRED"               => $required,
                                                   "UNIQUED"                => $uniqued,
                                                   "COL_GROUP_ID"           => $itemData['COL_GROUP_ID'],
                                                   "INPUT_METHOD_ID"        => $itemData['INPUT_METHOD_ID'],
                                                   "MAX_LENGTH"             => $itemData['MAX_LENGTH'],
                                                   "PREG_MATCH"             => $itemData['PREG_MATCH'],
                                                   "MULTI_MAX_LENGTH"       => $itemData['MULTI_MAX_LENGTH'],
                                                   "MULTI_PREG_MATCH"       => $itemData['MULTI_PREG_MATCH'],
                                                   "INT_MIN"                => $itemData['INT_MIN'],
                                                   "INT_MAX"                => $itemData['INT_MAX'],
                                                   "FLOAT_MIN"              => $itemData['FLOAT_MIN'],
                                                   "FLOAT_MAX"              => $itemData['FLOAT_MAX'],
                                                   "FLOAT_DIGIT"            => $itemData['FLOAT_DIGIT'],
                                                   "OTHER_MENU_LINK_ID"     => $itemData['OTHER_MENU_LINK_ID'],
                                                   "PW_MAX_LENGTH"          => $itemData['PW_MAX_LENGTH'],
                                                   "UPLOAD_MAX_SIZE"        => $itemData['UPLOAD_MAX_SIZE'],
                                                   "LINK_LENGTH"            => $itemData['LINK_LENGTH'],
                                                   "REFERENCE_ITEM"         => $itemData['REFERENCE_ITEM'],
                                                   "TYPE3_REFERENCE"        => $itemData['TYPE3_REFERENCE'],
                                                   "SINGLE_DEFAULT_VALUE"   => $itemData['SINGLE_DEFAULT_VALUE'],
                                                   "MULTI_DEFAULT_VALUE"    => $itemData['MULTI_DEFAULT_VALUE'],
                                                   "INT_DEFAULT_VALUE"      => $itemData['INT_DEFAULT_VALUE'],
                                                   "FLOAT_DEFAULT_VALUE"    => $itemData['FLOAT_DEFAULT_VALUE'],
                                                   "DATETIME_DEFAULT_VALUE" => $itemData['DATETIME_DEFAULT_VALUE'],
                                                   "DATE_DEFAULT_VALUE"     => $itemData['DATE_DEFAULT_VALUE'],
                                                   "PULLDOWN_DEFAULT_VALUE" => $itemData['PULLDOWN_DEFAULT_VALUE'],
                                                   "LINK_DEFAULT_VALUE"     => $itemData['LINK_DEFAULT_VALUE'],
                                                   "DESCRIPTION"            => $itemData['DESCRIPTION'],
                                                   "ACCESS_AUTH"            => $menuData['menu']['ACCESS_AUTH'],
                                                   "NOTE"                   => $itemData['NOTE']
                                                  );

                        $g["page_dir"] = "2100160002";

                        // 登録処理
                        $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160002", 4);

                        if($arrayResult[0] !== "000"){
                            throw new Exception();
                        }
                        $itemData['CREATE_ITEM_ID'] = json_decode($arrayResult[2],true)['CREATE_ITEM_ID'];

                        //columnIdとITEM_IDを紐つける
                        $columnIdConvArray[$key] = $itemData['CREATE_ITEM_ID'];
                    }
                }
                unset($itemData);
                
                //////////////////////////
                // 縦メニュー情報を更新
                ////////////////////////// 

                $updateData = NULL;
                // 既存の縦メニュー項目を探す
                foreach($convertParamInfoArray as $convertParamInfoData){
                    $key = array_search($convertParamInfoData['CREATE_ITEM_ID'], array_column($createItemInfoArray, 'CREATE_ITEM_ID'));
                    if($key !== false && $createItemInfoArray[$key]['CREATE_MENU_ID'] == $menuData['menu']['CREATE_MENU_ID']){
                        $updateData = $convertParamInfoData;
                    }
                }
                
                // 既存の縦メニュー項目を廃止
                if(!array_key_exists('r1',$menuData['repeat']) && $updateData != NULL){
                    $strNumberForRI = $updateData['CONVERT_PARAM_ID'];       // 主キー
                    $reqDeleteData = array("DISUSE_FLAG"          => "0",
                                           "UPD_UPDATE_TIMESTAMP" => "T_" . preg_replace("/[^a-zA-Z0-9]/", "", $updateData['LAST_UPDATE_TIMESTAMP'])
                                          );

                    $g["page_dir"] = "2100160009";

                    // 廃止処理
                    $intBaseMode = 3;       // 3:廃止、5:復活
                    $arrayResult = deleteTableMain($intBaseMode, $strNumberForRI, $reqDeleteData, "2100160009", 4);
                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                }
                // 既存の縦メニュー項目を更新
                else if(array_key_exists('r1',$menuData['repeat']) && $updateData != NULL){
                    if($menuData['repeat']['r1']['COLUMNS'][0][0] == 'i'){
                        $createItemID = $menuData['item'][$menuData['repeat']['r1']['COLUMNS'][0]]['CREATE_ITEM_ID'];
                    }
                    else{
                        $curGroup = $menuData['repeat']['r1']['COLUMNS'][0];
                        while($curGroup[0] == 'g'){
                            $curGroup = $menuData['group'][$curGroup]['COLUMNS'][0];
                        }
                        $createItemID = $menuData['item'][$curGroup]['CREATE_ITEM_ID'];
                    }
                    $strNumberForRI = $updateData['CONVERT_PARAM_ID'];       // 主キー
                    $arrayUpdateData = array("CREATE_ITEM_ID"           => $createItemID,
                                               "COL_CNT"                => $repeatCount / ($menuData['repeat']['r1']['REPEAT_CNT'] - 1),
                                               "REPEAT_CNT"             => $menuData['repeat']['r1']['REPEAT_CNT'],
                                               "ACCESS_AUTH"            => $menuData['menu']['ACCESS_AUTH'],
                                               "UPD_UPDATE_TIMESTAMP"   => "T_" . preg_replace("/[^a-zA-Z0-9]/", "", $menuData['repeat']['LAST_UPDATE_TIMESTAMP'])
                                              );

                    $g["page_dir"] = "2100160009";

                    // 更新処理
                    $arrayResult = updateTableMain(3, $strNumberForRI, $arrayUpdateData, "2100160009", 4);
                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                }
                // 新規縦メニュー項目を登録
                else if(array_key_exists('r1',$menuData['repeat']) && $updateData == NULL){
                    if($menuData['repeat']['r1']['COLUMNS'][0][0] == 'i'){
                        $createItemID = $menuData['item'][$menuData['repeat']['r1']['COLUMNS'][0]]['CREATE_ITEM_ID'];
                    }
                    else{
                        $curGroup = $menuData['repeat']['r1']['COLUMNS'][0];
                        while($curGroup[0] == 'g'){
                            $curGroup = $menuData['group'][$curGroup]['COLUMNS'][0];
                        }
                        $createItemID = $menuData['item'][$curGroup]['CREATE_ITEM_ID'];
                    }
                    $arrayRegisterData = array("CREATE_ITEM_ID" => $createItemID,
                                               "COL_CNT"        => $repeatCount / ($menuData['repeat']['r1']['REPEAT_CNT'] - 1),
                                               "REPEAT_CNT"     => $menuData['repeat']['r1']['REPEAT_CNT'],
                                               "ACCESS_AUTH"    => $menuData['menu']['ACCESS_AUTH'],
                                              );

                    $g["page_dir"] = "2100160009";

                    // 登録処理
                    $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160009", 4);

                    if($arrayResult[0] !== "000"){
                        throw new Exception();
                    }
                }
                
                //////////////////////////
                // 一意制約(複数項目)を登録
                //////////////////////////
                //既存の一意制約(複数項目)を廃止
                foreach($uniqueConstraintArray as $uniqueConstraintData){
                    if($uniqueConstraintData['CREATE_MENU_ID'] == $menuData['menu']['CREATE_MENU_ID']){
                        $strNumberForRI = $uniqueConstraintData['UNIQUE_CONSTRAINT_ID'];       // 主キー
                        $reqDeleteData = array("DISUSE_FLAG"          => "0",
                                               "UPD_UPDATE_TIMESTAMP" => "T_" . preg_replace("/[^a-zA-Z0-9]/", "", $uniqueConstraintData['LAST_UPDATE_TIMESTAMP'])
                                              );

                        $g["page_dir"] = "2100160018";

                        // 廃止処理
                        $intBaseMode = 3;       // 3:廃止、5:復活
                        $arrayResult = deleteTableMain($intBaseMode, $strNumberForRI, $reqDeleteData, "2100160018", 4);
                        if($arrayResult[0] !== "000"){
                            throw new Exception();
                        }
                    }
                }

                //一意制約(複数項目)を登録
                if($menuData['menu']['UNIQUE_CONSTRAINT'] != ""){
                    $uniqueConstraintArray = explode(",", $menuData['menu']['UNIQUE_CONSTRAINT']);
                    foreach($uniqueConstraintArray as $idPattern){
                        //columnId「i1」をITEM_IDに置換する。
                        $idPatternArray = explode("-", $idPattern);
                        $idPatternConv = "";
                        foreach($idPatternArray as $id){
                            if(isset($columnIdConvArray[$id])){
                                $convId = $columnIdConvArray[$id];
                                if($idPatternConv == ""){
                                    $idPatternConv = $convId;
                                }else{
                                    $idPatternConv = $idPatternConv . "," . $convId;
                                }  
                            }else{
                                continue;
                            }
                        }

                        $arrayRegisterData = array("CREATE_MENU_ID" => $menuData['menu']['CREATE_MENU_ID'],
                                                   "UNIQUE_CONSTRAINT_ITEM" => $idPatternConv,
                                                   "ACCESS_AUTH"    => $menuData['menu']['ACCESS_AUTH'],
                                                  );

                        $g["page_dir"] = "2100160018";

                        // 登録処理
                        $arrayResult = registerTableMain(2, $arrayRegisterData, "2100160018", 4);

                        if($arrayResult[0] !== "000"){
                            throw new Exception();
                        }
                    }
                }

                //////////////////////////
                // メニュー作成管理テーブルに登録
                //////////////////////////
                $createMenuStatusTable = new CreateMenuStatusTable($g["objDBCA"], $g["db_model_ch"]);
                $insertData = array();
                $insertData['CREATE_MENU_ID'] = $menuData['menu']['CREATE_MENU_ID'];
                $insertData['STATUS_ID'] = "1";
                if($menuData['type'] === 'update'){
                    $insertData['MENU_CREATE_TYPE_ID'] = "3"; //編集
                }else{
                    //メニュー作成状態が2(作成済み)の場合は「初期化」に、それ以外(1(未作成))なら「新規作成」
                    if($menuCreateFlag == "2"){
                        $insertData['MENU_CREATE_TYPE_ID'] = "2"; //初期化
                    }else{
                        $insertData['MENU_CREATE_TYPE_ID'] = "1"; //新規作成
                    }
                }
                $insertData['FILE_NAME'] = "";
                $insertData['ACCESS_AUTH'] = $menuData['menu']['ACCESS_AUTH'];
                $insertData['NOTE'] = "";
                $insertData['DISUSE_FLAG'] = "0";
                $insertData['LAST_UPDATE_USER'] = $g['login_id'];
                
                $result = $createMenuStatusTable->insertTable($insertData, $seqNo, $jnlSeqNo);
                if(true !== $result){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $createResult = array("MM_STATUS_ID" => $seqNo,"CREATE_MENU_ID" => $menuData['menu']['CREATE_MENU_ID']);
                $arrayResult = array("000","",json_encode($createResult));

                // コミット
                $res = $g["objDBCA"]->transactionCommit();
                if ($res === false) {
                    web_log($g["objMTS"]->getSomeMessage("ITABASEH-ERR-900036", array(basename(__FILE__), __LINE__)));
                    $msg = $g["objMTS"]->getSomeMessage("ITABASEH-ERR-900036", array(basename(__FILE__), __LINE__));
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $tranStartFlg = false;
            }
            catch(Exception $e){
                if($tranStartFlg === true){
                    // ロールバック
                    $g["objDBCA"]->transactionRollback();
                }
            }
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                web_log( $arrayResult[2]);
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                web_log( $arrayResult[2]);
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // 入力方式リスト取得
        /////////////////////
        function selectInputMethod(){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();

            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");

            $inputMethodTable = new inputMethodTable($g["objDBCA"], $g["db_model_ch"]);
            $sql = $inputMethodTable->createSselect("WHERE DISUSE_FLAG = '0'");
            $result = $inputMethodTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                $arrayResult = array("999","", $msg);
                return makeAjaxProxyResultStream($arrayResult);
            }

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            // 権限があるでーたのみに絞る
            $ret = $obj->chkRecodeArrayAccessPermission($result);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            $filteredData = array();
            foreach($result as $imData){
                $addArray = array();
                $addArray['INPUT_METHOD_ID']   = $imData['INPUT_METHOD_ID'];
                $addArray['INPUT_METHOD_NAME'] = $imData['INPUT_METHOD_NAME'];
                $filteredData[] = $addArray;
            }
            
            $arrayResult = array("000","", json_encode($filteredData));
            
            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // 作成対象リスト取得
        /////////////////////
        function selectParamTarget(){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();

            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");

            $paramTargetTable = new ParamTargetTable($g["objDBCA"], $g["db_model_ch"]);
            $sql = $paramTargetTable->createSselect("WHERE DISUSE_FLAG = '0' ORDER BY DISP_SEQ");
            $result = $paramTargetTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                $arrayResult = array("999","", $msg);
                return makeAjaxProxyResultStream($arrayResult);
            }

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            // 権限があるでーたのみに絞る
            $ret = $obj->chkRecodeArrayAccessPermission($result);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            $filteredData = array();
            foreach($result as $ptData){
                $addArray = array();
                $addArray['TARGET_ID']   = $ptData['TARGET_ID'];
                $addArray['TARGET_NAME'] = $ptData['TARGET_NAME'];
                $filteredData[] = $addArray;
            }
            
            $arrayResult = array("000","", json_encode($filteredData));

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // 用途リスト取得
        /////////////////////
        function selectParamPurpose(){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");

            $paramPurposeTable = new ParamPurposeTable($g["objDBCA"], $g["db_model_ch"]);
            $sql = $paramPurposeTable->createSselect("WHERE DISUSE_FLAG = '0'");
            $result = $paramPurposeTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                $arrayResult = array("999","", $msg);
                return makeAjaxProxyResultStream($arrayResult);
            }

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            // 権限があるでーたのみに絞る
            $ret = $obj->chkRecodeArrayAccessPermission($result);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            $filteredData = array();
            foreach($result as $ppData){
                $addArray = array();
                $addArray['PURPOSE_ID']   = $ppData['PURPOSE_ID'];
                $addArray['PURPOSE_NAME'] = $ppData['PURPOSE_NAME'];
                $filteredData[] = $addArray;
            }
            
            $arrayResult = array("000","", json_encode($filteredData));

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // メニューグループリスト取得
        /////////////////////
        function selectMenuGroupList(){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            
            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
            
            $menuGroupTable = new MenuGroupTable($g["objDBCA"], $g["db_model_ch"]);
            $sql = $menuGroupTable->createSselect("WHERE DISUSE_FLAG = '0'");
            $result = $menuGroupTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                $arrayResult = array("999","", $msg);
                return makeAjaxProxyResultStream($arrayResult);
            }

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            // 権限があるでーたのみに絞る
            $ret = $obj->chkRecodeArrayAccessPermission($result);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            $filteredData = array();
            foreach($result as $mgData){
                $addArray = array();
                $addArray['MENU_GROUP_ID']   = $mgData['MENU_GROUP_ID'];
                $addArray['MENU_GROUP_NAME'] = $mgData['MENU_GROUP_NAME'];
                $filteredData[] = $addArray;
            }
            
            $arrayResult = array("000","", json_encode($filteredData));

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // プルダウン選択項目リスト取得
        /////////////////////
        function selectPulldownList(){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();

            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
            $pullDownTable = new PullDownTable($g["objDBCA"], $g["db_model_ch"]);
            $sql = $pullDownTable->createSselect("WHERE DISUSE_FLAG = '0'");
            $result = $pullDownTable->selectTable($sql);
            if(!is_array($result)){
                $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                $arrayResult = array("999","", $result);
                return makeAjaxProxyResultStream($arrayResult);
            }

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            // 権限があるデータのみに絞る
            $ret = $obj->chkRecodeArrayAccessPermission($result);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            $filteredData = array();

            foreach($result as $pdData){
                $addArray = array();
                $addArray['LINK_ID']       = $pdData['LINK_ID'];
                $addArray['MENU_ID']       = $pdData['MENU_ID'];
                $addArray['LINK_PULLDOWN'] = $pdData['LINK_PULLDOWN'];
                $filteredData[] = $addArray;
            }
            $arrayResult = array("000","", json_encode($filteredData));

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // 参照項目リスト取得（メニュー内の項目で選択されている「プルダウン選択」で利用できる「参照項目」のみ取得。ページ内で名前を変換するために利用。）
        /////////////////////
        function selectReferenceItemList($itemArray){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();

            if(!empty($itemArray)){
                require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");

                //項目に使われている「プルダウン選択」のLINK_IDを抽出
                $targetOtherMenuLinkIdList = array();
                foreach($itemArray as $itemData){
                    if($itemData['INPUT_METHOD_ID'] == 7){
                        array_push($targetOtherMenuLinkIdList, $itemData['OTHER_MENU_LINK_ID']);
                    }
                }
                //重複排除
                $targetOtherMenuLinkIdList = array_unique($targetOtherMenuLinkIdList);

                //他メニュー連携IDから、そのメニューのIDを取得
                $targetMenuIdList = array();
                $otherMenuLinkTable = new OtherMenuLinkTable($g["objDBCA"], $g["db_model_ch"]);
                foreach($targetOtherMenuLinkIdList as $linkId){
                    $sql = $otherMenuLinkTable->createSselect("WHERE DISUSE_FLAG = '0'AND LINK_ID = " . $linkId);

                    // SQL実行
                    $result = $otherMenuLinkTable->selectTable($sql);
                    if(!is_array($result)){
                        $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                        $arrayResult = array("999","", $result);
                        return makeAjaxProxyResultStream($arrayResult);
                    }
                    if(!empty($result)){
                        array_push($targetMenuIdList, $result[0]['MENU_ID']);
                    }
                }
                //重複排除
                $targetMenuIdList = array_unique($targetMenuIdList);

                //メニューIDにヒットする参照項目を取得
                $filteredData = array();
                $referenceItemTable = new ReferenceItemTable($g["objDBCA"], $g["db_model_ch"]);
                foreach($targetMenuIdList as $menuId){
                    $sql = $referenceItemTable->createSselect("WHERE DISUSE_FLAG = '0' AND MENU_ID = " . $menuId ." ORDER BY DISP_SEQ");
                    $result = $referenceItemTable->selectTable($sql);
                    if(!is_array($result)){
                        $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                        $arrayResult = array("999","", $result);
                        return makeAjaxProxyResultStream($arrayResult);
                    }

                    foreach($result as $pdData){
                        $addArray = array();
                        $addArray['ITEM_ID']         = $pdData['ITEM_ID'];
                        $addArray['LINK_ID']         = $pdData['LINK_ID'];
                        $addArray['MENU_ID']         = $pdData['MENU_ID'];
                        $addArray['DISP_SEQ']        = $pdData['DISP_SEQ'];
                        $addArray['COL_GROUP_NAME']  = $pdData['COL_GROUP_NAME'];
                        $addArray['ITEM_NAME']       = $pdData['ITEM_NAME'];
                        $addArray['ORIGINAL_MENU_FLAG'] = $pdData['ORIGINAL_MENU_FLAG'];
                        $filteredData[] = $addArray;
                    }
                }

                $arrayResult = array("000","", json_encode($filteredData));

            }else{
                $filteredData = array();
                $arrayResult =  array("000","", json_encode($filteredData));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // パラメータシート参照リスト取得(メニューのみ)
        /////////////////////
        function selectReferenceSheetType3List(){
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();

            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
            //パラメータシート参照の選択候補となる対象のメニューを取得
            $referenceSheetType3View = new ReferenceSheetType3View($g["objDBCA"], $g["db_model_ch"]);
            $sql = "SELECT DISTINCT MENU_NAME, MENU_ID, MENU_GROUP_NAME, ACCESS_AUTH, ACCESS_AUTH_01, ACCESS_AUTH_02, ACCESS_AUTH_03, ACCESS_AUTH_04 FROM G_CREATE_REFERENCE_SHEET_TYPE_3 WHERE DISUSE_FLAG = '0' ORDER BY MENU_ID, DISP_SEQ;"; //重複削除条件をつけたいため、記述したSELECT文を採用

            // SQL実行
            $result = $referenceSheetType3View->selectTable($sql);
            if(!is_array($result)){
                $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                $arrayResult = array("999","", $result);
                return makeAjaxProxyResultStream($arrayResult);
            }

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            // 権限があるデータのみに絞る
            $ret = $obj->chkRecodeArrayAccessPermission($result);
            if($ret === false) {
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                $arrayResult = array("999","", "");
                return makeAjaxProxyResultStream($arrayResult);
            }

            $filteredData = array();
            $arrayMenuId = array();
            foreach($result as $pdData){
                //MENU_IDの重複チェック
                if(!in_array($pdData['MENU_ID'], $arrayMenuId)){
                  array_push($arrayMenuId, $pdData['MENU_ID']);
                }else{
                  continue;
                }
                $addArray = array();
                $addArray['MENU_ID']       = $pdData['MENU_ID'];
                $addArray['MENU_NAME_PULLDOWN'] = $pdData['MENU_GROUP_NAME'].":".$pdData['MENU_NAME'];
                $filteredData[] = $addArray;
            }
            $arrayResult = array("000","", json_encode($filteredData));

            if($arrayResult[0]=="000"){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
            }else if(intval($arrayResult[0])<500){
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
            }else{
                web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
            }

            return makeAjaxProxyResultStream($arrayResult);

        }

        /////////////////////
        // パラメータシート参照の項目からメニューIDを取得（「パラメータシート参照」について、登録されている項目IDから対象のメニューIDを取得し、セレクトボックスの初期値を決定するため。）
        /////////////////////
        function selectReferenceSheetType3ItemData($itemArray){
            // グローバル変数宣言
            global $g;
            
            // ローカル変数宣言
            $arrayResult = array();

            if(!empty($itemArray)){
                require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
                $referenceSheetType3View = new ReferenceSheetType3View($g["objDBCA"], $g["db_model_ch"]);

                //項目に使われている「プルダウン選択」のLINK_IDを抽出
                $targetType3ReferenceItemData = array();
                $filteredData = array();
                foreach($itemArray as $itemData){
                    if($itemData['INPUT_METHOD_ID'] == 11){
                        $itemId = $itemData['TYPE3_REFERENCE'];
                        //項目IDからメニューIDを取得する
                        $sql = $referenceSheetType3View->createSselect("WHERE DISUSE_FLAG = '0' AND ITEM_ID = :ITEM_ID");
                        $sqlBind = array('ITEM_ID' => $itemId);

                        // SQL実行
                        $result = $referenceSheetType3View->selectTable($sql, $sqlBind);
                        if(!is_array($result)){
                            $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                            $arrayResult = array("999","",$msg); 
                            throw new Exception();
                        }
                        $targetType3ReferenceData = $result;

                        if(!empty($targetType3ReferenceData)){
                          //メニューIDを取得（レコードは1つの想定）
                          $menuId = $targetType3ReferenceData[0]['MENU_ID'];
                        }else{
                          continue;
                        }

                        //項目IDをkey、メニューIDをvalueにいれる。
                        $targetType3ReferenceItemData[$itemId] = $menuId;
                    }
                }
                $filteredData = $targetType3ReferenceItemData;
                $arrayResult = array("000","", json_encode($filteredData));

            }else{
                $filteredData = array();
                $arrayResult =  array("000","", json_encode($filteredData));
            }

            return makeAjaxProxyResultStream($arrayResult);
        }

        /////////////////////
        // メニュー作成情報関連データ取得
        /////////////////////
        function selectMenuInfo($createMenuId){
        
            // グローバル変数宣言
            global $g;

            // ローカル変数宣言
            $arrayResult = array();
            $returnDataArray = array();
            $userIdCreateParam = '-101601'; //「メニュー作成機能」ユーザID

            require_once ( $g["root_dir_path"] . "/libs/backyardlibs/create_param_menu/ky_create_param_menu_classes.php");
            
            try{
                //////////////////////////
                // メニュー作成情報を取得
                //////////////////////////
                $createMenuInfoTable = new CreateMenuInfoTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $createMenuInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $createMenuInfoTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $createMenuInfoArray = $result;
                
                //////////////////////////
                // メニュー項目作成情報を取得
                //////////////////////////
                $createItemInfoTable = new CreateItemInfoTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $createItemInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $createItemInfoTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $createItemInfoArray = $result;
                
                //////////////////////////
                // カラムグループ管理テーブルを検索
                //////////////////////////
                $columnGroupTable = new ColumnGroupTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $columnGroupTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $columnGroupTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $columnGroupArray = $result;
                
                /////////////////////
                // アカウントリスト取得
                /////////////////////
                $accountListTable = new AccountListTable($g["objDBCA"], $g["db_model_ch"]);
                $sql = $accountListTable->createSselect("WHERE DISUSE_FLAG = '0'");
                
                // SQL実行
                $result = $accountListTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","", $msg);
                    throw new Exception();
                }
                $accountListArray = $result;
                
                //////////////////////////
                // メニュー(縦)作成情報を取得
                //////////////////////////
                $convertParamInfoTable = new ConvertParamInfoTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $convertParamInfoTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $convertParamInfoTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg);
                    throw new Exception();
                }
                $convertParamInfoArray = $result;

                //////////////////////////
                // 一意制約(複数項目)情報を取得
                //////////////////////////
                $uniqueConstraintTable = new UniqueConstraintTable($g['objDBCA'], $g['db_model_ch']);
                $sql = $uniqueConstraintTable->createSselect("WHERE DISUSE_FLAG = '0'");

                // SQL実行
                $result = $uniqueConstraintTable->selectTable($sql);
                if(!is_array($result)){
                    $msg = $g["objMTS"]->getSomeMessage('ITACREPAR-ERR-5003', $result);
                    $arrayResult = array("999","",$msg); 
                    throw new Exception();
                }
                $uniqueConstraintArray = $result;
                
                // メニュー情報詰め込み
                $findFlag = false;
                foreach($createMenuInfoArray as $createMenuInfoData){
                    if($createMenuInfoData['CREATE_MENU_ID'] == $createMenuId){
                        $dispLastUpdateTimestamp = $createMenuInfoData['LAST_UPDATE_TIMESTAMP'];

                        //最終更新者が-101601(メニュー作成機能)の場合、履歴テーブルから最終更新者が「メニュー作成機能」以外で最新の対象を取得する。
                        if($createMenuInfoData['LAST_UPDATE_USER'] == $userIdCreateParam){
                            $sql = $createMenuInfoTable->createSselectJnl("WHERE LAST_UPDATE_TIMESTAMP = (SELECT max(LAST_UPDATE_TIMESTAMP) FROM F_CREATE_MENU_INFO_JNL WHERE DISUSE_FLAG = '0' AND NOT LAST_UPDATE_USER = :USER_ID_CREATE_PARAM AND CREATE_MENU_ID = :CREATE_MENU_ID)");
                            $sqlBind = array('CREATE_MENU_ID' => $createMenuId, 'USER_ID_CREATE_PARAM' => $userIdCreateParam);

                            // SQL実行
                            $result = $createMenuInfoTable->selectTable($sql, $sqlBind);
                            if(!is_array($result)){
                                $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5003', $result);
                                $arrayResult = array("999","",$msg); 
                                throw new Exception();
                            }
                            $latestCreateMenuInfoArray = $result;
                            foreach($latestCreateMenuInfoArray as $latestData){
                                $createMenuInfoData['LAST_UPDATE_USER'] = $latestData['LAST_UPDATE_USER']; //最終更新ユーザを差し替え
                                $dispLastUpdateTimestamp = $latestData['LAST_UPDATE_TIMESTAMP']; //表示用最終更新日時を差し替え
                            }
                        }

                        $findFlag = true;
                        $username = "";
                        foreach($accountListArray as $accountListData){
                            if($createMenuInfoData['LAST_UPDATE_USER'] == $accountListData['USER_ID']){
                                $username = $accountListData['USERNAME_JP'];
                            }
                        }
                        
                        $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $dispLastUpdateTimestamp);
                        
                        $returnDataArray['menu'] = array(
                            "CREATE_MENU_ID"           => $createMenuInfoData['CREATE_MENU_ID'],
                            "MENU_NAME"                => $createMenuInfoData['MENU_NAME'],
                            "PURPOSE"                  => $createMenuInfoData['PURPOSE'],
                            "TARGET"                   => $createMenuInfoData['TARGET'],
                            "VERTICAL"                 => $createMenuInfoData['VERTICAL'],
                            "MENUGROUP_FOR_INPUT"      => $createMenuInfoData['MENUGROUP_FOR_INPUT'],
                            "MENUGROUP_FOR_SUBST"      => $createMenuInfoData['MENUGROUP_FOR_SUBST'],
                            "MENUGROUP_FOR_VIEW"       => $createMenuInfoData['MENUGROUP_FOR_VIEW'],
                            "MENU_CREATE_STATUS"       => $createMenuInfoData['MENU_CREATE_STATUS'],
                            "DISP_SEQ"                 => $createMenuInfoData['DISP_SEQ'],
                            "DESCRIPTION"              => $createMenuInfoData['DESCRIPTION'],
                            "ACCESS_AUTH"              => $createMenuInfoData['ACCESS_AUTH'],
                            "NOTE"                     => $createMenuInfoData['NOTE'],
                            "LAST_UPDATE_USER"         => $username,
                            "LAST_UPDATE_TIMESTAMP"    => $createMenuInfoData['LAST_UPDATE_TIMESTAMP'],
                            "LAST_UPDATE_TIMESTAMP_FOR_DISPLAY" => $date->format('Y-m-d H:i:s')
                        );
                        break;
                    }
                }
                
                // 対応するメニュー情報がない場合、エラー
                if(false === $findFlag){
                    $msg = $g['objMTS']->getSomeMessage('ITACREPAR-ERR-5005', $result);
                    $arrayResult = array("999","",$msg);
                    throw new Exception($msg);
                }
                
                
                // メニュー項目情報を特定する
                $itemInfoArray = array();
                foreach($createItemInfoArray as $ciiData){
                    if($createMenuId == $ciiData['CREATE_MENU_ID']){
                        $itemInfoArray[] = $ciiData;
                    }
                }
                
                // 項目作成情報を表示順序、項番の昇順に並べ替える
                $dispSeqArray = array();
                $idArray = array();
                foreach ($itemInfoArray as $key => $itemInfo){
                    $dispSeqArray[$key] = $itemInfo['DISP_SEQ'];
                    $idArray[$key]      = $itemInfo['CREATE_ITEM_ID'];
                }
                array_multisort($dispSeqArray, SORT_ASC, $idArray, SORT_ASC, $itemInfoArray);
                
                // 縦メニュー
                $convertFlag = false;
                $cpiData = NULL;
                // 開始項目を探す
                foreach($convertParamInfoArray as $convertParamInfoData){
                    $searchIdx = array_search($convertParamInfoData['CREATE_ITEM_ID'], array_column($itemInfoArray, 'CREATE_ITEM_ID'));
                    if(false !== $searchIdx){
                        $cpiData = $convertParamInfoData;
                        $convertFlag = true;
                        break;
                    }
                }
                
                // リピート項目がない
                if(NULL === $cpiData){
                    $returnDataArray['repeat'][] = array();
                }
                // リピート項目がある
                else{
                    $columnsArray = array();
                    // リピート中の項目の配列を作る
                    for($i = 1 ; $i <= $cpiData['COL_CNT'] ; $i++){
                        $columnsArray[] = "i" . ($i + $searchIdx);
                    }
                    $returnDataArray['repeat']['r1'] = array(
                        "columns"    => $columnsArray,
                        "REPEAT_CNT" => $cpiData['REPEAT_CNT'],
                        "LAST_UPDATE_TIMESTAMP" => $cpiData['LAST_UPDATE_TIMESTAMP']
                    );
                }
                
                // 項目作成情報
                $tmpGroupArray = array();
                $checked = array();
                $itemNum = 1;
                $returnDataArray['item'] = array();
                foreach($itemInfoArray as $itemInfoData){
                    // 繰り返し項目判定([2],[3]...)
                    if($convertFlag == true && $itemNum >= $searchIdx + $cpiData['COL_CNT'] + 1 && $itemNum < $searchIdx + $cpiData['COL_CNT'] * $cpiData['REPEAT_CNT'] + 1){
                        $repeatItem = true;
                    }
                    else{
                        $repeatItem = false;
                    }
                    // 親カラムグループを探す
                    if($repeatItem == false){
                        $parent = "";
                        if($itemInfoData['COL_GROUP_ID'] != ""){
                            // カラムグループを記録
                            $tmpGroupArray[$itemInfoData['COL_GROUP_ID']][] = 'i' . $itemNum;
                            foreach($columnGroupArray as $columnGroupData){
                                if($columnGroupData['COL_GROUP_ID'] == $itemInfoData['COL_GROUP_ID']){
                                    $parent = $columnGroupData['FULL_COL_GROUP_NAME'];
                                    break;
                                }
                            }
                            $curGroup = $itemInfoData['COL_GROUP_ID'];
                            // 項目所属するカラムグループの親カラムグループの['columns']配列にカラム情報を入れます
                            $endFlag = false;
                            while(false == $endFlag){
                                foreach($columnGroupArray as $columnGroupData){
                                    if($columnGroupData['COL_GROUP_ID'] == $curGroup){
                                        if($columnGroupData['PA_COL_GROUP_ID'] == "" || true === in_array($curGroup,$checked)){
                                            $endFlag = true;
                                            break;
                                        }
                                        else{
                                            $tmpGroupArray[$columnGroupData['PA_COL_GROUP_ID']][] = $curGroup;
                                            $checked[] = $curGroup;
                                            $curGroup = $columnGroupData['PA_COL_GROUP_ID'];
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($itemInfoData['REQUIRED'] == "1"){
                        $required = true;
                    }
                    else{
                        $required = false;
                    }
                    if($itemInfoData['UNIQUED'] == "1"){
                        $uniqued= true;
                    }
                    else{
                        $uniqued = false;
                    }

                    $datetimeFormat = "";
                    $dateFormat = "";
                    if($itemInfoData['DATETIME_DEFAULT_VALUE'] != ""){
                        $datetimeDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfoData['DATETIME_DEFAULT_VALUE']);
                        if($datetimeDefaultValue != false) $datetimeFormat = $datetimeDefaultValue->format('Y/m/d H:i:s');
                    }
                    if($itemInfoData['DATE_DEFAULT_VALUE'] != ""){
                        $dateDefaultValue = DateTime::createFromFormat('Y-m-d H:i:s.u', $itemInfoData['DATE_DEFAULT_VALUE']);
                        if($dateDefaultValue != false) $dateFormat = $dateDefaultValue->format('Y/m/d');
                    }

                    $returnDataArray['item']['i' . $itemNum] = array(
                        "CREATE_MENU_ID"        => $itemInfoData['CREATE_MENU_ID'],
                        "CREATE_ITEM_ID"        => $itemInfoData['CREATE_ITEM_ID'],
                        "ITEM_NAME"             => $itemInfoData['ITEM_NAME'],
                        "DISP_SEQ"              => $itemInfoData['DISP_SEQ'],
                        "REQUIRED"              => $required,
                        "UNIQUED"               => $uniqued,
                        "COL_GROUP_ID"          => $itemInfoData['COL_GROUP_ID'],
                        "PARENT"                => $parent,
                        "INPUT_METHOD_ID"       => $itemInfoData['INPUT_METHOD_ID'],
                        "MAX_LENGTH"            => $itemInfoData['MAX_LENGTH'],
                        "PREG_MATCH"            => $itemInfoData['PREG_MATCH'],
                        "MULTI_MAX_LENGTH"      => $itemInfoData['MULTI_MAX_LENGTH'],
                        "MULTI_PREG_MATCH"      => $itemInfoData['MULTI_PREG_MATCH'],
                        "INT_MIN"               => $itemInfoData['INT_MIN'],
                        "INT_MAX"               => $itemInfoData['INT_MAX'],
                        "FLOAT_MIN"             => $itemInfoData['FLOAT_MIN'],
                        "FLOAT_MAX"             => $itemInfoData['FLOAT_MAX'],
                        "FLOAT_DIGIT"           => $itemInfoData['FLOAT_DIGIT'],
                        "OTHER_MENU_LINK_ID"    => $itemInfoData['OTHER_MENU_LINK_ID'],
                        "PW_MAX_LENGTH"         => $itemInfoData['PW_MAX_LENGTH'],
                        "UPLOAD_MAX_SIZE"       => $itemInfoData['UPLOAD_MAX_SIZE'],
                        "LINK_LENGTH"           => $itemInfoData['LINK_LENGTH'],
                        "REFERENCE_ITEM"        => $itemInfoData['REFERENCE_ITEM'],
                        "TYPE3_REFERENCE"       => $itemInfoData['TYPE3_REFERENCE'],
                        "SINGLE_DEFAULT_VALUE"  => $itemInfoData['SINGLE_DEFAULT_VALUE'],
                        "MULTI_DEFAULT_VALUE"   => $itemInfoData['MULTI_DEFAULT_VALUE'],
                        "INT_DEFAULT_VALUE"     => $itemInfoData['INT_DEFAULT_VALUE'],
                        "FLOAT_DEFAULT_VALUE"   => $itemInfoData['FLOAT_DEFAULT_VALUE'],
                        "DATETIME_DEFAULT_VALUE" => $datetimeFormat,
                        "DATE_DEFAULT_VALUE"    => $dateFormat,
                        "PULLDOWN_DEFAULT_VALUE" => $itemInfoData['PULLDOWN_DEFAULT_VALUE'],
                        "LINK_DEFAULT_VALUE"    => $itemInfoData['LINK_DEFAULT_VALUE'],
                        "DESCRIPTION"           => $itemInfoData['DESCRIPTION'],
                        "REPEAT_ITEM"           => $repeatItem,
                        "MIN_WIDTH"             => "",
                        "ACCESS_AUTH"           => $createMenuInfoData['ACCESS_AUTH'],
                        "NOTE"                  => $itemInfoData['NOTE'],
                        "LAST_UPDATE_TIMESTAMP" => $itemInfoData['LAST_UPDATE_TIMESTAMP']
                    );
                    $itemNum++; 
                }
                
                // カラムグループ
                
                $keyToId = array(); // {COL_GROUP_ID -> g1,g2,g3...}
                $returnGroupArray = array(); // WEBに返信するカラムグループ
                $groupNum = 1;
                // COL_GROUP_IDと対応のg番号配列を作る
                foreach($tmpGroupArray as $key => $groupData){
                    $keyToId[$key] = 'g' . $groupNum;
                    $groupNum++;
                }
                
                // カラムグループIDをg1,g2,g3...に変換
                foreach($tmpGroupArray as $key => $groupData){
                    foreach($columnGroupArray as $columnGroupData){
                        if($columnGroupData['COL_GROUP_ID'] == $key){
                            $parent = "";
                            if($columnGroupData['PA_COL_GROUP_ID'] != ""){
                                $parent = preg_replace('/\/' . $columnGroupData['COL_GROUP_NAME'] . '$/' , '' , $columnGroupData['FULL_COL_GROUP_NAME']);
                            }
                            $columns = array();
                            foreach($groupData as $column){
                                if(array_key_exists($column,$keyToId)){
                                    $columns[] = $keyToId[$column];
                                }
                                else{
                                    $columns[] = $column;
                                }
                            }
                            $returnGroupArray[$keyToId[$key]] = array(
                                "COL_GROUP_ID"   => $columnGroupData['COL_GROUP_ID'],
                                "COL_GROUP_NAME" => $columnGroupData['COL_GROUP_NAME'],
                                "PARENT" => $parent,
                                "COLUMNS" => $columns
                            
                            );
                            break;
                        }
                    }
                }
                $returnDataArray['group'] = $returnGroupArray;
                
                // リピートの位置 0:リピートなし 1:リピートがカラムグループの中にいる 2:その他
                $repeatCase = 0;
                // リピート項目の位置を決める(リピートカラムの共通カラムグループを判定)
                if($convertFlag == true){
                    $commonPrefixArray = array();
                    foreach($returnDataArray['repeat']['r1']['columns'] as $item){
                        $commonPrefixArray[] = $returnDataArray['item'][$item]['PARENT'] . "/";
                    }
                    sort($commonPrefixArray);
                    $s1 = $commonPrefixArray[0];
                    $s2 = $commonPrefixArray[count($commonPrefixArray) - 1];
                    $len = min(strlen($s1),strlen($s2));
                    
                    for($i = 0 ; $i < $len && $s1[$i] == $s2[$i] ; $i++);
                    
                    $prefix = substr($s1,0,$i);
                    $prefix = substr($prefix,0,strrpos($prefix,"/"));
                    
                    if($prefix == ""){
                        $repeatCase = 2;
                    }
                    else{
                        $repeatCase = 1;
                    }
                }

                // 冒頭(一番上)の項目配列(['menu']['columns'])を作成(i1,i2,g1,g2,r1とか)
                $columns = array();
                foreach($returnDataArray['item'] as $key => $item){
                    // 縦メニュー項目(repeat-item)の場合、r1を入る
                    if($repeatCase == 2 && in_array($key,$returnDataArray['repeat']['r1']['columns'])){
                        $columns[] = 'r1';
                    }
                    // 重複縦メニューの場合、スキップ
                    else if($item['REPEAT_ITEM'] === true){
                        continue;
                    }
                    // 親カラムがない項目の場合、項目を入る
                    else if($item['COL_GROUP_ID'] == ""){
                        $columns[] = $key;
                    }
                    // 親カラムがある項目の場合、ルート親カラムを入る
                    else{
                        $group = $keyToId[$item['COL_GROUP_ID']];
                        if($returnDataArray['group'][$group]["PARENT"] == ""){
                            $columns[] = $group;
                        }
                        else{
                            $parent = substr($returnDataArray['group'][$group]['PARENT'],0,strpos($returnDataArray['group'][$group]['PARENT'].'/','/'));
                            foreach($returnDataArray['group'] as $key => $group){
                                if($group['COL_GROUP_NAME'] == $parent && $group['PARENT'] == ''){
                                    $columns[] = $key;
                                    break;
                                }
                            }
                        }
                    }
                }
                $returnDataArray['menu']['columns'] = array_values(array_unique($columns));
                
                if($convertFlag == true){
                    // ['r1']['columns']のitem -> group
                    foreach($returnDataArray['repeat']['r1']['columns'] as &$item){
                        if($returnDataArray['item'][$item]['PARENT'] == $prefix){
                            continue;
                        }
                        if($repeatCase == 1){
                            $rootColInRepeat = str_replace($prefix . "/","",$returnDataArray['item'][$item]['PARENT']);
                        }else{
                            $rootColInRepeat = $returnDataArray['item'][$item]['PARENT'];
                        }
                        $rootColInRepeat = substr($rootColInRepeat,0,strpos($rootColInRepeat."/","/"));
                        
                        if($rootColInRepeat != ""){
                            foreach($returnDataArray['group'] as $key => $group){
                                if($group['COL_GROUP_NAME'] == $rootColInRepeat){
                                    $item = $key;
                                    break;
                                }
                            }
                        }
                    }
                    unset($item);
                    
                    $returnDataArray['repeat']['r1']['columns'] = array_values(array_unique($returnDataArray['repeat']['r1']['columns']));
                    if($repeatCase == 1){
                        // リピートが所属するカラムグループのitemとgroupをr1に変換
                        foreach($returnDataArray['group'] as &$group){
                            if($group['COL_GROUP_NAME'] == substr(strrchr("/".$prefix, "/"), 1)){
                                $idx = array_search($returnDataArray['repeat']['r1']['columns'][0],$group['COLUMNS']);
                                array_splice($group['COLUMNS'],$idx,count($returnDataArray['repeat']['r1']['columns']),'r1');
                                break;
                            }
                        }
                        unset($group);
                    }
                }
                
                $returnDataArray['menu']['number-item']  = count($returnDataArray['item']);
                $returnDataArray['menu']['number-group'] = count($returnDataArray['group']);

                //一意制約(複数項目)
                $addUniqueConstraintArray = array();
                foreach($uniqueConstraintArray as $uniqueConstraintData){
                    if($uniqueConstraintData['CREATE_MENU_ID'] == $createMenuId){
                        array_push($addUniqueConstraintArray, $uniqueConstraintData['UNIQUE_CONSTRAINT_ITEM']);
                    }
                }

                //UNIQUE_CONSTRAINT_ITEMのID部分をcolumnId（「i1」「i2」など）に変換し配列に格納。
                $currentUniqueConstraintArray = array();
                foreach($addUniqueConstraintArray as $idPattern){
                    $idPatternArray = explode(",", $idPattern);
                    $currentPatternArray= array();
                    foreach($idPatternArray as $id){
                        foreach($returnDataArray['item'] as $key => $item){
                            if($id == $item['CREATE_ITEM_ID']){
                                array_push($currentPatternArray, array($key=>$item['ITEM_NAME']));
                            }
                        }
                    }
                    array_push($currentUniqueConstraintArray, $currentPatternArray);
                }
                $returnDataArray['menu']['unique-constraints-current'] = $currentUniqueConstraintArray;


                $arrayResult = array("000", "",json_encode($returnDataArray));

                if($arrayResult[0]=="000"){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
                }else if(intval($arrayResult[0])<500){
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
                }else{
                    web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
                }

            }
            catch(Exception $e){
                return makeAjaxProxyResultStream($arrayResult);
            }
            return makeAjaxProxyResultStream($arrayResult);
        }

        //-- サイト個別PHP要素、ここまで--
    }
    $server = new HTML_AJAX_Server();
    $db_access = new Db_Access();
    $server->registerClass($db_access);
    $server->handleRequest();
?>
