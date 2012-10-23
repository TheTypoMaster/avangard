<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Расширенный поиск");
?>
<br />
 <?$APPLICATION->IncludeComponent("bitrix:catalog.filter", "ADV-SEARCH-TEMPLATE", Array(
	"PHPSESSID"	=>	"21f6d4136e39fecf20af33852604a28a",
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"FILTER_NAME"	=>	"arrFilter",
	"FIELD_CODE"	=>	array(
		0	=>	"NAME",
		1	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"F_TYPE",
		1	=>	"COMPLECT",
		2	=>	"W_SEARCH_PARAM",
		3	=>	"COLLECTION",
		4	=>	"",
	),
	"LIST_HEIGHT"	=>	"5",
	"TEXT_WIDTH"	=>	"20",
	"NUMBER_WIDTH"	=>	"5",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600"
	)
);?>
<br />
 <?$APPLICATION->IncludeComponent("bitrix:catalog.sections.top", "ADV-SEARCH-RESULT-TOP", Array(
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"SECTION_SORT_FIELD"	=>	"sort",
	"SECTION_SORT_ORDER"	=>	"asc",
	"ELEMENT_SORT_FIELD"	=>	"sort",
	"ELEMENT_SORT_ORDER"	=>	"asc",
	"FILTER_NAME"	=>	"arrFilter",
	"SECTION_COUNT"	=>	"",
	"ELEMENT_COUNT"	=>	"",
	"LINE_ELEMENT_COUNT"	=>	"1",
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
		2	=>	"W_SEARCH_PARAM",
		3	=>	"",
		4	=>	"",
		5	=>	"",
	),
	"SECTION_URL"	=>	"section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL"	=>	"element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL"	=>	"/personal/basket.php",
	"ACTION_VARIABLE"	=>	"action",
	"PRODUCT_ID_VARIABLE"	=>	"id",
	"SECTION_ID_VARIABLE"	=>	"SECTION_ID",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"CACHE_FILTER"	=>	"N",
	"DISPLAY_PANEL"	=>	"N",
	"DISPLAY_COMPARE"	=>	"N",
	"SET_TITLE"	=>	"Y",
	"USE_PRICE_COUNT"	=>	"N",
	"SHOW_PRICE_COUNT"	=>	"1"
	)
);?>
<br />

<br />
 <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>