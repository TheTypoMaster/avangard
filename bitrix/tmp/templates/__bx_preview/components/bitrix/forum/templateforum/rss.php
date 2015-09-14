<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
	"bitrix:forum.rss",
	"",
	array(
		"TYPE" => $arResult["TYPE"],
		"IID" => $arResult["IID"],
		"MODE" => $arResult["MODE"],
		
		"USE_RSS" => $arParams["USE_RSS"],
		"RSS_CACHE" => $arParams["RSS_CACHE"],
		"TYPE_RANGE" => $arParams["RSS_TYPE_RANGE"],
		"FID_RANGE" => $arParams["RSS_FID_RANGE"],
		"YANDEX" => $arParams["RSS_YANDEX"],
		"TN_TITLE" => $arParams["RSS_TN_TITLE"],
		"TN_DESCRIPTION" => $arParams["RSS_TN_DESCRIPTION"],
		"TN_TEMPLATE" => $arParams["RSS_TN_TEMPLATE"],
		"COUNT" => $arParams["RSS_COUNT"],
		"PATH_TO_SMILE" =>  $arParams["PATH_TO_SMILE"],
		
		"URL_TEMPLATES_RSS" => $arResult["URL_TEMPLATES_RSS"],
		"URL_TEMPLATES_INDEX" =>  $arResult["URL_TEMPLATES_INDEX"],
		"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
		"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
		"URL_TEMPLATES_MESSAGE" =>  $arResult["URL_TEMPLATES_MESSAGE"],
		"URL_TEMPLATES_PROFILE_VIEW" => $arResult["URL_TEMPLATES_PROFILE_VIEW"],
		
		"CACHE_TYPE" => $arResult["CACHE_TYPE"],
		"CACHE_TIME" => $arResult["CACHE_TIME"],
		
	),
	$component
);
?>