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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = '★★★INFO★★★';

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

    $table = new TableControlAgent('G_★★★TABLE★★★_H','ROW_ID', 'No', 'G_★★★TABLE★★★_H_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_★★★TABLE★★★_H_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_★★★TABLE★★★_H_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_★★★TABLE★★★_H');
    $table->setDBJournalTableHiddenID('F_★★★TABLE★★★_H_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // マルチユニーク制約
    $table->addUniqueColumnSet(array('HOST_ID','OPERATION_ID'));
     
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel('★★★MENU★★★');
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', '★★★MENU★★★');

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    // 検索機能の制御----

    $c = new IDColumn('HOST_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102601"),'C_STM_LIST','SYSTEM_ID','HOSTNAME','');
    $c->setDescription('choose host');//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102603"));

        // オペレーションID
        $c = new NumColumn('OPERATION_ID_DISP',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102606"));
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102607"));
        $c->setSubtotalFlag(false);
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $cg->addColumn($c);

        // オペレーション名
        $c = new TextColumn('OPERATION_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102608"));
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102609"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $cg->addColumn($c);

        // 基準日
        $c = new DateTimeColumn('BASE_TIMESTAMP', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102615") ,'DATETIME','DATETIME',false);
        $c->setOutputType("filter_table", new OutputType(new FilterTabHFmt(), new SingleDateFilterTabBFmt()));
        $c->setEvent("filter_table", "onclose", "search_async", array("'idcolumn_filter_default'"));
        $c->setMinuteScaleInputOnFilter(5);
        $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
        $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102616"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $cg->addColumn($c);

        // 実施予定日
        $c = new DateTimeColumn('OPERATION_DATE', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102604"),'DATETIME','DATETIME',false);
        $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
        $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102605"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $cg->addColumn($c);

        // 最終実行日
        $c = new DateTimeColumn('LAST_EXECUTE_TIMESTAMP', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102617"),'DATETIME','DATETIME',false);
        $c->setSecondsInputOnIU(false);          /* UI表示時に秒を非表示       */
        $c->setSecondsInputOnFilter(false);      /* フィルタ表示時に秒を非表示 */
        $c->setHiddenMainTableColumn(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102618"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $cg->addColumn($c);

        // オペレーションID：オペレーション名
        $c = new IDColumn('OPERATION_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102610"), 'G_OPERATION_LIST', 'OPERATION_ID', 'OPERATION_ID_N_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('OPERATION_ID_N_NAME'),'ORDER'=>'ORDER BY ADD_SELECT_1') );
        $c->setHiddenMainTableColumn(true);
        $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102611"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("print_table")->setVisible(false);
        $c->getOutputType("print_journal_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->setRequired(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

★★★INPUT_ORDER★★★

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102612"));

★★★ITEM★★★

    $table->addColumn($cg);

    $table->fixColumn();

    $c = $table->getColumns();
    $c['ROW_ID']->getOutputType("filter_table")->setVisible(false);
    $c['NOTE']->getOutputType("filter_table")->setVisible(false);
    $c['DISUSE_FLAG']->getOutputType("filter_table")->setVisible(false);
    $c['LAST_UPDATE_TIMESTAMP']->getOutputType("filter_table")->setVisible(false);
    $c['LAST_UPDATE_USER']->getOutputType("filter_table")->setVisible(false);

    global $objTemp00Function;
    $objTemp00Function = function($objTable){
        global $g;

        $intErrorType = null;
        $retStrLastErrMsg = null;
        $key_num = 0;
        $boolBinaryDistinctOnDTiS = false;

        try{

            $query = "";
            $arrayObjColumn = $objTable->getColumns();
            $filterArray = $objTable->getFilterArray(false);
            $hostIdLike = NULL;
            $hostIdRf = NULL;
            $baseTimeStamp = NULL;

            // カラム名取得
            $arraySqlSelectCols = array();

            foreach($arrayObjColumn as $objColumn){
                if($objColumn->isDBColumn()){
                    $arraySqlSelectCols[] = $objColumn->getPartSqlInSelectZone();
                }
            }
            $strColStream = implode(",",$arraySqlSelectCols);

            // HOST_IDの検索条件を取得
            $hostIdFilter = "";
            $filterQuery = "";
            $richSearchQuery = "";
            $hostIdObj = $arrayObjColumn['HOST_ID'];
            $filterQuery = $hostIdObj->getFilterQuery($boolBinaryDistinctOnDTiS);
            $richSearchQuery = $hostIdObj->getRichSearchQuery($boolBinaryDistinctOnDTiS);

            if("" !== $filterQuery){
                $hostIdFilter = "($filterQuery)";
            }
            if("" !== $richSearchQuery){
                if("" === $hostIdFilter){
                    $hostIdFilter = "($richSearchQuery)";
                }
                else{
                    $hostIdFilter .= " OR ($richSearchQuery)";
                }
            }

            // HOST_IDのBIND変数を取得
            $hostIdBindData = array();
            $aryFilterValue = $arrayObjColumn['HOST_ID']->getFilterValuesForDTiS(true,$boolBinaryDistinctOnDTiS);
            foreach($aryFilterValue as $key => $value){
                //----BIND変数は ":VALUE__n" の形式 __nは数字
                $hostIdBindData['HOST_ID__'.$key] = $value;
            }

            $arrayRichValues = $arrayObjColumn['HOST_ID']->getRichFilterValuesForDTiS(true);
            foreach($arrayRichValues as $key => $value){
                //----BIND変数は ":VALUE_RF__n" の形式 __nは数字
                $hostIdBindData['HOST_ID_RF__'.$key] = $value;
            }

            foreach($filterArray as $key => $filter){
                if("BASE_TIMESTAMP__0" === $key || 0 === strncmp($key, "BASE_TIMESTAMP_RF__", strlen("BASE_TIMESTAMP_RF__"))){
                    if(strnatcmp($baseTimeStamp, str_replace("%", "", $filter)) < 0){
                        $baseTimeStamp = str_replace("%", "", $filter);
                    }
                }
            }

            $strSql  = "SELECT {$strColStream} ";
            $strSql .= "FROM {$objTable->getDBMainTableBody()} {$objTable->getShareTableAlias()} ";
            $strSql .= "{$objTable->getLeftJoinTableQuery()} ";

            $strSql .= "WHERE `T1`.`DISUSE_FLAG`='0' AND                                             ";
            $strSql .= "      `T1`.`BASE_TIMESTAMP` = (SELECT MAX(BASE_TIMESTAMP)          ";
            $strSql .= "                                   FROM {$objTable->getDBMainTableBody()} AS TAB_D      ";
            $strSql .= "                                   WHERE DISUSE_FLAG='0'                    ";
            $strSql .= "                                         AND `T1`.`HOST_ID`=TAB_D.HOST_ID    ";
            if(NULL !== $baseTimeStamp){
                $strSql .= "                                     AND TAB_D.BASE_TIMESTAMP <= '" . $baseTimeStamp . "' ";
            }
            $strSql .= "                            )                                               ";

            if("" !== $hostIdFilter){
                $strSql .= " AND ($hostIdFilter)";
            }

            $objQuery = $g["objDBCA"]->sqlPrepare($strSql);

            if( $objQuery->getStatus()===false ){
                // 例外処理へ
                throw new Exception( 'SQL PREPARE' );
            }

            $objQuery->sqlBind($hostIdBindData);

            $retBoolResult = $objQuery->sqlExecute();

            if($retBoolResult!=true){
                // 例外処理へ
                throw new Exception( 'SQL EXECUTE' );
            }

            //結果を多段配列に格納したうえで連想配列に変換
            $tempArray = array();
            while ( $row = $objQuery->resultFetch() ){
                $key_num ++;
                $tempArray[] = $row;
                $objTable->addData( $row, false);
            }
        }
        catch (Exception $e){
            $intErrorType = 500;
            //$strTmpStrBody = $e->getMessage();
            $strErrInitTime = getMircotime(1);
            $strSSEAErrInitKey = '[sSEA-Err-initKey:'.md5($strErrInitTime.bin2hex($strSql)).']';
            
            $tmpAryData = debug_backtrace($limit=1);
            $aryBackTrace = array($tmpAryData[0]['file'],$tmpAryData[0]['line']);
            
            $strTmpStrBody = '([FILE]'.$aryBackTrace[0].',[LINE]'.$aryBackTrace[1].')'.$strSSEAErrInitKey.' '.$e->getMessage();
            $boolRet = false;
            $retStrLastErrMsg = $objQuery->getLastError();
            
            if ( isset($objQuery) )    unset($objQuery);
            $objQuery = null;
            
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-605",array($strTmpStrBody,"UNKNOWN")));
            web_log($strSSEAErrInitKey.$strSql);
            web_log($strSSEAErrInitKey.$retStrLastErrMsg);
        }

        return array( $key_num, $intErrorType, array($retStrLastErrMsg) );   
    };

    // 件数カウント用
    $objTemp01Function = function($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting, $aryOverride){
        global $objTemp00Function;
        $filterArray = $objTable->getFilterArray(false);
        return $objTemp00Function($objTable);
    };

    // 検索用
    $objTemp02Function = function($objTable, $strFormatterId, $mode, $filterData, $aryVariant, $arySetting, $aryOverride){
        global $objTemp00Function;
        return $objTemp00Function($objTable);
    };

    // EXCEL・CSV出力用
    $objTemp03Function = function($arrayFileterBody, $objTable, $intJsonLimit, $strFormatterId, $filterData, $aryVariant, $arySetting){
        global $objTemp00Function;
        return $objTemp00Function($objTable);
    };

    $table->setGeneObject("functionsForOverride", array("recCountMain"      =>array("print_table"   =>array(
                                                                                                            "recCount"              =>$objTemp01Function,
                                                                                                           ),
                                                                                   ),
                                                        "printTableMain"    =>array("print_table"   =>array(
                                                                                                            "selectResultFetch"     =>$objTemp02Function,
                                                                                                           ),
                                                                                   ),
                                                        "dumpDataFromTable" =>array("excel"         =>array(
                                                                                                            "selectResultFetch"     =>$objTemp03Function,
                                                                                                           ),
                                                                                    "csv"           =>array(
                                                                                                            "selectResultFetch"     =>$objTemp03Function,
                                                                                                           ),
                                                                                   ),
                                                       )
                         );

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);

?>