<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# admin@bitrixsoft.com                       #
##############################################
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");
define("HELP_FILE", "settings/module_admin.php");

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);

$id = $_REQUEST["id"];

$arModules = array();
function OnModuleInstalledEvent($id)
{
	$db_events = GetModuleEvents("main", "OnModuleInstalled");
	while ($arEvent = $db_events->Fetch())
		ExecuteModuleEventEx($arEvent, array($id));
}

$handle=@opendir($DOCUMENT_ROOT.BX_ROOT."/modules");
if($handle)
{
	while (false !== ($dir = readdir($handle)))
	{
		if(is_dir($DOCUMENT_ROOT.BX_ROOT."/modules/".$dir) && $dir!="." && $dir!=".." && strpos($dir, ".") !== false)
		{
			$module_dir = $DOCUMENT_ROOT.BX_ROOT."/modules/".$dir;
			if($info = CModule::CreateModuleObject($dir))
			{
				$arModules[$dir]["MODULE_ID"] = $info->MODULE_ID;
				$arModules[$dir]["MODULE_NAME"] = $info->MODULE_NAME;
				$arModules[$dir]["MODULE_DESCRIPTION"] = $info->MODULE_DESCRIPTION;
				$arModules[$dir]["MODULE_VERSION"] = $info->MODULE_VERSION;
				$arModules[$dir]["MODULE_VERSION_DATE"] = $info->MODULE_VERSION_DATE;
				$arModules[$dir]["MODULE_SORT"] = $info->MODULE_SORT;
				$arModules[$dir]["MODULE_PARTNER"] = $info->PARTNER_NAME;
				$arModules[$dir]["MODULE_PARTNER_URI"] = $info->PARTNER_URI;
				$arModules[$dir]["IsInstalled"] = $info->IsInstalled();
			}
		}
	}
	closedir($handle);
}

uasort($arModules, create_function('$a, $b', 'if($a["MODULE_SORT"] == $b["MODULE_SORT"]) return strcasecmp($a["MODULE_NAME"], $b["MODULE_NAME"]); return ($a["MODULE_SORT"] < $b["MODULE_SORT"])? -1 : 1;'));

$stableVersionsOnly = COption::GetOptionString("main", "stable_versions_only", "Y");
$arRequestedModules = CUpdateClientPartner::GetRequestedModules("");

$arUpdateList = CUpdateClientPartner::GetUpdatesList($errorMessage, LANG, $stableVersionsOnly, $arRequestedModules);

$strError_tmp = "";
$arClientModules = CUpdateClientPartner::GetCurrentModules($strError_tmp);
	
$bHaveNew = false;
$modules = Array();
$modulesNew = Array();
if(!empty($arUpdateList["MODULE"]))
{
	foreach($arUpdateList["MODULE"] as $k => $v)
	{
		if(!array_key_exists($v["@"]["ID"], $arClientModules))
		{
			$bHaveNew = true;
			$modulesNew[] = Array(
					"NAME" => $v["@"]["NAME"],
					"ID" => $v["@"]["ID"],
					"DESCRIPTION" => $v["@"]["DESCRIPTION"],
					"PARTNER" => $v["@"]["PARTNER_NAME"]
				);
		}
		else
		{
			$modules[$v["@"]["ID"]] = (isset($v["#"]["VERSION"]) ? $v["#"]["VERSION"][count($v["#"]["VERSION"]) - 1]["@"]["ID"] : "");
			unset($arUpdateList["MODULE"][$k]);
		}
	}
}

