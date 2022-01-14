<?php

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

    global $g;
    // ルートディレクトリを取得
    $tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }

    $param = explode ( "?" , $_SERVER["REQUEST_URI"] , 2 );
    if(count($param) == 2){
        $url_add_param = "&" . $param[1];
    }
    else{
        $url_add_param = "";
    }

    // DBアクセスを伴う処理を開始
    try{
        //----ここから01_系から06_系全て共通
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
        //ここまで01_系から06_系全て共通----

        // browse系共通ロジックパーツ01
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_01.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }

    $strCmdWordAreaOpen = $g['objMTS']->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $g['objMTS']->getSomeMessage("ITAWDCH-STD-252");

    // 共通HTMLステートメントパーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_statement.php");

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");

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
    $strVersion = str_replace('Exastro IT Automation Base functions version ', '',$releaseBase);
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
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
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
