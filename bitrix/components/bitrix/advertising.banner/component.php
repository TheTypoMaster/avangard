<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

//Params
$arParams["TYPE"] = (isset($arParams["TYPE"]) ? trim($arParams["TYPE"]) : "");

if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
	$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
else
	$arParams["CACHE_TIME"] = 0;

//Result
$arResult = Array(
	"BANNER" => "",
	"BANNER_PROPERTIES" => Array(),
);

$obCache = new CPHPCache;
$cache_id = SITE_ID."|advertising.banner|".serialize($arParams)."|".$USER->GetGroups();
$cache_path = "/".SITE_ID.$this->GetRelativePath();

if ($obCache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
{
	if(!CModule::IncludeModule("advertising"))
		return;

	$arBanner = CAdvBanner::GetRandom($arParams["TYPE"]);
	$strReturn = CAdvBanner::GetHTML($arBanner);

	$arResult["BANNER"] = $strReturn;
	$arResult["BANNER_PROPERTIES"] = $arBanner;

	if (strlen($arResult["BANNER"])>0)
		CAdvBanner::FixShow($arBanner);

	$this->IncludeComponentTemplate();

	$templateCachedData = $this->GetTemplateCachedData();

	$obCache->EndDataCache(
		Array(
			"arResult" => $arResult,
			"templateCachedData" => $templateCachedData
		)
	);
}
else
{
	$arVars = $obCache->GetVars();
	$arResult = $arVars["arResult"];
	$this->SetTemplateCachedData($arVars["templateCachedData"]);
}


if (!empty($arResult["BANNER_PROPERTIES"]))
{
	$arIcons = Array(
		Array(
			"URL" => "/bitrix/admin/adv_banner_edit.php?lang=".LANGUAGE_ID."&amp;ID=".$arResult["BANNER_PROPERTIES"]["ID"]. "&amp;CONTRACT_ID=".$arResult["BANNER_PROPERTIES"]["CONTRACT_ID"],
			"ICON" => "banner-edit",
			"TITLE" => GetMessage("AD_PUBLIC_ICON_EDIT_BANNER")
		),
		Array(
			"URL" => "/bitrix/admin/adv_banner_list.php?lang=".LANGUAGE_ID."&amp;find_id=".$arResult["BANNER_PROPERTIES"]["ID"]. "&amp;find_id_exact_match=Y&amp;find_contract_id[]=".$arResult["BANNER_PROPERTIES"]["CONTRACT_ID"]. "&amp;find_type_sid[]=".$arBanner["TYPE_SID"]."&amp;set_filter=Y",
			"ICON" => "banner-view",
			"TITLE" => GetMessage("AD_PUBLIC_ICON_BANNER_LIST")
		),
	);

	$this->AddIncludeAreaIcons($arIcons);
}
?>