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
    $boolExecute = true;

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);

    // DBアクセスを伴う処理を開始
    try{
        $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($p_menu_id) === false ){
            throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        // レコードをSELECT

        $strSelectLastUpdateTimestamp = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_1.LAST_UPDATE_TIMESTAMP","DATEDATE",false);

        $sql = "SELECT TAB_1.DISUSE_FLAG       AS DISUSE_FLAG,
                        TAB_3.DISUSE_FLAG      AS MG_DISUSE_FLAG,
                        TAB_1.MENU_ID,
                        TAB_1.MENU_GROUP_ID,
                        TAB_3.MENU_GROUP_NAME,
                        TAB_1.MENU_NAME,
                        TAB_4.NAME             AS LOGIN_NECESSITY_DISP,
                        TAB_1.DISP_SEQ,
                        TAB_1.NOTE,
                        {$strSelectLastUpdateTimestamp}  AS LAST_UPDATE_TIMESTAMP,
                        TAB_1.LAST_UPDATE_USER           AS LAST_UPDATE_USER_RAW,
                        TAB_2.USERNAME_JP                AS LAST_UPDATE_USER_JP 
                FROM   A_MENU_LIST                       TAB_1
                        LEFT JOIN A_ACCOUNT_LIST         TAB_2 ON (TAB_1.LAST_UPDATE_USER = TAB_2.USER_ID)
                        LEFT JOIN A_MENU_GROUP_LIST      TAB_3 ON (TAB_1.MENU_GROUP_ID = TAB_3.MENU_GROUP_ID)
                        LEFT JOIN A_LOGIN_NECESSITY_LIST TAB_4 ON (TAB_1.LOGIN_NECESSITY = TAB_4.FLAG )
                WHERE  TAB_1.DISUSE_FLAG      IN ('0','1')
                AND    TAB_3.DISUSE_FLAG      IN ('0','1')
                AND    TAB_1.MENU_ID = :MENU_ID_BV ";

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

        if( $showTgtRow['MG_DISUSE_FLAG'] == '1' ){
            //----所属しているメニューグループが廃止されています。<br>
            $output_str .= "<span class=\"warningErrMsg\">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040065")}</span>";
        }

        if( $boolExecute === true ){
            // 項目生成
            $strDisuseFlagShow = "";
            $BG_COLOR = "";
            if( $showTgtRow['DISUSE_FLAG'] === "0" ){
                $strDisuseFlagShow = "";
                $BG_COLOR = "";
            }
            else if( $showTgtRow['DISUSE_FLAG'] === "1" ){
                //廃止
                $strDisuseFlagShow = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040051");
                $BG_COLOR = " class=\"disuse\" ";
            }

            $strDispLastUpdateUser = "";
            if( $showTgtRow['LAST_UPDATE_USER_JP'] === "" ){
                $strDispLastUpdateUser = $showTgtRow['LAST_UPDATE_USER_RAW'];
                if(0 < strlen($strDispLastUpdateUser) ){
                    $strDispLastUpdateUser = "(".$strDispLastUpdateUser.")";
                }
            }
            else{
                $strDispLastUpdateUser = $showTgtRow['LAST_UPDATE_USER_JP'];
            }
            
            $COLUMN_00 = nl2br($strDisuseFlagShow);
            $COLUMN_01 = nl2br(htmlspecialchars($showTgtRow['MENU_ID']));
            $COLUMN_02 = nl2br(htmlspecialchars($showTgtRow['MENU_GROUP_ID']));
            $COLUMN_03 = nl2br(htmlspecialchars($showTgtRow['MENU_GROUP_NAME']));
            $COLUMN_04 = nl2br(htmlspecialchars($showTgtRow['MENU_NAME']));
            $COLUMN_05 = nl2br(htmlspecialchars($showTgtRow['LOGIN_NECESSITY_DISP']));
            $COLUMN_06 = nl2br(htmlspecialchars($showTgtRow['DISP_SEQ']));
            $COLUMN_07 = nl2br(htmlspecialchars($showTgtRow['NOTE']));
            $COLUMN_08 = nl2br(htmlspecialchars($showTgtRow['LAST_UPDATE_TIMESTAMP']));
            $COLUMN_09 = nl2br(htmlspecialchars($strDispLastUpdateUser));
            
            $output_str .= 
<<< EOD
            <div class="fakeContainer_Yobi0">
            <table id="DbTable_Yobi0">
                <tr class="defaultExplainRow">
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1049101")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040101")}</span></th>
                    <th scope="col" colspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040201")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040601")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040701")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040901")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1049601")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1049201")}</span></th>
                    <th scope="col" rowspan="2" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1049301")}</span></th>
                </tr>
                <tr class="defaultExplainRow">
                    <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040301")}</span></th>
                    <th scope="col" ><span class="generalBold">{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040401")}</span></th>
                </tr>

                <tr valign="top">
                    <td class="likeHeader" scope="row" >{$COLUMN_00}</td>
                    <td class="likeHeader number" scope="row" >{$COLUMN_01}</td>
                    <td class="number" {$BG_COLOR}>{$COLUMN_02}</td>
                    <td{$BG_COLOR}>{$COLUMN_03}</td>
                    <td{$BG_COLOR}>{$COLUMN_04}</td>
                    <td{$BG_COLOR}>{$COLUMN_05}</td>
                    <td class="number" {$BG_COLOR}>{$COLUMN_06}</td>
                    <td{$BG_COLOR}>{$COLUMN_07}</td>
                    <th class="likeHeader" scope="row" >{$COLUMN_08}</td>
                    <th class="likeHeader" scope="row" >{$COLUMN_09}</td>
                </tr>
            </table>
            </div>
EOD;
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        $error_flag = 1;
        
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        // DBアクセス事後処理
        if ( isset($objQuery) )    unset($objQuery);
        
        web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
    }

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
