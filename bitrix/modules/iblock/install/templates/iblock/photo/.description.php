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

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

$arTemplateDescription = Array(
	".separator" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_PHOTO"),
			"DESCRIPTION" => "",
			"SEPARATOR"	 => "Y",
		)
	);

$arYesNoArray = Array();
$arYesNoArray["N"] = GetMessage("T_IBLOCK_DESC_YNA_NO");
$arYesNoArray["Y"] = GetMessage("T_IBLOCK_DESC_YNA_YES");

/**************************************************************************************
				Component for displaying random photo
**************************************************************************************/

$arTemplateDescription["random.php"] = array(
	"NAME"			=> GetMessage("T_IBLOCK_DESC_PHOTO_LIST"),
	"DESCRIPTION"	=> GetMessage("T_IBLOCK_DESC_PHOTO_DESC"),
	"ICON"		 	=> "/bitrix/images/iblock/components/photo_view.gif",
	"PARAMS"		=> array(
		"IBLOCK_TYPE" => array(
			"NAME"				=> GetMessage("IBLOCK_TYPE"),
			"TYPE"				=> "LIST",
			"ADDITIONAL_VALUES"	=> "Y",
			"VALUES"			=> $arIblockType,
			),
		)
	);

/**************************************************************************************
	Component for displaying top elements of the a block groupped by Photogallery block groups
**************************************************************************************/

$arTemplateDescription["sections_top.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_SECTIONS_TOP_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_SECTIONS_TOP_TEMPLATE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/iblock/components/photo_sections_top.gif",
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
			"DEFAULT"	=> "/about/gallery/section.php?"
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
		"FILTER_NAME" => array(
			"NAME"		=> GetMessage("IBLOCK_FILTER_NAME"),
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
		Component for displaying Photogallery elements in table order
**************************************************************************************/

$arTemplateDescription["section.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_SECTION_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_SECTION_TEMPLATE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/iblock/components/photo_section.gif",
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
			"DEFAULT"	=> "50"
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
					Detailed photo view
**************************************************************************************/

$arTemplateDescription["photo.php"] = array(
	"PARENT"		=> ".separator",
	"NAME"			=> GetMessage("IBLOCK_ELEMENT_TEMPLATE_NAME"),
	"DESCRIPTION"	=> GetMessage("IBLOCK_ELEMENT_TEMPLATE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/iblock/components/photo_detail.gif",
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
			"DEFAULT"	=> "/about/gallery/section.php?"
			),
		"ELEMENT_SORT_FIELD" => array(
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_FIELD_NEXT_PREV_LINK"),
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
			"NAME"				=> GetMessage("IBLOCK_ELEMENT_SORT_ORDER_NEXT_PREV_LINK"),
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
		"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),	
		)
	);
?>