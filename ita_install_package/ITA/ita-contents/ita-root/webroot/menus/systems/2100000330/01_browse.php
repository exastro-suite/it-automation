<?php
//   Copyright 2021 NEC Corporation
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
require_once ( $root_dir_path . '/libs/webindividuallibs/systems/2100000330/web_parts_for_data_import.php');
?>
<?php if (is_array($retImportAry)): ?>
<?php if (count($retImportAry) > 0): ?>
<script type="text/javascript">
$(function() {
    $('#import_whole').on('click', function() {
        $('.import_all').prop('checked', this.checked).change();
    });

    $('.import_all').on('click', function() {
        if ($('.import_all_div :checked').length == $('.import_all_div :input').length) {
            $('#import_whole').prop('checked', true);
        } else {
            $('#import_whole').prop('checked', false);
        }
    });
<?php foreach($retImportAry as $key => $value): ?>
    $('#import_all_<?php echo $key; ?>').change(function() {
        $('.import_<?php echo $key; ?>').prop('checked', this.checked);
    });

    $('.import_<?php echo $key; ?>').change(function() {
        if ($('#import_div_<?php echo $key; ?> :checked').length == $('#import_div_<?php echo $key; ?> :input').length) {
            $('#import_all_<?php echo $key; ?>').prop('checked', true);
        } else {
            $('#import_all_<?php echo $key; ?>').prop('checked', false);
        }
        if ($('.import_all_div :checked').length == $('.import_all_div :input').length) {
            $('#import_whole').prop('checked', true);
        } else {
            $('#import_whole').prop('checked', false);
        }
    });
<?php endforeach; ?>
    show('Mix2_Midashi', 'Mix2_Nakami');
});
</script>
<?php endif; ?>
<?php else: ?>
            <div id="filter_alert_area" class="alert_area" style="display:block" ><?php echo $retImportAry; ?></div>

<?php endif; ?>
<?php
$timeStamp_style_css = filemtime("$root_dir_path/webroot/menus/systems/2100000330/style.css");
print <<< EOD
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/menus/systems/2100000330/style.css?{$timeStamp_style_css}">
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
        <th scope="col"><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class"><?php echo $headerLabel1; ?></div></th>
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
             <?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_7');?>
         </p>
    </div>
</div>
<!-------------------------------- 説明 -------------------------------->
<!---------------------------- アップロード ---------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
        <th scope="col"><div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class"><?php echo $uploadLabel1; ?></div></th>
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
            <p><input type="submit" value="<?php echo $uploadLabel1; ?>" id="zipInputSubmit"></p>
            <input type="hidden" name="post_kind" value="upload">
            <input type="hidden" name="menu_on" value="" class="menu_on">
        </form>
    </div>
</div>
<!---------------------------- アップロード ---------------------------->
<!----------------------------- インポート ----------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
        <th scope="col"><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class"><?php echo $importLabel1; ?></div></th>
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
                <?php echo $importLabel2; ?>
<?php else: ?>

            </div>
                <form method="post" action="/menus/systems/<?php echo $g['page_dir']; ?>/03_data_import.php?no=<?php echo $g['page_dir']; ?>" id="import_form">
                <p><label><input type="checkbox" value="" id="import_whole"><strong><span class="export_whole"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-900018');?></span></strong></label></p>
                <p style="margin-bottom: 10px;"></p>
<div id="table_area" class="table_area">
    <div class="fakeContainer_Filter1Print">
        <div id="Mix1_1_itaTable" class="itaTable def tableSticky">
            <div id="Mix1_1_itaTableBody" class="itaTableBody scrollRight">
                <div class="tableScroll">
                    <table id="Mix1_1" aria-describedby="">
                        <tr class="defaultExplainRow">
                            <th class="tdInner checkbox_col" scope="col"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_1');?></th>
                            <th scope="col"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_2');?></th>
                            <th scope="col"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_3');?></th>
                            <th scope="col"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_4');?></th>
                            <th scope="col"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_5');?></th>
                            <th scope="col"><?php echo $objMTS->getSomeMessage('ITABASEH-MNU-2100000330_6');?></th>
                        </tr>
                        <?php foreach($retImportAry as $menuGroupId => $menuGroupInfo): ?>
                            <?php foreach($menuGroupInfo['menu'] as $menuInfo): ?>
                                <tr class="menu_id_label">
                                    <td class="thSticky left">
                                        <input class="menu_id_checkbox" type="checkbox" <?php if($menuInfo['disabled']==1)echo 'disabled checked="false"'; ?> name="import_<?php echo $menuGroupId; ?>[]" value="<?php echo $menuInfo['menu_id']; ?>" id="<?php echo $menuGroupId; ?>" class="import_<?php echo $menuGroupId; ?> menu">
                                    </td>
                                    <td style="max-width: 80px">
                                        <div class="tdInner"><?php echo $menuGroupInfo["menu_group_name"]; ?></div>
                                    </td>
                                    <td style="max-width: 200px">
                                        <div class="tdInner"><?php echo $menuInfo["menu_name"]; ?></div>
                                    </td>
                                    <td style="max-width: 70px">
                                        <div class="tdInner"><?php echo $menuInfo["menu_id"]; ?></div>
                                    </td>
                                    <td style="max-width: 300px">
                                        <div class="tdInner"><?php echo $menuInfo["file_name"]; ?></div>
                                    </td>
                                    <td style="max-width: 220px">
                                        <div class="tdInner"><?php if (isset($menuInfo["error"])) echo $menuInfo["error"]; ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
                <div id="importMsg" style="color: red;"></div>
                <input type="submit" value="<?php echo $importLabel1; ?>" id="importButton" name="importButton">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="hidden" name="post_kind" value="import">
                <input type="hidden" name="menu_on" value="" class="menu_on">
                </form>
                <script type="text/javascript">itaTable( 'Mix1_1' );</script>
<?php endif; ?>
<?php endif; ?>
            
    </div>
</div>

</div><!--import_all-->

<!----------------------------- インポート ----------------------------->
<?php
// 共通HTMLフッタパーツ
require_once ( $root_dir_path . '/libs/webcommonlibs/web_parts_html_footer.php');
