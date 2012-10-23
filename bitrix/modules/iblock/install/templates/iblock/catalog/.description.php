<?
IncludeTemplateLangFile(__FILE__);
if (!CModule::IncludeModule("iblock")) return;

$rsType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while ($arr=$rsType->Fetch())
{
	if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
		$arIblockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
}

$rsIblock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIblock->Fetch()) $arIblock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsIblock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["LINK_IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIblock->Fetch()) $arIblock_LINK[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["LINK_IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	if (in_array($arr["PROPERTY_TYPE"], array("E")))
	{
		$arProperty_LINK[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

if (CModule::IncludeModule("catalog"))
{
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}

$arYesNoArray = Array();
$arYesNoArray["N"] = GetMessage("T_IBLOCK_DESC_YNA_NO");
$arYesNoArray["Y"] = GetMessage("T_IBLOCK_DESC_YNA_YES");

$arTemplateDescription = Array(
	".separator" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_CAT"),
			"DESCRIPTION" => "",
			"SEPARATOR"	 => "Y",
		)
	);

/**************************************************************************************
					Component for displaying Filter form
**************************************************************************************/

$arTemplateDescription["element_filter.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_FILTER_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_FILTER_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/iblock_filter.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"arrFIELD_CODE" => array(
			"NAME" => GetMessage("IBLOCK_FIELD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array(
				"ID"					=> GetMessage("IBLOCK_ID"),
				"SEARCHABLE_CONTENT"	=> GetMessage("IBLOCK_SEARCHABLE_CONTENT"),
				"NAME"					=> GetMessage("IBLOCK_NAME"),
				"PREVIEW_TEXT"			=> GetMessage("IBLOCK_PREVIEW_TEXT"),
				"DETAIL_TEXT"			=> GetMessage("IBLOCK_DETAIL_TEXT"),
				"DATE_ACTIVE_FROM"		=> GetMessage("IBLOCK_DATE_ACTIVE_FROM"),
				"DATE_ACTIVE_TO"		=> GetMessage("IBLOCK_DATE_ACTIVE_TO"),
				"SECTION_ID"			=> GetMessage("IBLOCK_SECTION")
				)
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"arrPRICE_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PRICE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"CURRENCY_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_CURRENCY_CODE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "(USD)"
			),
		"SAVE_IN_SESSION" => array(
			"NAME"				=> GetMessage("IBLOCK_SAVE_IN_SESSION"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("Y" => GetMessage("IBLOCK_YES"), "N" => GetMessage("IBLOCK_NO")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"FILTER_NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_FILTER_NAME_OUT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "arrFilter"
			),
		"LIST_HEIGHT" => array(
			"NAME"		=> GetMessage("IBLOCK_LIST_HEIGHT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "5"
			),
		"TEXT_WIDTH" => array(
			"NAME"		=> GetMessage("IBLOCK_TEXT_WIDTH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "20"
			),
		"NUMBER_WIDTH" => array(
			"NAME"		=> GetMessage("IBLOCK_NUMBER_WIDTH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "5"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		)
	);


/**************************************************************************************
		Component for displaying Catalog items on the Home page
**************************************************************************************/

$arTemplateDescription["main_page.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_MAIN_PAGE_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_MAIN_PAGE_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_all.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "9"
			),
		"LINE_ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "3"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"PRICE_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		"ACTION_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRODUCT_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "id"
			),
		"SHOW_DESCRIPTION" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_DESCRIPTION"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"USE_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"SHOW_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "1"
			),
		)
	);



/**************************************************************************************
		Component for displaying top selected elements grouped by Information block groups
**************************************************************************************/

$arTemplateDescription["sections_top.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_SECTIONS_TOP_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_SECTIONS_TOP_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/sections_top.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"PARENT_SECTION_ID" => array(
			"NAME"		=> GetMessage("IBLOCK_PARENT_SECTION_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> ""
			),
		"SECTION_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_SECTION_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"depth_level"	=> GetMessage("IBLOCK_SORT_DEPTH_LEVEL"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"SECTION_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_SECTION_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"SECTION_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "20"
			),
		"SECTION_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/phone/section.php?"
			),
		"ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "9"
			),
		"LINE_ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "3"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"PRICE_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"FILTER_NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_FILTER_NAME_IN"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "arrFilter"
			),
		"CACHE_FILTER" => array(
			"NAME"				=> GetMessage("IBLOCK_CACHE_FILTER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("Y" => GetMessage("IBLOCK_YES"), "N" => GetMessage("IBLOCK_NO")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		"ACTION_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRODUCT_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "id"
			),
		"SHOW_DESCRIPTION" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_DESCRIPTION"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"USE_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"SHOW_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "1"
			),
		)
	);

