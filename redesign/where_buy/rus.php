<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "��� ������ �����? ��� ������ ������ ������? ������ ��������� ������� � ������� �� ���� ������. ��������-������� � ��������� �� ������.");
$APPLICATION->SetTitle("��� ������ �����? ��� ������ ������ ������? ������ ��������� ������� � ������� �� ���� ������. ��������-������� � ��������� �� ������.");
?> 
<div style="text-align: justify; "> 
<script type="text/javascript">


function getHint(e, cityUrl, positY, ident) {
    if (!$('#hint').is(':hidden'))
    {
        closeHint();
    }
	$.ajax({
		type: "POST",
		url: "/redesign/where_buy/detail.php",
		data: "city=y" + ident,
		success: function(msg){
			$("#hint_content").empty().append(msg);
			var smesh = self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
            $('#hint').css('top' , smesh-400);
            $('#hint').css('left',  '17%');
            $('#hint').show('slow');
		}
			
	});
	  
}

function getHintHeight() {
    return $("#hint").height();
}
function closeHint() {
    $('#hint').hide('slow');
}
</script>
 
<style>
#hint {
    display: none;
    padding: 10px;
    width: 720px;
    border: 1px solid gray;
    background-color: #fff;
    position: absolute;
    z-index: 255;
  }
</style>
 </div>
 
<div width="720" class="gray_td_left"> 
  <h1 style="text-align: justify; ">��� ������ �����?</h1>
 </div>
 
<div style="text-align: justify; "><?$russia = $_GET[russia];
  $moscow = $_GET[moscow]; 


$kol=0;

if($russia) $sect = 7;
else $sect = 6;
 ?></div>
 
<div style="position: relative; top: -13px; width: 100%; height: 40px; border: 1px solid rgb(225, 225, 225); "> 
	<table class="zakladki" width="100%" cellpadding="0" cellspacing="0" height="40" style="text-align: justify; "> 
		<tbody> 
			<tr>
				<td style="vertical-align: middle; text-align: center; width: 33%; "> <b><font style="font-size: 14px; ">������</font></b> 
					<br />
					<a href="/redesign/where_buy/map.php?id=7" target="_new" >���������� �� �����</a> 
				</td>
				<td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 33%; ">
					&nbsp;
				</td> 
				<td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 34%; ">
					&nbsp;
				</td> 
			</tr>
		</tbody>
	</table>
</div>
 
<div id="hint"> <span id="hint_content"></span> 
  <div style="border-width: 0px; height: 18px; padding: 2px; cursor: pointer; float: right; "> <a href="#" onclick="closeHint();return false;" class="control" >�������</a> </div>
 </div>
 	 
<div class="cityname" style="text-align: justify; margin-bottom: 5px; "> <img src="/wharetobuy/maps/podium.gif" style="border: 0px none ;" width="25" border="0" height="20"  /> 		 ��������� �������</div>
 	 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "><?$arrFilterType = Array( "PROPERTY_SALON_TYPE_2_VALUE" => "��������� ������");?> <?$APPLICATION->IncludeComponent(
	"anp:catalog.section",
	"spisok_russia1",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $sect,
		"ELEMENT_SORT_FIELD" => "PROPERTY_SALON_TYPE",
		"ELEMENT_SORT_ORDER" => "desc",
		"FILTER_NAME" => "arrFilterType",
		"PAGE_ELEMENT_COUNT" => "300",
		"LINE_ELEMENT_COUNT" => "3",
		"PROPERTY_CODE" => array(0=>"SALON_ADRESS",1=>"SALON_CITY",2=>"SALON_TYPE_2",3=>"SALON_PHONE",4=>"SALON_METRO",5=>"SALON_TIME",6=>"",),
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
);?> </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; ">������� ������ ������ ��� &laquo;��������&raquo; - ������� ���������������-�������� �����������, ����������� ������ ������ ���������� �����������, ��������� ������������� ������ ��������������� ����������. � 2000-�� ���� ������� �������� ���� �� ������� ���� �� ���������� ����� �� ������������ ������ ������. �� ������� ������� ������, ��������� �� �������, ��������� �������� ������� ������� � �������� ������ � ��������� ����. ���������� ������������ ������ ������ ��������� ���������������. ��� ������ ����� �������� �������� ����� ��� �� 18 �������. ��� ������ ������ ��������� ���������������, �������� ������������ ����������, ������� � ������ ������ ���������� ����������. ������ ��������� �� ������ �������� ������� � � ������� �������.</div>
 
<div style="text-align: justify; "> 
  <br />
 </div>
 
<div style="text-align: justify; ">��� �� ������ ������ ������ ���������? �������� ������������ ���� ������������ ���������� ���������� �������� � ��������� ������� � ����������� ������� �������� ��������� ������� � ������� ������ � ������� ��� &ndash; ����� 100 ��������� ����������������. ����� ���� ���� ������ ������ �������� � ��������-�������� � ��������� �� ������ ������. �� ����������� ������ ���������� �������� �������, ������ ��� ���� ������ ������ � ��� ������ �� �������������. ������� ������������ ������������, ������ ������� ��������� ��� ��� ����������� ���������� ������������ �������� ��� ��� ��������. ����� �� ������ ������� �� ��������� ��������� ������������ ������� � �� ������ ����� ������� �� ��� ����.</div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>