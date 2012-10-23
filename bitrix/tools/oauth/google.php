<?
/*
This is callback page for Google OAuth 2.0 authentication.
Google redirects only to specific back url set in the OAuth application.
The page opens in popup window after user authorized on Google.
*/

if(isset($_REQUEST["state"]))
{
	$arState = array();
	parse_str($_REQUEST["state"], $arState);

	if(isset($arState['site_id']))
		define("SITE_ID", $arState['site_id']);
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

$bNewUserReg = (COption::GetOptionString("main", "new_user_registration", "N") == "Y");

if(!$USER->IsAuthorized() && $bNewUserReg && CModule::IncludeModule("socialservices"))
{
	$oAuthManager = new CSocServAuthManager();
	$oAuthManager->Authorize("GoogleOAuth");
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");
?>