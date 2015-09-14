<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	if (($arParams["TMPLT_SHOW_MENU"] == "BOTH") || ($arParams["TMPLT_SHOW_MENU"] == "TOP"))
	{
	?><?$APPLICATION->IncludeComponent(
		"bitrix:forum.menu",
		"",
		array(
			"FID" =>  $arResult["FID"],
			"TID" =>  $arResult["TID"],
			"PAGE_NAME" =>  $arResult["PAGE_NAME"],
			
			"URL_TEMPLATES_INDEX" => $arResult["URL_TEMPLATES_INDEX"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_PROFILE_VIEW" =>  $arResult["URL_TEMPLATES_PROFILE_VIEW"],
			"URL_TEMPLATES_SUBSCR_LIST" =>  $arResult["URL_TEMPLATES_SUBSCR_LIST"],
			"URL_TEMPLATES_ACTIVE" =>  $arResult["URL_TEMPLATES_ACTIVE"],
			"URL_TEMPLATES_SEARCH" =>  $arResult["URL_TEMPLATES_SEARCH"],
			"URL_TEMPLATES_HELP" =>  $arResult["URL_TEMPLATES_HELP"],
			"URL_TEMPLATES_RULES" =>  $arResult["URL_TEMPLATES_RULES"],
			"URL_TEMPLATES_USER_LIST" =>  $arResult["URL_TEMPLATES_USER_LIST"],
			"URL_TEMPLATES_PM_LIST" =>  $arResult["URL_TEMPLATES_PM_LIST"],
			"URL_TEMPLATES_PM_EDIT" =>  $arResult["URL_TEMPLATES_PM_EDIT"],
			"URL_TEMPLATES_PM_FOLDER" =>  $arResult["URL_TEMPLATES_PM_FOLDER"],
			"PATH_TO_AUTH_FORM" =>  $arResult["PATH_TO_AUTH_FORM"],
			
			"FID_RANGE" => $arParams["FID"],
			"SHOW_FORUM_ANOTHER_SITE" =>  $arParams["SHOW_FORUM_ANOTHER_SITE"],
			"AJAX_TYPE" => $arParams["AJAX_TYPE"],
			
			"CACHE_TIME" => $arResult["CACHE_TIME"],
			"CACHE_TYPE" => $arResult["CACHE_TYPE"],
			
			"TMPLT_SHOW_AUTH_FORM" =>  $arParams["TMPLT_SHOW_AUTH_FORM"],
			"SHOW_ADD_MENU" => ($arParams["SHOW_ADD_MENU"] == "Y" ? "Y" : "N")
		),
		$component
	);
	?><div class="forum-br"></div><?
	}
	if (strLen(trim($arParams["TMPLT_SHOW_TOP"])) > 0)
	{
		?><?=$arParams["~TMPLT_SHOW_TOP"]?><?
		?><div class="forum-br"></div><?
	}

	?><?$APPLICATION->IncludeComponent(
		"bitrix:forum.index",
		"",
		array(
			"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
			"URL_TEMPLATES_PROFILE_VIEW" =>  $arResult["URL_TEMPLATES_PROFILE_VIEW"],
			"URL_TEMPLATES_MESSAGE_APPR" =>  $arResult["URL_TEMPLATES_MESSAGE_APPR"],
			
			"FORUMS_PER_PAGE" => $arResult["FORUMS_PER_PAGE"],
			"PAGE_NAVIGATION_TEMPLATE" => $arParams["PAGE_NAVIGATION_TEMPLATE"],
			"FID" =>  $arParams["FID"],
			"DATE_FORMAT" =>  $arResult["DATE_FORMAT"],
			"DATE_TIME_FORMAT" =>  $arResult["DATE_TIME_FORMAT"],
			"WORD_LENGTH" => $arParams["WORD_LENGTH"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			
			"SHOW_FORUM_ANOTHER_SITE" =>  $arResult["SHOW_FORUM_ANOTHER_SITE"],
			"SHOW_FORUMS_LIST" =>  "Y",
			
			"SET_TITLE" => $arResult["SET_TITLE"],
			"CACHE_TIME" => $arResult["CACHE_TIME"],
			"CACHE_TYPE" => $arResult["CACHE_TYPE"],
			
			"TMPLT_SHOW_ADDITIONAL_MARKER" => $arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"],
		),
		$component
	);
	?><div class="forum-br"></div><?
	
	if (strLen($arParams["TMPLT_SHOW_BOTTOM"]) > 0):
		?><div class="forum-index-bottom"><?
		if ($arParams["TMPLT_SHOW_BOTTOM"] != "SET_BE_READ"):
			?><?=$arParams["~TMPLT_SHOW_BOTTOM"]?><?
		else:
			?><a href="<?=htmlspecialcharsEx($APPLICATION->GetCurPageParam("ACTION=SET_BE_READ", array("ACTION", "sessid", BX_AJAX_PARAM_ID)))?>" title="<?=GetMessage("F_MARK_AS_READED")?>" class="forum-read"><?=GetMessage("F_MARK_AS_READED_DO")?></a><?
		endif;
		?></div><div class="forum-br"></div><?
	endif;

	?><?$APPLICATION->IncludeComponent("bitrix:forum.statistic", ".default", Array(
		"FID"	=>	0,
		"TID"	=>	0,
		"PERIOD"	=>	$arParams["TIME_INTERVAL_FOR_USER_STAT"],
		"SHOW"	=>	array("STATISTIC", "BIRTHDAY", "USERS_ONLINE"),
		"SHOW_FORUM_ANOTHER_SITE"	=>	$arParams["SHOW_FORUM_ANOTHER_SITE"],
		"FORUM_ID"	=>	$arParams["FID"],
		
		"URL_TEMPLATES_PROFILE_VIEW"	=>	$arResult["URL_TEMPLATES_PROFILE_VIEW"],
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"WORD_LENGTH"	=>	$arParams["WORD_LENGTH"]
		),
		$component
	);
	
?><div class="forum-br"></div>
<table class="clear" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr valign="top">
	<td width="50%">
	<div class="forum-legend">
	<div><div class="icon-new-message"></div> <?=GetMessage("F_INFO_NEW_MESS")?></div>
	<div><div class="icon-no-message"></div> <?=GetMessage("F_INFO_NO_MESS")?></div><?
	if ($GLOBALS["USER"]->IsAdmin()):
	?><div><div class="icon-na"></div> <?=GetMessage("F_INFO_NA")?></div><?
	endif;
	?></div>
	</td>
	<td width="50%"><?
	
	if ($arParams["USE_RSS"] == "Y" && !empty($arParams["RSS_FID_RANGE"])):
	?><div class="forum-other">
	<?$APPLICATION->IncludeComponent("bitrix:forum.rss", ".default", 
		Array(
			"TYPE_RANGE"	=>	$arParams["RSS_TYPE_RANGE"],
			"IID"	=>	0,
			"MODE_TEMPLATE"	=>	"link",
			"URL_TEMPLATES_RSS"	=>	$arResult["URL_TEMPLATES_RSS"],
			
			"CACHE_TYPE"	=>	$arParams["RSS_CACHE"],
			"CACHE_TIME"	=>	$arParams["CACHE_TYPE"]
			), $component
		);?></div><?
	endif;?>
	</td>
</tr>
</table><?

	if (($arParams["TMPLT_SHOW_MENU"] == "BOTH") || ($arParams["TMPLT_SHOW_MENU"] == "BOTTOM"))
	{
	?><br/><?
	$APPLICATION->IncludeComponent(
		"bitrix:forum.menu",
		"",
		array(
			"FID" =>  $arResult["FID"],
			"TID" =>  $arResult["TID"],
			"PAGE_NAME" =>  $arResult["PAGE_NAME"],
			
			"URL_TEMPLATES_INDEX" => $arResult["URL_TEMPLATES_INDEX"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_PROFILE_VIEW" =>  $arResult["URL_TEMPLATES_PROFILE_VIEW"],
			"URL_TEMPLATES_SUBSCR_LIST" =>  $arResult["URL_TEMPLATES_SUBSCR_LIST"],
			"URL_TEMPLATES_ACTIVE" =>  $arResult["URL_TEMPLATES_ACTIVE"],
			"URL_TEMPLATES_SEARCH" =>  $arResult["URL_TEMPLATES_SEARCH"],
			"URL_TEMPLATES_HELP" =>  $arResult["URL_TEMPLATES_HELP"],
			"URL_TEMPLATES_RULES" =>  $arResult["URL_TEMPLATES_RULES"],
			"URL_TEMPLATES_USER_LIST" =>  $arResult["URL_TEMPLATES_USER_LIST"],
			"URL_TEMPLATES_PM_LIST" =>  $arResult["URL_TEMPLATES_PM_LIST"],
			"URL_TEMPLATES_PM_EDIT" =>  $arResult["URL_TEMPLATES_PM_EDIT"],
			"URL_TEMPLATES_PM_FOLDER" =>  $arResult["URL_TEMPLATES_PM_FOLDER"],
			"PATH_TO_AUTH_FORM" =>  $arResult["PATH_TO_AUTH_FORM"],
			
			"FID_RANGE" => $arParams["FID"],
			"SHOW_FORUM_ANOTHER_SITE" =>  $arParams["SHOW_FORUM_ANOTHER_SITE"],
			"AJAX_TYPE" => $arParams["AJAX_TYPE"],
			
			"CACHE_TIME" => $arResult["CACHE_TIME"],
			"CACHE_TYPE" => $arResult["CACHE_TYPE"],
			
			"TMPLT_SHOW_AUTH_FORM" =>  $arParams["TMPLT_SHOW_AUTH_FORM"],
			"SHOW_ADD_MENU" => ($arParams["SHOW_ADD_MENU"] == "Y" ? "Y" : "N")
		),
		$component
	);?><?
	}
?>