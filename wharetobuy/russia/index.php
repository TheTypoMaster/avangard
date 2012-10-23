<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Фирменные салоны и подиумы");
?><?
global $arrFilterCity;
    /*
	if($_REQUEST["CITY_ID"]){
	$city_id = (latrus($_REQUEST["CITY_ID"]));
	//echo $city_id;
	$arrFilterCity = Array("PROPERTY_SALON_CITY" => "$city_id");
	}
      */
	if($_REQUEST["SALON_ID"]){
	$salon_id=$_REQUEST["SALON_ID"];
	$arrFilterCity = Array("ID" => "$salon_id");
	}
?> <?if($_REQUEST["print"] != "Y"){?> 
<div class="gray_td" style="width: 720px;"> 
  <h1><a class="head" href="/wharetobuy/russia/" >Россия</a></h1>
 </div>
  <?}?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"salon-list-russia1",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => "7",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilterCity",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(0=>"SALON_CITY",1=>"",2=>"",),
		"SECTION_URL" => "/wharetobuy/russia/",
		"DETAIL_URL" => "/wharetobuy/russia/salon_#ELEMENT_ID#.html",
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
		"PRICE_CODE" => "",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Салоны",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	)
);?> <?if(($_REQUEST["CITY_ID"])||($_REQUEST["SALON_ID"])){?> <a href="/wharetobuy/russia/" >Обратно в раздел &raquo;</a> <?}else{?> <a href="/wharetobuy/" >Обратно в раздел »</a> <?}?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>