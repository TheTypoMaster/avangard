<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	if (($arParams["TMPLT_SHOW_MENU"] == "BOTH") || ($arParams["TMPLT_SHOW_MENU"] == "TOP"))
	{
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
	);
	?><br/><?
	}
	
	?><?$APPLICATION->IncludeComponent("bitrix:forum.topic.list", "", 
		Array(
			"FID"	=>	$arResult["FID"],
			"USE_DESC_PAGE"	=>	$arParams["USE_DESC_PAGE_TOPIC"],
			
			"URL_TEMPLATES_INDEX"	=>	$arResult["URL_TEMPLATES_INDEX"],
			"URL_TEMPLATES_LIST"	=>	$arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_READ"	=>	$arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
			"URL_TEMPLATES_PROFILE_VIEW"	=>	$arResult["URL_TEMPLATES_PROFILE_VIEW"],
			"URL_TEMPLATES_MESSAGE_APPR"	=>	$arResult["URL_TEMPLATES_MESSAGE_APPR"],
			"URL_TEMPLATES_TOPIC_NEW"	=>	$arResult["URL_TEMPLATES_TOPIC_NEW"],
			"URL_TEMPLATES_SUBSCR_LIST"	=>	$arResult["URL_TEMPLATES_SUBSCR_LIST"],
			"URL_TEMPLATES_TOPIC_MOVE"	=>	$arResult["URL_TEMPLATES_TOPIC_MOVE"],
			
			"PAGEN" => intVal($GLOBALS["NavNum"] + 1),
			"TOPICS_PER_PAGE"	=>	$arParams["TOPICS_PER_PAGE"],
			"MESSAGES_PER_PAGE"	=>	$arParams["MESSAGES_PER_PAGE"],
			"DATE_FORMAT"	=>	$arParams["DATE_FORMAT"],
			"DATE_TIME_FORMAT"	=>	$arParams["DATE_TIME_FORMAT"],
			"PAGE_NAVIGATION_TEMPLATE" =>  $arParams["PAGE_NAVIGATION_TEMPLATE"],
			"SET_NAVIGATION"	=>	$arParams["SET_NAVIGATION"],
			"WORD_LENGTH"	=>	$arParams["WORD_LENGTH"],
			"ADD_INDEX_NAV" => $arParams["ADD_INDEX_NAV"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			
			"SET_TITLE"	=>	$arParams["SET_TITLE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			
			"TMPLT_SHOW_ADDITIONAL_MARKER"	=>	$arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"],
			"PATH_TO_ICON"	=> $arParams["PATH_TO_ICON"],
		), $component
	);?><?
	?><div class="forum-br"></div><?
	?><?$APPLICATION->IncludeComponent("bitrix:forum.statistic", "", 
		Array(
			"FID"	=>	$arResult["FID"],
			"TID"	=>	0,
			"PERIOD"	=>	$arParams["TIME_INTERVAL_FOR_USER_STAT"],
			"SHOW"	=>	array("USERS_ONLINE", "STATISTIC"),
			"URL_TEMPLATES_PROFILE_VIEW"	=>	$arResult["URL_TEMPLATES_PROFILE_VIEW"],
			
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"WORD_LENGTH"	=>	$arParams["WORD_LENGTH"]
		), $component
	);?><?

	?><div class="forum-br"></div>
	<table border="0" cellpadding="0" cellspacing="0" class="clear" width="100%" class="clear">
		<tr valign="top">
			<td width="50%">
				<div class="forum-legend">
					<table width="0%" cellpadding="0" cellspacing="0" border="0"  class="clear">
					<tr valign="top"><td nowrap="nowrap">
						<div><div class="icon-new-message"></div> <?=GetMessage("F_INFO_NEW_MESS")?></div>
						<div><div class="icon-moved"></div> <?=GetMessage("F_INFO_MOVED")?></div>
					<?if ($GLOBALS['USER']->IsAdmin()):
					?><div><div class="icon-na"></div> <?=GetMessage("F_INFO_NA")?></div><?
					endif;?>
					</td>
					<td nowrap="nowrap">
						<div><div class="icon-no-message"></div> <?=GetMessage("F_INFO_NO_MESS")?></div>
						<div><div class="icon-closed-new-message"></div> <?=GetMessage("F_INFO_CLOSED")?></div>
					</td></tr></table>
			</div></td>
			<td width="50%">
			<div class="forum-other"><?
	if ($arParams["USE_RSS"] == "Y" && in_array($arResult["FID"], $arParams["RSS_FID_RANGE"]))
	{
		$APPLICATION->IncludeComponent(
			"bitrix:forum.rss", "",
			Array(
				"TYPE_RANGE"	=> $arParams["RSS_TYPE_RANGE"],
				"FID_RANGE"	=> $arParams["RSS_FID_RANGE"],
				"IID"	=>	$arResult["FID"],
				"MODE_TEMPLATE"	=>	"link",
				"URL_TEMPLATES_RSS"	=>	$arResult["URL_TEMPLATES_RSS"],
				
				"CACHE_TYPE"	=>	$arParams["RSS_CACHE"],
				"CACHE_TIME"	=>	$arParams["CACHE_TYPE"]
			), $component);?><br /><?
	}
		
	if (!empty($arParams["FORUMS_ANOTHER"])):?>
		<?=GetMessage("F_INFO_FORUMS_ANOTHER")?>
		<select>
		<?foreach ($arParams["FORUMS_ANOTHER"] as $key=>$val):?>
		<?endforeach;?>
		</select><?
	endif;
		?>
	</div></td></tr></table><?
	
	if (strLen($arParams["TMPLT_SHOW_BOTTOM"]) > 0):
	
		if ($arParams["TMPLT_SHOW_BOTTOM"] != "SET_BE_READ"):?>
<div class="forum-hr"></div>
<table width="100%" class="clear"><tr><td><?=$arParams["~TMPLT_SHOW_BOTTOM"]?></td></tr></table>
<div class="forum-hr"></div><?
		else:?>
<div class="forum-br"></div>
<table width="100%" class="clear"><tr><td>
	<center><a href="<?=htmlspecialcharsEx($GLOBALS["APPLICATION"]->GetCurPageParam("ACTION=SET_BE_READ&".bitrix_sessid_get(), array("ACTION", "sessid", BX_AJAX_PARAM_ID)))?>" title="<?=GetMessage("F_TOPIC_MARK_READ")?>"><?=GetMessage("F_TOPIC_MARK_READ_DO")?></a></center>
</td></tr></table><?
		endif;

	endif;
		
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
	);
	}
?>