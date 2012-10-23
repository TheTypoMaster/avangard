<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
$sSectionName = GetMessage("T_IBLOCK_DESC_NAME");
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

$arSections=Array();
if ($arCurrentValues["ID"]):

	$db_section = CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID" => ($arCurrentValues["ID"]!="-"?$arCurrentValues["ID"]:""), "IBLOCK_TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));

	if ($db_section->SelectedRowsCount() > 0)

	$arSections[0] = GetMessage("T_IBLOCK_DESC_ALL_SECTIONS");

	while($arRes = $db_section->Fetch())
		$arSections[$arRes["ID"]] = $arRes["NAME"];
endif;


$DETAIL_PAGE_URL="";
if ($arCurrentValues["ID"]):
	if ($arIBlock = GetIBlock($arCurrentValues["ID"]))
		$DETAIL_PAGE_URL = $arIBlock["DETAIL_PAGE_URL"];
endif;

$LIST_PAGE_URL_DEF="";
if ($arCurrentValues["IBLOCK_ID"]):
	if ($arIBlock = GetIBlock($arCurrentValues["IBLOCK_ID"])):

		if (strlen(trim($arIBlock["SECTION_PAGE_URL"])) > 0)
			$LIST_PAGE_URL_DEF = $arIBlock["SECTION_PAGE_URL"];
		else
			$LIST_PAGE_URL_DEF = $arIBlock["LIST_PAGE_URL"];
	endif;
endif;

$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

