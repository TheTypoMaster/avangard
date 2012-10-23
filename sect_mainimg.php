<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Хиты продаж");
?><?$APPLICATION->IncludeComponent("bitrix:news.list", "MAIN-IMAGE-TOP", Array(
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"NEWS_COUNT"	=>	"1",
	"SORT_BY1"	=>	"RAND",
	"SORT_ORDER1"	=>	"ASC",
	"SORT_BY2"	=>	"RAND",
	"SORT_ORDER2"	=>	"ASC",
	"FILTER_NAME"	=>	"TOP",
	"FIELD_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"TOP",
		1	=>	"",
	),
	"DETAIL_URL"	=>	"/catalogue/#SECTION_ID#/tov_#ELEMENT_ID#.html",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"CACHE_FILTER"	=>	"N",
	"PREVIEW_TRUNCATE_LEN"	=>	"100",
	"ACTIVE_DATE_FORMAT"	=>	"d.m.Y",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"N",
	"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
	"ADD_SECTIONS_CHAIN"	=>	"N",
	"HIDE_LINK_WHEN_NO_DETAIL"	=>	"N",
	"PARENT_SECTION"	=>	"",
	"DISPLAY_TOP_PAGER"	=>	"N",
	"DISPLAY_BOTTOM_PAGER"	=>	"N",
	"PAGER_TITLE"	=>	"Новости",
	"PAGER_SHOW_ALWAYS"	=>	"N",
	"PAGER_TEMPLATE"	=>	"",
	"PAGER_DESC_NUMBERING"	=>	"N",
	"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	"36000",
	"DISPLAY_DATE"	=>	"N",
	"DISPLAY_NAME"	=>	"N",
	"DISPLAY_PICTURE"	=>	"Y",
	"DISPLAY_PREVIEW_TEXT"	=>	"N"
	)
);?>
