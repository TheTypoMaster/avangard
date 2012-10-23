<?
define("ADMIN_MODULE_NAME", "clouds");

/*.require_module 'standard';.*/
/*.require_module 'pcre';.*/
/*.require_module 'bitrix_main_include_prolog_admin_before';.*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

/*.require_module 'bitrix_clouds_include';.*/
if(!CModule::IncludeModule('clouds'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

$obBucket = new CCloudStorageBucket(intval($_GET["bucket"]));
if(!$obBucket->Init())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$path = (string)$_GET["path"];
$sTableID = "tbl_clouds_file_list";
$lAdmin = new CAdminList($sTableID);

$arID = $lAdmin->GroupAction();
$action = isset($_REQUEST["action"]) && is_string($_REQUEST["action"])? "$_REQUEST[action]": "";
if(is_array($arID))
{
	foreach($arID as $ID)
	{
		if(strlen($ID) <= 0)
			continue;

		switch($action)
		{
		case "delete":
			if(substr($ID, 0, 1) === "F")
			{
				$file_size = $obBucket->GetFileSize($path.substr($ID, 1));
				if(!$obBucket->DeleteFile($path.substr($ID, 1)))
				{
					$e = $APPLICATION->GetException();
					$lAdmin->AddUpdateError($e->GetString(), $ID);
				}
				else
				{
					$obBucket->DecFileCounter($file_size);
				}
			}
			elseif(substr($ID, 0, 1) === "D")
			{
				$arFiles = $obBucket->ListFiles($path.substr($ID, 1), true);
				foreach($arFiles["file"] as $i => $file)
				{
					if(!$obBucket->DeleteFile($path.substr($ID, 1)."/".$file))
					{
						$e = $APPLICATION->GetException();
						$lAdmin->AddUpdateError($e->GetString(), $ID);
						break;
					}
					else
					{
						$obBucket->DecFileCounter($arFiles["file_size"][$i]);
					}
				}
			}
			break;
		default:
			break;
		}
	}
}

$arHeaders = array(
	array(
		"id" => "FILE_NAME",
		"content" => GetMessage("CLO_STORAGE_FILE_NAME"),
		"default" => true,
	),
	array(
		"id" => "FILE_SIZE",
		"content" => GetMessage("CLO_STORAGE_FILE_SIZE"),
		"align" => "right",
		"default" => true,
	),
);

$lAdmin->AddHeaders($arHeaders);

$arData = /*.(array[int][string]string).*/array();
$arFiles = $obBucket->ListFiles($path);
if($path != "/")
	$arData[] = array("ID" => "D..", "TYPE" => "dir", "NAME" => "..", "SIZE" => "");
if(is_array($arFiles))
{
	foreach($arFiles["dir"] as $i => $dir)
		$arData[] = array("ID" => "D".$dir, "TYPE" => "dir", "NAME" => $dir, "SIZE" => '');
	foreach($arFiles["file"] as $i => $file)
		$arData[] = array("ID" => "F".$file, "TYPE" => "file", "NAME" => $file, "SIZE" => $arFiles["file_size"][$i]);
}
$rsData = new CDBResult;
$rsData->InitFromArray($arData);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(''));

while(is_array($arRes = $rsData->NavNext()))
{
	$row =& $lAdmin->AddRow($arRes["ID"], $arRes);

	if($arRes["TYPE"] === "dir")
	{
		if($arRes["NAME"] === "..")
		{
			$row->bReadOnly = true;
			$row->AddViewField("FILE_NAME", '<div class="clouds_menu_icon_folder_up"></div><a href="'.htmlspecialchars('clouds_file_list.php?lang='.LANGUAGE_ID.'&bucket='.$obBucket->ID.'&path='.urlencode(preg_replace('#([^/]+)/$#', '', $path))).'">'.htmlspecialcharsex($arRes["NAME"]).'</a>');
			$row->AddViewField("FILE_SIZE", '&nbsp;');
		}
		else
		{
			$row->AddViewField("FILE_NAME", '<div class="clouds_menu_icon_folder"></div><a href="'.htmlspecialchars('clouds_file_list.php?lang='.LANGUAGE_ID.'&bucket='.$obBucket->ID.'&path='.urlencode($path.$arRes["NAME"].'/')).'">'.htmlspecialcharsex($arRes["NAME"]).'</a>');
			if($_GET["size"] === "y")
			{
				$arDirFiles = $obBucket->ListFiles($path.$arRes["NAME"]."/", true);
				$size = array_sum($arDirFiles["file_size"]);
				$row->AddViewField("FILE_SIZE", CFile::FormatSize((float)$size));
			}
			else
			{
				$row->AddViewField("FILE_SIZE", '&nbsp;');
			}
		}
	}
	else
	{
		$row->AddViewField("FILE_NAME", '<a href="'.htmlspecialchars($obBucket->GetFileSRC(array("URN" => $path.$arRes["NAME"]))).'">'.htmlspecialcharsex($arRes["NAME"]).'</a>');
		$row->AddViewField("FILE_SIZE", CFile::FormatSize((float)$arRes["SIZE"]));
	}

	$arActions = /*.(array[int][string]string).*/array();
	$arActions[] = array(
		"ICON"=>"delete",
		"TEXT"=>GetMessage("CLO_STORAGE_FILE_DELETE"),
		"ACTION"=>"if(confirm('".GetMessage("CLO_STORAGE_FILE_DELETE_CONF")."')) ".$lAdmin->ActionDoGroup($arRes["ID"], "delete", 'bucket='.$obBucket->ID.'&path='.urlencode($path))
	);

	if(!empty($arActions))
		$row->AddActions($arActions);
}

$arFooter = array(
	array(
		"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
		"value" => $path === "/"? $rsData->SelectedRowsCount(): $rsData->SelectedRowsCount()-1, // W/O ..
	),
	array(
		"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
		"value" => 0,
		"counter" => true,
	),
);
$lAdmin->AddFooter($arFooter);

$arGroupActions = array(
	"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
);
$lAdmin->AddGroupActionTable($arGroupActions);

$chain = $lAdmin->CreateChain();
$arPath = explode("/", $path);
$curPath = "/";
foreach($arPath as $dir)
{
	if($dir != "")
	{
		$curPath .= $dir."/";
		$url = "clouds_file_list.php?lang=".LANGUAGE_ID."&bucket=".$obBucket->ID."&path=".urlencode($curPath);
		$chain->AddItem(array(
			"TEXT" => htmlspecialcharsex($dir),
			"LINK" => htmlspecialchars($url),
			"ONCLICK" => $lAdmin->ActionAjaxReload($url).';return false;',
		));
	}
}
$lAdmin->ShowChain($chain);

$aContext = array(
	array(
		"TEXT" => GetMessage("CLO_STORAGE_FILE_SHOW_DIR_SIZE"),
		"LINK" => "/bitrix/admin/clouds_file_list.php?lang=".LANGUAGE_ID.'&bucket='.$obBucket->ID.'&path='.urlencode($path).'&size=y',
		"TITLE" => GetMessage("CLO_STORAGE_FILE_SHOW_DIR_SIZE_TITLE"),
		"ICON" => "btn_list",
	),
);
$lAdmin->AddAdminContextMenu($aContext, /*$bShowExcel=*/false);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle($obBucket->BUCKET);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>