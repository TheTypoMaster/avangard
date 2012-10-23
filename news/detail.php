<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новости");
?><?$APPLICATION->IncludeComponent("bitrix:catalog.element", "news_element", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"13",
	"ELEMENT_ID"	=>	$_REQUEST["ID"],
	"FIELD_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"MORE_PHOTO",
		1	=>	"",
	),
	"IBLOCK_URL"	=>	"news.php?ID=#IBLOCK_ID#",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"META_KEYWORDS"	=>	"-",
	"META_DESCRIPTION"	=>	"-",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"N",
	"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
	"ADD_SECTIONS_CHAIN"	=>	"N",
	"ACTIVE_DATE_FORMAT"	=>	"d.m.Y",
	"USE_PERMISSIONS"	=>	"N",
	"DISPLAY_TOP_PAGER"	=>	"N",
	"DISPLAY_BOTTOM_PAGER"	=>	"N",
	"PAGER_TITLE"	=>	"Страница",
	"PAGER_TEMPLATE"	=>	"",
	"DISPLAY_DATE"	=>	"Y",
	"DISPLAY_NAME"	=>	"Y",
	"DISPLAY_PICTURE"	=>	"N",
	"DISPLAY_PREVIEW_TEXT"	=>	"N"
	)
);?><?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>