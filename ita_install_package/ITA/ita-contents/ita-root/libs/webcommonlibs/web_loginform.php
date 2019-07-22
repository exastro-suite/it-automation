<?php print($strLoginFormHeadBody); ?>
<div id="gateLoginContainer" class="gateContainer">
 <form id="gateLoginForm" class="inputUserInfoForm" method="POST" name="loginform" action="<?php print($strGateUrl); ?>?login&grp=<?php print($ASJTM_grp_id); ?>&no=<?php print($ASJTM_id); ?>">
   <table id="gateLoginItemTable" class="headerLeftTable inputItemTable" border="0">
     <tr>
       <th class="inputItemExplain"><?php print($strLoginIDCaption); ?></th>
       <td class="inputItemWrapper"><input class="inputUserId" type="text" name="username" /></td>
     </tr>
     <tr>
       <th class="inputItemExplain"><?php print($strLoginPWCaption); ?></th>
       <td class="inputItemWrapper"><input class="inputUserPw" type="password" name="password" /></td>
     </tr>
   </table>
   <input id="loginTryExecute" class="loginGateSubmitElement tryExecute" type="submit" name="login" value="<?php print($strLoginActionCaption); ?>" />
 </form>
</div>
<?php print($strLoginFormTailBody); ?>
