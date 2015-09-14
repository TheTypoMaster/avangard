<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Распродажа мягкой мебели. Угловой диван и кресла кожа купить в магазине недорого");
$APPLICATION->SetTitle("Распродажа мягкой мебели. Угловой диван и кресла кожа купить в магазине недорого");
?><?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> <?if($city) { 
echo "<div align=left>";
foreach($id as $ident) {
?> 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"salon_detail_russia",
	Array(
		"IBLOCK_TYPE" => "shops",
		"IBLOCK_ID" => "8",
		"ELEMENT_ID" => $ident,
		"SECTION_ID" => "",
		"PROPERTY_CODE" => array(0=>"GOOGLE_MAP",1=>"SALON_TYPE_2",2=>"SALON_PHONE",3=>"SALON_METRO",4=>"SALON_ROUTE",5=>"SALON_TIME",6=>"SALON_ACTION",7=>"",),
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
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
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
 <?
}
echo "</div>";
} else {
	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_SALON_REGION");
    $arFilter = Array("IBLOCK_ID"=>IntVal(8), "ID" => $id, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $res_items = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    $ob = $res_items->GetNextElement();
    $arFields = $ob->GetFields();
    $rg_salon = $arFields[PROPERTY_SALON_REGION_VALUE];
	if ($rg_salon !='') $nal_ref = '';
	else $nal_ref = '<a href="/wharetobuy/mebel_in_salon.php?id='.$id.'" ><strong>Диваны в наличии</strong></a>'; // $_GET['id']
?><sc ript="" language="Javascript"> 
  <table cellspacing="0" cellpadding="0" width="100%"> 
    <tbody> 
      <tr bgcolor="#e20a17" height="21"><td colspan="3"></td></tr>
     
      <tr><td><a href="http://www.avangard.biz/" ><img alt="Мебельная фабрика Авангард" hspace="20" src="/images/logotype.gif" border="0"  /></a></td><td><?=$nal_ref?></td><td><a href="http://www.avangard.biz/" >На главную</a> <a style="MARGIN-LEFT: 12px" href="/redesign/where_buy/" >На страницу &quot;Где купить&quot;</a> </td></tr>

      <tr><td align="center" colspan="3">
		  <? //echo 'id='.$id.'<br>';?>
          <p><?$APPLICATION->IncludeComponent("bitrix:catalog.element", "salon_detail", array(
	"IBLOCK_TYPE" => "shops",
	"IBLOCK_ID" => "8",
	"ELEMENT_ID" => $id,
	"ELEMENT_CODE" => "",
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"PROPERTY_CODE" => array(
		0 => "GOOGLE_MAP",
		1 => "SALON_ADRESS",
		2 => "SALON_CITY",
		3 => "SALON_REGION",
		4 => "SALON_TYPE_2",
		5 => "SALON_PHONE",
		6 => "SALON_METRO",
		7 => "SALON_ROUTE",
		8 => "SALON_TIME",
		9 => "SALON_ACTION",
		10 => "SALON_PHOTO",
		11 => "SALON_ITEMS",
		12 => "",
	),
	"OFFERS_LIMIT" => "0",
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"USE_ELEMENT_COUNTER" => "Y",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"LINK_IBLOCK_TYPE" => "",
	"LINK_IBLOCK_ID" => "",
	"LINK_PROPERTY_SID" => "",
	"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
	),
	false
);?></p>
         </td></tr>
     
      <tr><td align="left" colspan="3"> 
          <div style="padding: 0px 12px 12px 100px; "><a name="nal"></a>
<?// echo '<a id="bxid_511446" href="/mebel_sal.php?id='.$id.'" >Диваны в наличии</a>';?> 
<?
if ($rg_salon !='')
{?>

<table cellspacing="0" cellpadding="0"> 
	<tbody> 
		<tr>
			<td width="100%"> 
				<div class="gray_td"> 
					<h1>Диваны на заказ в этом салоне</h1>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<?
				$mainFilter= array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "!PROPERTY_IN_CATALOG" => false);
				if($_REQUEST["collection"]!='')
					$mainFilter["PROPERTY_COLLECTION"]= (int)$_REQUEST["collection"];
				$APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_bigpics_rg", Array(
	"IBLOCK_TYPE" => "news",	// Тип инфоблока
	"IBLOCK_ID" => "5",	// Инфоблок
	"SECTION_ID" => $_REQUEST["SECTION_ID"],	// ID раздела
	"SECTION_CODE" => "",	// Код раздела
	"SECTION_USER_FIELDS" => array(	// Свойства раздела
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",	// По какому полю сортируем элементы
	"ELEMENT_SORT_ORDER" => "asc",	// Порядок сортировки элементов
	"FILTER_NAME" => "mainFilter",	// Имя массива со значениями фильтра для фильтрации элементов
	"INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
	"SHOW_ALL_WO_SECTION" => "Y",	// Показывать все элементы, если не указан раздел
	"PAGE_ELEMENT_COUNT" => "40",	// Количество элементов на странице
	"LINE_ELEMENT_COUNT" => "3",	// Количество элементов выводимых в одной строке таблицы
	"PROPERTY_CODE" => array(	// Свойства
		0 => "COLLECTION",
		1 => "PRICE",
		2 => "SKIDKA",
		3 => "IN_CATALOG",
		4 => "FULLCOLOR_PIC",
		5 => "BLACKWHITE_PIC",
		6 => "",
	),
	"OFFERS_LIMIT" => "0",	// Максимальное количество предложений для показа (0 - все)
	"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",	// URL, ведущий на страницу с содержимым раздела
	"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",	// URL, ведущий на страницу с содержимым элемента раздела
	"BASKET_URL" => "/personal/basket.php",	// URL, ведущий на страницу с корзиной покупателя
	"ACTION_VARIABLE" => "action",	// Название переменной, в которой передается действие
	"PRODUCT_ID_VARIABLE" => "id",	// Название переменной, в которой передается код товара для покупки
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",	// Название переменной, в которой передается количество товара
	"PRODUCT_PROPS_VARIABLE" => "prop",	// Название переменной, в которой передаются характеристики товара
	"SECTION_ID_VARIABLE" => "SECTION_ID",	// Название переменной, в которой передается код группы
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"CACHE_TYPE" => "N",	// Тип кеширования
	"CACHE_TIME" => "0",	// Время кеширования (сек.)
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"META_KEYWORDS" => "-",	// Установить ключевые слова страницы из свойства
	"META_DESCRIPTION" => "-",	// Установить описание страницы из свойства
	"BROWSER_TITLE" => "-",	// Установить заголовок окна браузера из свойства
	"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
	"DISPLAY_COMPARE" => "N",	// Выводить кнопку сравнения
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
	"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
	"PRICE_CODE" => "",	// Тип цены
	"USE_PRICE_COUNT" => "N",	// Использовать вывод цен с диапазонами
	"SHOW_PRICE_COUNT" => "1",	// Выводить цены для количества
	"PRICE_VAT_INCLUDE" => "Y",	// Включать НДС в цену
	"PRODUCT_PROPERTIES" => "",	// Характеристики товара
	"USE_PRODUCT_QUANTITY" => "N",	// Разрешить указание количества товара
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
	"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
	"PAGER_TITLE" => "Товары",	// Название категорий
	"PAGER_SHOW_ALWAYS" => "Y",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
	"PAGER_SHOW_ALL" => "Y",	// Показывать ссылку "Все"
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);?>
          </div>
         </td></tr>
     
      <tr><td colspan="3"></td></tr>
     </tbody>
   </table>
 <sc ript="" language="Javascript"><?}?>  </sc></sc><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "> 
    <div><sc ript="" language="Javascript"></sc></div>
   </blockquote></blockquote> 
<div><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> 
        
       </sc></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc></blockquote></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc></blockquote></blockquote><blockquote style="margin: 0px 0px 0px 40px; border: none; padding: 0px; "><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc><sc ript="" language="Javascript"> </sc></blockquote><sc ript="" language="Javascript"> 
    <p> </p>
   </sc> 
  <br />
 </div>


			</td>
		</tr>
	</tbody>
</table>
<?}?>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>