<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "������� �����-������� ���������. ���������� ������ �� �������� ������.");
$APPLICATION->SetTitle("������� �����-������� ���������. ���������� ������ �� �������� ������.");
?> 
<div style="text-align: justify;"><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"template_statii_det",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "17",
		"ELEMENT_ID" => "3162",
		"ELEMENT_CODE" => "",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
		"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"PROPERTY_CODE" => array("models","salons","tegs"),
		"OFFERS_LIMIT" => "0",
		"PRICE_CODE" => array(),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_PROPERTIES" => array(),
		"USE_PRODUCT_QUANTITY" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"USE_ELEMENT_COUNTER" => "Y"
	)
);?></div>
 
<div style="text-align: justify;"> 
  <br />
 
  <div><span>������ ��� ���������� ����� ����������, ���� ������������� �������� �� �����-���� ����� ������������ ������ ������ ������. ��������, ����� ����� �������� ����� ����� ������� �����-�������. ����������, ��� ������ ��������� ��������� �������� ������ ����� ����� ���� ������������ ���������, �� ����������� ���������� ����� � ���������� ������, � ����� �� ����������� �������� ���� ������������. ������� ����� ������� �� ���� �������, ������� ��� ������������� ������������� � ������� ������� � ������ ������������. �����, ����� �����-����������� ��� ������������� ����� ����������� � �������� ��������.</span></div>
 
  <br />
 
  <div><span>��� ��������, ����������� ������ ������ �� ������ ������������, �� � ������������������, � ��� ����� �����, ����� ���� ����������� ������ ������������ ���������� ����� ��� ������� ��� ���������������� ������. ������� �������, �������������, ����������� ������, ������� ����� ������ �������� ��������. ���������� ���������, �������������� ����� ������� &quot;������&quot; ��� � ������������� &quot;�����������&quot;, ����� ��������� � ����� � ������������ �������������. ������ � ������ ����� 12 ����������� ��������, ��� ���������� �������� ������� ���������� �������.</span></div>
 
  <br />
 
  <div><span>������� ������ �������� ���������� ��������� �������� ������ ������ � ��������� �������� �������� ���������� &quot;�����������&quot;, ������� ������������� �������� ��� ������������ �������������, ��� ��������� ��������� ����������� ���� ������������. ������ ����� ������� ���� ����� ��������������� � ������� ������� ������ ���������, ����� �� ������ ����������� ������������� ���� ��� ����������� �����, ������� ��������� ��������������� � �������, ����������� � ��������� ������ ��� �����.</span></div>
 
  <br />
 
  <div><span>���������� ������� �����-������� ������ �� ���������� ���������� ������� � ���������, �� ������������� ����� ��������� ����� ���������, ����� ���: ������, �������, ��������������, ��������. ����� ���������� ������������� �������������� ��� ����������� ������������� ���� � ����� ������������ ���� ����� � �����������. ������ ������� �������� - ��� ���������� ������������� � ������������!</span></div>
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>