<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);

$arTypesEx = Array("-"=>" ");
if(CModule::IncludeModule("iblock")):

$db_iblock_type = CIBlockType::GetList(Array("SORT"=>"ASC"));
while($arRes = $db_iblock_type->Fetch())
	if($arIBType = CIBlockType::GetByIDLang($arRes["ID"], LANG))
		$arTypesEx[$arRes["ID"]] = $arIBType["NAME"];

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arTemplateDescription =
Array(
	"menu_items.php" =>
		Array(
			"NAME" => GetMessage("T_IBLOCK_DESC_MENU_ITEMS"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_MENU_ITEMS_DESC"),
			"ICON" => "/bitrix/images/iblock/components/cat_list.gif",
			"PARAMS" =>
			Array(
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_MENU_ITEM_ID"), "TYPE"=>"STRING", "DEFAULT"=>'={$_REQUEST["ID"]}'),
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_MENU_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"catalog", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"IBLOCK_ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_MENU_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'1', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_MENU_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0'),
				"SECTION_URL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_MENU_SECTION_URL"), "TYPE"=>"STRING", "DEFAULT"=>"/catalog/phone/section.php?"),
			),
		),
);

endif;
?>
