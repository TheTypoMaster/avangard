<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

if ('GET' == $_SERVER['REQUEST_METHOD'])
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if (('POST' == $_SERVER['REQUEST_METHOD']) && (false == isset($_REQUEST['saveresult'])))
	CUtil::JSPostUnescape();

global $DB;
global $APPLICATION;
global $USER;

define('DEF_LIST_VALUE_COUNT',5);

/*
* $intPropID - ID value or n0...nX
* $arPropInfo = array(
* 		ID
* 		XML_ID
* 		VALUE
* 		SORT
* 		DEF = Y/N
* 		MULTIPLE = Y/N
* )
*/
function __AddListValueIDCell($intPropID,$arPropInfo)
{
	return (0 < intval($intPropID) ? $intPropID : '&nbsp;');
}

function __AddListValueXmlIDCell($intPropID,$arPropInfo)
{
	return '<input type="text" name="PROPERTY_VALUES['.$intPropID.'][XML_ID]" id="PROPERTY_VALUES_XML_'.$intPropID.'" value="'.htmlspecialchars($arPropInfo['XML_ID']).'" size="15" maxlength="200">';
}

function __AddListValueValueCell($intPropID,$arPropInfo)
{
	return '<input type="text" name="PROPERTY_VALUES['.$intPropID.'][VALUE]" id="PROPERTY_VALUES_XML_'.$intPropID.'" value="'.htmlspecialchars($arPropInfo['VALUE']).'" size="35" maxlength="255">';
}

function __AddListValueSortCell($intPropID,$arPropInfo)
{
	return '<input type="text" name="PROPERTY_VALUES['.$intPropID.'][SORT]" id="PROPERTY_VALUES_SORT_'.$intPropID.'" value="'.intval($arPropInfo['SORT']).'" size="5" maxlength="11">';
}

function __AddListValueDefCell($intPropID,$arPropInfo)
{
	return '<input type="'.('Y' == $arPropInfo['MULTIPLE'] ? 'checkbox' : 'radio').'" name="PROPERTY_VALUES_DEF'.('Y' == $arPropInfo['MULTIPLE'] ? '[]' : '').'" id="PROPERTY_VALUES_DEF_'.$arPropInfo['ID'].'" value="'.$arPropInfo['ID'].'" '.('Y' == $arPropInfo['DEF'] ? 'checked="checked"' : '').'>';
}

function __AddListValueRow($intPropID,$arPropInfo)
{
	return '<tr><td>'.__AddListValueIDCell($intPropID,$arPropInfo).'</td>
	<td>'.__AddListValueXmlIDCell($intPropID,$arPropInfo).'</td>
	<td>'.__AddListValueValueCell($intPropID,$arPropInfo).'</td>
	<td>'.__AddListValueSortCell($intPropID,$arPropInfo).'</td>
	<td>'.__AddListValueDefCell($intPropID,$arPropInfo).'</td></tr>';
}

$arDisabledPropFields = array(
	'ID',
	'IBLOCK_ID',
	'TIMESTAMP_X',
	'TMP_ID',
	'VERSION',
);

$arDefPropInfo = array(
	'ID' => 'ntmp_xxx',
	'XML_ID' => '',
	'VALUE' => '',
	'SORT' => '500',
	'DEF' => 'N',
	'MULTIPLE' => 'N',
);

$arDefPropInfo = array(
	'ID' => 0,
	'IBLOCK_ID' => 0,
	'FILE_TYPE' => '',
	'LIST_TYPE' => 'L',
	'ROW_COUNT' => '1',
	'COL_COUNT' => '30',
	'LINK_IBLOCK_ID' => '0',
	'DEFAULT_VALUE' => '',
	'USER_TYPE_SETTINGS' => false,
	'WITH_DESCRIPTION' => '',
	'SEARCHABLE' => '',
	'FILTRABLE' => '',
	'ACTIVE' => 'Y',
	'MULTIPLE_CNT' => '5',
	'XML_ID' => '',
	'PROPERTY_TYPE' => 'S',
	'NAME' => '',
	'USER_TYPE' => '',
	'MULTIPLE' => 'N',
	'IS_REQUIRED' => 'N',
	'SORT' => '500',
	'CODE' => '',
	'SHOW_DEL' => 'N',
	'VALUES' => false,
);

$arHiddenPropFields = array(
	'IBLOCK_ID',
	'FILE_TYPE',
	'LIST_TYPE',
	'ROW_COUNT',
	'COL_COUNT',
	'LINK_IBLOCK_ID',
	'DEFAULT_VALUE',
	'USER_TYPE_SETTINGS',
	'WITH_DESCRIPTION',
	'SEARCHABLE',
	'FILTRABLE',
	'MULTIPLE_CNT',
	'XML_ID',
	'VALUES',
);

