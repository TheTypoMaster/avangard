<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "���������� ������ ������. ������� ����� � ������ ���� ������ � �������� ��������");
$APPLICATION->SetTitle("���������� ������ ������. ������� ����� � ������ ���� ������ � �������� ��������");
?><?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> <?if($city) { 
echo "<div align=left>";
foreach($id as $ident) {
?> 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"salon_detail_russia",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"ELEMENT_ID" => $ident,
		"SECTION_ID" => "",
		"PROPERTY_CODE" => array(0=>"GOOGLE_MAP",1=>"SALON_TYPE_2",2=>"SALON_PHONE",3=>"SALON_METRO",4=>"SALON_ROUTE",5=>"SALON_TIME",6=>"SALON_ACTION",7=>"",),
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
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
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
 <?
}
echo "</div>";
} else {
	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_SALON_REGION");
    $arFilter = Array("IBLOCK_ID"=>IntVal(8), "ID" => $id, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $res_items = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    $ob = $res_items->GetNextElement();
    $arFields = $ob->GetFields();
    $rg_salon = $arFields[PROPERTY_SALON_REGION_VALUE];
	if ($rg_salon !='') $nal_ref = '';
	else $nal_ref = '<a href="/wharetobuy/mebel_in_salon.php?id='.$id.'" ><strong>������ � �������</strong></a>'; // $_GET['id']
?><sc ript="" language="Javascript"> 
  <table cellspacing="0" cellpadding="0" width="100%"> 
    <tbody> 
      <tr bgcolor="#e20a17" height="21"><td colspan="3"></td></tr>
     
      <tr><td><a href="http://www.avangard.biz/" ><img alt="��������� ������� ��������" hspace="20" src="/images/logotype.gif" border="0"  /></a></td><td><?=$nal_ref?></td><td><a href="http://www.avangard.biz/" >�� �������</a> <a style="MARGIN-LEFT: 12px" href="/redesign/where_buy/" >�� �������� &quot;��� ������&quot;</a> </td></tr>

      <tr><td align="center" colspan="3">
		  <? //echo 'id='.$id.'<br>';?>
          <p><?$APPLICATION->IncludeComponent("bitrix:catalog.element", "salon_detail", array(
	"IBLOCK_TYPE" => "shops",
	"IBLOCK_ID" => "8",
	"ELEMENT_ID" => $id,
	"ELEMENT_CODE" => "",
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"PROPERTY_CODE" => array(
		0 => "GOOGLE_MAP",
		1 => "SALON_ADRESS",
		2 => "SALON_CITY",
		3 => "SALON_REGION",
		4 => "SALON_TYPE_2",
		5 => "SALON_PHONE",
		6 => "SALON_METRO",
		7 => "SALON_ROUTE",
		8 => "SALON_TIME",
		9 => "SALON_ACTION",
		10 => "SALON_PHOTO",
		11 => "SALON_ITEMS",
		12 => "",
	),
	"OFFERS_LIMIT" => "0",
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"USE_ELEMENT_COUNTER" => "Y",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"LINK_IBLOCK_TYPE" => "",
	"LINK_IBLOCK_ID" => "",
	"LINK_PROPERTY_SID" => "",
	"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	),
	false
);?></p>
         </td></tr>
     
      <tr><td align="left" colspan="3"> 
          <div style="padding: 0px 12px 12px 100px; "><a name="nal"></a>
<?// echo '<a id="bxid_511446" href="/mebel_sal.php?id='.$id.'" >������ � �������</a>';?> 
<?
if ($rg_salon !='')
{?>

<table cellspacing="0" cellpadding="0"> 
	<tbody> 
		<tr>
			<td width="100%"> 
				<div class="gray_td"> 
					<h1>������ �� ����� � ���� ������</h1>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<?
				$mainFilter= array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "!PROPERTY_IN_CATALOG" => false);
				if($_REQUEST["collection"]!='')
					$mainFilter["PROPERTY_COLLECTION"]= (int)$_REQUEST["collection"];
				$APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_bigpics_rg", Array(
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
	"PAGE_ELEMENT_COUNT" => "40",	// ���������� ��������� �� ��������
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
);?>
          </div>
         </td></tr>
     
      <tr><td colspan="3"></td></tr>
     </tbody>
   </table>
 <sc ript="" language="Javascript"><?}?>��</sc></sc><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "> 
    <div><sc ript="" language="Javascript"></sc></div>
   </blockquote></blockquote> 
<div><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> 
        
       </sc></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc></blockquote></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc></blockquote></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc></blockquote><sc ript="" language="Javascript"> 
    <p> </p>
   </sc> 
  <br />
 </div>


			</td>
		</tr>
	</tbody>
</table>
<?}?>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>