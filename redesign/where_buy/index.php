<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Адреса магазинов, салонов где продаётся мягкая мебель. Купить диван");
$APPLICATION->SetPageProperty("keywords", "купить угловой диван кресло кровать мебель мягкая");
$APPLICATION->SetPageProperty("description", "Где купить мягкую мебель фабрики Авангард : салоны и подиумы Москвы и России");
$APPLICATION->SetTitle("Адреса магазинов, салонов где продаётся мягкая мебель. Купить диванАдреса магазинов, салонов где продаётся мягкая мебель. Купить диван");
?> 
<div class="gray_td_left" width="720"> 
  <h1 style="text-align: justify; ">Где купить диван?</h1>
 </div>
 
<div style="text-align: justify; "><?$russia = $_GET[russia];
  $moscow = $_GET[moscow]; 


$kol=0;

if($russia) $sect = 7;
else $sect = 6;
 ?></div>
 
<div style="position: relative; top: -13px; width: 100%; height: 40px; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-top-color: rgb(225, 225, 225); border-right-color: rgb(225, 225, 225); border-bottom-color: rgb(225, 225, 225); border-left-color: rgb(225, 225, 225); border-image: initial; "> 
	<table width="100%" height="40" cellspacing="0" cellpadding="0" class="zakladki" style="text-align: justify; "> 
		<tbody> 
			<tr>
				<td style="vertical-align: middle; text-align: center; width: 33%; border-image: initial; "> 
					<b><font style="font-size: 14px; ">Москва и МО</font></b> 
					<br />
					<a target="_new" href="/redesign/where_buy/map.php?id=6" >посмотреть на карте</a> 
				</td> 
				<td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 34%; border-image: initial; "> 
					&nbsp;
				</td> 
				<td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 33%; border-image: initial; ">
					&nbsp;
				</td>
			</tr>
		</tbody>
	</table>
</div>
 
<div style="text-align: justify; margin-bottom: 5px; " class="cityname"><img width="25" height="20" border="0" style="border-width: 0px; border-style: none;" src="/wharetobuy/maps/salon.gif"  />Фирменные Салоны</div>
 	 
<div style="text-align: justify; "><?$arrFilterType = Array( "PROPERTY_SALON_TYPE_2_VALUE" => "Фирменный Салон");?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"spisok",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $sect,
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilterType",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(0=>"SALON_ADRESS",1=>"SALON_CITY",2=>"SALON_TYPE_2",3=>"SALON_PHONE",4=>"SALON_METRO",5=>"SALON_TIME",6=>"SALON_ACTION_TEXT",7=>"SALON_ACTION_TEXT_2",8=>"",9=>"",),
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
);?></div>
 
<div style="text-align: justify; margin-bottom: 5px; " class="cityname"> <img width="25" height="20" border="0" style="border-width: 0px; border-style: none;" src="/wharetobuy/maps/podium.gif"  /> 		 Фирменные подиумы</div>
 
<p style="text-align: justify; "> 	 <?$arrFilterType = Array( "PROPERTY_SALON_TYPE_2_VALUE" => "Фирменный Подиум");?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"spisok",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $sect,
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilterType",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(0=>"SALON_ADRESS",1=>"SALON_CITY",2=>"SALON_TYPE_2",3=>"SALON_PHONE",4=>"SALON_METRO",5=>"SALON_TIME",6=>"SALON_ACTION_TEXT",7=>"SALON_ACTION_TEXT_2",8=>"",9=>"",),
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
);?></p>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; ">      </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>