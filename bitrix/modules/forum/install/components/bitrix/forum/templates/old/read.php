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
			"URL_TEMPLATES_FORUMS"	=>	$arResult["URL_TEMPLATES_FORUMS"],
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
		),
		$component
	);
	?><br/><?
	}
	$arInfo = $APPLICATION->IncludeComponent(
		"bitrix:forum.topic.read",
		"",
		array(
			"FID" => $arResult["FID"],
			"TID" => $arResult["TID"],
			"MID" => $arResult["MID"],
			"MESSAGES_PER_PAGE" => $arResult["MESSAGES_PER_PAGE"],
			
			"URL_TEMPLATES_INDEX" =>  $arResult["URL_TEMPLATES_INDEX"],
			"URL_TEMPLATES_FORUMS"	=>	$arResult["URL_TEMPLATES_FORUMS"],
			"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
			"URL_TEMPLATES_PROFILE_VIEW" => $arResult["URL_TEMPLATES_PROFILE_VIEW"],
			"URL_TEMPLATES_MESSAGE_MOVE" => $arResult["URL_TEMPLATES_MESSAGE_MOVE"],
			"URL_TEMPLATES_TOPIC_NEW" => $arResult["URL_TEMPLATES_TOPIC_NEW"],
			"URL_TEMPLATES_SUBSCR_LIST" => $arResult["URL_TEMPLATES_SUBSCR_LIST"],
			"URL_TEMPLATES_TOPIC_MOVE" => $arResult["URL_TEMPLATES_TOPIC_MOVE"],
			"URL_TEMPLATES_PM_EDIT" => $arResult["URL_TEMPLATES_PM_EDIT"],
			"URL_TEMPLATES_MESSAGE_SEND" => $arResult["URL_TEMPLATES_MESSAGE_SEND"],
			
			"PAGEN" => intVal($GLOBALS["NavNum"] + 1),
			"PATH_TO_SMILE" =>  $arParams["PATH_TO_SMILE"],
			"PATH_TO_ICON" => $arParams["PATH_TO_ICON"],
			"WORD_LENGTH" => $arParams["WORD_LENGTH"],
			"DATE_FORMAT" =>  $arResult["DATE_FORMAT"],
			"DATE_TIME_FORMAT" =>  $arResult["DATE_TIME_FORMAT"],
			"PAGE_NAVIGATION_TEMPLATE" =>  $arParams["PAGE_NAVIGATION_TEMPLATE"],
			"FILES_COUNT" => $arParams["FILES_COUNT"], 
			"IMAGE_SIZE" => $arParams["IMAGE_SIZE"], 			
			"AJAX_TYPE" => $arParams["AJAX_TYPE"],
			
			"SET_NAVIGATION" => $arResult["SET_NAVIGATION"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			"SET_TITLE" => $arResult["SET_TITLE"],
			"SET_PAGE_PROPERTY" => $arResult["SET_PAGE_PROPERTY"],
			"CACHE_TYPE" => $arResult["CACHE_TYPE"],
			"CACHE_TIME" => $arResult["CACHE_TIME"],
			
			"SEND_MAIL" => $arParams["SEND_MAIL"],
			"SEND_ICQ" => "A",
			"HIDE_USER_ACTION" => $arParams["HIDE_USER_ACTION"]
		),
		$component
	);
	?><br/><?
	?><?$APPLICATION->IncludeComponent("bitrix:forum.post_form", "", 
		Array(
			"FID"	=>	$arResult["FID"],
			"TID"	=>	$arResult["TID"],
			"MID"	=>	0,
			"PAGE_NAME"	=>	"read",
			"MESSAGE_TYPE"	=>	"REPLY",
			"FORUM" => $arInfo["FORUM"],
			"bVarsFromForm" => $arInfo["bVarsFromForm"],
			
			"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
			
			"PATH_TO_SMILE"	=>	$arParams["PATH_TO_SMILE"],
			"PATH_TO_ICON"	=>	$arParams["PATH_TO_ICON"],
			"SMILE_TABLE_COLS" => $arParams["SMILE_TABLE_COLS"],
			"FILES_COUNT" => $arParams["FILES_COUNT"], 
			
			"AJAX_TYPE"	=>	"N",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			
			"SHOW_TAGS" => $arParams["SHOW_TAGS"]
		),
		$component
	);
	?><div class="forum-br"></div><?
	?><?$APPLICATION->IncludeComponent("bitrix:forum.statistic", "", 
		Array(
			"FID"	=>	$arResult["FID"],
			"TID"	=>	$arResult["TID"],
			"PERIOD"	=>	$arParams["TIME_INTERVAL_FOR_USER_STAT"],
			"SHOW"	=>	array("USERS_ONLINE"),
			"URL_TEMPLATES_PROFILE_VIEW"	=>	$arResult["URL_TEMPLATES_PROFILE_VIEW"],
			"WORD_LENGTH"	=>	$arParams["WORD_LENGTH"],
			
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"]
		), $component
	);?><?
	
	if ($arParams["USE_RSS"] == "Y" && (in_array($arResult["FID"], $arParams["RSS_FID_RANGE"]) || empty($arParams["RSS_FID_RANGE"])))
	{
	?><div class="forum-br"></div>
	<table border="0" cellpadding="0" cellspacing="0" class="clear" width="100%" class="clear">
		<tr valign="top"><td width="50%">&nbsp;</td><td width="50%">
			<div class="forum-other"><?
		$APPLICATION->IncludeComponent(
			"bitrix:forum.rss", "",
			Array(
				"TYPE_RANGE"	=> $arParams["RSS_TYPE_RANGE"],
				"FID_RANGE"	=> $arParams["RSS_FID_RANGE"],
				"IID"	=>	$arResult["TID"],
				"MODE_TEMPLATE"	=>	"link",
				"MODE" => "topic",
				"URL_TEMPLATES_RSS"	=>	$arResult["URL_TEMPLATES_RSS"],
				
				"CACHE_TYPE"	=>	$arParams["RSS_CACHE"],
				"CACHE_TIME"	=>	$arParams["CACHE_TYPE"]
			), $component);?><br /><?
		?>
	</div></td></tr></table><?
	}
	if (($arParams["TMPLT_SHOW_MENU"] == "BOTH") || ($arParams["TMPLT_SHOW_MENU"] == "BOTTOM"))
	{
	?><br/><?
	?><?$APPLICATION->IncludeComponent(
		"bitrix:forum.menu",
		"",
		array(
			"FID" =>  $arResult["FID"],
			"TID" =>  $arResult["TID"],
			"PAGE_NAME" =>  $arResult["PAGE_NAME"],
			
			"URL_TEMPLATES_INDEX" => $arResult["URL_TEMPLATES_INDEX"],
			"URL_TEMPLATES_FORUMS"	=>	$arResult["URL_TEMPLATES_FORUMS"],
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
		),
		$component
	);?><?
	}
?>