<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мягкая мебель купить магазин. Фото каталог диванов для дома, продажа");
$APPLICATION->SetPageProperty("up_inc_file", "none");
$APPLICATION->SetPageProperty("right_inc_file", "right_nomain_inc_file.php");
$APPLICATION->SetTitle("Мягкая мебель купить магазин. Фото каталог диванов для дома, продажа");
?> 
<table cellspacing="0" cellpadding="0"> 
	<tbody> 
		<tr>
			<td width="100%"> 
				<div class="gray_td"> 
					<h1>Диваны нв заказ в этом салоне</h1>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<?
				$mainFilter= array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "!PROPERTY_IN_CATALOG" => false);
				if($_REQUEST["collection"]!='')
					$mainFilter["PROPERTY_COLLECTION"]= (int)$_REQUEST["collection"];
				$APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_bigpics_re", Array(
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
			</td>
		</tr>
	</tbody>
</table>
<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>