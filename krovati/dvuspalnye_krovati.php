<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "����������� ������� �� �������������. ������ ������� ������� ������� � ��������-��������. ������� ������. ���������� ����������� ������� ����� �������� � ������������� ����������. �������� ����.");
$APPLICATION->SetTitle("����������� ������� �� �������������. ������ ������� ������� ������� � ��������-��������. ������� ������. ���������� ����������� ������� ����� �������� � ������������� ����������. �������� ����.");
?> 
<h2 style="text-align: justify; "><span style="background-color: rgb(255, 255, 255); "><font color="#ff0000"> ����������� �������</font></span></h2>
 
<p style="text-align: justify; "><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"template_statii_det",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "17",
		"ELEMENT_ID" => "4659",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"PROPERTY_CODE" => array(0=>"",1=>"models",2=>"salons",3=>"tegs",),
		"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
		"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?></p>
 
<p style="text-align: justify; "> 
  <br />
 </p>
 
<p style="text-align: justify; "> 
  <br />
 </p>
 
<p style="text-align: justify; ">������ ����� �������� - ��� ������� � �������. � ������ ������ ���� ���� ��������� �� ���� ����������������. ���� �� ������� ������������ � ���� ������� ����� �������� �������������� �� ����� ����. ����������� ��� �������� ���������� ������� - ����������� ������� �������.</p>
 
<div style="text-align: justify; ">������ ���� ������� ������������� �������� ����� �� ����� ���� ����� ������� �������� ������. �������� ���� ����� ����� �������� ������� �� ����, ������������������� ������� ������� �� �������� ��� ���. ��� ����� ��������, ��� �� ���������� �������� ����������� ������ ���������� ��� ����������. ��� ������ ��� ������������ � ���� � ���� � �����. ��������� ����������� �������� ���� ��������� ����������, ��������� ����. ������ � ���� ������ �� ������ ������� ���� ����������,���� ��������� ����� ��������� �, ��� ���������, �������� ���� ������������� ��������� � ������ ��������� ���������� ���������, ������� ������ ��������� ��� ��������.</div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; ">��� ��������� ����������, �������, �� ��� ������, ������ ��������������� ����������� �������:</div>
 
<div> 
  <ul> 
    <li style="text-align: justify; ">��� ������ ���� �������, ����� ���������� ������� ��� � ������� ���� ����</li>
   
    <li style="text-align: justify; ">��� ������ ����� ����������� ������ ��� ��� ������</li>
   
    <li style="text-align: justify; ">��� ������ ���� ������� � ����������</li>
   
    <li style="text-align: justify; ">��� ������ ��������������� ������ ��������� �������.</li>
   </ul>
 </div>
 
<div style="text-align: justify; ">���� ���� ����������� �������� ����������� �������, ������� �� ������ ���������� � ����� ��������-��������.�</div>
 
<p></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>