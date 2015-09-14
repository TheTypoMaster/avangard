<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("up_inc_file", "none");
$APPLICATION->SetPageProperty("tags", "��������,������,������,�����,������,�������,������,�������������,�������,���������,����������");
$APPLICATION->SetPageProperty("title","������� ������ ������ ��������. ������ ������� ����� �������. ������ ������, ������ �� ������������� � ������. ������� ������� �������.");
$APPLICATION->SetPageProperty("keywords", "�������� ������ ������ ����� ������ ������� ������ ������������� ������� ��������� ����������");
$APPLICATION->SetPageProperty("description", "��������� ������� �������� - ������ ������ �� �������������. ��������� ������, ������� ������. ���������.");
$APPLICATION->SetTitle("��������� ������� �������� - ������������ ������ ������, �������, ������. ��������� ������, ������� ������. ���������.");

$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"mainpage_slider",
	Array(
		"AJAX_MODE" => "N",
		"IBLOCK_ID" => "28",
		"SHOW_ALL_WO_SECTION" => "Y",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(),
		"PRODUCT_PROPERTIES" => array("image", "link"),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_SHOW_ALL" => "N",
	)
);

$APPLICATION->IncludeComponent(
	"avang:mainpage.tabs",
	"",
	Array(
		"IBLOCK_ID" => "5",
		"CACHE_TIME" => "36000000",
	)
);
?> <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"mainpage_banners",
	Array(
		"IBLOCK_ID" => "14",
		"PAGE_ELEMENT_COUNT" => "3",
		"PROPERTY_CODE" => array("href","pictorflash"),
		"AJAX_MODE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"SET_TITLE" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"SHOW_ALL_WO_SECTION" => "Y"
	)
);?> 

<table cellspacing="0" cellpadding="0"> 	
  <tbody> 		
    <tr> 			<td width="100%"> 				
        <div class="gray_td"> 					
          <h1>������� ������ ������</h1>
         				</div>
       			</td> 		</tr>
   		
    <tr> 			<td> 				<?
				$mainFilter= array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "!PROPERTY_IN_CATALOG" => false);
				if($_REQUEST["collection"]!='')
					$mainFilter["PROPERTY_COLLECTION"]= (int)$_REQUEST["collection"];
				$APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_main_page", Array(
	"IBLOCK_TYPE" => "news",	// ��� ���������
	"IBLOCK_ID" => "5",	// ��������
	"SECTION_ID" => $_REQUEST["SECTION_ID"],	// ID �������
	"SECTION_CODE" => "",	// ��� �������
	"SECTION_USER_FIELDS" => array(	// �������� �������
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",	// �� ������ ���� ��������� ��������
	"ELEMENT_SORT_ORDER" => "asc",	// ������� ���������� ���������
	"FILTER_NAME" => "mainFilter",	// ��� ������� �� ���������� ������� ��� ���������� ���������
	"INCLUDE_SUBSECTIONS" => "Y",	// ���������� �������� ����������� �������
	"SHOW_ALL_WO_SECTION" => "Y",	// ���������� ��� ��������, ���� �� ������ ������
	"PAGE_ELEMENT_COUNT" => "200",	// ���������� ��������� �� ��������
	"LINE_ELEMENT_COUNT" => "3",	// ���������� ��������� ��������� � ����� ������ �������
	"PROPERTY_CODE" => array(	// ��������
		0 => "COLLECTION",
		1 => "PRICE",
		2 => "SKIDKA",
		3 => "IN_CATALOG",
		4 => "FULLCOLOR_PIC",
		5 => "BLACKWHITE_PIC",
		6 => "",
	),
	"OFFERS_LIMIT" => "0",	// ������������ ���������� ����������� ��� ������ (0 - ���)
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",	// URL, ������� �� �������� � ���������� �������
	"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",	// URL, ������� �� �������� � ���������� �������� �������
	"BASKET_URL" => "/personal/basket.php",	// URL, ������� �� �������� � �������� ����������
	"ACTION_VARIABLE" => "action",	// �������� ����������, � ������� ���������� ��������
	"PRODUCT_ID_VARIABLE" => "id",	// �������� ����������, � ������� ���������� ��� ������ ��� �������
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",	// �������� ����������, � ������� ���������� ���������� ������
	"PRODUCT_PROPS_VARIABLE" => "prop",	// �������� ����������, � ������� ���������� �������������� ������
	"SECTION_ID_VARIABLE" => "SECTION_ID",	// �������� ����������, � ������� ���������� ��� ������
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	"CACHE_TYPE" => "N",	// ��� �����������
	"CACHE_TIME" => "0",	// ����� ����������� (���.)
	"CACHE_GROUPS" => "Y",	// ��������� ����� �������
	"META_KEYWORDS" => "-",	// ���������� �������� ����� �������� �� ��������
	"META_DESCRIPTION" => "-",	// ���������� �������� �������� �� ��������
	"BROWSER_TITLE" => "-",	// ���������� ��������� ���� �������� �� ��������
	"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
	"DISPLAY_COMPARE" => "N",	// �������� ������ ���������
	"SET_TITLE" => "Y",	// ������������� ��������� ��������
	"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
	"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
	"PRICE_CODE" => "",	// ��� ����
	"USE_PRICE_COUNT" => "N",	// ������������ ����� ��� � �����������
	"SHOW_PRICE_COUNT" => "1",	// �������� ���� ��� ����������
	"PRICE_VAT_INCLUDE" => "Y",	// �������� ��� � ����
	"PRODUCT_PROPERTIES" => "",	// �������������� ������
	"USE_PRODUCT_QUANTITY" => "N",	// ��������� �������� ���������� ������
	"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DISPLAY_BOTTOM_PAGER" => "Y",	// �������� ��� �������
	"PAGER_TITLE" => "������",	// �������� ���������
	"PAGER_SHOW_ALWAYS" => "Y",	// �������� ������
	"PAGER_TEMPLATE" => "",	// �������� �������
	"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// ����� ����������� ������� ��� �������� ���������
	"PAGER_SHOW_ALL" => "Y",	// ���������� ������ "���"
	"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
	),
	false
);?> 			</td> 		</tr>
   	</tbody>
 </table> 
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
 