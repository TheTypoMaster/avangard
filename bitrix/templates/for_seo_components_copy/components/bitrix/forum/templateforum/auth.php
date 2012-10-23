<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	if (strLen(trim($arParams["TMPLT_SHOW_MENU"])) <= 0)
		$arParams["TMPLT_SHOW_MENU"] = "BOTH";
	$arParams["TMPLT_SHOW_MENU"] = strToUpper($arParams["TMPLT_SHOW_MENU"]);
		
	if (($arParams["TMPLT_SHOW_MENU"] == "BOTH") || ($arParams["TMPLT_SHOW_MENU"] == "TOP"))
	{
		$APPLICATION->IncludeComponent(
			"bitrix:forum.menu",
			"",
			array(
				"CACHE_TIME" => $arResult["CACHE_TIME"],
				"CACHE_TYPE" => $arResult["CACHE_TYPE"],
				"URL_TEMPLATES_INDEX" => $arResult["URL_TEMPLATES_INDEX"],
				"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
				"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
				"URL_TEMPLATES_PROFILE_VIEW" =>  $arResult["URL_TEMPLATES_PROFILE_VIEW"],
				"URL_TEMPLATES_SUBSCR_LIST" =>  $arResult["URL_TEMPLATES_SUBSCR_LIST"],
				"URL_TEMPLATES_ACTIVE" =>  $arResult["URL_TEMPLATES_ACTIVE"],
				"URL_TEMPLATES_SEARCH" =>  $arResult["URL_TEMPLATES_SEARCH"],
				"URL_TEMPLATES_HELP" =>  $arResult["URL_TEMPLATES_HELP"],
				"URL_TEMPLATES_USER_LIST" =>  $arResult["URL_TEMPLATES_USER_LIST"],
				"URL_TEMPLATES_PM_LIST" =>  $arResult["URL_TEMPLATES_PM_LIST"],
				"URL_TEMPLATES_PM_EDIT" =>  $arResult["URL_TEMPLATES_PM_EDIT"],
				"URL_TEMPLATES_PM_FOLDER" =>  $arResult["URL_TEMPLATES_PM_FOLDER"],
				"FID" =>  $arResult["FID"],
				"TID" =>  $arResult["TID"],
				"PAGE_NAME" =>  $arResult["PAGE_NAME"],
				"PATH_TO_AUTH_FORM" =>  $arResult["PATH_TO_AUTH_FORM"],
				"TMPLT_SHOW_AUTH_FORM" =>  $arParams["TMPLT_SHOW_AUTH_FORM"],
				"FID_RANGE" => $arParams["FID"],
			),
			$component
		);
	?><br /><?
	}
$APPLICATION->AuthForm("");
?>