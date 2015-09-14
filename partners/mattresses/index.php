<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Матрасы");
$APPLICATION->SetTitle("");
?> 

<table cellspacing="0" cellpadding="0" style="text-align: justify;"> 
  <tbody> 
    <tr><td> 
        <p><?$APPLICATION->IncludeComponent("anp:catalog.section", "seo_accessories", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => IntVal(5),
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrFilter",
	"PAGE_ELEMENT_COUNT" => "30",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
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
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	),
	false
);?> </p>
       </td></tr>
   
    <tr><td> 
        <p></p>
       </td></tr>
   </tbody>
 </table>
 
<div style="text-align: justify;"> 
  <p st yle="font-family: Arial, Tahoma, Verdana, sans-serif; font-size: 12.727272033691406px; text-align: start; background-color: rgb(255, 255, 255); text-indent: 35.4pt;"> 
    <br />
   
    <br />

  </p>

</div>

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>