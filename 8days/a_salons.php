<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������ ����������� � �����");
?> 
<script type="text/javascript">

function getHint(e, cityUrl, positY, ident) {
    if (!$('#hint').is(':hidden'))
    {
        closeHint();
    }

	$.ajax({
		type: "POST",
		url: "/redesign/where_buy/detail.php",
		data: "id="+ident,
		success: function(msg){
			$("#hint_content").empty().append(msg);
			var smesh = self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
               $('#hint').css('top' , smesh-400);
               $('#hint').css('left',  '17%');
	       $('#hint').show('slow');
		}
			
	});
	  
}


function opWind(myUrl) {
myWin=window.open(myUrl, "wind1", "width=816,height=616,resizable=no,scrollbars=no,menubar=no");
}

function getHintHeight() {
    return $("#hint").height();
}
function closeHint() {
    $('#hint').hide('slow');
}

</script>
 
 
<div id="hint">
 
  <table style="padding-right: 6px; padding-left: 6px; padding-bottom: 6px; padding-top: 0px" width="100%">
 
    <tbody>
 
      <tr><td align="left"></td><td align="right">
 
          <div style="border-right-color: #000000; border-right-width: 0px; border-right-style: none; padding-right: 2px; border-top-color: #000000; border-top-width: 0px; border-top-style: none; padding-left: 2px; float: right; padding-bottom: 2px; border-left-color: #000000; border-left-width: 0px; border-left-style: none; cursor: pointer; padding-top: 2px; border-bottom-color: #000000; border-bottom-width: 0px; border-bottom-style: none; height: 18px"><a class="control" onclick="closeHint();return false;" href="#" >�������</a> </div>
         
 </td><td width="10"></td></tr>
     
 
 
      <tr><td colspan="3"><span id="hint_content"></span></td></tr>
     
 </tbody>
   
 </table>
 
 </div>
 
 
 
<div class="gray_td">
 
  <h1>����� �������� � �������:</h1>
 
 </div>
 
 
 