$arSectSortFields = Array(
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

$arYesNoArray = Array();
$arYesNoArray["N"] = GetMessage("T_IBLOCK_DESC_YNA_NO");
$arYesNoArray["Y"] = GetMessage("T_IBLOCK_DESC_YNA_YES");

if(!is_array($arProperty_LNS))
	$arProperty_LNS = array();

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arDATE_FIELD = Array(
	"DATE_ACTIVE_FROM" => "[DATE_ACTIVE_FROM] ".GetMessage("T_IBLOCK_DESC_CAL_DATE_ACTIVE_FROM"), 
	"DATE_ACTIVE_TO" => "[DATE_ACTIVE_TO] ".GetMessage("T_IBLOCK_DESC_CAL_DATE_ACTIVE_TO"),
	"TIMESTAMP_X" => "[TIMESTAMP_X] ".GetMessage("T_IBLOCK_DESC_CAL_TIMESTAMP_X"),
	"DATE_CREATE" => "[DATE_CREATE] ".GetMessage("T_IBLOCK_DESC_CAL_DATE_CREATE"),
	);

/*
//может понадобиться для календаря, чтобы использовать свойства типа даты.
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"]), "USER_TYPE"=>"DateTime"));
while ($arr=$rsProp->Fetch())
	$arDATE_FIELD["PROPERTY_".$arr["CODE"]] = "[".$arr["CODE"]."] ".GetMessage("T_IBLOCK_DESC_CAL_PROP")." \"".$arr["NAME"]."\"";
*/


$arTemplateDescription =
Array(
	"news/.separator" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_NEWS"),
			"DESCRIPTION" => "",
			"SEPARATOR"	 => "Y",
		),
	"news/index.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_ALLNEWS"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_ALLNEWS_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/news_all.gif",
			"PARAMS"		=>
				Array(
					"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBTYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypes, "DEFAULT"=>"news", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "N"),
					"IBLOCK_SORT_BY" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBSORT"), "TYPE"=>"LIST", "VALUES"=>Array("SORT"=>GetMessage("T_IBLOCK_DESC_SORT"), "NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"), "ID"=>GetMessage("T_IBLOCK_DESC_ID")), "DEFAULT"=>"SORT", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N"),
					"IBLOCK_SORT_ORDER" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBSORTBY"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"ASC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
					"NEWS_COUNT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBCNT"), "TYPE"=>"STRING", "DEFAULT"=>"5", "COLS"=>"3"),
					"SORT_BY1" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD1"), "TYPE"=>"LIST", "DEFAULT"=>"ACTIVE_FROM", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
					"SORT_ORDER1" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY1"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"DESC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
					"SORT_BY2" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD2"), "TYPE"=>"LIST", "DEFAULT"=>"SORT", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
					"SORT_ORDER2" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY2"), "TYPE"=>"LIST", "DEFAULT"=>"ASC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
					"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0')
				)
		),
	"news/news.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_LIST"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_LIST_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/news_list.gif",
			"PARAMS"		=>
			Array(
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"news", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'={$_REQUEST["ID"]}', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"Y", "REFRESH" => "Y"),
				"SECTION_ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_SECTION_ID"), "TYPE"=>"LIST", "VALUES"=>$arSections, "DEFAULT"=>"false", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N"),
				"NEWS_COUNT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_CONT"), "TYPE"=>"STRING", "DEFAULT"=>"20", "COLS"=>"3"),
				"SORT_BY1" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD1"), "TYPE"=>"LIST", "DEFAULT"=>"ACTIVE_FROM", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER1" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY1"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"DESC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"SORT_BY2" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD2"), "TYPE"=>"LIST", "DEFAULT"=>"SORT", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER2" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY2"), "TYPE"=>"LIST", "DEFAULT"=>"ASC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"arrPROPERTY_CODE" => array(
					"NAME"				=> GetMessage("T_IBLOCK_PROPERTY"),
					"TYPE"				=> "LIST",
					"MULTIPLE"			=> "Y",
					"ADDITIONAL_VALUES"	=> "N",
					"VALUES"			=> $arProperty_LNS
					),				
				"DETAIL_PAGE_URL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_DETAIL_PAGE_URL"), "TYPE"=>"STRING", "DEFAULT"=>$DETAIL_PAGE_URL),
				"INCLUDE_IBLOCK_INTO_CHAIN" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_INCLUDE_IBLOCK_INTO_CHAIN"), "TYPE"=>"LIST", "VALUES" => array("Y" => GetMessage("IBLOCK_YES"), "N" => GetMessage("IBLOCK_NO")),	"ADDITIONAL_VALUES"	=> "N", "DEFAULT"=>"Y"),
				"FILTER" => Array("NAME"=>GetMessage("T_IBLOCK_FILTER"), "TYPE"=>"STRING", "DEFAULT"=>''),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0'),
				"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
			)
		),
	"news/detail.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_DETAIL"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_DETAIL_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/news_detail.gif",
			"PARAMS"		=>
			Array(
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_DETAIL_ID"), "TYPE"=>"STRING", "DEFAULT"=>'={$_REQUEST["ID"]}'),
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"news", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"IBLOCK_ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'1', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"arrPROPERTY_CODE" => array(
					"NAME"				=> GetMessage("T_IBLOCK_PROPERTY"),
					"TYPE"				=> "LIST",
					"MULTIPLE"			=> "Y",
					"ADDITIONAL_VALUES"	=> "N",
					"VALUES"			=> $arProperty_LNS
					),
				"META_KEYWORDS" =>array(
					"NAME"				=> GetMessage("T_IBLOCK_DESC_KEYWORDS"),
					"TYPE"				=> "LIST",
					"MULTIPLE"			=> "N",
					"ADDITIONAL_VALUES"	=> "N",
					"DEFAULT" => "-",
					"VALUES"			=> array_merge(Array("-"=>" "),$arProperty_LNS)
					),
				"META_DESCRIPTION" =>array(
					"NAME"				=> GetMessage("T_IBLOCK_DESC_DESCRIPTION"),
					"TYPE"				=> "LIST",
					"MULTIPLE"			=> "N",
					"ADDITIONAL_VALUES"	=> "N",
					"DEFAULT" => "-",
					"VALUES"			=> array_merge(Array("-"=>" "),$arProperty_LNS)
					),
				"LIST_PAGE_URL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_PAGE_URL"), "TYPE"=>"STRING", "DEFAULT"=>$LIST_PAGE_URL_DEF),
				"INCLUDE_IBLOCK_INTO_CHAIN" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_INCLUDE_IBLOCK_INTO_CHAIN"), "TYPE"=>"LIST", "VALUES" => array("Y" => GetMessage("IBLOCK_YES"), "N" => GetMessage("IBLOCK_NO")),	"ADDITIONAL_VALUES"	=> "N", "DEFAULT"=>"Y"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0'),
				"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
			)
		),
	"news/news_line.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_LINE"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_LINE_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/news_line.gif",
			"PARAMS"		=>
			Array(
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"news", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH"=>"Y"),
				"IBLOCK" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'', "MULTIPLE"=>"Y", "CNT"=>"1"),
				"NEWS_COUNT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_CONT"), "TYPE"=>"STRING", "DEFAULT"=>"20", "COLS"=>"3"),
				"SORT_BY1"		=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD1"), "TYPE"=>"LIST", "DEFAULT"=>"ACTIVE_FROM", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER1" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY1"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"DESC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"SORT_BY2" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD2"), "TYPE"=>"LIST", "DEFAULT"=>"SORT", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER2" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY2"), "TYPE"=>"LIST", "DEFAULT"=>"ASC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0')
			)
		),

	"news/news_params.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_LIST_PARAMS"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_LIST_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/iblock_news_list.gif",
			"PARAMS"		=>
			Array(
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"news", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'={$_REQUEST["ID"]}', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"Y"),
				"NEWS_COUNT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_CONT"), "TYPE"=>"STRING", "DEFAULT"=>"20", "COLS"=>"3"),
				"SORT_BY1" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD1"), "TYPE"=>"LIST", "DEFAULT"=>"ACTIVE_FROM", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER1" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY1"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"DESC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"SORT_BY2" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBORD2"), "TYPE"=>"LIST", "DEFAULT"=>"SORT", "VALUES"=>$arSortFields, "ADDITIONAL_VALUES"=>"N"),
				"SORT_ORDER2" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_IBBY2"), "TYPE"=>"LIST", "DEFAULT"=>"ASC", "VALUES"=>$arSorts, "ADDITIONAL_VALUES"=>"N"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'0'),
				"DISPLAY_PANEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PANEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),	
				"DISPLAY_DATE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_DATE"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
				"DISPLAY_NAME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_NAME"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
				"DISPLAY_PREVIEW_PICTURE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
				"DISPLAY_PREVIEW_TEXT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_TEXT"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N"),
				"DISPLAY_PAGE_TITLE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_NEWS_PAGE_TITLE"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"Y", "ADDITIONAL_VALUES"=>"N")
			)
		),
