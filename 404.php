<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

$sapi_type = php_sapi_name();
if ($sapi_type=="cgi") 
	header("Status: 404");
else 
	header("HTTP/1.1 404 Not Found");

@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 - HTTP not found");
?>
<h1>Данной страницы не существует.</h1>
<h1>Предлагаем Вам ознакомиться с нашими новинками и хитами продаж.</h1>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>