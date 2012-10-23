<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Фирменные салоны и подиумы");
?> 
<div class="gray_td"> 
  <h1>Фирменные салоны и подиумы</h1>
 </div>
  
<div class="new"> <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"wharetobuy",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_URL" => "/wharetobuy/#SECTION_CODE#/",
		"COUNT_ELEMENTS" => "N",
		"DISPLAY_PANEL" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	)
);?> </div>
    
<p> Дорогие покупатели, 
  <br />
  В наших салонах Вы можете узнать все, что Вам интересно о диванах, креслах, пуфах, кушетках. 
  <br />
  Наши продавцы помогут подобрать именно ту модель дивана, о которой Вы мечтали всю жизнь. Вам расскажут о том, из чего состоит диван, какой внутри механизм трансформации, что делает диванные подушки такими мягкими и комфортными, какое дерево использовалось при отделке и многое другое... 
  <br />
  Мы расскажем Вам все о диване, раскроем все секреты, чтобы после покупки Вы не столкнулись с массой &quot;приятных&quot; сюрпризов. 
  <br />
  Для того чтобы Вам было удобно купить диван или кресло, мы открыли свои мебельные салоны практически во всех крупных мебельных центрах <a href="/wharetobuy/moscow/" >Москвы</a> и <a href="/wharetobuy/russia/" >России</a>. Просто выберите ближайший и вперед, за диваном своей мечты!</p>
    <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>