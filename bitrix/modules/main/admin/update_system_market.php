<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("USMP_TITLE"));

if(!$USER->CanDoOperation('install_updates'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");


if(!in_array(LANGUAGE_ID, Array("ru", "ua")))
{
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/update_system_market_notru.php");
}
else
{
	$arClientModules = CUpdateClientPartner::GetCurrentModules($strError_tmp);

	$req = "&domain=".htmlspecialchars($_SERVER["HTTP_HOST"]);
	$modules = array();
	if(is_array($arClientModules) && !empty($arClientModules))
	{
		foreach($arClientModules as $k => $v)
		{
			if(strpos($k, ".") !== false)
				$req .= "&m[".htmlspecialchars($k)."]=".$v["IS_DEMO"];
		}
	}
	?>
	<div width="100%">
	<iframe src="<?=$APPLICATION->IsHTTPS()?"https://":"http://"?>marketplace.1c-bitrix.ru/solutions/?update_sys=Y<?=$req?>" width="100%" height="2500" frameborder="0"></iframe>
	</div>
	<?
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>