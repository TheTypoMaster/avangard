<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новая страница");
?><?$APPLICATION->IncludeComponent("bitrix:catalog.element", "news_element", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"13",
	"ELEMENT_ID"	=>	$_REQUEST["ID"],
	"SECTION_ID"	=>	"",
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"MORE_PHOTO",
		2	=>	"DATE_ACTIVE_FROM",
	),
	"SECTION_URL"	=>	"/news/",
	"DETAIL_URL"	=>	"element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL"	=>	"/personal/basket.php",
	"ACTION_VARIABLE"	=>	"action",
	"PRODUCT_ID_VARIABLE"	=>	"id",
	"SECTION_ID_VARIABLE"	=>	"SECTION_ID",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"META_KEYWORDS"	=>	"-",
	"META_DESCRIPTION"	=>	"-",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"Y",
	"ADD_SECTIONS_CHAIN"	=>	"N",
	"USE_PRICE_COUNT"	=>	"N",
	"SHOW_PRICE_COUNT"	=>	"0",
	"PRICE_VAT_INCLUDE"	=>	"N",
	"PRICE_VAT_SHOW_VALUE"	=>	"N",
	"LINK_IBLOCK_TYPE"	=>	"",
	"LINK_IBLOCK_ID"	=>	"",
	"LINK_PROPERTY_SID"	=>	"",
	"LINK_ELEMENTS_URL"	=>	"link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>