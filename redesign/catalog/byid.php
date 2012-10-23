<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Купить диван от  российского производителя. Продажа отечественной мягкой мебели");
$APPLICATION->SetTitle(" Купить диван от  российского производителя. Продажа отечественной мягкой мебели ");

?> <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"detail_element",
	Array(
		"IBLOCK_TYPE" => "catalogue",
		"IBLOCK_ID" => "5",
		"ELEMENT_ID" => $id,
		"SECTION_ID" => "",
		"PROPERTY_CODE" => array(0=>"F_TYPE",1=>"COMPLECT",2=>"W_SEARCH_PARAM",3=>"LENGTH",4=>"WIDTH",5=>"HEIGHT",6=>"PLACES",7=>"TRANSFORMATION",8=>"TOP",9=>"NOVELTY",10=>"HIT",11=>"SLIDER",12=>"SEO_TITLE_R",13=>"",),
		"SECTION_URL" => "",
		"DETAIL_URL" => "/#ELEMENT_ID#",
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
);?> <? //echo '<a href="/mebel_sal.php?all_models='.$id.'&from_catalog=y">Посмотреть наличие в салонах Москвы и МО</a>'; ?> 

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>