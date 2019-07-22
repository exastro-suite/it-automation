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
        if( $objIntNumVali->isValid($p_user_id) === false ){
            throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        // DBコネクト
        $num_rows = 0;
        
        // ユーザ一覧(A_ACCOUNT_LIST)が存在しているかチェック
        $sql = "SELECT DISUSE_FLAG
                FROM   A_ACCOUNT_LIST
                WHERE  USER_ID = :USER_ID_BV 
                AND    DISUSE_FLAG IN ('0','1')";

        $tmpAryBind = array('USER_ID_BV'=>$p_user_id);
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
                    throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            break;
        }
        
        switch($mode){
            // ----モードによって処理分岐
            case 0 :
                // ----参照表示(mode=0)

                // メンテナンスボタンの表示/非表示を切り替え
                if( $p_account_list_disuse_flag === '0' ){
                    $BG_COLOR = "";
                    $LNK_ABLE = "";
                }
                else{
                    $BG_COLOR = " class=\"disuse\" ";
                    $LNK_ABLE = "disabled";
                }
                
                // 所属しているロールのリストを生成

                $sql = "SELECT TAB_1.ROLE_ID,
                                TAB_2.ROLE_NAME
                        FROM   A_ROLE_ACCOUNT_LINK_LIST TAB_1
                                LEFT JOIN A_ROLE_LIST TAB_2 ON (TAB_1.ROLE_ID = TAB_2.ROLE_ID )
                        WHERE  TAB_1.DISUSE_FLAG = '0'
                        AND    TAB_2.DISUSE_FLAG = '0'
                        AND    TAB_1.USER_ID = :USER_ID_BV
                        ORDER BY TAB_1.ROLE_ID ";

                $tmpAryBind = array('USER_ID_BV'=>$p_user_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $output_str .= 
<<< EOD
                        <div class="fakeContainer_Yobi1">
                        <table id="DbTable_Yobi1">
                            <tr class="defaultExplainRow">
                                <th scope="col"  onClick="tableSort(1,this,'DbTable_Yobi1_data',0, nsort);"  class="sort" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060101")}</span></th>
                                <th scope="col"  onClick="tableSort(1,this,'DbTable_Yobi1_data',1       );"  class="sort" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060201")}</span></th>
                            </tr>
EOD;
                    $row_counter = 0;
                    while($row = $objQuery->resultFetch() ){
                        $row_counter += 1;
                        $COLUMN_00 = nl2br(htmlspecialchars($row['ROLE_ID']));
                        $COLUMN_01 = nl2br(htmlspecialchars($row['ROLE_NAME']));
                        $output_str .=
<<< EOD
                            <tr valign="top">
                                <td class="likeHeader number" scope="row" >{$COLUMN_00}</td>
                                <td{$BG_COLOR}>{$COLUMN_01}</td>
                            </tr>
EOD;
                    }
                    unset($objQuery);

                    $output_str .= 
<<< EOD
                        </table>
                        </div>
EOD;
                    // ----0件の場合はTABLEではなくメッセージのみを返却するようハンドリング
                    if( $row_counter == 0 ){
                        //----当該ユーザがいずれのロールにも紐付いていません。<br>
                        $output_str = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070052");
                    }
                    // 0件の場合はTABLEではなくメッセージのみを返却するようハンドリング----

                    if( $g['privilege'] == '1' ){
                        $output_str .=
<<< EOD
                        <input type="button" class="tableOuterElement linkbutton" value="{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070053")}" onClick=location.href="javascript:edit_role_list(1,$p_user_id);" $LNK_ABLE   >
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

                // ロールアカウント紐付リストの中で一番LAST_UPDATE_TIMESTAMPが新しいものをメモする

                $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"MAX(LAST_UPDATE_TIMESTAMP)","DATETIME",true,true);
                
                $strSelectMaxLastUpdateTimestamp = "CASE WHEN MAX(LAST_UPDATE_TIMESTAMP) IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END";
                
                $sql = "SELECT {$strSelectMaxLastUpdateTimestamp} AS MAX_LAST_UPDATE_TIMESTAMP
                        FROM   A_ROLE_ACCOUNT_LINK_LIST
                        WHERE  USER_ID = :USER_ID_BV ";
                        
                $tmpAryBind = array('USER_ID_BV'=>$p_user_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    
                    $intTmpRowCount=0;
                    $getTgtRow = array();
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
                
                $sql = "SELECT TAB_1.ROLE_ID,
                                TAB_2.ROLE_NAME,
                                1 AS FLAG
                        FROM   A_ROLE_ACCOUNT_LINK_LIST TAB_1
                                LEFT JOIN A_ROLE_LIST TAB_2 ON (TAB_1.ROLE_ID = TAB_2.ROLE_ID)
                        WHERE  TAB_1.DISUSE_FLAG = '0'
                        AND    TAB_2.DISUSE_FLAG = '0'
                        AND    TAB_1.USER_ID = :USER_ID_BV
                        UNION
                        SELECT TAB_1.ROLE_ID,
                                TAB_1.ROLE_NAME,
                                NULL AS FLAG
                        FROM   A_ROLE_LIST TAB_1
                        WHERE  TAB_1.ROLE_ID NOT IN ( SELECT ROLE_ID
                                                        FROM   A_ROLE_ACCOUNT_LINK_LIST
                                                        WHERE  USER_ID = :USER_ID_BV
                                                        AND    DISUSE_FLAG = '0' )
                        AND    TAB_1.DISUSE_FLAG = '0' ";

                $tmpAryBind = array('USER_ID_BV'=>$p_user_id);
                $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    
                    $output_str .= 
<<< EOD
                        <div id="max_last_update_timestamp"  style="display:none;" >{$strMaxLastUpdateTimestamp}</div>
                        <div class="fakeContainer_Yobi1">
                        <table id="DbTable_Yobi1">
                            <tr class="defaultExplainRow">
                                <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070054")}</span></th>
                                <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060101")}</span></th>
                                <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1060201")}</span></th>
                            </tr>
EOD;
                    $row_counter1 = 0;
                    while ( $row1 = $objQuery->resultFetch() ){
                        // 念のため改行コード(LF)を<br>に変換する
                        
                        $COLUMN_01 = nl2br(htmlspecialchars($row1['ROLE_ID']));
                        $COLUMN_02 = nl2br(htmlspecialchars($row1['ROLE_NAME']));
                        
                        // レコード通番を採番する
                        $row_counter1 += 1;
                        //$row_counter = oci_num_rows($stid_2);
                        
                        // 所属済みの場合はチェック状態にする
                        $checked_flag="";
                        if($row1['FLAG']){
                            $checked_flag="checked";
                        }
                        $str_temp = 
<<< EOD
                            <tr valign="top">
                                <td class="likeHeader" scope="row"><div align="center"><input type="checkbox" id="role_id_{$row_counter1}" value="{$COLUMN_01}" $checked_flag></div></td>
                                <td class="likeHeader number" scope="row">$COLUMN_01</td>
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
                        <input type="button" class="tableOuterElement updatebutton" value="{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070055")}" onClick=location.href="javascript:edit_role_list(0,$p_user_id);" >
                        <input type="button" class="tableOuterElement updatebutton" value="{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070056")}" onClick=location.href="javascript:edit_role_list(2,$p_user_id);" id="now_on_maintenance" >
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

                if(is_array($p_role_array)===true){
                    $objIntNumValiROLE = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
                    $tmpArrayForUniqueCheck = array();
                    foreach($p_role_array as $tmpKey=>$tmpVal){
                        if( $objIntNumValiROLE->isValid($tmpVal) === false ){
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

                $objTextValiLUD4U = new TextValidator();
                if( $objTextValiLUD4U->isValid($p_max_last_update_timestamp) === false ){
                    // エラーフラグをON
                    throw new Exception( '00001100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objTextValiLUD4U);

                $boolExeContinue = true;

                //----トランザクション開始
                $varTrzStart = $g['objDBCA']->transactionStart();

                if( $varTrzStart === false ){
                    throw new Exception( '00001200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                // アカウントリストが廃止されている場合はメンテナンスさせない
                $sql = "SELECT DISUSE_FLAG
                        FROM   A_ACCOUNT_LIST
                        WHERE  USER_ID = :USER_ID
                        AND DISUSE_FLAG IN ('0','1')
                        FOR UPDATE ";

                $tmpAryBind = array('USER_ID'=>$p_user_id);
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
                        throw new Exception( '00001300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    unset($objQuery);
                }
                else{
                    throw new Exception( '00001400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                if( $row0['DISUSE_FLAG'] != '0' ){
                    $output_str = "<span class=\"generalErrMsg\">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070057")}</span>";
                    $boolExeContinue = false;
                }
                
                // ----シーケンスを掴む
                if( $boolExeContinue === true  ){
                    $retArray = getSequenceLockInTrz('JSEQ_A_ROLE_ACCOUNT_LINK_LIST','A_SEQUENCE');
                    if( $retArray[1] != 0 ){
                        throw new Exception( '00001500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    else{
                        $retArray = getSequenceLockInTrz('SEQ_A_ROLE_ACCOUNT_LINK_LIST','A_SEQUENCE');
                        if( $retArray[1] != 0 ){
                            throw new Exception( '00001600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                }
                // シーケンスを掴む----
                
                if( $boolExeContinue === true  ){
                    // ----該当のユーザIDのリストを取得する
                    $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"LAST_UPDATE_TIMESTAMP","DATETIME",true,true);
                    $strSelectMaxLastUpdateTimestamp = "CASE WHEN LAST_UPDATE_TIMESTAMP IS NULL THEN 'VALNULL' ELSE {$tmpStrSelectPart} END LUT4U";
                    
                    // ----全行および全行中、最後に更新された日時を取得する
                    $arrayConfigForSelect = array(
                        "JOURNAL_SEQ_NO"=>"",
                        "JOURNAL_ACTION_CLASS"=>"",
                        "JOURNAL_REG_DATETIME"=>"",
                        "LINK_ID"=>"",
                        "USER_ID"=>"",
                        "ROLE_ID"=>"",
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
                        "USER_ID"=>"",
                        "ROLE_ID"=>"",
                        "NOTE"=>"",
                        "DISUSE_FLAG"=>"",
                        "LAST_UPDATE_TIMESTAMP"=>"",
                        "LAST_UPDATE_USER"=>"",
                        $strSelectMaxLastUpdateTimestamp=>""
                    );
                    $arrayValue = $arrayValueTmpl;
                                
                    $temp_array = array('WHERE'=>"DISUSE_FLAG IN ('0','1') AND USER_ID = :USER_ID ");
                    
                    $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                                                    "SELECT FOR UPDATE",
                                                    "LINK_ID",
                                                    "A_ROLE_ACCOUNT_LINK_LIST",
                                                    "A_ROLE_ACCOUNT_LINK_LIST_JNL",
                                                    $arrayConfigForSelect,
                                                    $arrayValue,
                                                    $temp_array );
                    $aryResult01 = array();
                    $intSelectMaxLUTimestamp = "";
                    if( $retArray[0] === false ){
                        throw new Exception( '00001700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];
                    $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
                    
                    if( $objQueryUtn->getStatus()===false ){
                        throw new Exception( '00001800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $arrayUtnBind['USER_ID'] = $p_user_id;
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                        throw new Exception( '00001900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    $boolResult = $objQueryUtn->sqlExecute();
                    if( $boolResult === false ){
                        throw new Exception( '00002000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    
                    //----発見行だけループ
                    while ( $row = $objQueryUtn->resultFetch() ){
                        if($row!==false){
                            $strRoleId = $row['ROLE_ID'];
                            if( strlen($strRoleId) == 0 ){
                                throw new Exception( '00002100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            else{
                                $aryResult01[$strRoleId] = $row;
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
                    
                    if($intSelectMaxLUTimestamp === ""){
                        $strSelectMaxLUTimestamp = "VALNULL";
                    }
                    else{
                        $strSelectMaxLUTimestamp = convFromUnixtimeToStrDate($intSelectMaxLUTimestamp,true,1);
                    }
                    unset($intSelectMaxLUTimestamp);
                    
                    if( $strSelectMaxLUTimestamp != $p_max_last_update_timestamp ){
                        $output_str = "<span class=\"generalErrMsg\">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070058")}</span>";
                        $boolExeContinue = false;
                    }
                    // 全行および全行中、最後に更新された日時を取得する----
                    // 該当のメニューIDのリストを取得する----
                } 
                   
                if( $boolExeContinue === true  ){
                    // ----チェックが入っているロールと「$p_role_array」と、テーブルのありもの、を統合する
                    $tmpArrayOrder = array();
                    foreach($p_role_array as $tmpKeySeq=>$tmpValRole){
                        $tmpArrayOrder[$tmpValRole] = '0';
                    }
                    $tmpChkArray = array();
                    foreach($aryResult01 as $strKeyRole=>$aryValue){
                        if(array_key_exists($strKeyRole,$tmpArrayOrder)===false){
                            //----フォームから送信されたが、テーブルにはあるロールを追加
                            $tmpChkArray = $aryResult01[$strKeyRole];
                            if( $tmpChkArray['DISUSE_FLAG'] === '0' || $tmpChkArray['DISUSE_FLAG'] === '1' ){
                                $tmpArrayOrder[$strKeyRole] = '1';
                            }
                            else{
                                //----隠しフラグ用
                                //隠しフラグ用----
                            }
                            //フォームから送信されたが、テーブルにはあるロールを追加----
                        }
                    }
                    unset($tmpChkArray);
                    // チェックが入っているロールと「$p_role_array」と、テーブルのありもの、を統合する----
                }
                    
                if( $boolExeContinue === true  ){
                    $arrayConfigForIUD = array(
                        "JOURNAL_SEQ_NO"=>"",
                        "JOURNAL_ACTION_CLASS"=>"",
                        "JOURNAL_REG_DATETIME"=>"",
                        "LINK_ID"=>"",
                        "USER_ID"=>"",
                        "ROLE_ID"=>"",
                        "NOTE"=>"",
                        "DISUSE_FLAG"=>"",
                        "LAST_UPDATE_TIMESTAMP"=>"",
                        "LAST_UPDATE_USER"=>""
                    );
                    
                    foreach($tmpArrayOrder as $intRole=>$strOrd){
                        //----ループ
                        $tmpRowExists = false;
                        $strDisuseFlag = $strOrd;
                        $tgtSource_row = array();
                        
                        if(array_key_exists($intRole, $aryResult01) === true){
                            $cln_update_row = $aryResult01[$intRole];
                            $tmpRowExists = true;
                        }
                        
                        if( $tmpRowExists === true ){
                            //----更新
                            if( $cln_update_row['DISUSE_FLAG'] == $strDisuseFlag ){
                                //----値の変更がなく、レコード変更の必要がないので、スキップ
                                continue;
                                //値の変更がなく、レコード変更の必要がないので、スキップ----
                            }
                            $cln_update_row['DISUSE_FLAG']    = $strDisuseFlag;
                            $cln_update_row['LAST_UPDATE_USER'] = $g['login_id'];
                            $tgtSource_row = $cln_update_row;
                            $sqlType = "UPDATE";
                            //更新----
                        }
                        else{
                            //----登録
                            if( $strDisuseFlag == '1' ){
                                //----行がなく登録すべきだが、廃止フラグが立っているのでスキップ
                                continue;
                                //行がなく登録すべきだが、廃止フラグが立っているのでスキップ----
                            }
                            $retArray = getSequenceValueFromTable('SEQ_A_ROLE_ACCOUNT_LINK_LIST', 'A_SEQUENCE', FALSE );
                            if( $retArray[1] != 0 ){
                                throw new Exception( '00002200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            }
                            $varRISeq = $retArray[0];
                            $sqlType = "INSERT";
                            $new_insert_row = $arrayValueTmpl;
                            $new_insert_row['LINK_ID']        = $varRISeq;
                            $new_insert_row['USER_ID']        = $p_user_id;
                            $new_insert_row['ROLE_ID']        = $intRole;
                            $new_insert_row['DISUSE_FLAG']    = $strDisuseFlag;
                            $new_insert_row['LAST_UPDATE_USER'] = $g['login_id'];
                            $tgtSource_row = $new_insert_row;
                            //登録----
                        }
                        
                        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch'],
                            $sqlType,
                            "LINK_ID",
                            "A_ROLE_ACCOUNT_LINK_LIST",
                            "A_ROLE_ACCOUNT_LINK_LIST_JNL",
                            $arrayConfigForIUD,
                            $tgtSource_row 
                        );
                        
                        if( $retArray[0] === false ){
                            throw new Exception( '00002300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        $sqlUtnBody = $retArray[1];
                        $arrayUtnBind = $retArray[2];
                        
                        $sqlJnlBody = $retArray[3];
                        $arrayJnlBind = $retArray[4];
                        
                        // 履歴シーケンス払い出し
                        $retArray = getSequenceValueFromTable('JSEQ_A_ROLE_ACCOUNT_LINK_LIST', 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            throw new Exception( '00002400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        $varJSeq = $retArray[0];
                        $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                        
                        $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
                        $objQueryJnl = $g['objDBCA']->sqlPrepare($sqlJnlBody);
                        
                        if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                            throw new Exception( '00002500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                            throw new Exception( '00002600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        $rUtn = $objQueryUtn->sqlExecute();
                        if($rUtn!=true){
                            throw new Exception( '00002700-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        $rJnl = $objQueryJnl->sqlExecute();
                        if($rJnl!=true){
                            throw new Exception( '00002800-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        
                        //SQL実行----
                        unset($objQueryUtn);
                        unset($objQueryJnl);
                        //ループ----
                    }
                    unset($tmpArrayOrder);
                    $boolResult = $g['objDBCA']->transactionCommit();
                    if ( $boolResult === false ){
                        throw new Exception( '00002900-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $g['objDBCA']->transactionExit();
                    // トランザクション終了----
                    
                    if( $boolExeContinue === true  ){
                        // ----正常の場合はログインID(数字)を返却する(受け取り側でmatchメソッドを使うので、文字列型へキャスト)
                        // ----暗黙の型変換されないように注意すること
                        $output_str = (string)$p_user_id;
                        // 正常の場合はログインID(数字)を返却する(受け取り側でmatchメソッドを使うので、文字列型へキャスト)----
                    }
                }
                break;
                // 編集画面で実行を押下(mode=2)----
            default :
                // ----異常発生画面(未定義mode)
                throw new Exception( '00003000-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 異常発生画面(未定義mode)----
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
