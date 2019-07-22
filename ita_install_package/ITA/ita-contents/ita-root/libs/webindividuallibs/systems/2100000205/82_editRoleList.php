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

    $intControlDebugLevel01 = 50;
    $varTrzStart = null;

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);

    // DBアクセスを伴う処理開始
    try{
        $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($p_menu_id) === false ){
            // エラーフラグをON
            throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        // DBコネクト
        $num_rows = 0;
        
        // メニュー一覧(A_MENU_LIST)が存在しているかチェック
        $sql = "SELECT DISUSE_FLAG
                FROM   A_MENU_LIST
                WHERE  MENU_ID = :MENU_ID_BV 
                AND    DISUSE_FLAG IN ('0','1')";
        
        $tmpAryBind = array('MENU_ID_BV'=>$p_menu_id);
        $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
        if( $retArray[0] === true ){
            $intTmpRowCount=0;
            $showTgtRow = array();
            $objQuery =& $retArray[1];
            while($row = $objQuery->resultFetch() ){
                if($row !== false){
                    $intTmpRowCount+=1;
                }
                if($intTmpRowCount==1){
                    $showTgtRow = $row;
                }
            }
            $selectRowLength = $intTmpRowCount;
            if( $selectRowLength != 1 ){
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($objQuery);
        }
        else{
            throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        $p_account_list_disuse_flag    = $showTgtRow['DISUSE_FLAG'];
        
        switch($mode){
            case 1 :
            case 2 :
                if( $g['privilege'] != '1' ){
                    //----メンテ権限がない
                    throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                break;
        }
        
        switch($mode){
            // ----モードによって処理分岐
            case 0 :
                // ----参照表示(mode=0)

                // メンテナンスボタンの表示/非表示を切り替え
                if($p_account_list_disuse_flag === '0' ){
                    $BG_COLOR = "";
                    $LNK_ABLE = "";
                }
                else{
                    $BG_COLOR = " class=\"disuse\" ";
                    $LNK_ABLE = "disabled";
                }

                // 所属しているロールのリストを生成
                $row_counter = 0;
                $sql = "SELECT TAB_1.ROLE_ID,
                                TAB_3.NAME AS PRIVILEGE_DISP,
                                TAB_1.PRIVILEGE,
                                TAB_2.ROLE_NAME
                        FROM   A_ROLE_MENU_LINK_LIST TAB_1
                                LEFT JOIN A_ROLE_LIST TAB_2 ON (TAB_1.ROLE_ID = TAB_2.ROLE_ID)
                                LEFT JOIN A_PRIVILEGE_LIST TAB_3 ON (TAB_1.PRIVILEGE = TAB_3.FLAG)
                        WHERE  TAB_1.DISUSE_FLAG = '0'
                        AND    TAB_2.DISUSE_FLAG = '0'
                        AND    TAB_1.MENU_ID = :MENU_ID_BV
                        ORDER BY TAB_1.ROLE_ID ";

                $tmpAryBind = array('MENU_ID_BV'=>$p_menu_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];

                    $output_str .= 
<<< EOD
                        <div class="fakeContainer_Yobi1">
                        <table id="DbTable_Yobi1">
                            <tr class="defaultExplainRow">
                                <th scope="col"  onClick="tableSort(1,this,'DbTable_Yobi1_data',0, nsort);"  class="sort" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060101")}</span></th>
                                <th scope="col"  onClick="tableSort(1,this,'DbTable_Yobi1_data',1       );"  class="sort" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040052")}</span></th>
                                <th scope="col"  onClick="tableSort(1,this,'DbTable_Yobi1_data',2       );"  class="sort" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060201")}</span></th>
                            </tr>
EOD;

                    $row_counter = 0;
                    while ( $row =  $objQuery->resultFetch() ){
                        $row_counter += 1;

                        $COLUMN_00 = nl2br(htmlspecialchars($row['ROLE_ID']));
                        if($row['PRIVILEGE'] == 1 || $row['PRIVILEGE'] == 2){
                            $COLUMN_01 = nl2br(htmlspecialchars($row['PRIVILEGE_DISP']));
                        }
                        else if($row['PRIVILEGE'] == 0 ){
                            $COLUMN_01 = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040053");
                        }
                        else{
                            $COLUMN_01 = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040054");
                        }
                        $COLUMN_02 = nl2br(htmlspecialchars($row['ROLE_NAME']));
                        $output_str .= 
<<< EOD
                            <tr valign="top">
                                <td class="likeHeader number" scope="row" >{$COLUMN_00}</td>
                                <td{$BG_COLOR}>{$COLUMN_01}</td>
                                <td{$BG_COLOR}>{$COLUMN_02}</td>
                            </tr>
EOD;
                    }
                    unset($objQuery);

                    $output_str .= 
<<< EOD
                        </table>
                        </div>
EOD;
                    // レコード数を取得

                    // 0件の場合はTABLEではなくメッセージのみを返却するようハンドリング
                    if($row_counter == 0){
                        //----"当該メニューがいずれのロールにも紐付いていません。<br>";
                        $output_str = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040055");
                    }
                    if( $g['privilege'] == '1' ){
                        $output_str .= 
<<< EOD
                        <input type="button" class="tableOuterElement linkbutton" value="{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040056")}" onClick=location.href="javascript:edit_role_list(1,$p_menu_id);" $LNK_ABLE   >
EOD;
                    }
                }
                else{
                    throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                break;
                // 参照表示(mode=0)----
            case 1 :
                // ----編集画面に遷移(mode=1)

                // ロールメニュー紐付リストの中で一番LAST_UPDATE_TIMESTAMPが新しいものをメモする

                $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"MAX(LAST_UPDATE_TIMESTAMP)","DATETIME",true,true);
                
                $strSelectMaxLastUpdateTimestamp = "CASE WHEN MAX(LAST_UPDATE_TIMESTAMP) IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END";

                $sql = "SELECT {$strSelectMaxLastUpdateTimestamp} AS MAX_LAST_UPDATE_TIMESTAMP
                        FROM   A_ROLE_MENU_LINK_LIST
                        WHERE  MENU_ID = :MENU_ID_BV ";

                
                $tmpAryBind = array('MENU_ID_BV'=>$p_menu_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    
                    $intTmpRowCount=0;
                    $getTgtRow = array();
                    $objQuery = $retArray[1];
                    while($row = $objQuery->resultFetch() ){
                        if($row !== false){
                            $intTmpRowCount+=1;
                        }
                        if($intTmpRowCount==1){
                            $getTgtRow = $row;
                        }
                    }
                    $selectRowLength = $intTmpRowCount;
                    if( $selectRowLength != 1 ){
                        throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    unset($objQuery);
                }
                else{
                    throw new Exception( '00000700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                $strMaxLastUpdateTimestamp = $getTgtRow['MAX_LAST_UPDATE_TIMESTAMP'];

                // ロール一覧(A_ROLE_LIST)をSELECT

                $sql = "SELECT TAB_1.ROLE_ID,
                                TAB_1.PRIVILEGE,
                                TAB_2.ROLE_NAME
                        FROM   A_ROLE_MENU_LINK_LIST TAB_1
                                LEFT JOIN A_ROLE_LIST TAB_2 ON (TAB_1.ROLE_ID = TAB_2.ROLE_ID)
                        WHERE  TAB_1.DISUSE_FLAG = '0'
                        AND    TAB_2.DISUSE_FLAG = '0'
                        AND    TAB_1.MENU_ID = :MENU_ID_BV
                        UNION
                        SELECT TAB_1.ROLE_ID,
                                0 AS PRIVILEGE,
                                TAB_1.ROLE_NAME
                        FROM   A_ROLE_LIST TAB_1
                        WHERE  TAB_1.ROLE_ID NOT IN ( SELECT ROLE_ID
                                                        FROM   A_ROLE_MENU_LINK_LIST
                                                        WHERE  MENU_ID = :MENU_ID_BV
                                                        AND    DISUSE_FLAG = '0' )
                        AND    TAB_1.DISUSE_FLAG = '0' ";

                

                $tmpAryBind = array('MENU_ID_BV'=>$p_menu_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    
                    $output_str .= 
<<< EOD
                        <div id="max_last_update_timestamp"  style="display:none;" >{$strMaxLastUpdateTimestamp}</div>
                        <div class="fakeContainer_Yobi1">
                        <table id="DbTable_Yobi1">
                            <tr class="defaultExplainRow">
                                <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040057")}</span></th>
                                <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060101")}</span></th>
                                <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060201")}</span></th>
                            </tr>
EOD;
                    $row_counter = 0;
                    while ($row = $objQuery->resultFetch() ){
                        $row_counter += 1;

                        // 念のため改行コード(LF)を<br>に変換する
                        $COLUMN_01 = nl2br(htmlspecialchars($row['ROLE_ID']));
                        $COLUMN_02 = nl2br(htmlspecialchars($row['ROLE_NAME']));

                        // 所属済みの場合はチェック状態にする
                        $checked_flag=$row['PRIVILEGE'];
                        $selected0 = ($checked_flag == 0 ? "selected":"");
                        $selected1 = ($checked_flag == 1 ? "selected":"");
                        $selected2 = ($checked_flag == 2 ? "selected":"");
                        $str_temp = 
<<< EOD
<tr valign="top">
<td class="likeHeader" scope="row" ><span id="role_id_{$row_counter}" style="display:none">{$row['ROLE_ID']}</span><SELECT id="priv_{$row_counter}" >
    <OPTION value="0" {$selected0}></OPTION>
    <OPTION value="1" {$selected1}>{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040058")}</OPTION>
    <OPTION value="2" {$selected2}>{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040059")}</OPTION>
</SELECT>
</td>
<td class="likeHeader number" scope="row" >$COLUMN_01</td>
<td>$COLUMN_02</td>
</tr>
EOD;
                            $output_str .= $str_temp;
                    }
                    unset($objQuery);

                    $str_temp = 
<<< EOD
                        </table>
                        </div>
EOD;
                    $output_str .= $str_temp;

                    // 実行ボタンと戻るボタンを出力
                    $str_temp = 
<<< EOD
                        <input type="button" class="tableOuterElement updatebutton" value="{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040060")}" onClick=location.href="javascript:edit_role_list(0,$p_menu_id);" >
                        <input type="button" class="tableOuterElement updatebutton" value="{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040061")}" onClick=location.href="javascript:edit_role_list(2,$p_menu_id);" id="now_on_maintenance" >
EOD;
                    $output_str .= $str_temp;
                }
                else{
                    throw new Exception( '00000800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                break;
                // 編集画面に遷移(mode=1)----
            case 2 :
                // ----編集画面で実行を押下(mode=2)

                if(is_array($p_role_array_id)===true){
                    $objIntNumValiROLE = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
                    $tmpArrayForUniqueCheck = array();
                    foreach($p_role_array_id as $tmpKey=>$tmpVal){
                        if( $objIntNumValiROLE->isValid($tmpVal) === false ){
                            // エラーフラグをON
                            throw new Exception( '00000900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        else{
                            if( array_key_exists($tmpVal, $tmpArrayForUniqueCheck) === false ){
                                //----重複判定のためにキーに登録
                                $tmpArrayForUniqueCheck[$tmpVal] = 1;
                                //重複判定のためにキーに登録----
                            }
                            else{
                                //----重複キーが送信された場合
                                // エラーフラグをON
                                throw new Exception( '00001000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                //重複キーが送信された場合----
                            }
                        }
                    }
                    unset($objIntNumValiROLE);
                    unset($tmpArrayForUniqueCheck);
                }
                
                if(is_array($p_role_array_priv)===true){
                    $objIntNumValiPRV = new IntNumValidator(0,2,"","",array("NOT_NULL"=>true));
                    foreach($p_role_array_priv as $tmpKey=>$tmpVal){
                        if( $objIntNumValiPRV->isValid($tmpVal) === false ){
                            // エラーフラグをON
                            throw new Exception( '00001100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    unset($objIntNumValiPRV);
                }

                $objTextValiLUD4U = new TextValidator();
                if( $objTextValiLUD4U->isValid($p_max_last_update_timestamp) === false ){
                    // エラーフラグをON
                    throw new Exception( '00001200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objTextValiLUD4U);
                    
                $p_role_array_merge = array_combine( $p_role_array_id, $p_role_array_priv );
                if( $p_role_array_merge === false ){
                    // エラーフラグをON
                    throw new Exception( '00001300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                $boolExeContinue = true;

                //----トランザクション開始
                $varTrzStart = $g['objDBCA']->transactionStart();
                if( $varTrzStart === false ){
                    // エラーフラグをON
                    throw new Exception( '00001400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }                   

                // メニューリストが廃止されている場合はメンテナンスさせない
                $sql = "SELECT DISUSE_FLAG
                        FROM   A_MENU_LIST
                        WHERE  MENU_ID = :MENU_ID_BV
                        FOR UPDATE ";

                $tmpAryBind = array('MENU_ID_BV'=>$p_menu_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $intTmpRowCount=0;
                    $showTgtRow = array();
                    $objQuery =& $retArray[1];
                    while($row = $objQuery->resultFetch() ){
                        if($row !== false){
                            $intTmpRowCount+=1;
                        }
                        if($intTmpRowCount==1){
                            $row0 = $row;
                        }
                    }
                    $selectRowLength = $intTmpRowCount;
                    if( $selectRowLength != 1 ){
                        throw new Exception( '00001500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    unset($objQuery);
                }
                else{
                    throw new Exception( '00001600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                if( $row0['DISUSE_FLAG'] != '0' ){
                    $output_str = "<span class=\"generalErrMsg\">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040062")}<br></span>";
                    $boolExeContinue = false;
                }

                // ----シーケンスを掴む
                $retArray = getSequenceLockInTrz('JSEQ_A_ROLE_MENU_LINK_LIST','A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    throw new Exception( '00001700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                else{
                    $retArray = getSequenceLockInTrz('SEQ_A_ROLE_MENU_LINK_LIST','A_SEQUENCE');
                    if( $retArray[1] != 0 ){
                        throw new Exception( '00001800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                // シーケンスを掴む----
                            
                if( $boolExeContinue === true  ){
                    // ----該当のメニューIDのリストを取得する
                    $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
                    $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
                    
                    // ----全行および全行中、最後に更新された日時を取得する
                    $arrayConfigForSelect = array(
                        "JOURNAL_SEQ_NO"=>"",
                        "JOURNAL_ACTION_CLASS"=>"",
                        "JOURNAL_REG_DATETIME"=>"",
                        "LINK_ID"=>"",
                        "MENU_ID"=>"",
                        "ROLE_ID"=>"",
                        "PRIVILEGE"=>"",
                        "NOTE"=>"",
                        "DISUSE_FLAG"=>"",
                        "LAST_UPDATE_TIMESTAMP"=>"",
                        "LAST_UPDATE_USER"=>"",
                        $strSelectMaxLastUpdateTimestamp=>""
                    );
                    
                    $arrayValueTmpl = array(
                        "JOURNAL_SEQ_NO"=>"",
                        "JOURNAL_ACTION_CLASS"=>"",
                        "JOURNAL_REG_DATETIME"=>"",
                        "LINK_ID"=>"",
                        "MENU_ID"=>"",
                        "ROLE_ID"=>"",
                        "PRIVILEGE"=>"",
                        "NOTE"=>"",
                        "DISUSE_FLAG"=>"",
                        "LAST_UPDATE_TIMESTAMP"=>"",
                        "LAST_UPDATE_USER"=>"",
                        $strSelectMaxLastUpdateTimestamp=>""
                    );
                    $arrayValue = $arrayValueTmpl;
                            
                    $temp_array = array('WHERE'=>"DISUSE_FLAG IN('0','1') AND MENU_ID = :MENU_ID ");
                    
                    $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                                    "SELECT FOR UPDATE",
                                                    "LINK_ID",
                                                    "A_ROLE_MENU_LINK_LIST",
                                                    "A_ROLE_MENU_LINK_LIST_JNL",
                                                    $arrayConfigForSelect,
                                                    $arrayValue,
                                                    $temp_array );
                    $aryResult01 = array();
                    $intSelectMaxLUTimestamp = "";
                    if( $retArray[0] === false ){
                        throw new Exception( '00001900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];
                    $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
                    if( $objQueryUtn->getStatus()===false ){
                        throw new Exception( '00002000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $arrayUtnBind['MENU_ID'] = $p_menu_id;
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                        throw new Exception( '00002100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $boolResult = $objQueryUtn->sqlExecute();
                    if( $boolResult === false ){
                        throw new Exception( '00002200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //----発見行だけループ
                    while ( $row = $objQueryUtn->resultFetch() ){
                        if($row!==false){
                            $strRole = $row['ROLE_ID'];
                            if( strlen($strRole) == 0 ){
                                throw new Exception( '00002300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            else{
                                $aryResult01[$strRole] = $row;
                                if( "VALNULL" != ($row["LUT4U"]) ){
                                    //----マイクロ秒付のUNIXタイムスタンプへ
                                    $intFocusLUT = convFromStrDateToUnixtime($row["LUT4U"],true);
                                    //マイクロ秒付のUNIXタイムスタンプへ----
                                    
                                    if(bccomp($intFocusLUT, $intSelectMaxLUTimestamp,6) == 1){
                                        $intSelectMaxLUTimestamp = $intFocusLUT;
                                    }
                                }
                            }
                        }
                    }
                    //発見行だけループ----
                    unset($objQueryUtn);
                    
                    if( $intSelectMaxLUTimestamp === "" ){
                        $strSelectMaxLUTimestamp = "VALNULL";
                    }
                    else{
                        $strSelectMaxLUTimestamp = convFromUnixtimeToStrDate($intSelectMaxLUTimestamp,true,1);
                    }
                    unset($intSelectMaxLUTimestamp);

                    if( $strSelectMaxLUTimestamp != $p_max_last_update_timestamp ){
                        $output_str = "<span class=\"generalErrMsg\">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040063")}</span>";
                        $boolExeContinue = false;
                    }
                    // 全行および全行中、最後に更新された日時を取得する----
                    // 該当のメニューIDのリストを取得する----
                }
                
                if( $boolExeContinue === true ){
                    // 2つの配列を結合
                    $p_role_array_merge = array_combine( $p_role_array_id, $p_role_array_priv );
                    
                    $arrayConfigForIUD = array(
                        "JOURNAL_SEQ_NO"=>"",
                        "JOURNAL_ACTION_CLASS"=>"",
                        "JOURNAL_REG_DATETIME"=>"",
                        "LINK_ID"=>"",
                        "MENU_ID"=>"",
                        "ROLE_ID"=>"",
                        "PRIVILEGE"=>"",
                        "NOTE"=>"",
                        "DISUSE_FLAG"=>"",
                        "LAST_UPDATE_TIMESTAMP"=>"",
                        "LAST_UPDATE_USER"=>""
                    ); 
                    // 権限ごとに配列を作成
                    foreach($p_role_array_merge as $intRole =>$intPriv){
                        //----ループ
                        $tmpRowExists = false;
                        $strDisuseFlag = '1';
                        $tgtSource_row = array();
                        if( $intPriv == 1 ){
                            //----メンテ可が指定された
                            $strDisuseFlag = '0';
                            if(array_key_exists($intRole, $aryResult01) === true){
                                $tmpRowExists = true;
                                $cln_update_row = $aryResult01[$intRole];
                            }
                            //メンテ可が指定された----
                        }
                        else if( $intPriv == 2){
                            //----参照のみが指定された
                            $strDisuseFlag = '0';
                            if(array_key_exists($intRole, $aryResult01) === true){
                                $tmpRowExists = true;
                                $cln_update_row = $aryResult01[$intRole];
                            }
                            //参照のみが指定された----
                        }
                        else{
                            //----その他が指定された
                            $intPriv = '';
                            $strDisuseFlag = '1';
                            if(array_key_exists($intRole, $aryResult01) === true){
                                $tmpRowExists = true;
                                $cln_update_row = $aryResult01[$intRole];
                            }
                            //その他が指定された----
                        }
                        
                                
                        if( $tmpRowExists === true ){
                            //----更新
                            if( $cln_update_row['PRIVILEGE'] == $intPriv && $cln_update_row['DISUSE_FLAG'] == $strDisuseFlag ){
                                //----値の変更がなく、レコード変更の必要がないので、スキップ
                                continue;
                                //値の変更がなく、レコード変更の必要がないので、スキップ----
                            }
                            $cln_update_row['PRIVILEGE']      = $intPriv;
                            $cln_update_row['DISUSE_FLAG']    = $strDisuseFlag;
                            $cln_update_row['LAST_UPDATE_USER'] = $g['login_id'];
                            $tgtSource_row = $cln_update_row;
                            $sqlType = "UPDATE";
                            //更新----
                        }
                        else{
                            //----登録
                            if( $intPriv == '' && $strDisuseFlag == '1' ){
                                //----行がなく登録すべきだが、廃止フラグが立っているのでスキップ
                                continue;
                                //行がなく登録すべきだが、廃止フラグが立っているのでスキップ----
                            }
                            $retArray = getSequenceValueFromTable('SEQ_A_ROLE_MENU_LINK_LIST', 'A_SEQUENCE', FALSE );
                            if( $retArray[1] != 0 ){
                                throw new Exception( '00002400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            else{
                                $varRISeq = $retArray[0];
                            }
                            $sqlType = "INSERT";
                            $new_insert_row = $arrayValueTmpl;
                            $new_insert_row['LINK_ID']        = $varRISeq;
                            $new_insert_row['MENU_ID']        = $p_menu_id;
                            $new_insert_row['ROLE_ID']        = $intRole;
                            $new_insert_row['PRIVILEGE']      = $intPriv;
                            $new_insert_row['DISUSE_FLAG']    = $strDisuseFlag;
                            $new_insert_row['LAST_UPDATE_USER'] = $g['login_id'];
                            $tgtSource_row = $new_insert_row;
                            //登録----
                        }
                        
                        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                $sqlType,
                                "LINK_ID",
                                "A_ROLE_MENU_LINK_LIST",
                                "A_ROLE_MENU_LINK_LIST_JNL",
                                $arrayConfigForIUD,
                                $tgtSource_row );
                        
                        if( $retArray[0] === false ){
                            throw new Exception( '00002500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        $sqlUtnBody = $retArray[1];
                        $arrayUtnBind = $retArray[2];
                        
                        $sqlJnlBody = $retArray[3];
                        $arrayJnlBind = $retArray[4];
                        
                        // 履歴シーケンス払い出し
                        $retArray = getSequenceValueFromTable('JSEQ_A_ROLE_MENU_LINK_LIST', 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            throw new Exception( '00002600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        else{
                            $varJSeq = $retArray[0];
                            $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                        }
                        
                        $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
                        $objQueryJnl = $g['objDBCA']->sqlPrepare($sqlJnlBody);
                        
                        if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                            throw new Exception( '00002700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                            throw new Exception( '00002800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                                
                        //----SQL実行
                        $rUtn = $objQueryUtn->sqlExecute();
                        if($rUtn!=true){
                            throw new Exception( '00002900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }

                        $rJnl = $objQueryJnl->sqlExecute();
                        if($rJnl!=true){
                            throw new Exception( '00003000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        //SQL実行----
                        unset($objQueryUtn);
                        unset($objQueryJnl);
                        //ループ----
                    }
                    $boolResult = $g['objDBCA']->transactionCommit();
                    if ( $boolResult === false ){
                        throw new Exception( '00003100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $g['objDBCA']->transactionExit();
                    // トランザクション終了----

                    if( $boolExeContinue === true  ){
                        // ----正常の場合はメニューID(数字)を返却する(受け取り側でmatchメソッドを使うので、文字列型へキャスト)
                        // ----暗黙の型変換されないように注意すること[2015-03-03-add]
                        $output_str = (string)$p_menu_id;
                        // 正常の場合はメニューID(数字)を返却する(受け取り側でmatchメソッドを使うので、文字列型へキャスト)----
                    }
                }
                break;
                // 編集画面で実行を押下(mode=2)----
            default :
                // ----異常発生画面(未定義mode)
                throw new Exception( '00003200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 異常発生画面(未定義mode)----
                // モードによって処理分岐----
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        $error_flag = 1;
        
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        // DBアクセス事後処理
        if( isset($objQuery) )    unset($objQuery);
        if( isset($objQueryUtn) ) unset($objQueryUtn) ;
        if( isset($objQueryJnl) ) unset($objQueryJnl) ;
        
        if( $varTrzStart === true ){
            $varRollBack = $g['objDBCA']->transactionRollBack();
            if( $varRollBack === false ){
                //----1回目のロールバックが失敗してしまった場合
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4021",$strFxName));
                //1回目のロールバックが失敗してしまった場合----
            }
            $varTrzExit = $g['objDBCA']->transactionExit();
            if( $varTrzExit === false ){
                //----トランザクションが終了できないので以降は緊急停止
                web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4022",$strFxName));
                exit();
                //トランザクションが終了できないので以降は緊急停止----
            }
        }
        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
    }

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
