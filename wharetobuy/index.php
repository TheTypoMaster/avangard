<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("��������� ������ � �������");
?>
<div class="gray_td"> 
  <h1>��������� ������ � �������</h1>
 </div>
 
<div class="new">
	"bitrix:catalog.section.list",
	"wharetobuy",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_URL" => "/wharetobuy/#SECTION_CODE#/",
		"COUNT_ELEMENTS" => "N",
		"DISPLAY_PANEL" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	)
);?>
 
<p> ������� ����������,
  <br />
 
  <br />
 
  <br />
 
  <br />
 
 