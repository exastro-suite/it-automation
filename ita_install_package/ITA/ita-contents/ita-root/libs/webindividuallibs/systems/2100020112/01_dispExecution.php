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
    //                ・オーケストレータ別の設定記述あり
    //
    //////////////////////////////////////////////////////////////////////
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $execution_management_dir = "2100020113";
    
    //----オーケストレータ別の設定記述
    
    $strExeTableIdForSelect = 'E_ANSIBLE_LNS_EXE_INS_MNG';
    
    //オーケストレータ別の設定記述----
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    try{
        // パラメータチェック(ガードロジック)
        $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($target_execution_no) === false ){
            // エラー箇所をメモ
            throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
        // レコードをSELECT
        $strSelectLastUpdateTimestamp1 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.TIME_BOOK"            ,"DATEDATE",false);
        $strSelectLastUpdateTimestamp2 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.TIME_START"           ,"DATEDATE",false);
        $strSelectLastUpdateTimestamp3 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.TIME_END"             ,"DATEDATE",false);
        $strSelectLastUpdateTimestamp4 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.LAST_UPDATE_TIMESTAMP","DATEDATE",false);
        
        $strConnectString1 = makeStringConnectForSQLPart($g['db_model_ch'],array("'('","TAB_A.LAST_UPDATE_USER","')'"));

        //----オーケストレータ別の設定記述
        $sql = "SELECT  TAB_A.EXECUTION_NO,
                        TAB_A.SYMPHONY_NAME,
                        TAB_A.EXECUTION_USER,
                        TAB_A.PATTERN_ID,
                        TAB_A.I_PATTERN_NAME,
                        TAB_A.I_TIME_LIMIT,
                        TAB_A.ANS_HOST_DESIGNATE_TYPE_NAME,
                        TAB_A.I_ANS_PARALLEL_EXE,
                        TAB_A.ANS_WINRM_FLAG_NAME,
                        TAB_A.STATUS_ID,
                        TAB_A.OPERATION_NO_UAPK,
                        TAB_A.I_OPERATION_NAME,
                        TAB_A.I_OPERATION_NO_IDBH,
                        TAB_A.STATUS_NAME,
                        {$strSelectLastUpdateTimestamp1} AS TIME_BOOK,
                        {$strSelectLastUpdateTimestamp2} AS TIME_START,
                        {$strSelectLastUpdateTimestamp3} AS TIME_END,
                        TAB_A.FILE_INPUT,
                        TAB_A.FILE_RESULT,
                        TAB_A.RUN_MODE_NAME,
                        TAB_A.I_ANS_PLAYBOOK_HED_DEF,
                        TAB_A.I_ANS_EXEC_OPTIONS,
                        TAB_A.EXEC_MODE,
                        TAB_A.EXEC_MODE_NAME,

                        TAB_A.NOTE, 
                        {$strSelectLastUpdateTimestamp4} AS LAST_UPDATE_TIMESTAMP,
                        CASE TAB_B.USERNAME_JP WHEN NULL THEN {$strConnectString1}
                                                   ELSE TAB_B.USERNAME_JP
                                                   END AS LAST_UPDATE_USER
                    FROM    {$strExeTableIdForSelect} TAB_A
                    LEFT JOIN A_ACCOUNT_LIST             TAB_B ON (TAB_A.LAST_UPDATE_USER = TAB_B.USER_ID)
                    WHERE   TAB_A.DISUSE_FLAG = '0'
                    AND     TAB_A.EXECUTION_NO = :EXECUTION_NO_BV ";
        //オーケストレータ別の設定記述----
        
        $tmpAryBind = array( 'EXECUTION_NO_BV'=>$target_execution_no );
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
        
        require_once($g['root_dir_path'] . "/webconfs/systems/{$execution_management_dir}_loadTable.php");
        
        $table = loadTable($execution_management_dir);
        $arrayColumn = $table->getColumns();
        
        $COLUMN_01 = nl2br(htmlspecialchars($showTgtRow['EXECUTION_NO']));
        $COLUMN_42 = nl2br(htmlspecialchars($showTgtRow['EXECUTION_USER']));
        $COLUMN_43 = nl2br(htmlspecialchars($showTgtRow['SYMPHONY_NAME']));
        $COLUMN_03 = nl2br(htmlspecialchars($showTgtRow['I_TIME_LIMIT']));
        
        $COLUMN_04 = nl2br(htmlspecialchars($showTgtRow['OPERATION_NO_UAPK']));
        $COLUMN_05 = nl2br(htmlspecialchars($showTgtRow['I_OPERATION_NAME']));
        
        //----オーケストレータ別の設定記述
        $strFocusColumnId = 'FILE_INPUT';
        $objColumn = $arrayColumn[$strFocusColumnId];
        $strLaBranchPerFUC_PADRIN = $objColumn->getLAPathToFUCItemPerRow($showTgtRow);
        if( file_exists( $strLaBranchPerFUC_PADRIN ) && !is_dir( $strLaBranchPerFUC_PADRIN ) ){
            $COLUMN_06 = '<a href="' . $objColumn->getOAPathToFUCItemPerRow($showTgtRow) . '" target="_blank">' . nl2br(htmlspecialchars($showTgtRow[$strFocusColumnId])) . '</a>';
        }
        else{
            $COLUMN_06 = '';
        }
        
        $strFocusColumnId = 'FILE_RESULT';
        $objColumn = $arrayColumn[$strFocusColumnId];
        $strLaBranchPerFUC_PADRIN = $objColumn->getLAPathToFUCItemPerRow($showTgtRow);
        if( file_exists( $strLaBranchPerFUC_PADRIN ) && !is_dir( $strLaBranchPerFUC_PADRIN ) ){
            $COLUMN_07 = '<a href="' . $objColumn->getOAPathToFUCItemPerRow($showTgtRow) . '" target="_blank">' . htmlspecialchars(nl2br($showTgtRow[$strFocusColumnId])) . '</a>';
        }
        else{
            $COLUMN_07 = '';
        }
        
        $COLUMN_11 = nl2br(htmlspecialchars($showTgtRow['TIME_BOOK']));
        $COLUMN_12 = nl2br(htmlspecialchars($showTgtRow['TIME_START']));
        $COLUMN_13 = nl2br(htmlspecialchars($showTgtRow['TIME_END']));
        $COLUMN_14 = nl2br(htmlspecialchars($showTgtRow['STATUS_NAME']));
        $COLUMN_08 = nl2br(htmlspecialchars($showTgtRow['RUN_MODE_NAME']));
        $COLUMN_31 = nl2br(htmlspecialchars($showTgtRow['PATTERN_ID']));
        $COLUMN_32 = nl2br(htmlspecialchars($showTgtRow['I_PATTERN_NAME']));
        $COLUMN_33 = nl2br(htmlspecialchars($showTgtRow['I_OPERATION_NO_IDBH']));
        
        $COLUMN_35 = nl2br(htmlspecialchars($showTgtRow['ANS_HOST_DESIGNATE_TYPE_NAME']));
        $COLUMN_36 = nl2br(htmlspecialchars($showTgtRow['ANS_WINRM_FLAG_NAME']));

        $status_id = htmlspecialchars($showTgtRow['STATUS_ID']);

        // 表示フィルター用 オペレーションID生成
        $ope_param      = sprintf("%s",rawurlencode($showTgtRow['I_OPERATION_NO_IDBH']));
        // 表示フィルター用 MovementID生成
        $movement_param = sprintf("%s",rawurlencode($showTgtRow['PATTERN_ID']));

        // 作業対象ホストへ遷移するボタン生成
        $url = sprintf("/default/menu/01_browse.php?no=2100020108&ope_id=%s&movement_id=%s", $ope_param,$movement_param);
        $caption = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-5010001");
        $COLUMN_38 =  sprintf("<input class=\"linkBtnInTbl\" type=\"button\" value=\"%s\" onClick=\"window.open('%s')\">",$caption,$url);

        // 代入値管理へ遷移するボタン生成
        $caption = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-5010003");
        $url = sprintf("/default/menu/01_browse.php?no=2100020109&ope_id=%s&movement_id=%s", $ope_param,$movement_param);
        $COLUMN_39 =  sprintf("<input class=\"linkBtnInTbl\" type=\"button\" value=\"%s\" onClick=\"window.open('%s')\">",$caption,$url);

        // Movement一覧へ遷移するボタン生成
        $caption = $COLUMN_32;
        $url = sprintf("/default/menu/01_browse.php?no=2100020103&movement_id=%s",$movement_param);
        $COLUMN_40 =  sprintf("<input class=\"linkBtnInTbl\" type=\"button\" value=\"%s\" onClick=\"window.open('%s')\">",$caption,$url);

        //オーケストレータ別の設定記述----

        //----オーケストレータ別の設定記述
        $output_str .=
<<< EOD
                <div class="fakeContainer_Yobi0">
                <table id="DbTable_Yobi0">
                    <tr class="defaultExplainRow">
                        <th scope="row" rowspan="1" colspan="1" class="noBorderRight" ><span class="generalBold"></th>
                        <th scope="row" rowspan="1" colspan="1" class="noBorderBoth"  ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101035")}</span></th>
                        <th scope="row" rowspan="1" colspan="1" class="noBorderLeft"  ><span class="generalBold"></th>
                        <th scope="row" rowspan="1" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101060")}</span><!--値//--></th>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101070")}</span><!--作業№//--></td>
                        <td                                     >{$COLUMN_01}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101075")}</span><!--実行種別//--></td>
                        <td                                     >{$COLUMN_08}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103050")}</span><!--ステータス//--></td>
                        <td                                     >{$COLUMN_14}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103110")}</span><!--シンフォニークラス//--></td>
                        <td                                     >{$COLUMN_43}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103100")}</span><!--実行ユーザ//--></td>
                        <td                                     >{$COLUMN_42}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="5" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101080")}</span><!--作業パターン//--></td>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101095")}</span><!--ID//--></td>
                        <td                                     >{$COLUMN_31}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-101096")}</span><!--名称//--></td>
                        <td                                     >{$COLUMN_40}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102010")}</span><!--遅延タイマ(分)//--></td>
                        <td                                     >{$COLUMN_03}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="2" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102011")}</span><!--Ansible利用情報//--></td>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102012")}</span><!--ホスト指定形式//--></td>
                        <td                                     >{$COLUMN_35}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102013")}</span><!--WinRM接続//--></td>
                        <td                                     >{$COLUMN_36}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="3" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102030")}</span><!--オペレーション情報//--></td>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102040")}</span><!--№//--></td>
                        <td                                     >{$COLUMN_04}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102050")}</span><!--名称//--></td>
                        <td                                     >{$COLUMN_05}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102055")}</span><!--ID//--></td>
                        <td                                     >{$COLUMN_33}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-5010000")}</span><!--作業対象ホスト//--></td>
                        <td                                     >{$COLUMN_38}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="3" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-5010002")}</span><!--代入値//--></td>
                        <td                                     >{$COLUMN_39}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102060")}</span><!--入力データ//--></td>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102070")}</span><!--投入データ//--></td>
                        <td                                     >{$COLUMN_06}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102080")}</span><!--出力データ//--></td>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-102090")}</span><!--結果データ//--></td>
                        <td                                     >{$COLUMN_07}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="3" colspan="1" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103010")}</span><!--作業状況//--></td>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103020")}</span><!--予約日時//--></td>
                        <td                                     >{$COLUMN_11}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103030")}</span><!--開始日時//--></td>
                        <td                                     >{$COLUMN_12}</td>
                    </tr>
                    <tr>
                        <td class="likeHeader" scope="row" rowspan="1" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-103040")}</span><!--終了日時//--></td>
                        <td                                     >{$COLUMN_13}</td>
                    </tr>
                </table>
                </div>
                <div id="status_id" style="display:none;">{$status_id}</div>
EOD;
        //オーケストレータ別の設定記述----
    }
    catch (Exception $e){
        //----正常時でも飛ばす版
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        // DBアクセス事後処理
        if ( isset($objQuery) )    unset($objQuery);

        if( !empty($output_str) ){
            //----正常（単なる処理省略）
            //正常（単なる処理省略）----
        }
        else if( !empty($warning_info) ){
            //----警告
            //警告----
        }
        else{
            // エラーフラグをON
            if( empty($error_info) ) $error_info = $tmpErrMsgBody;
        }
        //正常時でも飛ばす版----
    }
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
