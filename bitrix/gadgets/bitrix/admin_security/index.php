<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<style type="text/css">
.bx-gadgets-security .bx-gadgets-top-title {background:url("/bitrix/gadgets/bitrix/admin_security/images/gadgets-sprite.png") no-repeat 0 -52px; height:25px; margin-left:8px; padding-left:29px;}
* html .bx-gadgets-security .bx-gadgets-top-title {height:33px;}
</style>
<?
$aGlobalOpt = CUserOptions::GetOption("global", "settings", array());
$bShowSecurity = (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/install/index.php") && $aGlobalOpt['messages']['security'] <> 'N');

if (!$bShowSecurity)
	return false;

$bSecModuleInstalled = CModule::IncludeModule("security");
if($bSecModuleInstalled):
	$bSecurityFilter = CSecurityFilter::IsActive();
	if($bSecurityFilter):
		$lamp_class = " bx-gadgets-info";
		$text1 = GetMessage("GD_SECURITY_ON");
		$text2_class = "green";
		$text2 = GetMessage("GD_SECURITY_FILTER_ON");
		$text3 = GetMessage("GD_SECURITY_LEVEL", array("#LANGUAGE_ID#"=>LANGUAGE_ID));
	else:
		$lamp_class = " bx-gadgets-note";	
		$text1 = GetMessage("GD_SECURITY_CHECK");
		$text2_class = "red";
		$text2 = GetMessage("GD_SECURITY_FILTER_OFF");
		$text3 = '<p>'.GetMessage("GD_SECURITY_FILTER_DESC").'</p><form method="get" action="security_filter.php"><input type="hidden" name="lang" value="'.LANGUAGE_ID.'"><input type="submit" name="" value="'.GetMessage("GD_SECURITY_FILTER_TURN_ON").'"'.($GLOBALS["APPLICATION"]->GetGroupRight("security")<"W" ? " disabled" : "").'></form>';
	endif;
else:
	$lamp_class = "";
	$text1 = GetMessage("GD_SECURITY_OFF");
	$text2_class = "red";
	$text2 = GetMessage("GD_SECURITY_MODULE");
	$text3 = '<p>'.GetMessage("GD_SECURITY_MODULE_DESC").'</p><form method="get" action="module_admin.php"><input type="hidden" name="lang" value="'.LANGUAGE_ID.'"><input type="hidden" name="id" value="security">'.bitrix_sessid_post().'<input type="submit" name="install" value="'.GetMessage("GD_SECURITY_MODULE_INSTALL").'"'.(!$GLOBALS["USER"]->CanDoOperation('edit_other_settings') ? " disabled" : "").'></form>';
endif;
?>
<div class="bx-gadgets-warning<?=$lamp_class?>">
	<div class="bx-gadgets-warning-cont-ball"><?=$text1?></div>
	<div class="bx-gadgets-warning-bord"></div>
	<div class="bx-gadgets-warning-bord2"></div>
	<div class="bx-gadgets-warning-text-<?=$text2_class?>">
		<div class="bx-gadgets-warning-cont"><?=$text2?></div>
	</div>
	<div class="bx-gadgets-warning-bord2"></div>
	<div class="bx-gadgets-warning-bord"></div>
</div>
<div class="bx-gadgets-text"><?=$text3?></div>