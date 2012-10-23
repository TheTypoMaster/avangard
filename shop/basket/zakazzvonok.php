<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?php
$APPLICATION->RestartBuffer();

if (isset($_POST["phone"]) && strlen($_POST["phone"]) > 10) {
	$_POST["phone"] = str_replace(array("%2B", "%20"), array("+", " "), $_POST["phone"]);
	
	$toaddress = "av.shop10@gmail.com";
	$subject = 'Запрос звонка: '.$_POST["phone"];
	$mailcontent = 'Запрос звонка: '.$_POST["phone"];
	mail($toaddress, $subject, $mailcontent);
	echo "Ваш запрос успешно отправлен! <a href=\"#\" id=\"clww\">Закрыть окно</a>";
	
} else {
	echo "Произошла ошибка, попробуйте еще раз!";
}

die();
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>