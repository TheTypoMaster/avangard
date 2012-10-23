<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?><?$APPLICATION->IncludeFile("iblock/rss/rss_out.php", Array(
	"ID"	=>	"1",		// Информационный блок, новости которого экспортируются
	"NUM_NEWS"	=>	"20",	// Количество новостей для экспорта
	"NUM_DAYS"	=>	"30",	// Количество дней для экспорта
	"YANDEX"	=>	"N",	// Экспортировать в диалект Яндекса
	"CACHE_TIME"	=>	"600",// Время кэширования результата (0 - не кэшировать)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>