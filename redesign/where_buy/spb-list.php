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
<div style="position: relative; top: -13px; width: 100%; height: 40px; border: 1px solid rgb(225, 225, 225);"> 	 
  <table class="zakladki" width="100%" cellpadding="0" cellspacing="0" height="40"> 		 
    <tbody> 			 
      <tr> 				<td style="vertical-align: middle; text-align: center; width: 34%;"> 					<b><font style="font-size: 14px;">�����-���������</font></b> 					 
          <br />
         					<a href="/redesign/where_buy/map.php?id=38" target="_new" >���������� �� �����</a> 				</td> 				<td style="vertical-align: middle; text-align: center; width: 33%;" bgcolor="#e1e1e1"> 					&nbsp; 				</td> 				<td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 33%;"> 					&nbsp; 				</td> 			</tr>
     		</tbody>
   	</table>
 </div>
 
<div class="cityname" style="margin-bottom: 5px;"> <img src="/wharetobuy/maps/podium.gif" style="border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none; border-color: initial; " width="25" border="0" height="20"  /> 		 ��������� �������</div>
 
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
 
<br />
 
<p style="font-size: 10pt; line-height: 1.2;">� � � � � ��
  <br />
 
  <br />
<b style="font-size: 10pt; line-height: 1.2; color: rgb(0, 0, 255);">������ �������</b><span style="font-size: 10pt; line-height: 1.2;"> </span></p>

<p style="font-size: 10pt; line-height: 1.2;"> �� �������� ������������ � ����������������� ������������ ������� ���������� �� ���.<b>+7-921-418-15-84</b></p>
 
<div class="gray_line_small"> </div>
 <?php
/*
 
<p style="font-size: 10pt; line-height: 1.2; "><strong style="color: rgb(0, 0, 255); ">&laquo;������ ������&raquo;:</strong>
  <br />
 
  <br />
1-10 �������: - &laquo;������&raquo; -15%, &laquo;Lemon&raquo; -10% 
  <br />
11-20 �������: - &laquo;�������&raquo; -15%, &laquo;Mango&raquo; -10% 
  <br />
21-30 �������: - ������� -15%, �Orange� -10% 
  <br />

  <br />
������ ��������������� �� ����� � ����� ���� 4 ���������, � ���� ���� 7 ���������. 
  <br />
��� ������������ ������ � ��������� ������ ������ �� ����������������.</p>
 
<div class="gray_line_small"></div>

 
<table class="s" width="100%" cellspacing="1" cellpadding="1" border="0" align="Left"> 
  <tbody> 
    <tr><td style="vertical-align: top;"><img id="bxid_56899" width="230" src="/upload/medialibrary/4a7/banner_230x115_spb.png"  /></td> 	<td width="60px">&nbsp;</td> 	<td> 		 
        <p style="font-size: 12px; line-height: 1.5; "><span style="color: rgb(102, 102, 102); font-weight: 700; ">����������� ����� 13�</span> 
          <br />
         </p>
      
        <p style="line-height: 1.5; "><span style="font-size: 12px; ">� ����� ����������� <b>20<font color="#ff0000">13</font></b> ���� � 1 �� <b><font color="#ff0000">13</font></b> ������ ������</span><font size="4"> <b><font color="#ff0000"> 13%</font></b></font><span style="font-size: 12px; "> �� ���� ��������� ���! </span>
          <br />
        <span style="font-size: 12px; "> 		������ ��������������� �� ����� � ����� ���� 4 ���������, � ���� ���� 7 ���������.</span></p>
       		 	</td> </tr>
   </tbody>
 </table>
 
<div style="clear: both; ">&nbsp;</div>

<div class="gray_line_small"></div>
<img id="bxid_768979" src="/upload/medialibrary/a0c/all-15-percent.png" height="115" width="230" style="border:none;"  />
<br /> 		 
<p style="font-size: 12px; line-height: 1.5; "><span style="color: rgb(102, 102, 102); font-weight: 700; ">� 27 �� 30 ������ �������� ����� ������� 15%�</span></p> 
<br />
<p style="font-size: 12px; line-height: 1.5; ">�� ���� ��������� ��� ��������������� ������ - <span style="color: rgb(102, 102, 102); font-weight: 700; "> 15%.</span></p>

<div class="gray_line_small"></div>
*/
?> 
<p style="font-size: 10pt; line-height: 1.2;">
  <br />
</p>
 
<p>&nbsp;</p>
 
<br />
 
<br />
 
<br />
 
<br />
 
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>