if (!check_bitrix_sessid())
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}
if ((!isset($_REQUEST["PARAMS"]['IBLOCK_ID'])) || (0 == strlen($_REQUEST["PARAMS"]['IBLOCK_ID'])))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo ShowError(GetMessage("BT_ADM_IEP_IBLOCK_ID_IS_ABSENT"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}
$intIBlockID = $_REQUEST["PARAMS"]['IBLOCK_ID'];
$intIBlockIDCheck = intval($intIBlockID);
if ($intIBlockIDCheck.'|' != $intIBlockID.'|' || $intIBlockIDCheck < 0)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo ShowError(GetMessage("BT_ADM_IEP_IBLOCK_ID_IS_INVALID"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}
else
{
	$intIBlockID = $intIBlockIDCheck;
	unset($intIBlockIDCheck);
}

if (0 < $intIBlockID)
{
	$rsIBlocks = CIBlock::GetList(array(), array("ID" => $intIBlockID, "CHECK_PERMISSIONS" => "N"));
	if (($arIBlock = $rsIBlocks->Fetch()))
	{
		if (!CIBlockRights::UserHasRightTo($intIBlockID, $intIBlockID, "iblock_edit"))
		{
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
			$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
			die();
		}
	}
	else
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		echo ShowError(str_replace('#ID#',$intIBlockID,GetMessage("BT_ADM_IEP_IBLOCK_NOT_EXISTS")));
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		die();
	}
}
else
{
	if (!$USER->IsAdmin())
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		die();
	}
}

