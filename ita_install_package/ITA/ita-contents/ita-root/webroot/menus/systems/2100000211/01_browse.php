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
 * データエクスポート画面
 *
 */
$tmpAry = explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

require_once ( $root_dir_path . '/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php');
require_once ( $root_dir_path . '/libs/webindividuallibs/systems/2100000211/web_parts_for_data_export.php');
?>
<script type="text/javascript">
$(function() {
    $('#export_whole').on('click', function() {
        $('.export_all').prop('checked', this.checked).change();
    });

    $('.export_all').on('click', function() {
        if ($('.export_all_div :checked').length == $('.export_all_div :input').length) {
            $('#export_whole').prop('checked', true);
        } else {
            $('#export_whole').prop('checked', false);
        }
    });

<?php foreach($retExportAry as $key => $value): ?>
    $('#export_all_<?php echo $key; ?>').change(function() {
        $('.export_<?php echo $key; ?>').prop('checked', this.checked);
    });

    $('.export_<?php echo $key; ?>').change(function() {
        if ($('#export_div_<?php echo $key; ?> :checked').length == $('#export_div_<?php echo $key; ?> :input').length) {
            $('#export_all_<?php echo $key; ?>').prop('checked', true);
        } else {
            $('#export_all_<?php echo $key; ?>').prop('checked', false);
        }
        if ($('.export_all_div :checked').length == $('.export_all_div :input').length) {
            $('#export_whole').prop('checked', true);
        } else {
            $('#export_whole').prop('checked', false);
        }
    });

<?php endforeach; ?> 
});
</script>
<?php
$timeStamp_style_css = filemtime("$root_dir_path/webroot/menus/systems/2100000211/style.css");
print <<< EOD
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/menus/systems/2100000211/style.css?{$timeStamp_style_css}">
EOD;
?>

<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
<div id="sysJSCmdText01" style="display:none" class="text"><?php echo $strCmdWordAreaOpen; ?></div>
<div id="sysJSCmdText02" style="display:none" class="text"><?php echo $strCmdWordAreaClose; ?></div>
<div id="messageTemplate" style="display:none" class="text"><?php echo $strTemplateBody; ?></div>
<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->

<!-------------------------------- 説明 -------------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
            <th scope="col"><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" ><?php echo $headerLabel1; ?></div></th>
            <td>
                <div id="SetsumeiMidashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" >
                </div>
            </td>
        </tr>
    </table>
</h2>
<div id="SetsumeiNakami" style="display:block" class="text">
    <div style="margin-left:10px">
        <?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900012');?>    
    </div>
</div>
<!-------------------------------- 説明 -------------------------------->
<!-------------------------------- エクスポート-------------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
            <th scope="col"><div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" ><?php echo $exportLabel1; ?></div></th>
            <td>
                <div id="Mix1_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" >
                </div>
            </td>
        </tr>
    </table>
</h2>

<?php if (strlen($resultMsg) > 0):?>
<div id="Mix1_Nakami" style="display:block" class="text">
    <div style="margin-left:20px;">
        <p><?php echo $resultMsg;?></p>
    </div>
</div>
<?php else:?>
<form method="post" action="/menus/systems/<?php echo $g['page_dir']; ?>/03_data_export.php?no=<?php echo $g['page_dir']; ?>" id="export_form">
    <div id="Mix2_Nakami" style="display:block" class="text">
        <div style="margin-left:20px;">
            <div>
                <div class="export_choose_title"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900025');?></div>
                <div>
                    <p class="export_radio_label_block">
                        <label id="export_radio1" class="export_label">
                            <input type="radio" class="export_radio" value="1" name="dp_mode" for="export_radio1" checked="true">
                            <span class="export_label_name"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900026');?></span>
                        </label>
                    </p>
                    <p class="export_radio_label_block">
                        <label id="export_radio2" class="export_label">
                            <input type="radio" class="export_radio" value="2" name="dp_mode" for="export_radio2">
                            <span class="export_label_name"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900027');?></span>
                            <span class="export_label_input"><input id="bookdatetime" name="specified_timestamp" type="text" maxlength="16" disabled="disabled"></span>
                        </label>
                    </p>
                </div>
            </div>
            <div>
                <div class="export_choose_title"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900028');?></div>
                <div>
                    <p class="export_radio_label_block">
                        <label id="export_radio3" class="export_label">
                            <input type="radio" class="export_radio" value="1" name="abolished_type" for="export_radio3" checked="true">
                            <span class="export_label_name"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900029');?></span>
                        </label>
                    </p>
                    <p class="export_radio_label_block">
                        <label id="export_radio4" class="export_label">
                            <input type="radio" class="export_radio" value="2" name="abolished_type" for="export_radio3">
                            <span class="export_label_name"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900030');?></span>
                        </label>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div id="Mix1_Nakami" style="display:block" class="text">
        <div style="margin-left:20px;">
            <p><label><input type="checkbox" value="" id="export_whole"><strong><span class="export_whole"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900018');?></span></strong></label></p>
            <p style="margin-bottom: 10px;"></p>
<?php foreach($retExportAry as $key => $value): ?>
            <div class="export_all_div">
            <p>&nbsp;&nbsp;<label><input type="checkbox" value="" id="export_all_<?php echo $key; ?>" class="export_all"><strong><?php echo $value['menu_group_name']; ?></strong></label></p>
            </div>
            <div id="export_div_<?php echo $key;?>">
<?php foreach($value['menu'] as $menu): ?>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo $key; ?>[]" value="<?php echo $menu['menu_id']; ?>" id="<?php echo $key; ?>" class="export_<?php echo $key; ?>"><?php echo $menu['menu_name']; ?></label></p>
<?php endforeach; ?>
            </div>
            <p style="margin-bottom: 20px;"></p>
<?php endforeach; ?>
            <div id="exportMsg" style="color: red;"></div>
            <input type="submit" value="<?php echo $exportLabel1; ?>">
            <input type="hidden" name="zip" value="export">
            <input type="hidden" name="menu_on" value="" class="menu_on">
        </div>
    </div>
</form>
<?php endif;?>
<!-------------------------------- エクスポート -------------------------------->

<?php
// 共通HTMLフッタパーツ
require_once ( $root_dir_path . '/libs/webcommonlibs/web_parts_html_footer.php');
