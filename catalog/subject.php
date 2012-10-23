<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог товаров");
//print_r($_GET);
?> <?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb",
	"",
	Array(
	)
);?> <?$APPLICATION->IncludeComponent("bitrix:news.detail", "detail_subject", array(
	"IBLOCK_TYPE" => "catalogue",
	"IBLOCK_ID" => "19",
	"ELEMENT_ID" => $_GET["ELEMENT_ID"],
	"ELEMENT_CODE" => "",
	"CHECK_DATES" => "N",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "WIDTH",
		1 => "DEPTH",
		2 => "HEIGHT",
		3 => "LENGTH_PLACE",
		4 => "WIDTH_PLACE",
		5 => "COLLECTION",
		6 => "SALONS",
		7 => "TRANSFORMATION",
		8 => "COMPLETE",
		9 => "ALSO",
		10 => "",
	),
	"IBLOCK_URL" => "/catalog/subject/#ELEMENT_ID#.html",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"DISPLAY_PANEL" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "Y",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "Y",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"USE_PERMISSIONS" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Страница",
	"PAGER_TEMPLATE" => "",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?> <?

$rs=CIBlockElement::GetList(array(), array("ACTIVE"=>"Y", "IBLOCK_ID"=>19, "ID" => $_GET["ELEMENT_ID"]), false, array(), array("ID", "NAME", "PROPERTY_COLLECTION", "PROPERTY_COLLECTION.NAME"));
while($ar=$rs->GetNext()) {
	//print_r($ar);
	$APPLICATION->AddChainItem($ar["PROPERTY_COLLECTION_NAME"], "/catalog/divan".$ar["PROPERTY_COLLECTION_VALUE"].".htm");
	$APPLICATION->AddChainItem($ar["NAME"], "/catalog/subject/".$ar["ID"].".html");
}

$res = CIBlockElement::GetByID($_GET["ELEMENT_ID"]);
$ar_res = $res->Fetch();
//$APPLICATION->AddChainItem($ar_res["NAME"]);
?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
