<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
?> 
<div class="gray_td"><h1>Поиск</h1></div>
<div width="720">
<?$APPLICATION->IncludeComponent("bitrix:search.page", "search_new", Array(
	"RESTART"	=>	"N",
	"CHECK_DATES"	=>	"N",
	"USE_TITLE_RANK"	=>	"N",
	"arrWHERE"	=>	array(
		0	=>	"iblock_news",
		1	=>	"iblock_refer",
		2	=>	"iblock_catalogue",
		3	=>	"iblock_shops",
	),
	"arrFILTER"	=>	array(
		0	=>	"iblock_catalogue",
		1	=>	"",
	),
	"arrFILTER_iblock_news"	=>	array(
	),
	"arrFILTER_iblock_refer"	=>	array(
	),
	"arrFILTER_iblock_catalogue"	=>	array(
		0	=>	"5",
	),
	"SHOW_WHERE"	=>	"N",
	"PAGE_RESULT_COUNT"	=>	"50",
	"AJAX_MODE"	=>	"N",
	"AJAX_OPTION_SHADOW"	=>	"Y",
	"AJAX_OPTION_JUMP"	=>	"N",
	"AJAX_OPTION_STYLE"	=>	"Y",
	"AJAX_OPTION_HISTORY"	=>	"N",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"PAGER_TITLE"	=>	"Результаты поиска",
	"PAGER_SHOW_ALWAYS"	=>	"Y",
	"PAGER_TEMPLATE"	=>	""
	)
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>