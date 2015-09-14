<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("index2");
?>
<div><?$APPLICATION->IncludeComponent("anp:catalog.section", "imcataloglist", array(
	"IBLOCK_TYPE" => "catalogue",
	"IBLOCK_ID" => "31",
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrFilter",
	"PAGE_ELEMENT_COUNT" => "30",
	"LINE_ELEMENT_COUNT" => "2",
	"PROPERTY_CODE" => array(
		0 => "ARTICLE",
		1 => "PRICE_NEW",
		2 => "PRICE_OLD",
		3 => "MODEL",
		4 => "COMPLECTATION",
		5 => "MECHANISM",
		6 => "",
	),
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL" => "element.php?id=#ELEMENT_ID#",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"DISPLAY_PANEL" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "Y",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	),
	false
);?></div>

<div></div>
 
<div></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>