<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
$iSort = 100;

$arTypes = Array();
$arTypesEx = Array("-"=>" ");
if(CModule::IncludeModule("iblock")):

$db_iblock_type = CIBlockType::GetList(Array("SORT"=>"ASC"));
while($arRes = $db_iblock_type->Fetch())
	if($arIBType = CIBlockType::GetByIDLang($arRes["ID"], LANG))
	{
		$arTypesEx[$arRes["ID"]] = $arIBType["NAME"];
		$arTypes[$arRes["ID"]] = $arIBType["NAME"];
	}

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);


$arUGroupsEx = Array();
$dbUGroups = CGroup::GetList($by = "c_sort", $order = "asc");
while($arUGroups = $dbUGroups -> Fetch())
{
	$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
}




$arTemplateDescription =
Array(
	".separator" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_SALE"),
			"DESCRIPTION" => "",
			"SEPARATOR"	 => "Y",
		),
	"news.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_SALE_LIST"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_LIST_SALE_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/iblock_subscr_list.gif",
			"PARAMS"		=>
			Array(
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"catalog", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'={$_REQUEST["ID"]}', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"Y", "REFRESH" => "N"),
				"GROUP_PERMISSIONS" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_SALE_UGROUPS"), "TYPE"=>"LIST", "VALUES"=>$arUGroupsEx, "DEFAULT"=> Array(1), "MULTIPLE"=>"Y", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "N"),
				"NEWS_COUNT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_CONT"), "TYPE"=>"STRING", "DEFAULT"=>"20", "COLS"=>"3"),
				"SORT_BY1" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD1"), "TYPE"=>"LIST", "DEFAULT"=>"ACTIVE_FROM", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER1" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY1"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"DESC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"SORT_BY2" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD2"), "TYPE"=>"LIST", "DEFAULT"=>"SORT", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER2" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY2"), "TYPE"=>"LIST", "DEFAULT"=>"ASC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0')
			)
		),
	"detail.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_DETAIL"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_DETAIL_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/iblock_subscr_det.gif",
			"PARAMS"		=>
			Array(
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_DETAIL_ID"), "TYPE"=>"STRING", "DEFAULT"=>'={$_REQUEST["ID"]}'),
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"catalog", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "N"),
				"GROUP_PERMISSIONS" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_SALE_UGROUPS"), "TYPE"=>"LIST", "VALUES"=>$arUGroupsEx, "DEFAULT"=> Array(1), "MULTIPLE"=>"Y", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "N"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0'),
			)
		),
);
endif;
?>
