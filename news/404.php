<?
define("AUTH_404","Y");
header("HTTP/1.1 200 OK");
$arrPath = pathinfo($_SERVER["REQUEST_URI"]);
$params = "";
if(($p=strpos($_SERVER["REQUEST_URI"], "?"))!==false)
{
	$params = substr($_SERVER["REQUEST_URI"], $p+1);
}
parse_str($params, $_GET);
extract($_GET, EXTR_SKIP);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$arr = explode("?",$arrPath["basename"]);
$fname = $arr[0];
if (strlen(trim($arrPath["extension"]))>0)
{
	$arr = explode(".",$fname);
	$ID = intval($arr[0]);
	?>
	<?$APPLICATION->IncludeFile("iblock/news/detail.php", Array(
	"ID"	=>	$ID,				// ID новости
	"IBLOCK_TYPE"	=>	"news",				// Тип информационного блока (используется только для проверки)
	"IBLOCK_ID"	=>	"1",				// Код информационного блока
	"arrPROPERTY_CODE"	=>	Array(			// Свойства
					"AUTHOR",
					"SOURCE"
				),
	"LIST_PAGE_URL"	=>	"#SITE_DIR#about/news/",// URL страницы просмотра списка элементов (по умолчанию - из настроек инфоблока)
	"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",		// Включать инфоблок в цепочку навигации
	"CACHE_TIME"	=>	"0",				// Время кэширования (0 - не кэшировать)
	"DISPLAY_PANEL"	=>	"Y",			// Добавлять в админ. панель кнопки для данного компонента
	)
);?><?
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>