/**************************************************************************************
Component for displaying information block groups with count of the elements in each group
**************************************************************************************/

$arTemplateDescription["sections_top_2.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_SECTIONS_TOP_2_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_SECTIONS_TOP_2_TEMPLATE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/iblock/components/sections_top_count.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"SECTION_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_SECTION_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"depth_level"	=> GetMessage("IBLOCK_SORT_DEPTH_LEVEL"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"SECTION_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_SECTION_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"SECTION_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/phone/section.php?"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);

/**************************************************************************************
			Component for displaying group elements in table order
**************************************************************************************/

$arTemplateDescription["section.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_SECTION_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_SECTION_TEMPLATE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/iblock/components/cat_list.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"SECTION_ID" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["SECTION_ID"]}'
			),
		"PAGE_ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "30"
			),
		"LINE_ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "3"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"PRICE_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"FILTER_NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_FILTER_NAME_IN"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "arrFilter"
			),
		"CACHE_FILTER" => array(
			"NAME"				=> GetMessage("IBLOCK_CACHE_FILTER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("Y" => GetMessage("IBLOCK_YES"), "N" => GetMessage("IBLOCK_NO")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		"ACTION_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRODUCT_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "id"
			),
		"SECTION_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "SECTION_ID"
			),
		"SHOW_DESCRIPTION" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_DESCRIPTION"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"USE_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"SHOW_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "1"
			),
		)
	);

/**************************************************************************************
				Component for displaying list of elements of the group
**************************************************************************************/

$arTemplateDescription["section_2.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_SECTION_2_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_SECTION_2_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_list.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"SECTION_ID" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["SECTION_ID"]}'
			),
		"PRICE_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "N",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES"			=> $arPrice
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"PAGE_ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "50"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"FILTER_NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_FILTER_NAME_IN"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "arrFilter"
			),
		"CACHE_FILTER" => array(
			"NAME"				=> GetMessage("IBLOCK_CACHE_FILTER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("Y" => GetMessage("IBLOCK_YES"), "N" => GetMessage("IBLOCK_NO")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);

/**************************************************************************************
					Detailed view of the catalog item
**************************************************************************************/

$arTemplateDescription["element.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_ELEMENT_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_ELEMENT_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_detail.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"ELEMENT_ID" => array(
			"NAME"		=> GetMessage("IBLOCK_ELEMENT_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["ID"]}'
			),
		"SECTION_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/phone/section.php?"
			),
		"LINK_IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"LINK_IBLOCK_ID" => array(
			"NAME"		=> GetMessage("IBLOCK_LINK_IBLOCK_ID"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock_LINK,
			"REFRESH"			=> "Y"
			),
		"LINK_PROPERTY_SID" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_PROPERTY_SID"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arProperty_LINK
			),
		"LINK_ELEMENTS_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_LINK_ELEMENTS_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/accessory/byphone.php?"
			),
		"arrFIELD_CODE" => array(
			"NAME" => GetMessage("IBLOCK_FIELD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array(
				"NAME"					=> GetMessage("IBLOCK_NAME"),
				"PREVIEW_TEXT"			=> GetMessage("IBLOCK_PREVIEW_TEXT"),
				"DETAIL_TEXT"			=> GetMessage("IBLOCK_DETAIL_TEXT"),
				"DETAIL_PICTURE"		=> GetMessage("IBLOCK_DETAIL_PICTURE"),
				)
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);


/**************************************************************************************
			Component for displaying the list of catalog elements that are being compared
**************************************************************************************/

$arTemplateDescription["compare_list.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_COMPARE_LIST_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_COMPARE_LIST_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/iblock_compare_list.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"COMPARE_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_COMPARE_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/phone/compare.php"
			),
		"NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_COMPARE_NAME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "CATALOG_COMPARE_LIST"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		)
	);

