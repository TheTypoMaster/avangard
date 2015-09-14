<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мебельные аксессуары.");
$APPLICATION->SetTitle("");
?> 

<table cellspacing="0" cellpadding="0" style="text-align: justify;"> 
  <tbody> 
    <tr><td> 
        <p><?$APPLICATION->IncludeComponent("anp:catalog.section", "seo_accessories", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => IntVal(5),
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrFilter",
	"PAGE_ELEMENT_COUNT" => "30",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
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
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	),
	false
);?> </p>
       </td></tr>
   
    <tr><td> 
        <p></p>
       </td></tr>
   </tbody>
 </table>
 
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