$fb = ($id == 'fileman' && !$USER->CanDoOperation('fileman_install_control'));
if((strlen($uninstall)>0 || strlen($install)>0 || strlen($clear)>0) && $isAdmin && !$fb && check_bitrix_sessid())
{
	$id = str_replace("\\", "", str_replace("/", "", $id));
	if($Module = CModule::CreateModuleObject($id))
	{
		if($Module->IsInstalled() && strlen($uninstall)>0)
		{
			OnModuleInstalledEvent($id);
			$Module->DoUninstall();
			LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&mod=".$id."&result=DELOK");
		}
		elseif(!$Module->IsInstalled() && strlen($install) > 0)
		{
			if (strtolower($DB->type)=="mysql" && defined("MYSQL_TABLE_TYPE") && strlen(MYSQL_TABLE_TYPE)>0)
			{
				$DB->Query("SET table_type = '".MYSQL_TABLE_TYPE."'", true);
			}

			OnModuleInstalledEvent($id);
			$Module->DoInstall();
			LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&mod=".$id."&result=OK");
		}
		elseif(!$Module->IsInstalled() && strlen($clear) > 0)
		{
			if(strlen($Module->MODULE_ID) > 0 && is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$Module->MODULE_ID."/"))
			{
				DeleteDirFilesEx("/bitrix/modules/".$Module->MODULE_ID."/");
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&mod=".$id."&result=CLEAROK");
			}
		}
	}
}

$sTableID = "upd_partner_modules_all";
$lAdmin = new CAdminList($sTableID);

$sTableID1 = "upd_partner_modules_new";
$lAdmin1 = new CAdminList($sTableID1);

$lAdmin->BeginPrologContent();
echo "<h2>".GetMessage("MOD_SMP_AV_MOD")."</h2><p>".GetMessage("MOD_SMP_AV_MOD_TEXT1")."<br />".GetMessage("MOD_SMP_AV_MOD_TEXT2")."</p>";
$lAdmin->EndPrologContent();

$arHeaders = Array(
	array(
		"id" => "NAME",
		"content" => GetMessage("MOD_NAME"),
		"default" => true,
	),
	array(
		"id" => "PARTNER",
		"content" => GetMessage("MOD_PARTNER"),
		"default" => true,
	),
	array(
		"id" => "VERSION",
		"content" => GetMessage("MOD_VERSION"),
		"default" => true,
	),
	array(
		"id" => "DATE_UPDATE",
		"content" => GetMessage("MOD_DATE_UPDATE"),
		"default" => true,
	),
	array(
		"id" => "STATUS",
		"content" => GetMessage("MOD_SETUP"),
		"default" => true,
	),
);

$lAdmin->AddHeaders($arHeaders);

$rsData = new CDBResult;
$rsData->InitFromArray($arModules);
$rsData = new CAdminResult($rsData, $sTableID);

while($info = $rsData->Fetch())
{
	$row =& $lAdmin->AddRow($info["MODULE_ID"], $info);
	
	$row->AddViewField("NAME", "<b>".htmlspecialchars($info["MODULE_NAME"])."</b> (".htmlspecialchars($info["MODULE_ID"]).")<br />".htmlspecialchars($info["MODULE_DESCRIPTION"]));
	$row->AddViewField("PARTNER", ((strlen($info["MODULE_PARTNER"]) > 0) ? " ".str_replace(array("#NAME#", "#URI#"), array($info["MODULE_PARTNER"], $info["MODULE_PARTNER_URI"]), GetMessage("MOD_PARTNER_NAME"))."" : "&nbsp;"));
	$row->AddViewField("VERSION", $info["MODULE_VERSION"]);
	$row->AddViewField("DATE_UPDATE", CDatabase::FormatDate($info["MODULE_VERSION_DATE"], "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("SHORT")));
	$status = "";
	if($info["IsInstalled"])
		$status = GetMessage("MOD_INSTALLED");
	else
		$status = "<span class=\"required\">".GetMessage("MOD_NOT_INSTALLED")."</span>";

	if(!empty($modules[$info["MODULE_ID"]])) 
		$status .= "<br /><a href=\"/bitrix/admin/update_system_partner.php?tabControl_active_tab=tab2\" style=\"color:green;\">".GetMessage("MOD_SMP_NEW_UPDATES")."</a>";
	$row->AddViewField("STATUS", $status);
	
	$arActions = Array();
	if(!empty($modules[$info["MODULE_ID"]])) 
	{
		$arActions[] = array(
			"ICON" => "",
			"DEFAULT" => false,
			"TEXT" => GetMessage("MOD_SMP_UPDATE"),
			"ACTION" => $lAdmin->ActionRedirect("/bitrix/admin/update_system_partner.php?tabControl_active_tab=tab2"),
		);
	}

	if($info["IsInstalled"])
	{
		$arActions[] = array(
			"ICON" => "delete",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MOD_DELETE"),
			"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?id=".htmlspecialchars($info["MODULE_ID"])."&lang=".LANG."&uninstall=Y&".bitrix_sessid_get()),
		);
	}
	else
	{
		$arActions[] = array(
			"ICON" => "add",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MOD_INSTALL_BUTTON"),
			"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?id=".htmlspecialchars($info["MODULE_ID"])."&lang=".LANG."&install=Y&".bitrix_sessid_get()),
		);
		$arActions[] = array(
			"ICON" => "delete",
			"DEFAULT" => false,
			"TEXT" => GetMessage("MOD_SMP_DELETE"),
			"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?id=".htmlspecialchars($info["MODULE_ID"])."&lang=".LANG."&clear=Y&".bitrix_sessid_get()),
		);
	}
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	)
);
$lAdmin->CheckListMode();


