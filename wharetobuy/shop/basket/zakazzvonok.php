<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?php
$APPLICATION->RestartBuffer();

if (isset($_POST["phone"]) && strlen($_POST["phone"]) > 10) {
	$_POST["phone"] = str_replace(array("%2B", "%20"), array("+", " "), $_POST["phone"]);
	
	$toaddress = "av.shop10@gmail.com";
	$subject = '������ ������: '.$_POST["phone"];
	$mailcontent = '������ ������: '.$_POST["phone"];
	mail($toaddress, $subject, $mailcontent);
	echo "��� ������ ������� ���������! <a href=\"#\" id=\"clww\">������� ����</a>";
	
} else {
	echo "��������� ������, ���������� ��� ���!";
}

die();
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>