/**************************************************************************************
			Component for displaying Compare table of the catalog elements
**************************************************************************************/

$arTemplateDescription["compare_table.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_COMPARE_TABLE_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_COMPARE_TABLE_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/iblock_compare_tbl.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_COMPARE_NAME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "CATALOG_COMPARE_LIST"
			),
		"arrFIELD_CODE" => array(
			"NAME" => GetMessage("IBLOCK_FIELD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array(
				"ID"					=> GetMessage("IBLOCK_ID"),
				"NAME"					=> GetMessage("IBLOCK_NAME"),
				"PREVIEW_TEXT"			=> GetMessage("IBLOCK_PREVIEW_TEXT"),
				"PREVIEW_PICTURE"		=> GetMessage("IBLOCK_PREVIEW_PICTURE"),
				"DETAIL_TEXT"			=> GetMessage("IBLOCK_DETAIL_TEXT"),
				"DETAIL_PICTURE"		=> GetMessage("IBLOCK_DETAIL_PICTURE"),
				"DATE_ACTIVE_FROM"		=> GetMessage("IBLOCK_DATE_ACTIVE_FROM_2"),
				"DATE_ACTIVE_TO"		=> GetMessage("IBLOCK_DATE_ACTIVE_TO_2")
				)
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"arrPRICE_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PRICE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),

		"DISPLAY_ELEMENT_SELECT_BOX" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_ELEMENT_BOX"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"N", "ADDITIONAL_VALUES"=>"N"),

		"ELEMENT_SORT_FIELD_BOX" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD_BOX"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "name"
			),
		"ELEMENT_SORT_ORDER_BOX" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER_BOX"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "asc"
			),



		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		)
	);





$arTemplateDescription["compare_table_hr.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_COMPARE_TABLE_HR_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_COMPARE_TABLE_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/iblock_compare_tbl.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y"
			),
		"NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_COMPARE_NAME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "CATALOG_COMPARE_LIST"
			),
		"arrFIELD_CODE" => array(
			"NAME" => GetMessage("IBLOCK_FIELD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array(
				"ID"					=> GetMessage("IBLOCK_ID"),
				"NAME"					=> GetMessage("IBLOCK_NAME"),
				"PREVIEW_TEXT"			=> GetMessage("IBLOCK_PREVIEW_TEXT"),
				"PREVIEW_PICTURE"		=> GetMessage("IBLOCK_PREVIEW_PICTURE"),
				"DETAIL_TEXT"			=> GetMessage("IBLOCK_DETAIL_TEXT"),
				"DETAIL_PICTURE"		=> GetMessage("IBLOCK_DETAIL_PICTURE"),
				"DATE_ACTIVE_FROM"		=> GetMessage("IBLOCK_DATE_ACTIVE_FROM_2"),
				"DATE_ACTIVE_TO"		=> GetMessage("IBLOCK_DATE_ACTIVE_TO_2")
				)
			),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"arrPRICE_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PRICE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		)
	);



/**************************************************************************************
				Component for displaying the list elements that are linked with the current Catalog element
**************************************************************************************/

