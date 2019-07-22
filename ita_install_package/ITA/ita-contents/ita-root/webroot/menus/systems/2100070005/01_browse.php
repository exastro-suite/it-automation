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
    // 各種ローカル定数を定義

    // 初期値を宣言
    $interval = null;

    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // メニューのディレクトリを取得
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        // browse系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");

        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");

        // クエリ存在判定と成功時の取得
        if( !array_key_exists( "execution_no", $_GET ) ){
            // アクセスログ出力(クエリ無し警告)
            web_log( $objMTS->getSomeMessage("ITAOPENST-ERR-103010", array(__FILE__, __LINE__)) );
        }

        if( array_key_exists( "execution_no", $_GET ) === true ){
            // クエリからexecution_noを取得
            $execution_no = $_GET["execution_no"];
            
            // 整数でない場合はNGとする
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($execution_no) === false ){
                // アクセスログ出力(想定外エラー)
                web_log( $objMTS->getSomeMessage("ITAOPENST-ERR-103020", array(__FILE__, __LINE__, $execution_no)) );
            }
        }

        // 作業№が設定されている場合
        if( isset($execution_no) ){
            ////////////////////////////////////////////////////////////////
            // ステータスを取得                                           //
            ////////////////////////////////////////////////////////////////
            // SQL生成
            $sql = "SELECT  STATUS_ID
                    FROM    C_OPENST_RESULT_MNG
                    WHERE   DISUSE_FLAG = '0'
                    AND     EXECUTION_NO = :EXECUTION_NO_BV ";

            $objQuery = $g['objDBCA']->sqlPrepare($sql);

            if($objQuery->getStatus()===false){
                // アクセスログ出力(想定外エラー)
                web_log( $objMTS->getSomeMessage("ITAOPENST-ERR-404",array(__FILE__,__LINE__,"00000100")) );

                unset($objQuery);

                // 例外処理へ
                throw new Exception();
            }

            $objQuery->sqlBind( array( 'EXECUTION_NO_BV'=>$execution_no ) );

            $r = $objQuery->sqlExecute();

            if (!$r){
                // アクセスログ出力(想定外エラー)
                web_log( $objMTS->getSomeMessage("ITAOPENST-ERR-404",array(__FILE__,__LINE__,"00000200")) );

                unset($objQuery);

                // 例外処理へ
                throw new Exception();
            }

            while ( $row = $objQuery->resultFetch() ){
                // ステータスIDを取得
                $status_id_temp = $row['STATUS_ID'];
            }
            // ループ回数を取得
            $num_rows = $objQuery->effectedRowCount();

            // DBアクセス事後処理
            unset($objQuery);

            // 単一行セレクトか判定
            if( $num_rows != 1 ){
                // アクセスログ出力(想定外エラー)
                web_log( $objMTS->getSomeMessage("ITAOPENST-ERR-404",array(__FILE__,__LINE__,"00000300")) );

                // 例外処理へ
                throw new Exception();
            }

            ////////////////////////////////////////////////////////////////
            // インタフェース情報を取得                                   //
            ////////////////////////////////////////////////////////////////
            $aryRetBody = singleSQLExecuteAgent("SELECT * " 
                         ."FROM B_OPENST_IF_INFO TAB_1 "
                         ."WHERE TAB_1.DISUSE_FLAG = '0'",
                          array(),
                          "");
            $objQuery = $aryRetBody[1];
            while($row = $objQuery->resultFetch() ){
                $aryDataSet[]= $row;
            }

            $interval = $aryDataSet[0]['OPENST_REFRESH_INTERVAL']; // 最新化のインターバルタイム(msec)
        }
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }

    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");

    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAOPENSTC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");

    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----

    // サイト個別のHTMLステートメント
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_itabase_orchestrator_drive_style_css=filemtime("$root_dir_path/webroot/common/javascripts/itabase_orchestrator_drive.js");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <link rel="Stylesheet" type="text/css" href="/common/css/itabase_orchestrator_drive_style.css?{$timeStamp_itabase_orchestrator_drive_style_css}">


<style type="text/css">
.ui-widget-content{
    border:1px solid #666;

}

.progressWrapper .header .ui-widget-header{
    border:1px solid green;
    border-width:1px 1px 1px 0;
    background:green url('/common/imgs/jqimg/animated-overlay.gif');
    opacity:0.8;
}

.progressWrapper .detail .ui-widget-header{
    border:1px solid green;
    border-width:1px 1px 1px 0;
    background:green url('/common/imgs/jqimg/animated-overlay.gif');
    opacity:0.8;
}


