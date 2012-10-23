<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile(__FILE__);

$store_password = COption::GetOptionString("main", "store_password", "Y");
$sDocPath = $APPLICATION->GetCurPage();

$popUpError = false;
if(!empty($arAuthResult))
{
	if(!is_array($arAuthResult))
		$arAuthResult = array("MESSAGE"=>$arAuthResult, "TYPE"=>"ERROR");

	if (class_exists("CAdminMessage"))
	{
		CAdminMessage::ShowMessage(array(
			"MESSAGE"=>($arAuthResult["TYPE"] == "ERROR"? GetMessage("admin_authorize_error"):GetMessage("admin_authorize_info")),
			"DETAILS"=>$arAuthResult["MESSAGE"],
			"TYPE"=>$arAuthResult["TYPE"]
		));
	}
	else
		$popUpError = $arAuthResult["MESSAGE"];
}

$bSecure = false;
if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
{
	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys()))
	{
		$sec->SetKeys($arKeys);
		$sec->AddToForm('form_auth', array('USER_PASSWORD'));
		$bSecure = true;
	}
}
?>
<form name="form_auth" method="post" target="_top" class="bx-admin-auth-form" action="<?echo htmlspecialchars($sDocPath."?login=yes".(($s=DeleteParam(array("logout", "login"))) == ""? "":"&".$s));?>">
<?
foreach($GLOBALS["HTTP_POST_VARS"] as $vname=>$vvalue):
	if($vname=="USER_LOGIN" || $vname=="USER_PASSWORD")
		continue;
	dump_post_var($vname, $vvalue);
?>

<?
endforeach;
function dump_post_var($vname, $vvalue, $var_stack=array())
{
	if(is_array($vvalue))
	{
		foreach($vvalue as $key=>$value)
			dump_post_var($key, $value, array_merge($var_stack ,array($vname)));
	}
	else
	{
		if(count($var_stack)>0)
		{
			$var_name=$var_stack[0];
			for($i=1; $i<count($var_stack);$i++)
				$var_name.="[".$var_stack[$i]."]";
			$var_name.="[".$vname."]";
		}
		else
			$var_name=$vname;
		?><input type="hidden" name="<?echo htmlspecialchars($var_name)?>" value="<?echo htmlspecialchars($vvalue)?>"><?
	}
}
?>
<input type="hidden" name="AUTH_FORM" value="Y">
<input type="hidden" name="TYPE" value="AUTH">

<div class="bx-auth-form">
	<div class="bx-auth-header"><?=GetMessage("AUTH_PLEASE_AUTH")?>	</div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td>
	<div class="bx-auth-picture"></div>
	</td>
	<td>
	<div class="bx-auth-table">

	<?if ($popUpError !== false):?>
		<span style="color:red"><?=$arAuthResult["MESSAGE"]?></span>
	<?endif?>

	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="bx-auth-label"><?=GetMessage("AUTH_LOGIN")?>:</td>
			<td><input type="text" name="USER_LOGIN" maxlength="50" size="20" value="<?echo htmlspecialchars($last_login)?>" class="bx-auth-input-text"></td>
		</tr>
		<tr>
			<td class="bx-auth-label"><?=GetMessage("AUTH_PASSWORD")?>:</td>
			<td>
			<input type="password" name="USER_PASSWORD" maxlength="50" size="20" class="bx-auth-input-text">
<?if($bSecure):?>
				<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
				<noscript>
				<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
				</noscript>
<script type="text/javascript">
document.getElementById('bx_auth_secure').style.display = 'inline-block';
</script>
<?endif?>
			</td>
		</tr>
<?if($store_password=="Y") :?>
		<tr>
			<td>&nbsp;</td>
			<td><input type="checkbox" name="USER_REMEMBER" value="Y" id="USER_REMEMBER_F">&nbsp;<label for="USER_REMEMBER_F" class="bx-label"><?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
		</tr>
<?endif;?>
<?if($APPLICATION->NeedCAPTHAForLogin($last_login)):
		$CAPTCHA_CODE = $APPLICATION->CaptchaGetCode();?>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="hidden" name="captcha_sid" value="<?echo $CAPTCHA_CODE?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $CAPTCHA_CODE?>" width="180" height="40" alt="CAPTCHA" />
			</td>
		</tr>
		<tr>
			<td nowrap><?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:</td>
			<td><input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
<?endif;?>
		<tr>
			<td></td>
			<td><input type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>"></td>
		</tr>
	</table>
	</div>
	<br clear="all">
	</td>
</tr>
</table>

<?if($not_show_links!="Y"):?>
	<div class="bx-auth-footer">
		<p><b><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></b></p>
		<p><?=GetMessage("AUTH_GO")?> <a id="bx_forgot_password" href="<?echo htmlspecialchars($sDocPath."?forgot_password=yes".($s<>""? "&".$s:""));?>"><?=GetMessage("AUTH_GO_AUTH_FORM")?></a>.</p>
		<p><?=GetMessage("AUTH_MESS_1")?> <a id="bx_change_password" href="<?echo htmlspecialchars($sDocPath."?change_password=yes".($s<>""? "&".$s:""));?>"><?=GetMessage("AUTH_CHANGE_FORM")?></a>.</p>
	</div>
<?endif;?>
</div>
<?=bitrix_sessid_post()?>
</form>

<script type="text/javascript">
try{
<?if($last_login <> ""):?>
document.form_auth.USER_PASSWORD.focus();
<?else:?>
document.form_auth.USER_LOGIN.focus();
<?endif;?>
} catch (e) {}
</script>