$lAdmin1->BeginPrologContent();
echo "<h2>".GetMessage("MOD_SMP_BUY_MOD")."</h2><p>".GetMessage("MOD_SMP_BUY_MOD_TEXT1")."<br />".GetMessage("MOD_SMP_BUY_MOD_TEXT2")."</p>";
$lAdmin1->EndPrologContent();

$arHeaders1 = Array(
	array(
		"id" => "NAME",
		"content" => GetMessage("MOD_NAME"),
		"default" => true,
	),
	array(
		"id" => "PARTNER",
		"content" => GetMessage("MOD_PARTNER"),
		"default" => true,
	),
);
$lAdmin1->AddHeaders($arHeaders1);
$rsData = new CDBResult;
$rsData->InitFromArray($modulesNew);
$rsData = new CAdminResult($rsData, $sTableID1);

while($info = $rsData->Fetch())
{
	$row =& $lAdmin1->AddRow($info["ID"], $info);
	
	$row->AddViewField("NAME", "<b>".htmlspecialchars($info["NAME"])."</b> (".htmlspecialchars($info["ID"]).")<br />".htmlspecialchars($info["DESCRIPTION"]));
	$row->AddViewField("PARTNER", $info["PARTNER"]);
	
	$arActions = Array();
	$arActions[] = array(
		"ICON" => "",
		"DEFAULT" => true,
		"TEXT" => GetMessage("MOD_SMP_DOWNLOAD"),
		"ACTION" => $lAdmin1->ActionRedirect("/bitrix/admin/update_system_partner.php?tabControl_active_tab=tab2"),
	);

	$row->AddActions($arActions);
}

$lAdmin1->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	)
);

$lAdmin1->CheckListMode();


$APPLICATION->SetTitle(GetMessage("TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

if(strlen($mod) > 0 && $result == "OK")
{
	CAdminMessage::ShowNote(GetMessage("MOD_SMP_INSTALLED", Array("#MODULE_NAME#" => $arModules[$mod]["MODULE_NAME"])));
}
elseif(strlen($mod) > 0 && $result == "DELOK")
{
	CAdminMessage::ShowNote(GetMessage("MOD_SMP_UNINSTALLED", Array("#MODULE_NAME#" => $arModules[$mod]["MODULE_NAME"])));
}
elseif(strlen($mod) > 0 && $result == "CLEAROK")
{
	CAdminMessage::ShowNote(GetMessage("MOD_SMP_DELETED", Array("#MODULE_NAME#" => $mod)));
}

if($bHaveNew)
{
	$lAdmin1->DisplayList();
}
	
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