h3{
    width:100%;
    position:relative;
    padding:.25em 0 .2em .75em;
    border-left:6px solid #3498db;
    margin-bottom:20px;
    font-weight:bold;
}
h3::after{
    position:absolute;
    left:0;
    bottom:0;
    content:'';
    width:95%;
    height:0;
    border-bottom:1px solid #ccc;
}
.timeArea{
    margin-bottom:20px;

}
.progressBar{
    margin-left:20px;
    margin-right:10px;
    margin-top:5px;
}

.header .ui-progressbar{
    height:25px;
}

.detail .ui-progressbar{
    height:15px;
}

.progressWrapper{
    width:700px
}
.progressWrapper .header{
    width:680px;padding:10px;
}
.progressWrapper .detail{
    float:right;width:600px;padding:10px
}

.progressWrapper .header h3{
    border-left:6px solid orange;
}
.progressWrapper .detail h3{
    border-left:6px solid #3498db;
}
.progressWrapper .timeArea .timeFrom{
float:right;width:140px;text-align:left;
}
.progressWrapper .timeArea .timeTo{
float:right;width:140px;text-align:right;
}
.progressWrapper .timeArea .subText{
    float:left;width:200px;
}
.message{
    margin-top:5px;
    text-align:right;
    font-weight:bolder;
}
.dispLog{
    display:none;
    background:indianred;
    font-weight:bolder;
    color:white;
    float:right;
    cursor:pointer;
    border:0;
    border-radius:10px;
}
</style>

EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");

    $strPageExplainBody = $objMTS->getSomeMessage("ITAOPENST-MNU-160050");

print <<< EOD
    <!-------------------------------- 説明 -------------------------------->
    <div id="intervalOfDisp" style="display:none" class="text">{$interval}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <h2>
        <table width="100%">
            <tr>
                <!--説明-->
                <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAOPENST-MNU-160000")}</div></td>
                <td>
                    <div id="SetsumeiMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="SetsumeiNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            {$strPageExplainBody}
        </div>
    </div>

    <!-------------------------------- 対象作業 -------------------------------->
    <div id="ExecutionNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="disp_execution_area">

<div class="progressWrapper">
    <div class="header">
    </div>  

    <div class="detail">
    </div>
<button class="dispLog">{$objMTS->getSomeMessage("ITAOPENST-MNU-160060")}</button>
</div>

            </div>
        </div>
    </div>

EOD;

    //作業№が設定されている場合
    if( isset($execution_no) ){
        //ステータスが「未実行(予約)」の場合
        if( $status_id_temp == 2 ){
print <<< EOD
                <!-------------------------------- 予約取消 -------------------------------->
                <h2>
                    <table width="100%">
                        <tr>
                            <!--予約取消-->
                            <td><div onClick=location.href="javascript:show('Booking_Cancel_Midashi','Booking_Cancel_Nakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAOPENST-MNU-160010")}</div></td>
                            <td>
                                <div id="Booking_Cancel_Midashi" align="right">
                                    <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Booking_Cancel_Midashi','Booking_Cancel_Nakami');" >
                                </div>
                            </td>
                        </tr>
                    </table>
                </h2>
                <div id="Booking_Cancel_Nakami" style="display:block" class="text">
                    <div style="margin-left:10px">
                        <!--予約取消-->
                        <input type="button" id="book_cancel_button" class="updatebutton" value="{$objMTS->getSomeMessage("ITAOPENST-MNU-160020")}" onClick=location.href="javascript:book_cancel();" >
                    </div>
                </div>

EOD;
        }

    }

print <<< EOD

    <!-------------------------------- 緊急停止 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--緊急停止-->
                <td><div onClick=location.href="javascript:show('ScramMidashi','ScramNakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITAOPENST-MNU-160030")}</div></td>
                <td>
                    <div id="ScramMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('ScramMidashi','ScramNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="ScramNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="scram_area">
            <!--緊急停止-->
            <input type="button" id="scrumTryExecute" class="updatebutton scrumSubmitElement tryExecute" value="{$objMTS->getSomeMessage("ITAOPENST-MNU-160040")}" disabled>
            </div>
        </div>
    </div>

    <!-------------------------------- 必要情報 -------------------------------->
EOD;
    if( isset($execution_no) ){
print <<< EOD
    <div id="target_execution_no" style="display:none;">$execution_no</div>
EOD;
    }

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
    
?>