$arTemplateDescription["link_element_list.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_LINK_ELEMENT_LIST_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_LINK_ELEMENT_LIST_TEMPLATE_DESCRIPTION"),
	"ICON"		 => "/bitrix/images/iblock/components/iblock_link_ele.gif",
	"PARAMS" => array(
		"ELEMENT_ID" => array(
			"NAME"		=> GetMessage("IBLOCK_ELEMENT_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["ID"]}'
			),
		"LINK_IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"LINK_IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_IBLOCK_ID"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock_LINK,
			"REFRESH"			=> "Y"
			),
		"LINK_PROPERTY_SID" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_PROPERTY_SID"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arProperty_LINK
			),
		"PAGE_LINK_ELEMENT_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_PAGE_LINK_ELEMENT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "50"
			),
		"LINK_ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_ELEMENT_SORT_FIELD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"shows"			=> GetMessage("IBLOCK_SORT_SHOWS"),
				"sort"			=> GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x"	=> GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name"			=> GetMessage("IBLOCK_SORT_NAME"),
				"id"			=> GetMessage("IBLOCK_SORT_ID"),
				"active_from"	=> GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to"		=> GetMessage("IBLOCK_SORT_ACTIVE_TO"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "sort"
			),
		"LINK_ELEMENT_SORT_ORDER" => array(
			"NAME"				=> GetMessage("IBLOCK_LINK_ELEMENT_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("asc" => GetMessage("IBLOCK_SORT_ASC"), "desc" => GetMessage("IBLOCK_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "N"
			),
		"arrPROPERTY_LINK_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY_LINK"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LINK
			),
		"LINK_PRICE_CODE"		=> array(
			"NAME"				=> GetMessage("IBLOCK_LINK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "N",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES"			=> $arPrice
			),
		"CATALOG_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_CATALOG_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/index.php"
			),
		"SECTION_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_SECTION_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/catalog/phone/section.php?"
			),
		"BASKET_URL" => array(
			"NAME"		=> GetMessage("IBLOCK_BASKET_URL"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "/personal/basket.php"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);


$arTemplateDescription[".uni_catalog"] = Array(
		"NAME"		 => GetMessage("IBLOCK_UNI_CATALOG_SUB"),
		"DESCRIPTION" => "",
		"SEPARATOR"	 => "Y",
	);



$arPrTypesList = array();
if (CModule::IncludeModule("catalog"))
{
	$db_res = CCatalogGroup::GetList(($b="SORT"), ($o="ASC"), Array());
	while ($ar_res = $db_res->Fetch())
	{
		$arPrTypesList[$ar_res["ID"]] = $ar_res["NAME"];
		if ($ar_res["BASE"]=="Y")
			$arPrTypesList[$ar_res["ID"]] .= " [B]";
	}
}

$arTemplateDescription["uni_catalog.php"] = array(
	"PARENT"		=> ".uni_catalog",
	"NAME"			=> GetMessage("IBLOCK_UNI_CAT_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_UNI_CAT_DESCR"),
	"ICON"		 => "/bitrix/images/iblock/components/sections_top.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"LIST_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_CAT_PATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "catalog.php?BID=#IBLOCK_ID#"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "600"
			),
		"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);

$arTemplateDescription["uni_section_mt.php"] = array(
	"PARENT"		=> ".uni_catalog",
	"NAME"			=> GetMessage("IBLOCK_UNI_SEC_NAME")." (new)",
	"DESCRIPTION"	=> GetMessage("IBLOCK_UNI_SEC_DESCR"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_list.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y",
			"DEFAULT"	=> '={$_REQUEST["BID"]}'
			),
		"ID" => array(
			"NAME"		=> GetMessage("IBLOCK_PARENT_SECTION_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["ID"]}'
			),
		"SHOW_HEADER" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_TIT"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SHOW_SECTIONS" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_SEC"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SECTION_COLUMNS_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_COLS"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "3"
			),
		"SHOW_SECTIONS_EXT" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_SECEX"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SHOW_ITEMS" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_PROD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"ITEMS_LIST_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_CNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "5"
			),
		"LIST_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_GPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "catalog.php"
			),
		"ACTION_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARA"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRODUCT_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARP"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "PRODUCT_ID"
			),
		"BASKET_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_BPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "basket.php"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"strPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY_S"),
			"TYPE"				=> "STRING",
			"DEFAULT"	=> ""
			),
		"PRICE_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"USE_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"SHOW_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "1"
			),
		"IBLOCK_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_IBLOCK_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "BID"
			),
		"SECTION_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SECTION_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "GID"
			),
		)
	);

