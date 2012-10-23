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
	?><br/><?
	}

	$APPLICATION->IncludeComponent(
		"bitrix:forum.subscribe.list",
		"",
		array(
			"UID" => $arParams["UID"],
			
			"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
			"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
			"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
			"URL_TEMPLATES_SUBSCR_LIST" =>  $arResult["URL_TEMPLATES_SUBSCR_LIST"],
			"URL_TEMPLATES_PROFILE_VIEW" =>  $arResult["URL_TEMPLATES_PROFILE_VIEW"],
			
			"TOPICS_PER_PAGE" => $arParams["TOPICS_PER_PAGE"],
			"DATE_TIME_FORMAT" =>  $arParams["DATE_TIME_FORMAT"],
			"PAGE_NAVIGATION_TEMPLATE" =>  $arParams["PAGE_NAVIGATION_TEMPLATE"],
			"SET_NAVIGATION" => $arParams["SET_NAVIGATION"],
			
			"SET_TITLE" => $arResult["SET_TITLE"],
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