<table class="s" style="font-size: 11px; background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #e8e8e8; background-image: none" cellspacing="1" cellpadding="4" width="100%" border="0">
 
  <tbody>
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #e8e8e8; background-image: none; width: 70%"><b>�������� ������</b></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #e8e8e8; background-image: none; width: 30%"><b>�������</b></td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=328', this.offsetHeight, 328); return false;" href="#" target="_new" >��������� ����� � �. �������</a> <strong><font color="#ff0000">������ + �������</font></strong></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 516-40-02</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=326', this.offsetHeight, 326); return false;" href="#" target="_new" >��������� ����� �� ������������� ���������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 781-05-24</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=327', this.offsetHeight, 327 ); return false;" href="#" target="_new" >��������� ����� �� ������������� ��������</a>� <strong><font color="#ff0000">����������., ������ �� 50%</font></strong></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 221-28-05</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=329', this.offsetHeight, 329 ); return false;" href="#" target="_new" >��������� ����� �� ����� �����������</a> <strong><font color="#ff0000">������ + �������</font></strong></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 784-65-46</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=325', this.offsetHeight, 325 ); return false;" href="#" target="_new" >��������� ����� ��� ������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 668-15-11</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=330', this.offsetHeight, 330 ); return false;" href="#" target="_new" >��������� ����� �� ����������� �����</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(498) 720-50-44</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=331', this.offsetHeight, 331 ); return false;" href="#" target="_new" >����� � �. ������, �� &quot;������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 585-00-20</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=356', this.offsetHeight, 356 ); return false;" href="#" target="_new" >�� &quot;��� ����&quot;, ������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 723-82-82 ���. 2477</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=338', this.offsetHeight, 338 ); return false;" href="#" target="_new" >�� &quot;�����&quot;, ������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 723-80-01 ���. 4264</td></tr>
   
 
 
    <tr><td style="background-color: #f8f8f8"><a href="/redesign/where_buy/detail.php?id=358" target="_blank" >�� &quot;������ ������&quot;</a>, ������, ��.�. &quot;����&quot;</td><td style="background-color: #f8f8f8">(499) 188-68-83</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=332', this.offsetHeight, 332 ); return false;" href="#" target="_new" >�� &quot;�����&quot;, �. ��������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 505-68-92</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=334', this.offsetHeight, 334 ); return false;" href="#" target="_new" >������� ������ &quot;��������&quot;, ������, ��.�. &quot;�������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 359-80-00, 
 
        <br />
       
 359-80-01</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=336', this.offsetHeight, 336 ); return false;" href="#" target="_new" >����������, ������, ��.�. &quot;�������������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 916-61-67, 
 
        <br />
       
 393-48-20</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=344', this.offsetHeight, 344 ); return false;" href="#" target="_new" >����� &quot;��� ������&quot;, ������, ��.�. &quot;�������������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 787-26-92</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=354', this.offsetHeight, 354 ); return false;" href="#" target="_new" >���� ������� &quot;������ ���&quot;, ������, ��.�. &quot;������ ����&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 739-95-85</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=355', this.offsetHeight, 355 ); return false;" href="#" target="_new" >���� ������� &quot;������ ���&quot;, ������, ��.�. &quot;���������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 751-38-15</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=353', this.offsetHeight, 353 ); return false;" href="#" target="_new" >���� ������� &quot;������ ���&quot;, ������, ��.�. &quot;������������&quot;, &quot;���������� &quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">
 
        <p>(495) 545-58-69, 
 
          <br />
         
 165-56-11, 
 
          <br />
         
 (926) 226-58-72</p>
       
 </td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=349', this.offsetHeight, 349 ); return false;" href="#" target="_new" >���� ������� &quot;������ ���&quot;, ������, ��.�. &quot;������������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 995-13-12</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=347', this.offsetHeight, 347 ); return false;" href="#" target="_new" >���� ������� &quot;������ ���&quot;, ������, ��.�. &quot;�������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 346-56-11, 
 
        <br />
       
 346-53-01</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=352', this.offsetHeight, 352 ); return false;" href="#" target="_new" >���� ������� &quot;������ ���&quot;, ������, ��.�. &quot;����� ������������&quot;, &quot;������� �������� �������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(499) 723-53-90, 
 
        <br />
       
 723-52-81</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=333', this.offsetHeight, 333 ); return false;" href="#" target="_new" >�� &quot;���������&quot;, ������, ��.�. &quot;���-��������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 984-29-18</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=489', this.offsetHeight, 489 ); return false;" href="#" target="_new" >�� &quot;������ ������&quot;, ������, ��.�. &quot;����������������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 962-14-68, 8-926-323-01-97</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=479', this.offsetHeight, 479 ); return false;" href="#" target="_new" >��� &quot;�����-�����&quot;, ������, ��.�. &quot;������ ����&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">8 (926) 84-77-221</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><font color="#000000">������ ������ &quot;����&quot;, �.����������,</font><font color="#808080"> 
 
          <br />
         
 </font><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=483', this.offsetHeight, 483 ); return false;" href="#" target="_new" >������ 1650</a>, <a onclick="getHint(event,'/redesign/where_buy/detail.php?id=365', this.offsetHeight, 365 ); return false;" href="#" target="_new" >������ 1824, </a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(499) 729-94-40, 
 
        <br />
       
 717-29-88</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=342', this.offsetHeight, 342 ); return false;" href="#" target="_new" >&quot;����� ������&quot;, ������, ��.�. &quot;������������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 665-88-22, 
 
        <br />
       
 665-88-33</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a href="/wharetobuy/moscow/salon_511.html" >&quot;����� ������&quot;, �. ����������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">8-903-799-65-30</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=341', this.offsetHeight, 341 ); return false;" href="#" target="_new" >&quot;����� ������&quot;, �. ��������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(8496) 758-32-04, 
 
        <br />
       
 758-32-05</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=339', this.offsetHeight, 339 ); return false;" href="#" target="_new" >�� &quot;�������-������&quot;, �. �������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 777-41-71</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=362', this.offsetHeight, 362 ); return false;" href="#" target="_new" >�� &quot;����������� ���&quot;, ������, ��.�. &quot;��������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 389-71-33</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=359', this.offsetHeight, 359 ); return false;" href="#" target="_new" >�� &quot;������� �������&quot;, ������, ��.�. &quot;��������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 223-41-98</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=337', this.offsetHeight, 337 ); return false;" href="#" target="_new" >&quot;������ ������&quot;, ������, ��.�. &quot;���������-�����������&quot;, &quot;���������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 483-37-83</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=335', this.offsetHeight, 335 ); return false;" href="#" target="_new" >&quot;����� ����&quot;, ������, ��.�. &quot;����������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 475-57-00</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=484', this.offsetHeight, 484 ); return false;" href="#" target="_new" >�� &quot;����������&quot;, ������, ��.�. &quot;����� ���������&quot;, &quot;���������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"></td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=488', this.offsetHeight, 488 ); return false;" href="#" target="_new" >�� &quot;��������&quot;, ������, ��.�. &quot;����� ���������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 288-75-23, 23-22-999</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=489', this.offsetHeight, 489 ); return false;" href="#" target="_new" >��� &quot;������ ������&quot;, ������, ��.�. &quot;����������������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 962-14-68, 8-926-323-01-97</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=490', this.offsetHeight, 490 ); return false;" href="#" target="_new" >�� &quot;������&quot;, ������, ��.�. &quot;���������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 926-14-68</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=357', this.offsetHeight, 357 ); return false;" href="#" target="_new" >�� &quot;�������&quot;, ������, ��.�. &quot;����������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 956-90-49, 235-44-17, 235-48-17 ���. 218</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=503', this.offsetHeight, 503 ); return false;" href="#" target="_new" >��� &quot;���&quot;, ������, ��.�. &quot;���������&quot;</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 988-51-66, 988-51-67</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=468', this.offsetHeight, 468 ); return false;" href="#" target="_new" >����������� &quot;�������� �����&quot;, �. �����������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 504-84-81</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=467', this.offsetHeight, 467 ); return false;" href="#" target="_new" >�� &quot;���� ����&quot;, �. ��������</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 542-00-81, 542-00-82</td></tr>
   
 
 
    <tr><td style="background-color: #f8f8f8"><a href="/redesign/where_buy/detail.php?id=491" target="_blank" >&quot;�������� �����&quot;</a>, �. ��������, ��.�. &quot;����� �����������&quot;, &quot;�����������&quot;</td><td style="background-color: #f8f8f8">8-926-601-68-82</td></tr>
   
 
 
    <tr><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none"><a onclick="getHint(event,'/redesign/where_buy/detail.php?id=492', this.offsetHeight, 492 ); return false;" href="#" target="_new" >�� &quot;�����&quot;, �. �����</a></td><td style="background-attachment: scroll; background-repeat: repeat; background-position: 0% 0%; background-color: #f8f8f8; background-image: none">(495) 940-27-16</td></tr>
   
 </tbody>
 
 </table>
 
 
 
<p>� ������� �� ������ ������ ����� ��������� ���������� �� �����. ���� ����������� ����� ���� �������� �� ���� �������.</p>
 
 
 
<p>���������, �� ����� ���� ������� � ����!</p>
 
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>