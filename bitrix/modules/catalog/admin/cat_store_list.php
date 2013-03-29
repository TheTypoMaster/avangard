<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_store')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
$bReadOnly = !$USER->CanDoOperation('catalog_store');

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if (!CBXFeatures::IsFeatureEnabled('CatMultiStore'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$bExport = false;
if($_REQUEST["mode"] == "excel")
	$bExport = true;

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

$sTableID = "b_catalog_store";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = array();
$lAdmin->InitFilter($arFilterFields);
$arFilter = array();

if ($lAdmin->EditAction() && !$bReadOnly)
{
	foreach ($_POST['FIELDS'] as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);
		$arFields['ID']=$ID;
		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (!CCatalogStore::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(GetMessage("ERROR_UPDATING_REC")." (".$arFields["ID"].", ".$arFields["TITLE"].", ".$arFields["SORT"].")", $ID);

			$DB->Rollback();
		}

		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && !$bReadOnly)
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = CCatalogStore::GetList(array($by => $order));
		while ($arResult = $dbResultList->Fetch())
			$arID[] = $arResult['ID'];
	}

	foreach ($arID as $ID)
	{
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);

				$DB->StartTransaction();

				if (!CCatalogStore::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("ERROR_DELETING_TYPE"), $ID);
				}
				$DB->Commit();
				break;
		}
	}
}
$arSelect = array(
	"ID",
	"ACTIVE",
	"TITLE",
	"ADDRESS",
	"DESCRIPTION",
	"GPS_N",
	"GPS_S",
	"IMAGE_ID",
	"PHONE",
	"SCHEDULE",
	"XML_ID",
	"DATE_MODIFY",
	"DATE_CREATE",
	"USER_ID",
	"MODIFIED_BY"
);
$dbResultList = CCatalogStore::GetList(array($by => $order),false,false,false,$arSelect);
$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("group_admin_nav")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"TITLE","content"=>GetMessage("TITLE"), "sort"=>"TITLE", "default"=>true),
	array("id"=>"ACTIVE","content"=>GetMessage("STORE_ACTIVE"), "sort"=>"ACTIVE_FLAG", "default"=>true),
	array("id"=>"ADDRESS", "content"=>GetMessage("ADDRESS"), "sort"=>"ADDRESS", "default"=>true),
	array("id"=>"IMAGE_ID", "content"=>GetMessage("STORE_IMAGE"),  "sort"=>"IMAGE_ID", "default"=>false),
	array("id"=>"DESCRIPTION", "content"=>GetMessage("DESCRIPTION"),  "sort"=>"DESCRIPTION", "default"=>true),
	array("id"=>"GPS_N", "content"=>GetMessage("GPS_N"),  "sort"=>"GPS_N", "default"=>false),
	array("id"=>"GPS_S", "content"=>GetMessage("GPS_S"),  "sort"=>"GPS_S", "default"=>false),
	array("id"=>"PHONE", "content"=>GetMessage("PHONE"),  "sort"=>"PHONE", "default"=>true),
	array("id"=>"SCHEDULE", "content"=>GetMessage("SCHEDULE"),  "sort"=>"SCHEDULE", "default"=>true),
	array("id"=>"DATE_MODIFY", "content"=>GetMessage("DATE_MODIFY"),  "sort"=>"DATE_MODIFY", "default"=>true),
	array("id"=>"MODIFIED_BY", "content"=>GetMessage("MODIFIED_BY"),  "sort"=>"MODIFIED_BY", "default"=>true),
	array("id"=>"DATE_CREATE", "content"=>GetMessage("DATE_CREATE"),  "sort"=>"DATE_CREATE", "default"=>false),
	array("id"=>"USER_ID", "content"=>GetMessage("USER_ID"),  "sort"=>"USER_ID", "default"=>false),
));

$arSelectFields = $lAdmin->GetVisibleHeaderColumns();
if (!in_array('ID', $arSelectFields))
	$arSelectFields[] = 'ID';

