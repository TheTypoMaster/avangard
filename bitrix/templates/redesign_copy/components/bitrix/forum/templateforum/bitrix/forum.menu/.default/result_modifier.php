<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/* SHOW PANELS */
$arResult["MAIN_PANEL"] = array();
$arResult["MAIN_PANEL"]["SHOW"] = "N";
$arResult["MAIN_PANEL"]["FORUMS_LIST"] = "N";
$arResult["MAIN_PANEL"]["FORUMS"] = "N";

if (($arParams["SHOW_FORUMS_LIST"] != "N") && ($arResult["SHOW_FORUMS_LIST"] == "Y"))
	$arResult["MAIN_PANEL"]["FORUMS_LIST"] = "Y";
	
if (($arParams["SHOW_ADD_MENU"] == "Y") && ($arResult["IsAuthorized"] != "Y") && (($arResult["sSection"] == "INDEX") || ($arResult["sSection"] == "LIST")))
{
	$arResult["MAIN_PANEL"]["FORUMS"] = "Y";
}

$arResult["MANAGE_PANEL"] = array();
$arResult["MANAGE_PANEL"]["SHOW"] = "N";
$arResult["MANAGE_PANEL"]["SUBSCRIBE"] = "N";
$arResult["MANAGE_PANEL"]["TOPICS"] = "N";
$arResult["MANAGE_PANEL"]["MESSAGES"] = "N";
$arResult["MANAGE_PANEL"]["FORUMS"] = "N";

if ($arResult["IsAuthorized"] == "Y")
{
	if ($arResult["sSection"]=="LIST" || $arResult["sSection"]=="READ")
	{
		$arResult["MANAGE_PANEL"]["SUBSCRIBE"] = "Y";
		$arResult["MANAGE_PANEL"]["SHOW"] = "Y";
	}
	
	if ($arResult["UserPermission"] >= "Q")
	{
		if (($arResult["sSection"] == "LIST")||($arResult["sSection"] == "READ"))
		{
			$arResult["MANAGE_PANEL"]["TOPICS"] = "Y";
			$arResult["MANAGE_PANEL"]["SHOW"] = "Y";
		}
		if (($arResult["sSection"] == "READ")||($arResult["sSection"] == "MESSAGE_APPR"))
		{
			$arResult["MANAGE_PANEL"]["MESSAGES"] = "Y";
			$arResult["MANAGE_PANEL"]["SHOW"] = "Y";
		}
	}
	
	if ((($arResult["sSection"] == "INDEX") || ($arResult["sSection"] == "LIST")) && ($arParams["SHOW_ADD_MENU"] == "Y"))
	{
		$arResult["set_be_read"] = ForumAddPageParams(
			$arResult[strToLower($arResult["PAGE_NAME"])], 
			array("ACTION" => "SET_BE_READ"))."&amp;".bitrix_sessid_get();
		$arResult["MANAGE_PANEL"]["FORUMS"] = "Y";
		$arResult["MANAGE_PANEL"]["SHOW"] = "Y";
	}
}
/* POPUPS */
$arResult["popup"]["forums"] = array();
$arResult["popup"]["subscribe"] = array();
$arResult["popup"]["topics"] = array();
$arResult["popup"]["messages"] = array();
?>