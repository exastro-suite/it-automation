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
 * メニュー作成画面
 */
$tmpAry = explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
require_once ( $root_dir_path . '/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php');
require_once ( $root_dir_path . '/libs/webindividuallibs/systems/2100160003/web_parts_for_create_menu.php');
?>
<script type="text/javascript">
$(function() {
    $('#create_all').on('click', function() {
        if($('#create_all').prop('checked')){
            $('._create_').prop('checked', true);
        }else{
            $('._create_').prop('checked', false);
        }
    });
    $('#submit').on('click', function() {
        if($('#create_menu_form ._create_:checkbox:checked').length > 0){
            if(window.confirm("<?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102510"); ?>")){
                return true;
            }else{
                return false;
            }
        }else{
            window.alert("<?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102504"); ?>");
            return false;
        }
    });
});
</script>

<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
<div id="sysJSCmdText01" style="display:none" class="text"><?php echo $strCmdWordAreaOpen; ?></div>
<div id="sysJSCmdText02" style="display:none" class="text"><?php echo $strCmdWordAreaClose; ?></div>
<div id="messageTemplate" style="display:none" class="text"><?php echo $strTemplateBody; ?></div>
<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
<!-------------------------------- 説明 -------------------------------->
<h2>
    <table width="100%">
        <tr>
            <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" ><?php echo $headerLabel1; ?></div></td>
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
        <?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102501");?>    
    </div>
</div>
<!-------------------------------- 説明 -------------------------------->
<!-------------------------------- メニュー作成-------------------------------->
<?php if (isset($_REQUEST['create']) === false) { ?>
<h2>
    <table width="100%">
        <tr>
            <td><div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" ><?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102502"); ?></div></td>
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
<?php if (strlen($resultMsg) > 0):?>
        <p><?php echo $resultMsg;?></p>
<?php else:?>
        <form method="post" action="/default/menu//01_browse.php?no=2100160003" id="create_menu_form">
            
            <h3><label><strong><input type="checkbox" value="" id="create_all"><?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102505");?></strong></label></h3>
                <div style="margin-left:20px;">
<?php foreach($menuNameAry as $key => $value): ?>
                <p><label><input type="checkbox" value="<?php echo $key; ?>" class="_create_" name="<?php echo $key; ?>[]" ><strong><?php echo $value; ?></strong></label></p>
<?php endforeach; ?>

                </div>
            <div id="exportMsg" style="color: red;"></div>
            <input type="submit" id="submit" value="<?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102503"); ?>">
            <input type="hidden" name="create" value="create">

        </form>
<?php endif;?>
    </div>
</div>
<?php }else{?>
<h2>
    <table width="100%">
        <tr>
            <td><div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" ><?php echo $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102502"); ?></div></td>
            <td>
                <div id="Mix1_Midashi" align="right">
                    <input type="button" value="<?php echo $strCmdWordAreaClose; ?>" class="showbutton" onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" >
                </div>
            </td>
        </tr>
    </table>
</h2>
<div id="Mix1_Nakami" style="display:block" class="text">
    <div style="margin-left: 20px;">
        <?php echo $resultMsg;?>
    </div>
</div>
<?php }?>
<!-------------------------------- メニュー作成 -------------------------------->
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
// 共通HTMLフッタパーツ
require_once ( $root_dir_path . '/libs/webcommonlibs/web_parts_html_footer.php');
?>
