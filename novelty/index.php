<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новинки");
?><div style="padding-top:10px"></div>
<?$APPLICATION->IncludeComponent("bitrix:catalog.top", "main_hit", Array(
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"ELEMENT_SORT_FIELD"	=>	"NOVELTY",
	"ELEMENT_SORT_ORDER"	=>	"desc",
	"ELEMENT_COUNT"	=>	"1",
	"LINE_ELEMENT_COUNT"	=>	"1",
	"PROPERTY_CODE"	=>	array(
		"={0}"	=>	"",
		"={1}"	=>	"",
	),
	"SECTION_URL"	=>	"section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL"	=>	"element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL"	=>	"/personal/basket.php",
	"ACTION_VARIABLE"	=>	"action",
	"PRODUCT_ID_VARIABLE"	=>	"id",
	"SECTION_ID_VARIABLE"	=>	"SECTION_ID",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"DISPLAY_COMPARE"	=>	"N",
	"USE_PRICE_COUNT"	=>	"N",
	"SHOW_PRICE_COUNT"	=>	"1"
	)
);?><?$APPLICATION->IncludeComponent("bitrix:catalog.top", "NOVELTY1", Array(
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"ELEMENT_SORT_FIELD"	=>	"NOVELTY",
	"ELEMENT_SORT_ORDER"	=>	"asc",
	"ELEMENT_COUNT"	=>	"3",
	"LINE_ELEMENT_COUNT"	=>	"3",
	"PROPERTY_CODE"	=>	array(
		"={0}"	=>	"",
		"={1}"	=>	"",
	),
	"SECTION_URL"	=>	"/catalogue/index.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL"	=>	"/catalogue/index.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL"	=>	"/personal/basket.php",
	"ACTION_VARIABLE"	=>	"action",
	"PRODUCT_ID_VARIABLE"	=>	"id",
	"SECTION_ID_VARIABLE"	=>	"SECTION_ID",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"DISPLAY_COMPARE"	=>	"N",
	"USE_PRICE_COUNT"	=>	"N",
	"SHOW_PRICE_COUNT"	=>	"1"
	)
);?>
 						 						 
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>