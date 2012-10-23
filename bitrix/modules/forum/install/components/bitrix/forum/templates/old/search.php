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
		),
		$component
	);
	?><br /><?
	}
	if(IsModuleInstalled("search") && $arParams["SHOW_TAGS"] == "Y")
	{
		$arFilter = is_array($arParams["FID"]) ? $arParams["FID"] : array();
		$res = array();
		if (!empty($_REQUEST["FORUM_ID"]))
			$res = (is_array($_REQUEST["FORUM_ID"]) ? $_REQUEST["FORUM_ID"] : array($_REQUEST["FORUM_ID"]));
		if (empty($arFilter) && !empty($res))
			$arFilter = $res;
		elseif (!empty($arFilter) && !empty($res))
			$arFilter = array_intersect($res, $arFilter);
	
	?><div class="tags-cloud"><?
		$APPLICATION->IncludeComponent("bitrix:search.tags.cloud", ".default", 
			Array(
			"SEARCH" => $_REQUEST["q"],
			"TAGS" => $_REQUEST["~TAGS"],
			"CHECK_DATES" => $arParams["CHECK_DATES"],
			"SORT" => $arParams["TAGS_SORT"],
			"PAGE_ELEMENTS" => 50,
			"PERIOD" => $_REQUEST["DATE_CHANGE"],
			"URL_SEARCH" => $arResult["URL_TEMPLATES_SEARCH"],
			"TAGS_INHERIT" => $arParams["TAGS_INHERIT"],
			"FONT_MAX" => (empty($arParams["FONT_MAX"]) ? "30" : $arParams["FONT_MAX"]),
			"FONT_MIN" => (empty($arParams["FONT_MIN"]) ? "8" : $arParams["FONT_MIN"]),
			"COLOR_NEW" => (empty($arParams["COLOR_NEW"]) ? "707C8F" : $arParams["COLOR_NEW"]),
			"COLOR_OLD" => (empty($arParams["COLOR_OLD"]) ? "C0C0C0" : $arParams["COLOR_OLD"]),
			"PERIOD_NEW_TAGS" => $arParams["PERIOD_NEW_TAGS"],
			"SHOW_CHAIN" => $arParams["SHOW_CHAIN"],
			"COLOR_TYPE" => $arParams["COLOR_TYPE"],
			"WIDTH" => $arParams["WIDTH"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"RESTART" => $arParams["RESTART"],
			"arrFILTER" => array("forum"), 
			"arrFILTER_forum" => $arFilter
			), $component);
	?>
	</div>
	<?
	}

	$APPLICATION->IncludeComponent(
		"bitrix:forum.search",
		"",
		array(
			"URL_TEMPLATES_INDEX" => $arResult["URL_TEMPLATES_INDEX"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
			
			"FID_RANGE" => $arParams["FID"],
			"TOPICS_PER_PAGE" => $arParams["TOPICS_PER_PAGE"],
			"DATE_FORMAT" =>  $arParams["DATE_FORMAT"],
			"PAGE_NAVIGATION_TEMPLATE" => $arParams["PAGE_NAVIGATION_TEMPLATE"],
			
			"SET_TITLE" => $arResult["SET_TITLE"],
			"SET_NAVIGATION" => $arParams["SET_NAVIGATION"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			"CACHE_TIME" => $arResult["CACHE_TIME"],
			"CACHE_TYPE" => $arResult["CACHE_TYPE"],
		),
		$component 
	);
	
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
		),
		$component
	);
	}
?>