<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������ �����-����������");
?> 
<div width="720" class="gray_td_left"> 
  <h1>��� ������ �����?</h1>
 </div>
 <?
 $sect = 38;
 ?> 
<div style="position: relative; top: -13px; width: 100%; height: 40px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-color: rgb(225, 225, 225); border-right-color: rgb(225, 225, 225); border-bottom-color: rgb(225, 225, 225); border-left-color: rgb(225, 225, 225); border-image: initial; "> 
	<table class="zakladki" width="100%" cellpadding="0" cellspacing="0" height="40"> 
		<tbody> 
			<tr>
				<td style="vertical-align: middle; text-align: center; width: 34%; border-image: initial; "> 
					<b><font style="font-size: 14px; ">�����-���������</font></b> 
					<br />
					<a href="/redesign/where_buy/map.php?id=38" target="_new" >���������� �� �����</a> 
				</td> 
				<td style="vertical-align: middle; text-align: center; width: 33%; border-image: initial; " bgcolor="#e1e1e1"> 
					&nbsp;
				</td> 
				<td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 33%; border-image: initial; ">
					&nbsp;
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="cityname" style="margin-bottom: 5px; "> <img src="/wharetobuy/maps/podium.gif" style="border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none; border-color: initial; " width="25" border="0" height="20"  /> 		 ��������� �������</div>
 
<p> 	 <?$arrFilterType = Array( "PROPERTY_SALON_TYPE_2_VALUE" => "��������� ������");?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"spisok",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $sect,
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilterType",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(0=>"SALON_ADRESS",1=>"SALON_CITY",2=>"SALON_TYPE_2",3=>"SALON_PHONE",4=>"SALON_METRO",5=>"SALON_TIME",6=>"SALON_ACTION_TEXT",7=>"",),
		"SECTION_URL" => "/wharetobuy/moscow/",
		"DETAIL_URL" => "/wharetobuy/moscow/salon_#ELEMENT_ID#.html",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"DISPLAY_PANEL" => "N",
		"DISPLAY_COMPARE" => "N",
		"SET_TITLE" => "Y",
		"CACHE_FILTER" => "N",
		"PRICE_CODE" => array(),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "������",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	)
);?></p>
 
<br />
 
<div class="gray_line_small"> </div> 
<p style="font-size:10pt;line-height:1.2;"><img height="77" src="/8days/image/mmSPB_1.gif"  />&nbsp;<img height="77" src="/8days/image/mmFoto_1.png"  />
<br /><br /><b style="color: rgb(0, 0, 255);">����� ��������� � ������� ��� ��� ����� (� �������� �����-����������)</b>
<br /></p>
 
<ul style="font-size:10pt;line-height:1.2;"> 
  <li>������������ �� ���������� ���� (��������, ��������� � ��.)</li>
  <li> ����������� �� ��������� ���������� (�����, ����)</li>
</ul>
<p style="font-size:10pt;line-height:1.2;">���.: <b>+7-921-418-15-84</b></p> 

<div class="gray_line_small"> </div>
<p style="font-size:10pt;line-height:1.2;"><b style="color: rgb(0, 0, 255);">������ �������</b>
<br />�� �������� ������������ � ����������������� ������������ ������� ���������� �� ���.<b>+7-921-418-15-84</b></p>

<div class="gray_line_small"> </div>
<p style="font-size:10pt;line-height:1.2;"><strong style="color: rgb(0, 0, 255);">������� ������ - ������ 20%:</strong><br />
<br />� 8 �� 14 ������� 2012 � �������
<br />� 15 �� 21 ������� 2012 � ��������
<br />� 23 �� 31 ������� 2012 � ������� 
<br /><br />������ ��������������� �� ����� � ����� ���� 4 ���������, � ���� ���� 7 ���������.
<br />��� ������������ ������ � ��������� ������ ������ �� ����������������.</p>
 
<div class="gray_line_small"></div> 
<p style="font-size:10pt;line-height:1.2;"><img height="115" src="/8days/image/skidi_SPB_188x115_6.gif" />
<br /><br /><b style="color: rgb(0, 0, 255); "> ������ �� 30% �� 70% �� ����������� ������� � ������ ������ ���� 2</b> </p> 
<p>&nbsp;</p>
<br /> 
<br /> 
<br />
 
<br />
 
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>