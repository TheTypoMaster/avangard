<?
IncludeTemplateLangFile(__FILE__);
if (!CModule::IncludeModule("search")) return;
//Initialize dropdown lists
$arrDropdown=array();
if(CModule::IncludeModule("forum"))
{
	$arrDropdown["forum"] = "[forum] ".GetMessage("SEARCH_FORUM");
}
if(CModule::IncludeModule("iblock"))
{
	$rsType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
	while ($arr=$rsType->Fetch())
	{
		if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
			$arrDropdown["iblock_".$arr["ID"]] = "[iblock_".$arr["ID"]."] ".$ar["NAME"];
	}
}
if(CModule::IncludeModule("blog"))
{
	$arrDropdown["blog"] = "[blog] ".GetMessage("SEARCH_BLOG");
}

$arrFilterDropdown=array_merge(
	array("-"=>GetMessage("SEARCH_NO_LIMIT"), "main" => "[main] ".GetMessage("SEARCH_STATIC"))
	,$arrDropdown
	);
$arrFILTER=array();
if(is_array($arCurrentValues["arrFILTER"]))
{
	foreach($arCurrentValues["arrFILTER"] as $strFILTER)
	{
		if($strFILTER=="main")
		{
			//array_pop($arCurrentValues["arrFILTER"]);
		}
		elseif($strFILTER=="forum" && CModule::IncludeModule("forum"))
		{
			$arrFILTER[$strFILTER]["-"]=GetMessage("SEARCH_ALL");
			$rsForum = CForumNew::GetList();
			while($arForum=$rsForum->Fetch())
				$arrFILTER[$strFILTER][$arForum["ID"]]=$arForum["NAME"];
		}
		elseif(strpos($strFILTER,"iblock_")===0)
		{
			$arrFILTER[$strFILTER]["-"]=GetMessage("SEARCH_ALL");
			$rsIBlock = CIBlock::GetList(array("SORT"=>"ASC"),array("TYPE"=>substr($strFILTER,7)));
			while($arIBlock=$rsIBlock->Fetch())
				$arrFILTER[$strFILTER][$arIBlock["ID"]]=$arIBlock["NAME"];
		}
	}
}

$sSectionName = GetMessage("SEARCH_SECTION_NAME");

/**************************************************************************
			Component for displaying Search page
***************************************************************************/

$arTemplateDescription["page/default.php"] = array(
	"PARENT"		=> "page/.separator",
	"NAME"			=> GetMessage("SEARCH_SEARCH_PAGE_NAME"),
	"DESCRIPTION"	=> GetMessage("SEARCH_SEARCH_PAGE_DESCRIPTION"),
	"ICON"			=> "/bitrix/images/search/components/search_page.gif",
	"PARAMS" => array(
		"SHOW_WHERE" => array(
			"NAME"		=> GetMessage("SEARCH_SHOW_DROPDOWN"),
			"TYPE"		=> "LIST",
			"MULTIPLE"	=> "N",
			"VALUES"	=> array("Y"=>GetMessage("SEARCH_YES"), "N"=>GetMessage("SEARCH_NO")),
			"ADDITIONAL_VALUES"=>"N",
			"DEFAULT"	=> "Y"
			),
		"arrWHERE" => array(
			"NAME"		=> GetMessage("SEARCH_WHERE_DROPDOWN"),
			"TYPE"		=> "LIST",
			"MULTIPLE"	=> "Y",
			"VALUES"	=> $arrDropdown
			),
		"PAGE_RESULT_COUNT"	=> array(
			"NAME"		=> GetMessage("SEARCH_PAGE_RESULT_COUNT"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "50"
			),
		"CHAIN_TEMPLATE_PATH"	=> array(
			"NAME"		=> GetMessage("SEARCH_CHAIN_TEMPLATE_PATH"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> ""
			),
		"CACHE_TIME" => array(
			"NAME"		=> GetMessage("SEARCH_CACHE_TIME"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "0"
			),
		"arrFILTER" => array(
			"NAME"		=> GetMessage("SEARCH_WHERE_FILTER"),
			"TYPE"		=> "LIST",
			"MULTIPLE"	=> "Y",
			"VALUES"	=> $arrFilterDropdown,
			"ADDITIONAL_VALUES"=>"N",
			"DEFAULT"	=> "-",
			"REFRESH"	=> "Y"
			),
		"CHECK_DATES" => array(
			"NAME"		=> GetMessage("SEARCH_CHECK_DATES"),
			"TYPE"		=> "LIST",
			"MULTIPLE"	=> "N",
			"VALUES"	=> array("Y"=>GetMessage("SEARCH_YES"), "N"=>GetMessage("SEARCH_NO")),
			"ADDITIONAL_VALUES"=>"N",
			"DEFAULT"	=> "N"
			),
		),
	);

if(is_array($arCurrentValues["arrFILTER"]))
{
	foreach($arCurrentValues["arrFILTER"] as $strFILTER)
	{
		if($strFILTER=="main")
		{
			$arTemplateDescription["page/default.php"]["PARAMS"]["arrFILTER_".$strFILTER]=array(
				"NAME"		=> GetMessage("SEARCH_URL"),
				"TYPE"		=> "STRING",
				"MULTIPLE"	=> "Y",
				"ADDITIONAL_VALUES"=>"Y",
				"DEFAULT"	=> "",
				);
		}
		elseif($strFILTER=="forum")
		{
			$arTemplateDescription["page/default.php"]["PARAMS"]["arrFILTER_".$strFILTER]=array(
				"NAME"		=> GetMessage("SEARCH_FORUM"),
				"TYPE"		=> "LIST",
				"MULTIPLE"	=> "Y",
				"VALUES"	=> $arrFILTER[$strFILTER],
				"ADDITIONAL_VALUES"=>"N",
				"DEFAULT"	=> "-",
				);
		}
		elseif(strpos($strFILTER,"iblock_")===0)
		{
			$arTemplateDescription["page/default.php"]["PARAMS"]["arrFILTER_".$strFILTER]=array(
				"NAME"		=> GetMessage("SEARCH_IBLOCK_TYPE1").$arrFilterDropdown[$strFILTER].GetMessage("SEARCH_IBLOCK_TYPE2"),
				"TYPE"		=> "LIST",
				"MULTIPLE"	=> "Y",
				"VALUES"	=> $arrFILTER[$strFILTER],
				"ADDITIONAL_VALUES"=>"N",
				"DEFAULT"	=> "-",
				);
		}
	}
}
?>