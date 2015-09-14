<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мягкая мебель: акции, скидки, уценка. Угловые цветные диваны и кресла распродажа");
$APPLICATION->SetTitle("Мягкая мебель: акции, скидки, уценка. Угловые цветные диваны и кресла распродажа");
?> 
<div class="gray_td"> 
  <h1>Авангард детям</h1>
 </div>
 
<div><p><br /></p></div>
<table cellpadding="0" cellspacing="0" border="0" class="data-table">
<tr><td>
<?
  $res = CIBlockSection::GetList(
     Array("LEFT_MARGIN"=>"ASC"), 
     Array("IBLOCK_ID"=>33, "ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
     true,
     Array("ID", "NAME", "PICTURE")
  );
  while($arSection = $res->GetNext())
  {
	  if ($arSection["PICTURE"]) {
$big_picture = CFile::GetPath($arSection["PICTURE"]);
		  echo '<div style="float:left; overflow:hidden; width:230px; height:240px; margin:20px 20px 10px 0; text-align:center;"><span style="font-size:14px; font-weight:600;">'.$arSection['NAME'].'</span><br /><a href="/8days/for_children/index.php?id='.$arSection['ID'].'"><img style="margin-top:10px;" src="'.$big_picture.'"></a></div>';
	  }

  };
?>
<div style="clear: left"></div>
</td></tr>
</table>
<p><br /></p>
<p><br /></p>
<div> 
<br />
<br />
<br /><br /><FONT SIZE="4"><font color="#4169E1"><p style="font-size: 10pt; line-height: 1.5; "><b>Скидка 25% только в салонах</font></FONT>   
	<br /> <a href="/redesign/where_buy/detail.php?id=329">"на ш.Энтузиастов",&nbsp;&nbsp;</a> <a href="/redesign/where_buy/detail.php?id=327">"на Кронштадском бульваре",&nbsp;&nbsp;</a> <a href="/redesign/where_buy/detail.php?id=6343">"МЦ ROOMER". </b></a></a>
<br />
<br />
  <p><b>СЕНСАЦИЯ!
<br /><br />Только здесь и сейчас вы сможете заказать великолепные предметы интерьера для детской (фотошторы, фотопокрывало и фотоподушки) с сюжетами из фильмов Disney.
<br /><br />Вместе с Микки Маусом, Белоснежкой, Русалочной и Вини Пухом выросло не одно поколение.
<br /><br />Ваш малыш будет в восторге, что герои его любимых мультфильмов  теперь будут «жить» с ним в одной комнате, создавая настроение настоящего волшебства.
<br /><br />Наши фотоаксессуары  станут источником положительных эмоций для всех членов семьи, создадут атмосферу тепла и уюта.  Детская комната превратится в необыкновенный  уголок в вашем доме, где  ребенок сможет почувствовать себя сказочным персонажем.
<br /><br />Фотоаксессуары – это большой выбор сюжетов из различных мультфильмов для детей разного возраста
	  <br />Добро пожаловать в необыкновенный мир Disney!</b></p>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>