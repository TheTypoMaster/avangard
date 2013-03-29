<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доставка мебели");
?><div class="gray_td"  style="width: 720px;"><h1>Доставка мебели</h1></div>

<script language="javascript"><!--
function viewfoto(width, height, src) {		
		var height1 = height;
		if (height > 700) {
			height1 = 700;
			scrol = 1;
		} else scrol = 0;
		id=window.open('','viewphoto','scrollbars='+scrol+', width='+width+', height='+height1+'');	
		id.focus();
		id.document.open();
		id.document.write('<html><head><title>ViewImage</title></head><body leftMargin="0" topMargin="0" onclick="window.close();">');
		id.document.write('<div align="center" valign="middle"><img src="'+src+'" width="'+width+'" height="'+height+'" border="0" /></div>');	
		id.document.write('</body></html>');
		id.document.close();
	}
//--></script>

<!-- <h3>Служба логистики</h3> -->

<table class="s" cellspacing="0" cellpadding="0" width="100%" border="0">
  <tbody>
    <tr valign="top"><td width="170" height="100"> <a href="javascript:viewfoto(600,400,'/about/soft_furniture/images/sklad_01.jpg')">
<img height="100" src="/about/soft_furniture/images/sklad.jpg" width="150" alt="Склад готовой продукции"></a></td><td rowspan="7">
        <p>Склад готовой продукции, склад товарно-материальных ценностей расположены на территории ДСК-160 г.Королёв. 
          <br />
        Складские помещения отвечают всем требованиям, предъявляемым к складам, как с точки зрения соблюдения условий хранения, так и с точки зрения логистики. 
          <br />
        Каждый склад имеет удобный подъезд для грузового транспорта и погрузочно-разгрузочной техники. 
          <br />
        Также служба логистики включает в себя собственный обновленный автопарк. 
          <br />
        Силами нашей компании перевозка мебели всех видов и на любые расстояния осуществляется быстро, качественно и надежно. Для этого в нашем распоряжении есть широкий выбор транспортных средств, инструменты и большой штат высококвалифицированных сотрудников. 
          <br /></p>
      </td></tr>
  
    <tr><td width="170" height="30"> </td></tr>
  
    <tr><td width="170" height="100"> <a href="javascript:viewfoto(600,400,'/about/soft_furniture/images/avto_03.jpg')">
<img height="100" src="/about/soft_furniture/images/avto_03.jpg" width="150" alt="Доставка мебели"></a></td></tr>
  
    <tr><td width="170" height="30"> </td></tr>
  
    <tr><td width="170" height="100"> <a href="javascript:viewfoto(600,400,'/about/soft_furniture/images/avto_01.jpg')">
<img height="100" src="/about/soft_furniture/images/avto_01.jpg" width="150" alt="Доставка мебели"></a></td></tr>  
      
    <tr><td width="170" height="30"> </td></tr>
  
    <tr><td width="170" height="100"> <a href="javascript:viewfoto(600,400,'/about/soft_furniture/images/avto_02.jpg')">
<img height="100" src="/about/soft_furniture/images/avto_02.jpg" width="150" alt="Доставка мебели"></a></td></tr>
  </tbody>
</table>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>