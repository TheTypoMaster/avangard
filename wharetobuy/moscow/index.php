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
<div class="gray_td" style="WIDTH: 720px">
  <h1><a class="head" href="/wharetobuy/moscow/" >Москва и область</a></h1>
</div>

<p><font size="2"><strong>C 11 по 14 июня, ко Дню России - 
      <br />
    Дополнительная <font color="#ff0000">скидка 8%</font> на всю продукцию фабрики!</strong></font> </p>

<?}?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"salon-list-moscow1",
	Array(
		"IBLOCK_TYPE" => "shops", 
		"IBLOCK_ID" => "8", 
		"SECTION_ID" => "6", 
		"ELEMENT_SORT_FIELD" => "PROPERTY_SALON_TYPE", 
		"ELEMENT_SORT_ORDER" => "desc", 
		"FILTER_NAME" => "arrFilterCity", 
		"PAGE_ELEMENT_COUNT" => "300", 
		"LINE_ELEMENT_COUNT" => "1", 
		"PROPERTY_CODE" => array(0=>"SALON_CITY",1=>"SALON_TYPE",2=>"SALON_PHONE",3=>"SALON_METRO",4=>"SALON_ROUTE",5=>"SALON_TIME",6=>"",7=>"SALON_SHEMA",8=>"",), 
		"SECTION_URL" => "/wharetobuy/moscow/", 
		"DETAIL_URL" => "/wharetobuy/moscow/salon_#ELEMENT_ID#.html", 
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
		"PRICE_CODE" => array(), 
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
);?> <?if(($_REQUEST["CITY_ID"])||($_REQUEST["SALON_ID"])){?> <a href="/wharetobuy/moscow/" >Обратно в раздел &raquo;</a> <?}else{?> <a href="/wharetobuy/" >Обратно в раздел »</a> <?}?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>