$arTemplateDescription["uni_section.php"] = array(
	"PARENT"		=> ".uni_catalog",
	"NAME"			=> GetMessage("IBLOCK_UNI_SEC_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_UNI_SEC_DESCR"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_list.gif",
	"PARAMS" => array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			"REFRESH"			=> "Y"
			),
		"IBLOCK_ID" => array(
			"NAME"				=> GetMessage("IBLOCK_IBLOCK"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblock,
			"REFRESH"			=> "Y",
			"DEFAULT"	=> '={$_REQUEST["BID"]}'
			),
		"ID" => array(
			"NAME"		=> GetMessage("IBLOCK_PARENT_SECTION_ID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["ID"]}'
			),
		"SHOW_HEADER" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_TIT"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SHOW_SECTIONS" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_SEC"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SECTION_COLUMNS_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_COLS"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "3"
			),
		"SHOW_SECTIONS_EXT" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_SECEX"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SHOW_ITEMS" => array(
			"NAME"				=> GetMessage("IBLOCK_UNI_SEC_PROD"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"Y"			=> GetMessage("IBLOCK_UNI_SEC_YES"),
				"N"	=> GetMessage("IBLOCK_UNI_SEC_NO")
				),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"ITEMS_LIST_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_CNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "5"
			),
		"PRICE_TYPE_OLD" => Array(
			"NAME" => GetMessage("IBLOCK_UNI_SEC_OP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arPrTypesList
			),
		"PRICE_TYPE_NEW" => Array(
			"NAME" => GetMessage("IBLOCK_UNI_SEC_NP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arPrTypesList
			),
		"LIST_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_GPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "catalog.php?BID=#IBLOCK_ID#&ID=#ID#"
			),
		"DETAIL_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "detail.php?ID=#ID#"
			),
		"ACTION_VALIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARA"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRICE_ID_VALIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARP"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "PRICE_ID"
			),
		"BASKET_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_BPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "basket.php"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "600"
			),
		"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);


$arTemplateDescription["uni_detail_mt.php"] = array(
	"PARENT"		=> ".uni_catalog",
	"NAME"			=> GetMessage("IBLOCK_UNI_DET_NAME")." (new)",
	"DESCRIPTION"	=> GetMessage("IBLOCK_UNI_DET_DESCR"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_detail.gif",
	"PARAMS" => array(
		"ID" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_DET_PID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["ID"]}'
			),
		"GID" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_DET_GID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["GID"]}'
			),
		"LIST_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_GPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "catalog.php"
			),
		"ACTION_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARA"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRICE_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARP"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "ID"
			),
		"BASKET_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_BPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "basket.php"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "600"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		"arrPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"strPROPERTY_CODE" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY_S"),
			"TYPE"				=> "STRING",
			"DEFAULT"	=> ""
			),
		"arrPROPERTY_CODE_EXCL" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY_EXCL"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES"	=> "N",
			"VALUES"			=> $arProperty_LNS
			),
		"strPROPERTY_CODE_EXCL" => array(
			"NAME"				=> GetMessage("IBLOCK_PROPERTY_S_EXCL"),
			"TYPE"				=> "STRING",
			"DEFAULT"	=> ""
			),
		"PRICE_CODE"	=> array(
			"NAME"		=> GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE"				=> "LIST",
			"MULTIPLE"			=> "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"			=> $arPrice
			),
		"USE_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE"		=> "LIST",
			"VALUES"=>$arYesNoArray,
			"DEFAULT"	=> "N",
			"ADDITIONAL_VALUES"=>"N"
			),
		"SHOW_PRICE_COUNT" => array(
			"NAME"		=> GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "1"
			),
		"SECTION_ID_VARIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SECTION_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "GID"
			),
		)
	);

$arTemplateDescription["uni_detail.php"] = array(
	"PARENT"		=> ".uni_catalog",
	"NAME"			=> GetMessage("IBLOCK_UNI_DET_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_UNI_DET_DESCR"),
	"ICON"		 => "/bitrix/images/iblock/components/cat_detail.gif",
	"PARAMS" => array(
		"ID" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_DET_PID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["ID"]}'
			),
		"GID" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_DET_GID"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> '={$_REQUEST["GID"]}'
			),
		"LIST_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_GPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "catalog.php?BID=#IBLOCK_ID#&ID=#ID#"
			),
		"DETAIL_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "detail.php?ID=#ID#"
			),
		"PRICE_TYPE_OLD" => Array(
			"NAME" => GetMessage("IBLOCK_UNI_SEC_OP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arPrTypesList
			),
		"PRICE_TYPE_NEW" => Array(
			"NAME" => GetMessage("IBLOCK_UNI_SEC_NP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arPrTypesList
			),
		"ACTION_VALIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARA"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
			),
		"PRICE_ID_VALIABLE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_PARP"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "PRICE_ID"
			),
		"BASKET_PAGE_TEMPLATE" => array(
			"NAME"		=> GetMessage("IBLOCK_UNI_SEC_BPATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "basket.php"
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("IBLOCK_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "600"
			),
			"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
		)
	);
?>