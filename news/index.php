<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новости"); // задаем заголовок страницы
?>
<div class="gray_td"><h1>Новости</h1></div>

<?$APPLICATION->IncludeComponent("bitrix:news.list", "other_news_list", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"13",
	"NEWS_COUNT"	=>	"",
	"SORT_BY1"	=>	"ACTIVE_FROM",
	"SORT_ORDER1"	=>	"DESC",
	"SORT_BY2"	=>	"SORT",
	"SORT_ORDER2"	=>	"ASC",
	"FILTER_NAME"	=>	"",
	"FIELD_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
	),
	"DETAIL_URL"	=>	"/news/news_#ELEMENT_ID#.html",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"CACHE_FILTER"	=>	"N",
	"PREVIEW_TRUNCATE_LEN"	=>	"",
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
	"DISPLAY_DATE"	=>	"Y",
	"DISPLAY_NAME"	=>	"Y",
	"DISPLAY_PICTURE"	=>	"N",
	"DISPLAY_PREVIEW_TEXT"	=>	"Y"
	)
);?> <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>