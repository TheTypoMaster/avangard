<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>  <?$APPLICATION->IncludeComponent(
	"bitrix:catalog",
	"template_statii",
	Array(
		"IBLOCK_TYPE" => "info", 
		"IBLOCK_ID" => "17", 
		"BASKET_URL" => "/personal/basket.php", 
		"ACTION_VARIABLE" => "action", 
		"PRODUCT_ID_VARIABLE" => "id", 
		"SECTION_ID_VARIABLE" => "SECTION_ID", 
		"SEF_MODE" => "N", 
		"AJAX_MODE" => "N", 
		"AJAX_OPTION_SHADOW" => "Y", 
		"AJAX_OPTION_JUMP" => "N", 
		"AJAX_OPTION_STYLE" => "Y", 
		"AJAX_OPTION_HISTORY" => "N", 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "3600", 
		"CACHE_FILTER" => "N", 
		"DISPLAY_PANEL" => "Y", 
		"SET_TITLE" => "Y", 
		"USE_FILTER" => "N", 
		"USE_REVIEW" => "N", 
		"USE_COMPARE" => "N", 
		"PRICE_CODE" => "", 
		"USE_PRICE_COUNT" => "N", 
		"SHOW_PRICE_COUNT" => "1", 
		"PRICE_VAT_INCLUDE" => "Y", 
		"PRICE_VAT_SHOW_VALUE" => "N", 
		"SECTION_SORT_FIELD" => "sort", 
		"SECTION_SORT_ORDER" => "asc", 
		"SHOW_TOP_ELEMENTS" => "N", 
		"PAGE_ELEMENT_COUNT" => "30", 
		"LINE_ELEMENT_COUNT" => "3", 
		"ELEMENT_SORT_FIELD" => "sort", 
		"ELEMENT_SORT_ORDER" => "asc", 
		"LIST_PROPERTY_CODE" => array(0=>"",1=>"",), 
		"INCLUDE_SUBSECTIONS" => "Y", 
		"DETAIL_PROPERTY_CODE" => array(0=>"",1=>"salons",2=>"models",3=>"name",4=>"",), 
		"LINK_IBLOCK_TYPE" => "", 
		"LINK_IBLOCK_ID" => "", 
		"LINK_PROPERTY_SID" => "", 
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#", 
		"DISPLAY_TOP_PAGER" => "N", 
		"DISPLAY_BOTTOM_PAGER" => "Y", 
		"PAGER_TITLE" => "Товары", 
		"PAGER_SHOW_ALWAYS" => "Y", 
		"PAGER_TEMPLATE" => "", 
		"PAGER_DESC_NUMBERING" => "N", 
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000", 
		"VARIABLE_ALIASES" => Array(
			"SECTION_ID" => "SECTION_ID",
			"ELEMENT_ID" => "ELEMENT_ID"
		)
	)
);?>
<br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>