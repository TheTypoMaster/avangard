<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Распродажа мягкой мебели. Угловой диван и кресла кожа купить в магазине недорого");
$APPLICATION->SetTitle("Распродажа мягкой мебели. Угловой диван и кресла кожа купить в магазине недорого");
?><?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> <?if($city) { 
echo "<div align=left>";
foreach($id as $ident) {
?> 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"salon_detail_russia",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"ELEMENT_ID" => $ident,
		"SECTION_ID" => "",
		"PROPERTY_CODE" => array(0=>"GOOGLE_MAP",1=>"SALON_TYPE_2",2=>"SALON_PHONE",3=>"SALON_METRO",4=>"SALON_ROUTE",5=>"SALON_TIME",6=>"SALON_ACTION",7=>"",),
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
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?></p>
 <?
}
echo "</div>";
} else {?><sc ript="" language="Javascript"> 
  <table cellspacing="0" cellpadding="0" width="100%"> 
    <tbody> 
      <tr bgcolor="#e20a17" height="21"><td colspan="3"></td></tr>
     
      <tr><td><a href="http://www.avangard.biz/" ><img alt="Мебельная фабрика Авангард" hspace="20" src="/images/logotype.gif" border="0"  /></a></td><td><a href="/mebel_sal.php?id=<?=$_GET['id']?>" ><strong>Диваны в наличии</strong></a></td><td><a href="http://www.avangard.biz/" >На главную</a> <a style="MARGIN-LEFT: 12px" href="/redesign/where_buy/" >На страницу &quot;Где купить&quot;</a> <a style="MARGIN-LEFT: 12px" lick="DocPrint();" href="jav * ascript:void(0);" >Распечатать</a></td></tr>
     
      <tr><td align="center" colspan="3"> 
          <p><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"salon_detail",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"ELEMENT_ID" => $id,
		"SECTION_ID" => "",
		"PROPERTY_CODE" => array(0=>"GOOGLE_MAP",1=>"SALON_ADRESS",2=>"SALON_CITY",3=>"SALON_TYPE_2",4=>"SALON_PHONE",5=>"SALON_METRO",6=>"SALON_ROUTE",7=>"SALON_TIME",8=>"SALON_ACTION",9=>"SALON_PHOTO",10=>"SALON_ITEMS",11=>"",),
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
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?></p>
         </td></tr>
     
      <tr><td align="right" colspan="3"> 
          <div style="padding: 0px 12px 12px; "><?// echo '<a id="bxid_511446" href="/mebel_sal.php?id='.$id.'" >Диваны в наличии</a>';?> </div>
         </td></tr>
     
      <tr><td colspan="3"></td></tr>
     </tbody>
   </table>
 <sc ript="" language="Javascript"><?}?>  </sc></sc><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "> 
    <div><sc ript="" language="Javascript"></sc></div>
   </blockquote></blockquote> 
<div><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> 
        
       </sc></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc></blockquote></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc></blockquote></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc></blockquote><sc ript="" language="Javascript"> 
    <p> </p>
   </sc> 
  <br />
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>