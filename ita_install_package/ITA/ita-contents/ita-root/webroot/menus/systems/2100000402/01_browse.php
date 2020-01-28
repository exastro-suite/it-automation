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

/**
 * Symphony/オペレーションインポート画面
 *
 */
$tmpAry = explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);

try{
    // DBコネクト
    require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
    
    // 共通設定取得パーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
    
    // メニュー情報取得パーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
    
    // browse系共通ロジックパーツ01
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_01.php");
    
    // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
    // (この処理は非テンプレートのコンテンツのみに必要)
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_maintenance.php");
}
catch (Exception $e){
    // DBアクセス例外処理パーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
}

// 共通HTMLステートメントパーツ
require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_statement.php");

$strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
$strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");

// javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
$timeStamp_itabase_symphony_style_css=filemtime("{$g['root_dir_path']}/webroot/common/css/itabase_symphony_style.css");
$timeStamp_00_javascript_js=filemtime("{$g['root_dir_path']}/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");

print <<< EOD
<script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
<link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_symphony_style.css?{$timeStamp_itabase_symphony_style_css}">
EOD;

// browse系共通ロジックパーツ02
require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_02.php");

require_once ( $g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000402/web_parts_for_sym_ope_import.php');
$retImportOpeAry = array();
$retImportSymAry = array();
?>
<?php if (is_array($retImportAry)): ?>
<?php if (count($retImportAry) > 0):
    $retImportOpeAry = $retImportAry[0];
    $retImportSymAry = $retImportAry[1];
?>
<script type="text/javascript">
$(function() {
    show('Mix2_Midashi', 'Mix2_Nakami');
});
</script>
<?php if (count($retImportOpeAry) > 0): ?>
<script type="text/javascript">
$(function() {
    $('#import_whole_ope').on('click', function() {
        $('.import_all_ope').prop('checked', this.checked).change();
    });

<?php foreach($retImportOpeAry as $key => $value): ?>
    $('#import_ope_<?php echo $key; ?>').change(function() {
        if ($('.import_all_div_ope :checked').length == $('.import_all_div_ope :input').length) {
            $('#import_whole_ope').prop('checked', true);
        } else {
            $('#import_whole_ope').prop('checked', false);
        }
    });
<?php endforeach; ?>
});
</script>
<?php endif; ?>
<?php if (count($retImportSymAry) > 0): ?>
<script type="text/javascript">
$(function() {
    $('#import_whole_sym').on('click', function() {
        $('.import_all_sym').prop('checked', this.checked).change();
    });

<?php foreach($retImportSymAry as $key => $value): ?>
    $('#import_sym_<?php echo $key; ?>').change(function() {
        if ($('.import_all_div_sym :checked').length == $('.import_all_div_sym :input').length) {
            $('#import_whole_sym').prop('checked', true);
        } else {
            $('#import_whole_sym').prop('checked', false);
        }
    });
<?php endforeach; ?>
});
</script>
<?php endif; ?>
<?php endif; ?>

<?php else: ?>
            <div id="filter_alert_area" class="alert_area" style="display:block" ><?php echo $retImportAry; ?></div>

<?php endif; ?>

<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
<div id="sysJSCmdText01" style="display:none" class="text"><?php echo $strCmdWordAreaOpen; ?></div>
<div id="sysJSCmdText02" style="display:none" class="text"><?php echo $strCmdWordAreaClose; ?></div>
<div id="messageTemplate" style="display:none" class="text"><?php echo $strTemplateBody; ?></div>
<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->

<!-------------------------------- 説明 -------------------------------->
<h2>
    <table width="100%">
        <tr>
            <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class"><?php echo $g['objMTS']->getSomeMessage('ITAWDCH-STD-30011'); ?></div></td>
            <td>
                <div id="SetsumeiMidashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" >
                </div>
            </td>
        </tr>
    </table>
</h2>
<div id="SetsumeiNakami" style="display:block" class="text">
    <div style="margin-left:10px;">
         <p style="margin-bottom: 10px;">
             <?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900052');?>
         </p>
    </div>
</div>
<!-------------------------------- 説明 -------------------------------->
<!---------------------------- アップロード ---------------------------->
<h2>
    <table width="100%">
        <tr>
            <td><div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class"><?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900002'); ?></div></td>
            <td>
                <div id="Mix1_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" >
                </div>
            </td>
        </tr>
    </table>
</h2>
<div id="Mix1_Nakami" style="display:block" class="text">
    <div style="margin-left:20px;">
        <form method="post" action="/default/menu/01_browse.php?no=<?php echo $g['page_dir']; ?>" enctype="multipart/form-data">
            <p><input type="file" name="zipfile" id="zipinput"></p>
            <div id="uploadMsg" style="color: red; margin-top: 20px;"></div>
            <p><input type="submit" value="<?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900002'); ?>" id="zipInputSubmit"></p>
            <input type="hidden" name="post_kind" value="upload">
        </form>
    </div>
</div>
<!---------------------------- アップロード ---------------------------->
<!----------------------------- インポート ----------------------------->
<h2>
    <table width="100%">
        <tr>
            <td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class"><?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900003'); ?></div></td>
            <td>
                <div id="Mix2_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" >
                </div>
            </td>
        </tr>
    </table>
</h2>

<div id="import_all">

<div id="Mix2_Nakami" style="display:block" class="text">
    <div style="margin-left: 10px;">
<?php if (strlen($resultMsg) > 0): ?>
            <?php echo $resultMsg; ?>
<?php else: ?>
<?php if (count($retImportAry) === 0): ?>
                <?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900004'); ?>
<?php else: ?>
                <form method="post" action="/menus/systems/<?php echo $g['page_dir']; ?>/03_data_import.php?no=<?php echo $g['page_dir']; ?>" id="import_form">
<?php if (count($retImportOpeAry) != 0): ?>
                <p><label><input type="checkbox" value="" id="import_whole_ope"><strong><font size="4"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900053');?></font></strong></label></p>
                <p style="margin-bottom: 10px;"></p>
<?php foreach($retImportOpeAry as $key => $value): ?>
                  <div class="import_all_div_ope">
                      <p>&nbsp;&nbsp;<label><input type="checkbox" name="import_ope_<?php echo $key; ?>[]" value="<?php echo $key; ?>" id="import_ope_<?php echo $key; ?>" class="import_all_ope"><?php echo $key . ':' . $value; ?></label></p>
                  </div>
<?php endforeach; ?>
                <p style="margin-top: 20px;"></p>
                <div id="importMsg" style="color: red;"></div>
                <input type="hidden" name="post_kind" value="import">
<?php endif; ?>
<?php if (count($retImportSymAry) != 0): ?>
                <p><label><input type="checkbox" value="" id="import_whole_sym"><strong><font size="4"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900054');?></font></strong></label></p>
                <p style="margin-bottom: 10px;"></p>
<?php foreach($retImportSymAry as $key => $value): ?>
                  <div class="import_all_div_sym">
                      <p>&nbsp;&nbsp;<label><input type="checkbox" name="import_sym_<?php echo $key; ?>[]" value="<?php echo $key; ?>" id="import_sym_<?php echo $key; ?>" class="import_all_sym"><?php echo $key . ':' . $value; ?></label></p>
                  </div>
<?php endforeach; ?>
                <p style="margin-top: 20px;"></p>
                <div id="importMsg" style="color: red;"></div>
                <input type="hidden" name="post_kind" value="import">
<?php endif; ?>
                <input type="submit" value="<?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900003'); ?>" id="importButton">
                </form>
<?php endif; ?>
<?php endif; ?>
            
    </div>
</div>

</div><!--import_all-->

<!----------------------------- インポート ----------------------------->
<?php
// 共通HTMLフッタパーツ
require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_html_footer.php');
