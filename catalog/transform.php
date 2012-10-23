<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вид трансформации");
//print_r($_GET);
?>

<?$APPLICATION->IncludeComponent("bitrix:news.detail", "transform_detail", Array(
	"IBLOCK_TYPE"	=>	"refer",
	"IBLOCK_ID"	=>	"11",
	"ELEMENT_ID"	=>	$_GET["ELEMENT_ID"],
	"CHECK_DATES"	=>	"N",
	"FIELD_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
		2	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"WIDTH",
		2	=>	"DEPTH",
		3	=>	"HEIGHT",
		4	=>	"COLLECTION",
		5	=>	"SALONS",
		6	=>	"TRANSFORMATION",
		7	=>	"COMPLETE",
		8	=>	"",
	),
	"IBLOCK_URL"	=>	"/catalog/subject/#ELEMENT_ID#.html",
	"AJAX_MODE"	=>	"N",
	"AJAX_OPTION_SHADOW"	=>	"Y",
	"AJAX_OPTION_JUMP"	=>	"N",
	"AJAX_OPTION_STYLE"	=>	"Y",
	"AJAX_OPTION_HISTORY"	=>	"N",
	"CACHE_TYPE"	=>	"N",
	"CACHE_TIME"	=>	"3600",
	"META_KEYWORDS"	=>	"-",
	"META_DESCRIPTION"	=>	"-",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"Y",
	"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
	"ADD_SECTIONS_CHAIN"	=>	"Y",
	"ACTIVE_DATE_FORMAT"	=>	"d.m.Y",
	"USE_PERMISSIONS"	=>	"N",
	"DISPLAY_TOP_PAGER"	=>	"N",
	"DISPLAY_BOTTOM_PAGER"	=>	"Y",
	"PAGER_TITLE"	=>	"Страница",
	"PAGER_TEMPLATE"	=>	"",
	"DISPLAY_DATE"	=>	"Y",
	"DISPLAY_NAME"	=>	"Y",
	"DISPLAY_PICTURE"	=>	"Y",
	"DISPLAY_PREVIEW_TEXT"	=>	"Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>