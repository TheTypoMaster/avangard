<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Двуспальные диваны. Двуспальные диван-кровати");
$APPLICATION->SetTitle("Двухспальные диваны. Диван кровать двуспальный.");
?> 
<h2>Двуспальные диваны</h2>
 <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"template_statii_det",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "17",
		"ELEMENT_ID" => "1637",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"PROPERTY_CODE" => array(0=>"",1=>"models",2=>"salons",3=>"tegs",),
		"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
		"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?> 
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>