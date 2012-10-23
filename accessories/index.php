<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мебельные аксессуары.");
$APPLICATION->SetTitle("");
?> 
<table cellspacing="0" cellpadding="0" style="text-align: justify; "> 
  <tbody> 
    <tr><td> 
        <p><?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"seo_accessories",
	Array(
		"IBLOCK_TYPE" => "",
		"IBLOCK_ID" => "",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilter",
		"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
		"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"DISPLAY_PANEL" => "N",
		"DISPLAY_COMPARE" => "N",
		"SET_TITLE" => "Y",
		"PAGE_ELEMENT_COUNT" => "30",
		"LINE_ELEMENT_COUNT" => "3",
		"PROPERTY_CODE" => "",
		"PRICE_CODE" => "",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	)
);?> </p>
       </td></tr>
   
    <tr><td> 
        <p></p>
       </td></tr>
   </tbody>
 </table>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>