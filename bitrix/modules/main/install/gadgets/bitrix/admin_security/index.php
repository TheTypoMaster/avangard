<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/admin_security/styles.css');

$aGlobalOpt = CUserOptions::GetOption("global", "settings", array());
$bShowSecurity = (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/install/index.php") && $aGlobalOpt['messages']['security'] <> 'N');

if (!$bShowSecurity)
	return false;

$bSecModuleInstalled = CModule::IncludeModule("security");
if($bSecModuleInstalled){
	$bSecurityFilter = CSecurityFilter::IsActive();
	if($bSecurityFilter){
		$lamp_class = " bx-gadgets-info";
		$text2_class = "green";
		$securityEventsCount = CSecurityFilter::GetEventsCount();
		if($securityEventsCount > 0){
			$text2 = GetMessage("GD_SECURITY_EVENT_COUNT");
		} else {
			$text2 = GetMessage("GD_SECURITY_EVENT_COUNT_EMPTY");
		}
		if($securityEventsCount > 999){
			$securityEventsCount = round($securityEventsCount/1000,1).'K';
		}
	} else {
		$lamp_class = " bx-gadgets-note";
		$text2_class = "red";
		$text2 = GetMessage("GD_SECURITY_FILTER_OFF_DESC");
	}
} else {
	$lamp_class = "";
	$text2_class = "red";
	$text2 = GetMessage("GD_SECURITY_MODULE");
}

?><table class="bx-gadgets-content-layout"><?
	?><tr><?
		?><td><div class="bx-gadgets-title"><?=GetMessage("GD_SECURITY_TITLE")?></div></td><?
		?><td><div class="bx-gadgets-title2">Web Application<br>Firewall</div></td><?
	?></tr><?
	?><tr class="bx-gadget-bottom-cont<?=((!$bSecModuleInstalled && $GLOBALS["USER"]->CanDoOperation('edit_other_settings')) || ($bSecModuleInstalled && $GLOBALS["APPLICATION"]->GetGroupRight("security") >= "W") ? " bx-gadget-bottom-button-cont" : "")?>"><?

		if (!$bSecModuleInstalled && $GLOBALS["USER"]->CanDoOperation('edit_other_settings'))
		{
			?><td class="bx-gadgets-colourful-cell"><?
				?><a class="bx-gadget-button bx-gadget-button-clickable" href="/bitrix/admin/module_admin.php?id=security&install=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>">
					<div class="bx-gadget-button-lamp"></div>
					<div class="bx-gadget-button-text"><?=GetMessage("GD_SECURITY_MODULE_INSTALL")?></div>
				</a><?
			?></td><?
			?><td class="bx-gadgets-colourful-cell"><?
			?></td><?
		}
		elseif ($bSecModuleInstalled && $GLOBALS["APPLICATION"]->GetGroupRight("security") >= "W")
		{
			?><td class="bx-gadgets-colourful-cell"><?
				?><a class="bx-gadget-button bx-gadget-button-clickable<?=($bSecurityFilter ? " bx-gadget-button-active" : "")?>" href="/bitrix/admin/security_filter.php?lang=<?=LANGUAGE_ID?>">
					<div class="bx-gadget-button-lamp"></div>
					<div class="bx-gadget-button-text"><?=($bSecurityFilter ? GetMessage("GD_SECURITY_FILTER_ON") : GetMessage("GD_SECURITY_FILTER_OFF"))?></div>
				</a><?
			?></td><?
			?><td class="bx-gadgets-colourful-cell"><?
				if ($bSecurityFilter && $securityEventsCount > 0)
				{
					?><div class="bx-gadget-events"><?=$securityEventsCount?></div><?
				}
				?><div class="bx-gadget-desc"><?=$text2?></div><?
			?></td><?
	}

?></tr>
</table>
<div class="bx-gadget-shield"></div>