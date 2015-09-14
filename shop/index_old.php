<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Интернет магазин мягкой мебели. Купить недорогой диван онлайн");
$APPLICATION->SetTitle("Интернет магазин мягкой мебели. Купить недорогой диван онлайн");
?> 
<p><link rel="File-List" href="file:///C:\DOCUME~1\nick\LOCALS~1\Temp\msohtmlclip1\01\clip_filelist.xml"></link><link rel="themeData" href="file:///C:\DOCUME~1\nick\LOCALS~1\Temp\msohtmlclip1\01\clip_themedata.thmx"></link><link rel="colorSchemeMapping" href="file:///C:\DOCUME~1\nick\LOCALS~1\Temp\msohtmlclip1\01\clip_colorschememapping.xml"></link>
<style> &lt;!-- /* Font Definitions */ @font-face 	{font-family:&quot;Cambria Math&quot;; 	panose-1:2 4 5 3 5 4 6 3 2 4; 	mso-font-charset:1; 	mso-generic-font-family:roman; 	mso-font-format:other; 	mso-font-pitch:variable; 	mso-font-signature:0 0 0 0 0 0;} @font-face 	{font-family:Calibri; 	panose-1:2 15 5 2 2 2 4 3 2 4; 	mso-font-charset:204; 	mso-generic-font-family:swiss; 	mso-font-pitch:variable; 	mso-font-signature:-1610611985 1073750139 0 0 159 0;} /* Style Definitions */ p.MsoNormal, li.MsoNormal, div.MsoNormal 	{mso-style-unhide:no; 	mso-style-qformat:yes; 	mso-style-parent:&quot;&quot;; 	margin-top:0cm; 	margin-right:0cm; 	margin-bottom:10.0pt; 	margin-left:0cm; 	line-height:115%; 	mso-pagination:widow-orphan; 	font-size:11.0pt; 	font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; 	mso-ascii-font-family:Calibri; 	mso-ascii-theme-font:minor-latin; 	mso-fareast-font-family:Calibri; 	mso-fareast-theme-font:minor-latin; 	mso-hansi-font-family:Calibri; 	mso-hansi-theme-font:minor-latin; 	mso-bidi-font-family:&quot;Times New Roman&quot;; 	mso-bidi-theme-font:minor-bidi; 	mso-fareast-language:EN-US;} .MsoChpDefault 	{mso-style-type:export-only; 	mso-default-props:yes; 	mso-ascii-font-family:Calibri; 	mso-ascii-theme-font:minor-latin; 	mso-fareast-font-family:Calibri; 	mso-fareast-theme-font:minor-latin; 	mso-hansi-font-family:Calibri; 	mso-hansi-theme-font:minor-latin; 	mso-bidi-font-family:&quot;Times New Roman&quot;; 	mso-bidi-theme-font:minor-bidi; 	mso-fareast-language:EN-US;} .MsoPapDefault 	{mso-style-type:export-only; 	margin-bottom:10.0pt; 	line-height:115%;} @page Section1 	{size:612.0pt 792.0pt; 	margin:2.0cm 42.5pt 2.0cm 3.0cm; 	mso-header-margin:36.0pt; 	mso-footer-margin:36.0pt; 	mso-paper-source:0;} div.Section1 	{page:Section1;} --&gt; </style>
 </p>
<span style="font-size: 8pt; line-height: 115%;"><o:p></o:p></span>  <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	".default",
	Array(
		"AREA_FILE_SHOW" => "page",
		"AREA_FILE_SUFFIX" => "top_main_text",
		"EDIT_TEMPLATE" => "page_inc.php"
	)
);?>
<p></p>
 <?php
$arrFilter = array("PROPERTY_INET_SHOP" => "Да");
?> 
<p><?

$APPLICATION->IncludeComponent("bitrix:catalog.section", "template1", array(
	"IBLOCK_TYPE" => "catalogue",
	"IBLOCK_ID" => "5",
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "desc",
	"FILTER_NAME" => "arrFilter",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "N",
	"PAGE_ELEMENT_COUNT" => "30",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "INET_SHOP",
		1 => "",
	),
	"OFFERS_LIMIT" => "5",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);

?></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>