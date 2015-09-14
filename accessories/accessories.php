<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мебельные аксессуары.");
$APPLICATION->SetTitle("");
?> 
<? /*
$APPLICATION->IncludeComponent("bitrix:catalog.section.list", ".default", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => IntVal(5),
	"SECTION_ID" => IntVal(5),
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
);
*/ ?>
<div class="gray_td">
	<h1>Аксессуары</h1>
</div>
<?
  $res = CIBlockSection::GetList(
     Array("LEFT_MARGIN"=>"ASC"), 
     Array("IBLOCK_ID"=>5, "SECTION_ID"=>5,"ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
     true,
     Array("ID", "NAME", "PICTURE")
  );
  
  while($arSection = $res->GetNext())
  {
	  if ($arSection["PICTURE"]) {
$big_picture = CFile::GetPath($arSection["PICTURE"]);
		  echo '<div style="float:left; overflow:hidden; width:170px; height:190px; margin:17px 17px 10px 0; text-align:center;"><a href="/accessories/?SECTION_ID='.$arSection['ID'].'"><img style="width:170px; margin-bottom:5px;" src="'.$big_picture.'"></a><br /><span style="font-size:11px; font-weight:600;">'.$arSection['NAME'].'</span></div>';
	  }

  }
?>
<div style="clear: left"></div>
<br />
<br /> 
<div style="text-align: justify;"> 
  <p st yle="font-family: Arial, Tahoma, Verdana, sans-serif; font-size: 12.727272033691406px; text-align: start; background-color: rgb(255, 255, 255); text-indent: 35.4pt;"> 
    <br />
   
    <br />
   Сколько бы не было разговоров про интерьер и стилистику дома, главную роль  уютного жилья играют аксессуары. Именно мелкие предметы декора привносят в дом ощущение тепла и комфорта, первозданность тишины и покоя. Журнальные столики и столики-подставки с упругим валиком для ног, пуфики и кушетки, подушечки и пледы &ndash; все эти мелочи, просто обязаны присутствовать в гостиной современного человека. Мебельная фабрика &laquo;Авангард&raquo; выпускает огромный ассортимент таких элементов декора для композиционного решения стилистики гостиной.</p>
 
  <br />
 Если говорить о мягких «родственниках» диванов и кресел, то они прекрасно дополняют и приукрашивают комнату. Многочисленные диванные подушечки, уютные пледы, призваны быть не только украшением и обычным предметом обихода, но и играть роль удобного и лечебного спутника кратковременного отдыха. 
  <p></p>
 
  <br />
 Классическую гостиную можно с легкостью сделать более выразительной и яркой с помощью большого количества диванных подушечек. Все подушки имеют удобные чехлы, но не стоит забывать, что «наряды» диванных аксессуаров должны быть выдержаны и объединены одной темой. Это может быть какой-либо контрастный оттенок, либо характерный орнамент дорогой парчи, или иметь вычурный элемент отделки (витой шнур, кисти, кант, вышивка). 
  <p></p>
 
  <br />
 Пледы могут быть с этническим или цветочным мотивом, здесь тоже стоит обратить внимание на общий вид гостиной, чтобы пушистый элемент не «выпадал» из стиля интерьера. Не выбирайте пледы с расцветкой, имитирующей окрас животных, - такие мотивы утомляют зрение. 
  <p></p>
 
  <br />
 Если же взять во внимание журнальные столики, то они, несомненно, должны быть выполнены только из натуральных пород дерева, желательно благородного оттенка, с явно выраженной древесной фактурой (дуб, ясень, дорогое красное дерево). 
  <p></p>
 
  <br />
 Мягкие пуфики могут быть квадратной формы, либо любой другой. Каркас же наоборот имеет жесткую деревянную основу, что наиболее выгодно подчеркивает, что данная модель может называться классической мебелью. Но именно классический вид пуфа, имеет более шикарный  вид, за счет богатой отделки восточным атласом. 
  <p></p>
 </div>
 
<div style="text-align: justify;"> 
  <br />
 </div>
 
<div style="text-align: justify;"> 
  <br />
 </div>
 
<div style="text-align: justify;"> 
  <br />
 </div>
 
<div style="text-align: justify;"> 
  <br />
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>