$arSelectFieldsMap = array();
foreach ($arSelectFields as &$strOneFieldName)
{
	$arSelectFieldsMap[$strOneFieldName] = true;
}
if (isset($strOneFieldName))
	unset($strOneFieldName);

$arUserList = array();
$strNameFormat = CSite::GetNameFormat(true);

while ($arSTORE = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arSTORE);
	$row->AddField("ID", $f_ID);
	if ($bReadOnly)
	{
		$row->AddViewField("TITLE", $f_TITLE);
		$row->AddViewField("ADDRESS", $f_ADDRESS);
		$row->AddViewField("DESCRIPTION", $f_DESCRIPTION);

	}
	else
	{
		$row->AddInputField("TITLE");
		$row->AddCheckField("ACTIVE");
		$row->AddInputField("ADDRESS", array("size" => "30"));
		$row->AddInputField("DESCRIPTION", array("size" => "50"));
		$row->AddInputField("PHONE", array("size" => "25"));
		$row->AddInputField("SCHEDULE", array("size" => "35"));

		if (!$bExport)
			$row->AddField("IMAGE_ID", CFile::ShowImage($f_IMAGE_ID, 100, 100, "border=0", "", true));

	}

	$strCreatedBy = '';
	$strModifiedBy = '';
	if (array_key_exists('USER_ID', $arSelectFieldsMap))
	{
		$arSTORE['USER_ID'] = intval($arSTORE['USER_ID']);
		if (0 < $arSTORE['USER_ID'])
		{
			if (!array_key_exists($arSTORE['USER_ID'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arSTORE['USER_ID']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arSTORE['USER_ID']]))
				$strCreatedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arSTORE['USER_ID'].'">'.$arUserList[$arSTORE['USER_ID']].'</a>';
		}
	}
	if (array_key_exists('MODIFIED_BY', $arSelectFieldsMap))
	{
		$arSTORE['MODIFIED_BY'] = intval($arSTORE['MODIFIED_BY']);
		if (0 < $arSTORE['MODIFIED_BY'])
		{
			if (!array_key_exists($arSTORE['MODIFIED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arSTORE['MODIFIED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arSTORE['MODIFIED_BY']]))
				$strModifiedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arSTORE['MODIFIED_BY'].'">'.$arUserList[$arSTORE['MODIFIED_BY']].'</a>';
		}
	}

	if (array_key_exists('USER_ID', $arSelectFieldsMap))
		$row->AddViewField("USER_ID", $strCreatedBy);
	if (array_key_exists('DATE_CREATE', $arSelectFieldsMap))
		$row->AddViewField("DATE_CREATE", $arSTORE['DATE_CREATE']);
	if (array_key_exists('MODIFIED_BY', $arSelectFieldsMap))
		$row->AddViewField("MODIFIED_BY", $strModifiedBy);
	if (array_key_exists('DATE_MODIFY', $arSelectFieldsMap))
		$row->AddViewField("DATE_MODIFY", $arSTORE['DATE_MODIFY']);

	$arActions = array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("EDIT_STORE_ALT"), "ACTION"=>$lAdmin->ActionRedirect("cat_store_edit.php?ID=".$f_ID."&lang=".LANG."&".GetFilterParams("filter_").""), "DEFAULT"=>true);

	if (!$bReadOnly)
	{
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("DELETE_STORE_ALT"), "ACTION"=>"if(confirm('".GetMessage('DELETE_STORE_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResultList->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

if (!$bReadOnly)
{
	$lAdmin->AddGroupActionTable(
		array(
			"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
		)
	);
}

if (!$bReadOnly)
{
	$aContext = array(
		array(
			"TEXT" => GetMessage("STORE_ADD_NEW"),
			"ICON" => "btn_new",
			"LINK" => "cat_store_edit.php?lang=".LANG,
			"TITLE" => GetMessage("STORE_ADD_NEW_ALT")
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("STORE_TITLE"));
require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
$lAdmin->DisplayList();
?>

<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>