if ((!isset($_REQUEST["PARAMS"]['ID'])) || (0 == strlen($_REQUEST["PARAMS"]['ID'])))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo ShowError(GetMessage("BT_ADM_IEP_PROPERTY_ID_IS_ABSENT"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$str_PROPERTY_ID = htmlspecialchars($_REQUEST["PARAMS"]['ID']);
if (1 != preg_match('/^n\d+$/',$str_PROPERTY_ID))
{
	$str_PROPERTY_IDCheck = intval($str_PROPERTY_ID);
	if (0 == $intIBlockID || ($str_PROPERTY_IDCheck.'|' != $str_PROPERTY_ID.'|') || 0 >= $str_PROPERTY_IDCheck)
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		echo ShowError(GetMessage("BT_ADM_IEP_PROPERTY_ID_IS_ABSENT"));
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		die();
	}
	else
	{
		$str_PROPERTY_ID = $str_PROPERTY_IDCheck;
		unset($str_PROPERTY_IDCheck);
		$rsProps = CIBlockProperty::GetByID($str_PROPERTY_ID,$intIBlockID);
		if (!($arPropCheck = $rsProps->Fetch()))
		{
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
			echo ShowError(str_replace('#ID#',$str_PROPERTY_ID,GetMessage("BT_ADM_IEP_PROPERTY_IS_NOT_EXISTS")));
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
			die();
		}
	}
}

$strReceiver = '';

if (isset($_REQUEST["PARAMS"]['RECEIVER']))
	$strReceiver = preg_replace("/[^a-zA-Z0-9_:]/", "", htmlspecialchars(($_REQUEST["PARAMS"]['RECEIVER'])));

if (isset($_REQUEST['saveresult']))
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

	unset($_POST['saveresult']);
	$PARAMS = $_POST['PARAMS'];
	unset($_POST['PARAMS']);

	$arProperty = array();

	$arFieldsList = $DB->GetTableFieldsList("b_iblock_property");
	foreach ($arFieldsList as $strFieldName)
	{
		if (!in_array($strFieldName,$arDisabledPropFields))
		{
			if (isset($_POST['PROPERTY_'.$strFieldName]))
			{
				$arProperty[$strFieldName] = $_POST['PROPERTY_'.$strFieldName];
			}
			else
				$arProperty[$strFieldName] = $arDefPropInfo[$strFieldName];
		}
	}

	$arProperty['MULTIPLE'] = ('Y' == $arProperty['MULTIPLE'] ? 'Y' : 'N');
	$arProperty['IS_REQUIRED'] = ('Y' == $arProperty['IS_REQUIRED'] ? 'Y' : 'N');
	$arProperty['FILTRABLE'] = ('Y' == $arProperty['FILTRABLE'] ? 'Y' : 'N');
	$arProperty['SEARCHABLE'] = ('Y' == $arProperty['SEARCHABLE'] ? 'Y' : 'N');
	$arProperty['ACTIVE'] = ('Y' == $arProperty['ACTIVE'] ? 'Y' : 'N');
	$arProperty['MULTIPLE_CNT'] = intval($arProperty['MULTIPLE_CNT']);
	if (0 >= $arProperty['MULTIPLE_CNT'])
		$arProperty['MULTIPLE_CNT'] = DEF_LIST_VALUE_COUNT;
	$arProperty['WITH_DESCRIPTION'] = ('Y' == $arProperty['WITH_DESCRIPTION'] ? 'Y' : 'N');

	if (isset($_POST['PROPERTY_VALUES']) && is_array($_POST['PROPERTY_VALUES']))
	{
		$arProperty['VALUES'] = array();
		$boolDefCheck = false;
		if ('Y' == $arProperty['MULTIPLE'])
		{
			$boolDefCheck = (isset($_POST['PROPERTY_VALUES_DEF']) && is_array($_POST['PROPERTY_VALUES_DEF']));
		}
		else
		{
			$boolDefCheck = isset($_POST['PROPERTY_VALUES_DEF']);
		}
		$intNewKey = 0;
		foreach ($_POST['PROPERTY_VALUES'] as $key => $arValue)
		{
			if (!is_array($arValue) || !isset($arValue['VALUE']) || '' == trim($arValue['VALUE']))
				continue;
			$arProperty['VALUES'][(0 < intval($key) ? $key : 'n'.$intNewKey)] = array(
				'ID' => (0 < intval($key) ? $key : 'n'.$intNewKey),
				'VALUE' => strval($arValue['VALUE']),
				'XML_ID' => (isset($arValue['XML_ID']) ? strval($arValue['XML_ID']) : ''),
				'SORT' => (isset($arValue['SORT']) ? intval($arValue['SORT']) : 500),
				'DEF' => ($boolDefCheck ?
							('Y' == $arProperty['MULTIPLE'] ?
								(in_array($key, $_POST['PROPERTY_VALUES_DEF']) ? 'Y' : 'N') :
								($key == $_POST['PROPERTY_VALUES_DEF'] ? 'Y' : 'N')) :
							'N'),
			);
			if (0 >= intval($key))
				$intNewKey++;
		}
	}

	$arHidden = array();
	foreach ($arHiddenPropFields as &$strPropField)
	{
		if (isset($arProperty[$strPropField]))
		{
			$arHidden[$strPropField] = $arProperty[$strPropField];
			unset($arProperty[$strPropField]);
		}
	}
	$arProperty['PROPINFO'] = base64_encode(serialize($arHidden));

	$strResult = CUtil::PhpToJsObject($arProperty);
	?><script type="text/javascript">
	arResult = <? echo $strResult; ?>;
	if (top.<? echo $strReceiver; ?>)
	{
		top.<? echo $strReceiver; ?>.SetPropInfo('<? echo $PARAMS['ID']; ?>',arResult,'<? echo bitrix_sessid(); ?>');
	}
	top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();
</script><?
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
}
else
{
	$arProperty = array();
	$PROPERTY = $_POST['PROP'];
	$PARAMS = $_POST['PARAMS'];

	if ((isset($PARAMS['TITLE'])) && ('' != $PARAMS['TITLE']))
	{
		$APPLICATION->SetTitle($PARAMS['TITLE']);
	}

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	$arFieldsList = $DB->GetTableFieldsList("b_iblock_property");
	foreach ($arFieldsList as $strFieldName)
	{
		if (!in_array($strFieldName,$arDisabledPropFields))
			$arProperty[$strFieldName] = (isset($PROPERTY[$strFieldName]) ? htmlspecialcharsback($PROPERTY[$strFieldName]) : '');
	}
	$arProperty['PROPINFO'] = $PROPERTY['PROPINFO'];
	$arProperty['PROPINFO'] = base64_decode($arProperty['PROPINFO']);
	if (CheckSerializedData($arProperty['PROPINFO']))
	{
		$arTempo = unserialize($arProperty['PROPINFO']);
		if (is_array($arTempo))
		{
			foreach ($arTempo as $k => $v)
				$arProperty[$k] = $v;
		}
		unset($arTempo);
		unset($arProperty['PROPINFO']);
	}

	$arProperty['MULTIPLE'] = ('Y' == $arProperty['MULTIPLE'] ? 'Y' : 'N');
	$arProperty['IS_REQUIRED'] = ('Y' == $arProperty['IS_REQUIRED'] ? 'Y' : 'N');
	$arProperty['FILTRABLE'] = ('Y' == $arProperty['FILTRABLE'] ? 'Y' : 'N');
	$arProperty['SEARCHABLE'] = ('Y' == $arProperty['SEARCHABLE'] ? 'Y' : 'N');
	$arProperty['ACTIVE'] = ('Y' == $arProperty['ACTIVE'] ? 'Y' : 'N');
	$arProperty['MULTIPLE_CNT'] = intval($arProperty['MULTIPLE_CNT']);
	if (0 >= $arProperty['MULTIPLE_CNT'])
		$arProperty['MULTIPLE_CNT'] = DEF_LIST_VALUE_COUNT;
	$arProperty['WITH_DESCRIPTION'] = ('Y' == $arProperty['WITH_DESCRIPTION'] ? 'Y' : 'N');

	$arProperty['USER_TYPE'] = '';
	if (false !== strpos($arProperty['PROPERTY_TYPE'],':'))
	{
		list($arProperty['PROPERTY_TYPE'],$arProperty['USER_TYPE']) = explode(':', $arProperty['PROPERTY_TYPE'], 2);
	}

	$arTypesList = array(
		"S" => GetMessage("BT_ADM_IEP_PROP_TYPE_S"),
		"N" => GetMessage("BT_ADM_IEP_PROP_TYPE_N"),
		"L" => GetMessage("BT_ADM_IEP_PROP_TYPE_L"),
		"F" => GetMessage("BT_ADM_IEP_PROP_TYPE_F"),
		"G" => GetMessage("BT_ADM_IEP_PROP_TYPE_G"),
		"E" => GetMessage("BT_ADM_IEP_PROP_TYPE_E"),
	);

	?><form method="POST" name="frm_prop" id="frm_prop" action="<? echo $APPLICATION->GetCurPageParam(); ?>" enctype="multipart/form-data">
	<? echo bitrix_sessid_post(); ?>
	<input type="hidden" name="saveresult" value="Y">
	<input type="hidden" name="propedit" value="<? echo $str_PROPERTY_ID; ?>">
	<input type="hidden" name="receiver" value="<? echo $strReceiver; ?>">
	<?
	foreach ($PARAMS as $key => $value)
	{
		if ('TITLE' != $key)
		{
			?><input type="hidden" name="PARAMS[<? echo htmlspecialchars($key); ?>]" value="<? echo htmlspecialchars($value); ?>"><?
		}
	}
	?>
	<table class="edit-table" width="100%"><tbody><?
	$arProperty["ID"] = $PARAMS['ID'];
	$arProperty['USER_TYPE'] = trim($arProperty['USER_TYPE']);
	$arUserType = ('' != $arProperty['USER_TYPE'] ? CIBlockProperty::GetUserType($arProperty['USER_TYPE']) : array());

	$arPropertyFields = array();
	$USER_TYPE_SETTINGS_HTML = "";
	if(array_key_exists("GetSettingsHTML", $arUserType))
		$USER_TYPE_SETTINGS_HTML = call_user_func_array($arUserType["GetSettingsHTML"],
			array(
				$arProperty,
				array(
					"NAME"=>"PROPERTY_USER_TYPE_SETTINGS",
				),
				&$arPropertyFields,
			)
		);
	?><input type="hidden" id="PROPERTY_PROPERTY_TYPE" name="PROPERTY_PROPERTY_TYPE" value="<?echo htmlspecialchars($arProperty['PROPERTY_TYPE'].($arProperty['USER_TYPE']? ':'.$arProperty['USER_TYPE']: '')); ?>">
<tr>
	<td width="40%">ID:</td>
	<td width="60%"><? echo (0 < intval($arProperty['ID']) ? $arProperty['ID'] : GetMessage("BT_ADM_IEP_PROP_NEW"))?></td>
</tr>
<tr>
	<td width="40%"><? echo GetMessage('BT_ADM_IEP_PROPERTY_TYPE'); ?></td>
	<td width="60%"><?
	$strDescr = '';
	if (isset($arUserType['DESCRIPTION']))
	{
		$strDescr = $arUserType['DESCRIPTION'];
	}
	elseif (isset($arTypesList[$arProperty['PROPERTY_TYPE']]))
	{
		$strDescr = $arTypesList[$arProperty['PROPERTY_TYPE']];
	}
	echo $strDescr;
	?></td>
</tr>
<tr>
	<td width="40%"><label for="PROPERTY_ACTIVE_Y"><?echo GetMessage("BT_ADM_IEP_PROP_ACT")?></label></td>
	<td width="60%"><input type="hidden" id="PROPERTY_ACTIVE_N" name="PROPERTY_ACTIVE" value="N">
		<input type="checkbox" id="PROPERTY_ACTIVE_Y" name="PROPERTY_ACTIVE" value="Y"<?if ('Y' == $arProperty['ACTIVE']) echo ' checked="checked"'; ?>></td>
</tr>
<tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_SORT_DET")?></td>
	<td><input type="text" size="3" maxlength="10" id="PROPERTY_SORT" name="PROPERTY_SORT" value="<? echo intval($arProperty['SORT']); ?>"></td>
</tr>
<tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_NAME_DET")?></td>
	<td ><input type="text" size="30" maxlength="100" id="PROPERTY_NAME" name="PROPERTY_NAME" value="<? echo htmlspecialchars($arProperty['NAME']);?>"></td>
</tr>
<tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_CODE_DET")?></td>
	<td><input type="text" size="30" maxlength="50" id="PROPERTY_CODE" name="PROPERTY_CODE" value="<? echo htmlspecialchars($arProperty['CODE'])?>"></td>
</tr>
<?
	if (COption::GetOptionString("iblock", "show_xml_id", "N")=="Y")
	{?><tr>
		<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_EXTERNAL_CODE")?></td>
		<td><input type="text" size="30" maxlength="50" id="PROPERTY_XML_ID" name="PROPERTY_XML_ID" value="<? echo htmlspecialchars($arProperty['XML_ID'])?>"></td>
		</tr><?
	}?>
<tr>
	<td width="40%"><label for="PROPERTY_MULTIPLE_Y"><?echo GetMessage("BT_ADM_IEP_PROP_MULTIPLE")?></label></td>
	<td>
		<input type="hidden" id="PROPERTY_MULTIPLE_N" name="PROPERTY_MULTIPLE" value="N">
		<input type="checkbox" id="PROPERTY_MULTIPLE_Y" name="PROPERTY_MULTIPLE" value="Y"<?if('Y' == $arProperty['MULTIPLE']) echo ' checked="checked"'?> onclick="if(BX('PROPERTY_MULTIPLE_CNT')) BX('PROPERTY_MULTIPLE_CNT').disabled = !this.checked">
	</td>
</tr>
<tr>
	<td width="40%"><label for="PROPERTY_IS_REQUIRED_Y"><?echo GetMessage("BT_ADM_IEP_PROP_IS_REQUIRED")?></label></td>
	<td>
		<input type="hidden" id="PROPERTY_IS_REQUIRED_N" name="PROPERTY_IS_REQUIRED" value="N">
		<input type="checkbox" id="PROPERTY_IS_REQUIRED_Y" name="PROPERTY_IS_REQUIRED" value="Y"<?if('Y' == $arProperty['IS_REQUIRED'])echo ' checked="checked"'?>>
	</td>
</tr>
<?
	$bShow = true;
	if(is_array($arPropertyFields["SHOW"]) && in_array("SEARCHABLE", $arPropertyFields["SHOW"]))
		$bShow = true;
	elseif(is_array($arPropertyFields["HIDE"]) && in_array("SEARCHABLE", $arPropertyFields["HIDE"]))
		$bShow = false;
	elseif('E' == $arProperty['PROPERTY_TYPE'] || 'G' == $arProperty['PROPERTY_TYPE'])
		$bShow = false;

	if ($bShow)
	{?><tr>
		<td width="40%"><label id="PROPERTY_SEARCHABLE_Y"><?echo GetMessage("BT_ADM_IEP_PROP_SEARCHABLE")?></label></td>
		<td>
			<input type="hidden" id="PROPERTY_SEARCHABLE_N" name="PROPERTY_SEARCHABLE" value="N">
			<input type="checkbox" id="PROPERTY_SEARCHABLE_Y" name="PROPERTY_SEARCHABLE" value="Y" <?if('Y' == $arProperty['SEARCHABLE'])echo ' checked="checked"';?>>
		</td>
		</tr><?
	} elseif(
		is_array($arPropertyFields["SET"]) && array_key_exists("SEARCHABLE", $arPropertyFields["SET"])
	){
		?><input type="hidden" id="PROPERTY_SEARCHABLE_Y" name="PROPERTY_SEARCHABLE" value="<?echo htmlspecialchars($arPropertyFields["SET"]["SEARCHABLE"])?>"><?
	}
	$bShow = true;
	if(is_array($arPropertyFields["SHOW"]) && in_array("FILTRABLE", $arPropertyFields["SHOW"]))
		$bShow = true;
	elseif(is_array($arPropertyFields["HIDE"]) && in_array("FILTRABLE", $arPropertyFields["HIDE"]))
		$bShow = false;
	elseif($arProperty['PROPERTY_TYPE'] == 'F')
		$bShow = false;

	if ($bShow)
	{?><tr>
		<td width="40%"><label for="PROPERTY_FILTRABLE_Y"><?echo GetMessage("BT_ADM_IEP_PROP_FILTRABLE")?></label></td>
		<td>
			<input type="hidden" id="PROPERTY_FILTRABLE_N" name="PROPERTY_FILTRABLE" value="N">
			<input type="checkbox" id="PROPERTY_FILTRABLE_Y" name="PROPERTY_FILTRABLE" value="Y" <?if('Y' == $arProperty['FILTRABLE'])echo ' checked="checked"'?>>
		</td>
	</tr><?
	} elseif(
		is_array($arPropertyFields["SET"]) && array_key_exists("FILTRABLE", $arPropertyFields["SET"])
	){?>
		<input type="hidden" id="PROPERTY_FILTRABLE_Y" name="PROPERTY_FILTRABLE" value="<?echo htmlspecialchars($arPropertyFields["SET"]["FILTRABLE"])?>">
	<?}
	$bShow = true;
	if (is_array($arPropertyFields["SHOW"]) && in_array("WITH_DESCRIPTION", $arPropertyFields["SHOW"]))
		$bShow = true;
	elseif (is_array($arPropertyFields["HIDE"]) && in_array("WITH_DESCRIPTION", $arPropertyFields["HIDE"]))
		$bShow = false;
	elseif ('L' == $arProperty['PROPERTY_TYPE'] || 'G' == $arProperty['PROPERTY_TYPE'] || 'E' == $arProperty['PROPERTY_TYPE'])
		$bShow = false;

	if ($bShow)
	{?><tr>
		<td width="40%"><label for="PROPERTY_WITH_DESCRIPTION_Y"><?echo GetMessage("BT_ADM_IEP_PROP_WITH_DESC")?></label></td>
		<td>
			<input type="hidden" id="PROPERTY_WITH_DESCRIPTION_N" name="PROPERTY_WITH_DESCRIPTION" value="N">
			<input type="checkbox" id="PROPERTY_WITH_DESCRIPTION_Y" name="PROPERTY_WITH_DESCRIPTION" value="Y" <?if('Y' == $arProperty['WITH_DESCRIPTION'])echo " checked"?>>
		</td>
	</tr><?
	} elseif(
		is_array($arPropertyFields["SET"]) && array_key_exists("WITH_DESCRIPTION", $arPropertyFields["SET"])
	){?>
		<input type="hidden" id="PROPERTY_WITH_DESCRIPTION_Y" name="PROPERTY_WITH_DESCRIPTION" value="<?echo htmlspecialchars($arPropertyFields["SET"]["WITH_DESCRIPTION"])?>">
	<?
	}
	$bShow = true;
	if (is_array($arPropertyFields["SHOW"]) && in_array("MULTIPLE_CNT", $arPropertyFields["SHOW"]))
		$bShow = true;
	elseif (is_array($arPropertyFields["HIDE"]) && in_array("MULTIPLE_CNT", $arPropertyFields["HIDE"]))
		$bShow = false;
	elseif ('L' == $arProperty['PROPERTY_TYPE'])
		$bShow = false;

	if ($bShow)
	{?><tr>
		<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_MULTIPLE_CNT")?></td>
		<td><input type="text" id="PROPERTY_MULTIPLE_CNT" name="PROPERTY_MULTIPLE_CNT"  value="<?echo intval($arProperty['MULTIPLE_CNT']); ?>" size="3" <?echo 'Y' == $arProperty['MULTIPLE']? '': "disabled"?>></td>
		</tr><?
	} elseif(
		is_array($arPropertyFields["SET"]) && array_key_exists("MULTIPLE_CNT", $arPropertyFields["SET"])
	){?>
		<input type="hidden" id="PROPERTY_MULTIPLE_CNT" name="PROPERTY_MULTIPLE_CNT" value="<?echo htmlspecialchars($arPropertyFields["SET"]["MULTIPLE_CNT"])?>">
	<?
	}
// PROPERTY_TYPE specific properties
	if ('L' == $arProperty['PROPERTY_TYPE'])
	{?><tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_APPEARANCE")?></td>
	<td>
		<select id="PROPERTY_LIST_TYPE" name="PROPERTY_LIST_TYPE">
			<option value="L"<?if($arProperty['LIST_TYPE']!="C")echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_APPEARANCE_LIST")?></option>
			<option value="C"<?if($arProperty['LIST_TYPE']=="C")echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_APPEARANCE_CHECKBOX")?></option>
		</select>
	</td>
</tr>
<?
		$bShow = true;
		if (is_array($arPropertyFields["SHOW"]) && in_array("ROW_COUNT", $arPropertyFields["SHOW"]))
			$bShow = true;
		elseif (is_array($arPropertyFields["HIDE"]) && in_array("ROW_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_ROW_CNT")?></td>
			<td><input type="text" size="2" maxlength="10" id="PROPERTY_ROW_COUNT" name="PROPERTY_ROW_COUNT" value="<?echo intval($arProperty['ROW_COUNT']); ?>"></td>
		</tr><?
		} elseif(
			is_array($arPropertyFields["SET"]) && array_key_exists("ROW_COUNT", $arPropertyFields["SET"])
		){?>
			<input type="hidden" id="PROPERTY_ROW_COUNT" name="PROPERTY_ROW_COUNT" value="<?echo htmlspecialchars($arPropertyFields["SET"]["ROW_COUNT"])?>">
		<?}
?><tr class="heading"><td valign="top" colspan="2"><?echo GetMessage("BT_ADM_IEP_PROP_LIST_VALUES")?></td></tr>
<tr>
	<td colspan="2" align="center">
	<table cellpadding="1" cellspacing="0" border="0" id="list-tbl">
		<tr>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_ID")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_XML_ID")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_VALUE")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_SORT")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_DEFAULT")?></td>
		</tr>
	<?
		if ('Y' != $arProperty['MULTIPLE'])
		{
			$boolDef = true;
			if (isset($arProperty['VALUES']) && is_array($arProperty['VALUES']))
			{
				foreach ($arProperty['VALUES'] as &$arListValue)
				{
					if ('Y' == $arListValue['DEF'])
					{
						$boolDef = false;
						break;
					}
				}
				unset($arListValue);
			}
		?><tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="2"><?echo GetMessage("BT_ADM_IEP_PROP_LIST_DEFAULT_NO")?></td>
		<td><input type="radio" name="PROPERTY_VALUES_DEF" value="0" <?if ($boolDef) echo " checked"; ?>> </td>
		</tr>
		<?
		}
		$MAX_NEW_ID = 0;
		if (isset($arProperty['VALUES']) && is_array($arProperty['VALUES']))
		{
			foreach ($arProperty['VALUES'] as $intKey => $arListValue)
			{
				$arPropInfo = array(
					'ID' => $intKey,
					'XML_ID' => $arListValue['XML_ID'],
					'VALUE' => $arListValue['VALUE'],
					'SORT' => (0 < intval($arListValue['SORT']) ? intval($arListValue['SORT']) : '500'),
					'DEF' => ('Y' == $arListValue['DEF'] ? 'Y' : 'N'),
					'MULTIPLE' => $arProperty['MULTIPLE'],
				);
				echo __AddListValueRow($intKey,$arPropInfo);
			}
			$MAX_NEW_ID = sizeof($arProperty['VALUES']);
		}

		for ($i = $MAX_NEW_ID; $i < $MAX_NEW_ID+DEF_LIST_VALUE_COUNT; $i++)
		{
			$intKey = 'n'.$i;
			$arPropInfo = array(
				'ID' => $intKey,
				'XML_ID' => '',
				'VALUE' => '',
				'SORT' => '500',
				'DEF' => 'N',
				'MULTIPLE' => $arProperty['MULTIPLE'],
			);
			echo __AddListValueRow($intKey,$arPropInfo);
		}
		?>
		</table>
		<input type="hidden" name="PROPERTY_CNT" id="PROPERTY_CNT" value="<?echo ($MAX_NEW_ID+DEF_LIST_VALUE_COUNT)?>">
		<input type="button"  name="propedit_add" value="<?echo GetMessage("BT_ADM_IEP_PROP_LIST_MORE")?>" onclick="add_list_row()">
		</td>
