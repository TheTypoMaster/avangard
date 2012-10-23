<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
?><?$APPLICATION->IncludeComponent("bitrix:search.page", "search", Array(
	"CHECK_DATES"	=>	"N",
	"arrWHERE"	=>	array(
		0	=>	"iblock_news",
		1	=>	"iblock_refer",
		2	=>	"iblock_catalogue",
		3	=>	"iblock_shops",
	),
	"arrFILTER"	=>	array(
		0	=>	"no",
	),
	"SHOW_WHERE"	=>	"Y",
	"PAGE_RESULT_COUNT"	=>	"50",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600"
	)
);?> <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>