<?php
//   Copyright 2020 NEC Corporation
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
 * 連携先Terraform管理
 *
 */
$tmpAry = explode('ita-root', dirname(__FILE__));
$root_dir_path = $tmpAry[0] . 'ita-root';
unset($tmpAry);

require_once($root_dir_path . '/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php');
//----メッセージtmpl作成準備
$aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC", "STD", "_js");
$aryImportFilePath[] = $objMTS->getTemplateFilePath("ITATERRAFORM", "STD", "_js");
$strTemplateBody = getJscriptMessageTemplate($aryImportFilePath, $objMTS);
//メッセージtmpl作成準備----
?>
<?php
$timeStamp_style_css = filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/style.css");
print <<< EOD
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/style.css?{$timeStamp_style_css}">
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
            <th scope="col">
                <div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class"><?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106010'); ?></div>
            </th>
            <td>
                <div id="SetsumeiMidashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');">
                </div>
            </td>
        </tr>
    </table>
</h2>
<div id="SetsumeiNakami" style="display:block" class="text">
    <div style="margin-left:10px">
        <?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106020'); ?>
    </div>
</div>
<!-------------------------------- 説明 -------------------------------->
<!---------------------------- Organization登録管理 ---------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
            <th scope="col">
                <div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class"><?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106030'); ?></div>
            </th>
            <td>
                <div id="Mix1_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');">
                </div>
            </td>
        </tr>
    </table>
</h2>
<div class="open" style="display: block;">
    <div id="Mix1_Nakami" style="display:block" class="text">
        <div style="margin-left:20px;">
            <p><input type="button" value="<?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106040'); ?>" id="getOrganization" onClick=location.href="javascript:getOrganizationData();"></p>
        </div>
        <div class="table_area">
        </div>
    </div>
</div>
<!---------------------------- Organization登録管理 ---------------------------->
<!---------------------------- Workspace登録管理 ---------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
            <th scope="col">
                <div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class"><?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106050'); ?></div>
            </th>
            <td>
                <div id="Mix2_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');">
                </div>
            </td>
        </tr>
    </table>
</h2>
<div class="open" style="display: block;">
    <div id="Mix2_Nakami" style="display:block" class="text">
        <div style="margin-left:20px;">
            <p><input type="button" value="<?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106060'); ?>" id="getWorkspace" onClick=location.href="javascript:getWorkspaceData();"></p>
        </div>
        <div class="table_area">
        </div>
    </div>
</div>
<!---------------------------- Workspace登録管理 ---------------------------->
<!---------------------------- Policy登録管理 ---------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
            <th scope="col">
                <div onClick=location.href="javascript:show('Mix3_Midashi','Mix3_Nakami');" class="midashi_class"><?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106070'); ?></div>
            </th>
            <td>
                <div id="Mix3_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix3_Midashi','Mix3_Nakami');">
                </div>
            </td>
        </tr>
    </table>
</h2>
<div class="open" style="display: block;">
    <div id="Mix3_Nakami" style="display:block" class="text">
        <div style="margin-left:20px;">
            <p><input type="button" value="<?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106080'); ?>" id="getPolicy" onClick=location.href="javascript:getPolicyData();"></p>
        </div>
        <div class="table_area">
        </div>
    </div>
</div>
<!---------------------------- Policy登録管理 ---------------------------->
<!---------------------------- PolicySet登録管理 ---------------------------->
<h2>
    <table width="100%" aria-describedby="">
        <tr>
            <th scope="col">
                <div onClick=location.href="javascript:show('Mix4_Midashi','Mix4_Nakami');" class="midashi_class"><?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106090'); ?></div>
            </th>
            <td>
                <div id="Mix4_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix4_Midashi','Mix4_Nakami');">
                </div>
            </td>
        </tr>
    </table>
</h2>
<div class="open" style="display: block;">
    <div id="Mix4_Nakami" style="display:block" class="text">
        <div style="margin-left:20px;">
            <p><input type="button" value="<?php echo $objMTS->getSomeMessage('ITATERRAFORM-MNU-106100'); ?>" id="getPolicySet" onClick=location.href="javascript:getPolicySetData();"></p>
        </div>
        <div class="table_area">
        </div>
    </div>
</div>
<!---------------------------- PolicySet登録管理 ---------------------------->

<?php
//   Copyright 2020 NEC Corporation
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
// 共通HTMLフッタパーツ
require_once($root_dir_path . '/libs/webcommonlibs/web_parts_html_footer.php');
