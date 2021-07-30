<?php

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php");
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    //-- サイト個別PHP要素、ここから--
   
    //リリースファイル読み込み
    $releaseFile=array();
    foreach(glob($root_dir_path . "/libs/release/*") as $file) {
        if( $file==$root_dir_path . '/libs/release/ita_base' ){
            $releaseBase=file_get_contents($file);
        }else{
            array_push($releaseFile,file_get_contents($file));
        }
    }
    //バージョン取得
    $strVersion = substr($releaseBase, -6);
    //表を作成
    $table_code="";

    //テーブル項目+
    $table_code_label=
<<< EOD
<tr class="defaultExplainRow">
    <th><p class="generalBold">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-910004")}</p></th>
</tr>
<tr class="defaultExplainRow">
    <td><p class="generalBold">$strVersion</p></td>
</tr>
EOD;

    //表を作成
    $table_code2="";

    //テーブル項目+
    $table_code_label2=
<<< EOD
<tr class="defaultExplainRow">
    <th><p class="generalBold">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-910005")}</p></th>
</tr>
EOD;
    foreach($releaseFile as $release) {
        //テーブル要素
        $driverName=explode(" ", $release);
        $table_code_row=
<<< EOD
<tr>
    <td><p>$driverName[3]</p></td>
</tr>
EOD;
        $table_code2=$table_code2.$table_code_row;
    }

    //-- サイト個別PHP要素、ここまで--
    print 
<<< EOD
    <!-------------------------------- 記事部分 --------------------------------------->

    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="pageType" style="display:none" class="text">{$pageType}</div>
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysWebRowConfirm" style="display:none" class="text">{$varWebRowConfirm}</div>
    <div id="sysWebRowLimit" style="display:none" class="text">{$varWebRowLimit}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="webStdTableWidth" style="display:none" class="text">{$intTableWidth}</div>
    <div id="webStdTableHeight" style="display:none" class="text">{$intTableHeight}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
{$strDeveloperArea}
    <!-------------------------------- 説明 -------------------------------->

    <h2>
        <table width="100%">
            <tr>
                <td>
                    <div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" >
                        {$g['objMTS']->getSomeMessage("ITABASEH-MNU-910001")}
                    </div>
                </td>
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
            <table class="sDefault sDefault-Headers" id="Filter1Tbl-Headers" width="200" style="margin: 0px" rules="all" >{$table_code_label}</table>
        </div>
        <br />
        <div style="margin-left:10px">
            <table class="sDefault sDefault-Headers" id="Filter2Tbl-Headers" width="200" style="margin: 0px" rules="all" >{$table_code_label2}{$table_code2}</table>
        </div>
    </div>
    <!-------------------------------- 説明 -------------------------------->


EOD;
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
