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
$tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
require_once ( $root_dir_path . '/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php');
require_once ( $root_dir_path . '/libs/webindividuallibs/systems/2100000211/web_parts_for_data_export.php');
// アクセスログ出力(RESULT:SUCCESS)
web_log($g['objMTS']->getSomeMessage('ITAWDCH-STD-603'));
$task_no = '';
if (isset($_SESSION['data_export_task_no']) === true && strlen($_SESSION['data_export_task_no']) > 0) {
    $task_no = $_SESSION['data_export_task_no'];
}
?>
<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
<div id="menu_on" style="display:none" class="text"><?php echo $menuOn; ?></div>
<!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
<script type="text/javascript">
$(window).on('load', function(){
    var menu_on = '#' + $('#menu_on').text();
    $(menu_on).addClass('menu-on');
});
</script>

<h2><?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900006'); ?></h2>
<?php if ($resultFlg === true): ?>
<form method="post" action="/default/menu/01_browse.php?no=2100000213&task_no=<?php echo $task_no;?>">
    <div style="margin:10px 0 0 10px;">
        <p><?php echo $resultMsg; ?></p>
        <input type="submit" value="<?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900008'); ?>">
        <input type="hidden" name="menu_on" value="<?php echo $menuOn;?>">
    </div>
</form>
<?php else: ?>
<div style="margin:10px 0 0 10px;">
    <p><?php echo $resultMsg; ?></p>
    <input type="button" value="<?php echo $g['objMTS']->getSomeMessage('ITAWDCH-MNU-1040060'); ?>" onClick="location.href='/default/menu/01_browse.php?no=<?php echo $g['page_dir']; ?>'">
    <input type="hidden" name="menu_on" value="<?php echo $menuOn;?>">
</div>
<?php endif;?>
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


