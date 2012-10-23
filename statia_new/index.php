<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("right_inc_file", "none");
$APPLICATION->SetPageProperty("left_inc_file", "none");
$APPLICATION->SetTitle("Title");
?>Статья новая 
<br />
<?$APPLICATION->IncludeComponent("bitrix:catalog.element", "detail_element", Array(
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"ELEMENT_ID"	=>	"520",
	"SECTION_ID"	=>	"",
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
		2	=>	"",
	),
	"SECTION_URL"	=>	"section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
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
	"ADD_SECTIONS_CHAIN"	=>	"Y",
	"PRICE_CODE"	=>	array(
	),
	"USE_PRICE_COUNT"	=>	"N",
	"SHOW_PRICE_COUNT"	=>	"1",
	"PRICE_VAT_INCLUDE"	=>	"Y",
	"PRICE_VAT_SHOW_VALUE"	=>	"N",
	"LINK_IBLOCK_TYPE"	=>	"",
	"LINK_IBLOCK_ID"	=>	"",
	"LINK_PROPERTY_SID"	=>	"",
	"LINK_ELEMENTS_URL"	=>	"link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?>
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>