</tr><?
	}
	elseif ("F" == $arProperty['PROPERTY_TYPE'])
	{
		$bShow = true;
		if (is_array($arPropertyFields["SHOW"]) && in_array("COL_COUNT", $arPropertyFields["SHOW"]))
			$bShow = true;
		elseif (is_array($arPropertyFields["HIDE"]) && in_array("COL_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_COL_CNT")?></td>
			<td><input type="text" size="2" maxlength="10" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT'])?>"></td>
		</tr><?
		} elseif(
			is_array($arPropertyFields["SET"]) && array_key_exists("COL_COUNT", $arPropertyFields["SET"])
		){?>
		<input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo htmlspecialchars($arPropertyFields["SET"]["COL_COUNT"])?>">
		<?}?>
<tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES")?></td>
	<td>
		<input type="text"  size="30" maxlength="255" name="PROPERTY_FILE_TYPE" value="<?echo htmlspecialchars($arProperty['FILE_TYPE']); ?>" id="CURRENT_PROPERTY_FILE_TYPE">
		<select  onchange="if(this.selectedIndex!=0) document.getElementById('CURRENT_PROPERTY_FILE_TYPE').value=this[this.selectedIndex].value">
			<option value="-"></option>
			<option value=""<?if('' == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_ANY")?></option>
			<option value="jpg, gif, bmp, png, jpeg"<?if("jpg, gif, bmp, png, jpeg" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_PIC")?></option>
			<option value="mp3, wav, midi, snd, au, wma"<?if("mp3, wav, midi, snd, au, wma" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_SOUND")?></option>
			<option value="mpg, avi, wmv, mpeg, mpe"<?if("mpg, avi, wmv, mpeg, mpe" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_VIDEO")?></option>
			<option value="doc, txt, rtf"<?if("doc, txt, rtf" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_DOCS")?></option>
		</select>
	</td>
</tr>
<?
	}
	elseif ("G" == $arProperty['PROPERTY_TYPE'] || "E" == $arProperty['PROPERTY_TYPE'])
	{
		$bShow = false;
		if (is_array($arPropertyFields["SHOW"]) && in_array("COL_COUNT", $arPropertyFields["SHOW"]))
			$bShow = true;
		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_COL_CNT")?></td>
			<td><input type="text" size="2" maxlength="10" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT']);?>"></td>
			</tr>
			<?
		} elseif(
			is_array($arPropertyFields["SET"]) && array_key_exists("COL_COUNT", $arPropertyFields["SET"])
		){?>
			<input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo htmlspecialchars($arPropertyFields["SET"]["COL_COUNT"])?>">
		<?}?>
	<tr>
		<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_LINK_IBLOCK")?></td>
		<td>
		<?
		$b_f = ($arProperty['PROPERTY_TYPE']=="G" ? array("!ID"=>$intIBlockID) : array());
		echo GetIBlockDropDownList(
			$arProperty['LINK_IBLOCK_ID'],
			"PROPERTY_LINK_IBLOCK_TYPE_ID",
			"PROPERTY_LINK_IBLOCK_ID",
			$b_f
		);
		?>
		</td>
	</tr>
	<?}
	else
	{
		$bShow = true;
		if (is_array($arPropertyFields["HIDE"]) && in_array("COL_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;
		elseif (is_array($arPropertyFields["HIDE"]) && in_array("ROW_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_SIZE")?></td>
			<td>
				<input type="text"  size="2" maxlength="10" name="PROPERTY_ROW_COUNT" value="<?echo intval($arProperty['ROW_COUNT']); ?>"> x <input type="text"  size="2" maxlength="10" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT']); ?>">
			</td>
		</tr>
		<?} else {
			if (is_array($arPropertyFields["SET"]) && array_key_exists("ROW_COUNT", $arPropertyFields["SET"]))
			{?><input type="hidden" name="PROPERTY_ROW_COUNT" value="<?echo htmlspecialchars($arPropertyFields["SET"]["ROW_COUNT"])?>"><?}
			else
			{?><input type="hidden" name="PROPERTY_ROW_COUNT" value="<?echo intval($arProperty['ROW_COUNT'])?>"><?}

			if(is_array($arPropertyFields["SET"]) && array_key_exists("COL_COUNT", $arPropertyFields["SET"]))
			{?><input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo htmlspecialchars($arPropertyFields["SET"]["COL_COUNT"])?>"><? }
			else
			{ ?><input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT']); ?>"><? }
		}

		$bShow = true;
		if (is_array($arPropertyFields["HIDE"]) && in_array("DEFAULT_VALUE", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_DEFAULT")?></td>
			<td>
			<?if(array_key_exists("GetPropertyFieldHtml", $arUserType))
			{
				echo call_user_func_array($arUserType["GetPropertyFieldHtml"],
					array(
						$arProperty,
						array(
							"VALUE"=>$arProperty["DEFAULT_VALUE"],
							"DESCRIPTION"=>""
						),
						array(
							"VALUE"=>"PROPERTY_DEFAULT_VALUE",
							"DESCRIPTION"=>"",
							"MODE" => "EDIT_FORM",
							"FORM_NAME" => "frm_prop"
						),
					));
			} else {
				?><input type="text"  size="40" maxlength="2000" name="PROPERTY_DEFAULT_VALUE" value="<?echo htmlspecialchars($arProperty['DEFAULT_VALUE']);?>"><?
			}
		?></td>
	</tr><?
		}
	}
	if ($USER_TYPE_SETTINGS_HTML)
	{?><tr class="heading"><td colspan="2"><?
		echo (isset($arPropertyFields["USER_TYPE_SETTINGS_TITLE"]) && '' != trim($arPropertyFields["USER_TYPE_SETTINGS_TITLE"]) ? $arPropertyFields["USER_TYPE_SETTINGS_TITLE"] : GetMessage("BT_ADM_IEP_PROP_USER_TYPE_SETTINGS"));
		?></td></tr><?
		echo $USER_TYPE_SETTINGS_HTML;
	}
?></tbody></table></form>
<?
	if('L' == $arProperty['PROPERTY_TYPE'])
	{
?><script type="text/javascript">
BX.ready(
	function(){
			setTimeout(function(){
				window.oPropSet = {
					pTypeTbl: BX("list-tbl"),
					curCount: <? echo ($MAX_NEW_ID+5); ?>,
					intCounter: BX("PROPERTY_CNT")
				};
			},50);
		});

function add_list_row()
{
	var id = window.oPropSet.curCount++;
	window.oPropSet.intCounter.value = window.oPropSet.curCount;
	var newRow = window.oPropSet.pTypeTbl.insertRow(window.oPropSet.pTypeTbl.rows.length);

	var oCell = newRow.insertCell(-1);
	var strContent = '<? echo CUtil::JSEscape(__AddListValueIDCell('ntmp_xxx',$arDefPropInfo)); ?>';
	strContent = strContent.replace(/tmp_xxx/ig, id);
	oCell.innerHTML = strContent;
	var oCell = newRow.insertCell(-1);
	var strContent = '<? echo CUtil::JSEscape(__AddListValueXmlIDCell('ntmp_xxx',$arDefPropInfo)); ?>';
	strContent = strContent.replace(/tmp_xxx/ig, id);
	oCell.innerHTML = strContent;
	var oCell = newRow.insertCell(-1);
	var strContent = '<? echo CUtil::JSEscape(__AddListValueValueCell('ntmp_xxx',$arDefPropInfo)); ?>';
	strContent = strContent.replace(/tmp_xxx/ig, id);
	oCell.innerHTML = strContent;
	var oCell = newRow.insertCell(-1);
	var strContent = '<? echo CUtil::JSEscape(__AddListValueSortCell('ntmp_xxx',$arDefPropInfo)); ?>';
	strContent = strContent.replace(/tmp_xxx/ig, id);
	oCell.innerHTML = strContent;
	var oCell = newRow.insertCell(-1);
	var strContent = '<? echo CUtil::JSEscape(__AddListValueDefCell('ntmp_xxx',$arDefPropInfo)); ?>';
	strContent = strContent.replace(/tmp_xxx/ig, id);
	oCell.innerHTML = strContent;
}
</script>
<?
	}
?>
<script type="text/javascript">
	BX.WindowManager.Get().adjustSizeEx();
</script>
<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}
?>