<?php
$getCopy = $_GET;
$get_parameter = "";
$get_parameter_anp = "";
if("" != http_build_query($getCopy)){
    $get_parameter = "?" . http_build_query($getCopy);
    $get_parameter_anp = "&" . http_build_query($getCopy);
}
$get_parameter = str_replace('+', '%20', $get_parameter);
$get_parameter_anp = str_replace('+', '%20', $get_parameter_anp);

$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
?>

<?= $strLoginFormHeadBody ?>
<div id="gateLoginContainer" class="gateContainer">
 <form id="gateLoginForm" class="inputUserInfoForm" method="POST" name="loginform" action="<?= $strGateUrl ?><?= $get_parameter ?>">
   <table id="gateLoginItemTable" class="headerLeftTable inputItemTable" border="0" aria-describedby="">
     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
     <tr>
       <th scope="col" class="inputItemExplain"><?= $strLoginIDCaption ?></th>
       <td class="inputItemWrapper"><input class="inputUserId" type="text" name="username" /></td>
     </tr>
     <tr>
       <th scope="col" class="inputItemExplain"><?= $strLoginPWCaption ?></th>
       <td class="inputItemWrapper"><div class="input_password"><input class="inputUserPw" type="password" name="password" /><div class="password_eye"></div></td>
     </tr>
   </table>
   <input id="loginTryExecute" class="loginGateSubmitElement tryExecute" type="submit" name="login" value="<?= $strLoginActionCaption ?>" />
 </form>
</div>

<?php if (!empty($arySsoProviderList) && array_sum(array_column($arySsoProviderList, 'visibleFlag')) > 0) { ?>
<div id="ssoLoginContainer">
  <div class="ssoLoginTitle"><span class="ssoLoginTitleText"><?= $objMTS->getSomeMessage("ITAWDCH-STD-1011") ?></span></div>
    <ul class="ssoLoginList">
    <?php foreach ($arySsoProviderList as $item) { ?>
      <?php if ($item['visibleFlag'] === '1') { ?>

      <li class="ssoLoginItem">
        <a href="/common/common_sso_auth.php?<?= $item['authType'] ?>&providerId=<?= $item['providerId'] ?><?= $get_parameter_anp ?>">
          <span class="ssoLoginLinkInner">
          <?php if (!empty($item['providerLogo'])) { ?>

            <span class="ssoLoginIcon">
              <span class="ssoLoginIconImage" style="background-image: url(<?= $item['providerLogo'] ?>);"></span>
            </span>

          <?php } ?>

            <span class="ssoLoginAccountType"><?= $objMTS->getSomeMessage("ITAWDCH-STD-1012",$item['providerName']) ?></span>
          </span>
        </a>
      </li>
      <?php } ?>
    <?php } ?>

    </ul>
  </div>
</div>
<?php } ?>
<?= $strLoginFormTailBody ?>