"news/calendar.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_CALENDAR"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_CALENDAR_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/iblock_calendar.gif",
			"PARAMS"		=>
			Array(
				"IBLOCK_TYPE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_TYPE"), "TYPE"=>"LIST", "VALUES"=>$arTypesEx, "DEFAULT"=>"news", "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N", "REFRESH" => "Y"),
				"IBLOCK_ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_LIST_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'={$_REQUEST["ID"]}', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"Y", "REFRESH"=>"Y"),
				"month_var_name" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_MVN"), "TYPE"=>"STRING", "DEFAULT"=>"month", "COLS"=>"6"),
				"year_var_name" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_YVN"), "TYPE"=>"STRING", "DEFAULT"=>"year", "COLS"=>"6"),
				"week_start" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_WS"), "TYPE"=>"LIST", "DEFAULT"=>1, "VALUES"=>Array("1"=>GetMessage("T_IBLOCK_DESC_CAL_WS_1"), "0"=>GetMessage("T_IBLOCK_DESC_CAL_WS_0")), "ADDITIONAL_VALUES"=>"N"),
				"DATE_FIELD" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_DATE_FIELD"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "DEFAULT"=>"DATE_ACTIVE_FROM", "VALUES"=>$arDATE_FIELD, "ADDITIONAL_VALUES"=>"N"),
				"TYPE" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_TYPE"), "TYPE"=>"LIST", "DEFAULT"=>"EVENTS", "VALUES"=>Array("EVENTS"=>GetMessage("T_IBLOCK_DESC_CAL_TYPE_EVENTS"), "NEWS"=>GetMessage("T_IBLOCK_DESC_CAL_TYPE_NEWS")), "ADDITIONAL_VALUES"=>"N"),
				"SHOW_YEAR" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_SHOW_YEAR"), "TYPE"=>"LIST", "DEFAULT"=>"Y", "VALUES"=>Array("Y"=>GetMessage("T_IBLOCK_DESC_YNA_YES"), "N"=>GetMessage("T_IBLOCK_DESC_YNA_NO")), "ADDITIONAL_VALUES"=>"N"),
				"SHOW_TIME" 	=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_SHOW_TIME"), "TYPE"=>"LIST", "DEFAULT"=>"Y", "VALUES"=>Array("Y"=>GetMessage("T_IBLOCK_DESC_YNA_YES"), "N"=>GetMessage("T_IBLOCK_DESC_YNA_NO")), "ADDITIONAL_VALUES"=>"N"),
				
				"TITLE_LEN" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_TITLE_LEN"), "TYPE"=>"STRING", "DEFAULT"=>"0", "COLS"=>"3"),
				"REWRITE_TITLE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_REWRITE_TITLE"), "TYPE"=>"LIST", "DEFAULT"=>"Y", "VALUES"=>Array("Y"=>GetMessage("T_IBLOCK_DESC_YNA_YES"), "N"=>GetMessage("T_IBLOCK_DESC_YNA_NO")), "ADDITIONAL_VALUES"=>"N"),
				"SHOW_CURRENT_DATE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_SHOW_CURRENT_DATE"), "TYPE"=>"LIST", "DEFAULT"=>"N", "VALUES"=>Array("Y"=>GetMessage("T_IBLOCK_DESC_YNA_YES"), "N"=>GetMessage("T_IBLOCK_DESC_YNA_NO")), "ADDITIONAL_VALUES"=>"N"),
				"SHOW_MONTH_LIST" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_SHOW_MONTH_LIST"), "TYPE"=>"LIST", "DEFAULT"=>"N", "VALUES"=>Array("Y"=>GetMessage("T_IBLOCK_DESC_YNA_YES"), "N"=>GetMessage("T_IBLOCK_DESC_YNA_NO")), "ADDITIONAL_VALUES"=>"N"),
				"NEWS_COUNT"=> Array("NAME"=>GetMessage("T_IBLOCK_DESC_CAL_NEWS_COUNT"), "TYPE"=>"STRING", "DEFAULT"=>"0", "COLS"=>"3"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>"0"),
			)
		),

	"rss/rss_show.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_RSS_SHOW"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_RSS_SHOW_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/rss_in.gif",
			"PARAMS"		=>
			Array(
				"SITE" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_SITE"), "TYPE"=>"STRING", "DEFAULT"=>'www.bitrix.ru'),
				"PORT" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_PORT"), "TYPE"=>"STRING", "DEFAULT"=>'80'),
				"PATH" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_PATH"), "TYPE"=>"STRING", "DEFAULT"=>'/bitrix/rss.php'),
				"QUERY_STR" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_QUERY_STR"), "TYPE"=>"STRING", "DEFAULT"=>'ID=news_sm&LANG=ru&TYPE=news&LIMIT=5'),
				"OUT_CHANNEL" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_OUT_CHANNEL"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"N", "ADDITIONAL_VALUES"=>"N"),
				"NUM_NEWS" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_NUM_NEWS"), "TYPE"=>"STRING", "DEFAULT"=>'10'),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'600')
			)
		),
	"rss/rss_out.php" =>
		Array(
			"NAME"		 => GetMessage("T_IBLOCK_DESC_RSS_OUT"),
			"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_RSS_OUT_DESC"),
			"ICON"		 => "/bitrix/images/iblock/components/rss_out.gif",
			"PARAMS"		=>
			Array(
				"ID" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_ID"), "TYPE"=>"LIST", "VALUES"=>$arIBlocks, "DEFAULT"=>'', "MULTIPLE"=>"N", "ADDITIONAL_VALUES"=>"N"),
				"NUM_NEWS" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_NUM_NEWS1"), "TYPE"=>"STRING", "DEFAULT"=>'20'),
				"NUM_DAYS" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_NUM_DAYS"), "TYPE"=>"STRING", "DEFAULT"=>'30'),
				"YANDEX" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_YANDEX"), "TYPE"=>"LIST", "VALUES"=>$arYesNoArray, "DEFAULT"=>"N", "ADDITIONAL_VALUES"=>"N"),
				"CACHE_TIME" => Array("NAME"=>GetMessage("T_IBLOCK_DESC_RSS_CACHE_TIME"), "TYPE"=>"STRING", "DEFAULT"=>'600')
			)
		)
);

endif;
?>
