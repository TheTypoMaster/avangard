<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("В Новый Год с Новым Диваном!");
?> 
<p></p>
<? /*
$result = Array();
$arOrder = Array("IBLOCK_SECTION_ID"=>"ASC");
$arSelect = Array("ID","NAME","IBLOCK_SECTION_ID"); 
$arFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "SECTION_GLOBAL­_ACTIVE" => "Y", "SECTION_ID"=>5,"INCLUDE_SUBSEC­TIONS" => "Y", ); 
$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement())
{
 $arFields = $ob->GetFields();
$res1 = CIBlockSection::GetByID($arFields["IBLOCK_SECTION_ID"]);
if($ar_res = $res1->GetNext())
echo $ar_res['NAME'];
 $result[$arFields[IBLOCK_SECTION_ID]] = $ar_res['NAME']; 
	echo '<pre>';
	print_r($arFields);
	echo '</pre>';
}

 echo '<pre>';
 print_r($result);
echo '</pre>'; */
?>
<?
//$page = $APPLICATION->GetCurPage();
//echo $page;
?>
<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", ".default", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => IntVal(5),
	"SECTION_ID" => "5",
	"SECTION_CODE" => "",
	"COUNT_ELEMENTS" => "Y",
	"TOP_DEPTH" => "3",
	"SECTION_FIELDS" => array(
		0 => "ID",
		1 => "CODE",
		2 => "XML_ID",
		3 => "NAME",
		4 => "DESCRIPTION",
		5 => "PICTURE",
		6 => "DETAIL_PICTURE",
		7 => "",
	),
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "/accessories/?SECTION_ID=#SECTION_ID#",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y"
	),
	false
);?>

 
<p><span style="font-family: Arial, Helvetica, sans-serif;"> 
    <br />
   </span></p>
 
<p><span style="font-family: Arial, Helvetica, sans-serif;"> 
    <br />
   </span></p>
 
<p><span style="font-family: Arial, Helvetica, sans-serif;">Дорогие друзья!</span></p>
 
<p></p>
 
<p></p>
 
<p><span style="font-family: Arial, Helvetica, sans-serif;">Только с 22 ноября по 31 декабря </span><b style="font-family: Arial, Helvetica, sans-serif;">срок изготовления</b><span style="font-family: Arial, Helvetica, sans-serif;"> дивана - </span><b style="font-family: Arial, Helvetica, sans-serif;">7 дней!</b><span style="font-family: Arial, Helvetica, sans-serif;">. </span></p>
 
<p> Акция действует для <b>следующих моделей</b>: 
  <table class="s" height="300" cellspacing="1" cellpadding="1" width="100%" align="left" border="0"> 
    <tbody> 
      <tr><td><img height="125" hspace="0" src="/4newyear/arizona_125x250.jpg" width="250" align="top" border="0"  /></td><td><img height="125" hspace="0" src="/4newyear/kentukki_125x250.jpg" width="250" align="top" border="0"  /></td></tr>
     
      <tr><td><a href="http://www.avangard.biz/catalogue/1/tov_160.html" >Аризона</a></td><td><a href="http://www.avangard.biz/catalogue/1/tov_162.html" >Кентукки</a></td></tr>
     
      <tr><td></td><td></td></tr>
     
      <tr><td><img height="125" hspace="0" src="/4newyear/uta_125x250.jpg" width="250" align="top" border="0"  /></td><td><img height="125" hspace="0" src="/4newyear/illinois_125x250.jpg" width="250" align="top" border="0"  /></td></tr>
     
      <tr><td><a href="http://www.avangard.biz/catalogue/1/tov_163.html" >Юта</a></td><td><a href="http://www.avangard.biz/catalogue/1/tov_165.html" >Иллинойс</a></td></tr>
     
      <tr><td></td><td></td></tr>
     </tbody>
   </table>
 </p>
 
<br clear="left" />
 
<p></p>
 
<p>Вариант исполнения - № <b>1</b>, цвет декора - <b>орех</b>. 
  <br />
 <b>Образцы тканей</b>: 
  <table class="s" height="500" cellspacing="1" cellpadding="1" width="100%" align="left" border="0"> 
    <tbody> 
      <tr><td><a href="/4newyear/tkani/Denvil_khaki_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Denvil_khaki_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Denvil_terracot_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Denvil_terracot_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Hawai03_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Hawai03_sm.jpg" width="156" align="top" border="0"  /></a></td></tr>
     
      <tr><td>Denvil khaki</td><td>Denvil terracot</td><td>Hawai 03</td></tr>
     
      <tr><td></td><td></td><td></td></tr>
     
      <tr><td><a href="/4newyear/tkani/Jazz0762t_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Jazz0762t_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Jazz3812_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Jazz3812_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Lola027501_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Lola027501_sm.jpg" width="156" align="top" border="0"  /></a></td></tr>
     
      <tr><td>Jazz 0762-t</td><td>Jazz 3812</td><td>Lola 027501</td></tr>
     
      <tr><td></td><td></td><td></td></tr>
     
      <tr><td><a href="/4newyear/tkani/Tiffy2_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Tiffy2_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Tiffy5_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Tiffy5_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Tulsa29_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Tulsa29_sm.jpg" width="156" align="top" border="0"  /></a></td></tr>
     
      <tr><td>Tiffy-2</td><td>Tiffy-5</td><td>Tulsa 29</td></tr>
     
      <tr><td></td><td></td><td></td></tr>
     
      <tr><td><a href="/4newyear/tkani/Tulsa_41_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Tulsa41_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Monica01_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Monica01_sm.jpg" width="156" align="top" border="0"  /></a></td><td><a href="/4newyear/tkani/Monica06_big.jpg" target="_blank" ><img height="94" hspace="0" src="/4newyear/Monica06_sm.jpg" width="156" align="top" border="0"  /></a></td></tr>
     
      <tr><td>Tulsa 41</td><td>Monica 01</td><td>Monica 06</td></tr>
     
      <tr><td></td><td></td><td></td></tr>
     </tbody>
   </table>
 </p>
 
<br clear="left" />
 
<p></p>
 
<p>Более подробную информацию об акции Вы можете узнать в салонах. Наши специалисты будут рады ответить на Ваши вопросы.</p>
 
<p>Приходите, мы будем рады встрече с Вами!</p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>