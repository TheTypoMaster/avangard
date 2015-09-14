<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "ћ€гка€ мебель: акции, скидки, уценка. ”гловые цветные диваны и кресла распродажа");
$APPLICATION->SetTitle("ћ€гка€ мебель: акции, скидки, уценка. ”гловые цветные диваны и кресла распродажа");
?> 
<div class="gray_td"> 
  <h1>јвангард дет€м</h1>
 </div>
<br />
 †<?$APPLICATION->IncludeComponent("bitrix:photo.section", "for_children", array(
	"IBLOCK_TYPE" => "for_children",
	"IBLOCK_ID" => "33",
	"SECTION_ID" => $_REQUEST["id"],
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrFilter",
	"FIELD_CODE" => array(
		0 => "ID",
		1 => "NAME",
		2 => "DETAIL_PICTURE",
		3 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"PAGE_ELEMENT_COUNT" => "80",
	"LINE_ELEMENT_COUNT" => "3",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "‘отографии",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<div> 
  <p></p>
 </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>