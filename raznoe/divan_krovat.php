<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Диван-кровать со спальным местом. Купить недорогую раскладную мягкую мебель для сна. Угловой диван спальный.");
$APPLICATION->SetTitle(" Диван-кровать со спальным местом. Купить недорогую раскладную мягкую мебель для сна. Угловой диван спальный.");
?><? $_POST['design_tmp']='seo';?> <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Диван-кровати. Недорогая угловая мягкая мебель с выкатным механизмом раскладки");
$APPLICATION->SetTitle("Title");
?> 
<h2>Диван-кровать</h2>
 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"template_statii_det",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "17",
		"ELEMENT_ID" => "1510",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"PROPERTY_CODE" => array(0=>"",1=>"models",2=>"salons",3=>"",),
		"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
		"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	)
);?></p>
 
<p>    Каждый ли россиянин может позволить себе такую роскошь, как - отдельный диван для отдыха и кровать для сна? Скорее всего нет. Великолепной альтернативой всему этому послужит раскладной диван-кровать. Только он сможет сыграть одновременно роль спального ложа и комфортного места для отдыха.  </p>
 
<p>
  <br />
</p>

<p>    Мягкий диван-кровать идеально впишется в любой интерьер, сэкономив при этом значительно места. Также он может выступить в качестве дополнительного отсека для хранения постельного белья, подушек или одеял.</p>
 
<p>
  <br />
</p>

<p>    Мебельная фабрика &quot;Авангард&quot; выпускает большой ассортимент диванов-кроватей, удовлетворяя при этом любой потребительский вкус. В нашем каталоге представлены диваны для сна любой конфигурации и с различными механизмами трансформации. </p>
 
<p>
  <br />
</p>

<p>    Понравившуюся модель вы можете приобрести не только в интернет-магазине, но и в реальном.</p>
 
<div> 
  <br />
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>