<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?> 
<div width="720" class="gray_td_left"> 
  <h1>Где купить диван?</h1>
 </div>
 <?$russia = $_GET[russia];
  $moscow = $_GET[moscow]; 


$kol=0;

if($russia) $sect = 7;
else $sect = 6;
 ?> 
<div style="position: relative; top: -13px; width: 100%; height: 40px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-color: rgb(225, 225, 225); border-right-color: rgb(225, 225, 225); border-bottom-color: rgb(225, 225, 225); border-left-color: rgb(225, 225, 225); border-image: initial; "> 
  <table class="zakladki" width="100%" cellpadding="0" cellspacing="0" height="40"> 
    <tbody> 
      <tr><td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 33%; border-image: initial; "> <a href="/redesign/where_buy/" ><b><font style="font-size: 14px; ">Москва и МО</font></b></a> 
          <br />
         <a href="/redesign/where_buy/moscow.php" target="_new" >посмотреть на карте</a> </td> <td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 34%; border-image: initial; "> <a href="/redesign/where_buy/spb-list.php" ><b><font style="font-size: 14px; ">Санкт-Петербург</font></b></a> 
          <br />
         <a href="/redesign/where_buy/spb.php" target="_new" >посмотреть на карте</a> </td> <td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 33%; border-image: initial; "> <a href="rus.php?russia=1" ><b><font style="font-size: 14px; ">Россия</font></b></a> 
          <br />
         <a href="/redesign/where_buy/russia.php" target="_new" >посмотреть на карте</a> </td></tr>
     </tbody>
   </table>
 </div>
 	 
<div class="cityname" style="margin-bottom: 5px; "> <img src="/wharetobuy/maps/podium.gif" style="border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none; border-color: initial; " width="25" border="0" height="20"  /> 		 Фирменные подиумы</div>
 
<p> <?
$sect = 7;
if (isset($_REQUEST["t"])) {
	$city = antitranslit($_REQUEST["t"]);
	//echo $city;
$arrFilterType = Array( "PROPERTY_SALON_CITY" => $city);
}
?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"spisok2",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $sect,
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilterType",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(0=>"SALON_ADRESS",1=>"SALON_CITY",2=>"SALON_TYPE_2",3=>"SALON_PHONE",4=>"SALON_METRO",5=>"SALON_TIME",6=>"SALON_ACTION_TEXT",7=>"SALON_ACTION_TEXT_2",8=>"",),
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
		"SET_TITLE" => "N",
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
 
<br />
 <?php
function antitranslit ($str) {				
	$from = "абвгдезиклмнопрстуфцы"; 
    $in   = "abvgdeziklmnoprstufcy"; 
    $fromin = array_flip(array(  
        "й" => "jj", "е" => "e", "ж" => "zh", "х" => "kh", "ч" => "ch",  
        "ш" => "sh", "щ" => "shh", "э" => "je", "ю" => "yu", "я" => "ya", 
        "ъ" => "tv", "ь" => "mgz"));
         							
     //замена strtolower
     $str = strtr($str, $fromin);
     $str = strtr($str, $in, $from);
     $str = substr($str, 0, 50);
     $str = str_replace("_", " ", $str);
								
     return $str;								
}
?> <?
if (isset($_REQUEST["t"])) 
 {
   $city = $_REQUEST["t"]; 
   // if ($city == "nizhnijj_novgorod" || $city == "chelyabinsk" || $city == "sankt-peterburg") include_once( $_SERVER['DOCUMENT_ROOT']. '/8days/action20_regions.php' );
   if ($city == "nizhnijj_novgorod") include_once( $_SERVER['DOCUMENT_ROOT']. '/8days/act_nizhnijj.php' );
   //if ($city == "novosibirsk") include_once( $_SERVER['DOCUMENT_ROOT']. '/8days/act_novosibirsk.php' );
   include_once( $_SERVER['DOCUMENT_ROOT']. '/8days/action20_regions.php' ); 
 }
?> 
<br />

<br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>