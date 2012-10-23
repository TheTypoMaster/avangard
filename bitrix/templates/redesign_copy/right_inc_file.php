<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> 
<table width="182" cellspacing="0" cellpadding="0" border="0"> 
  <tbody> 
    <tr><td width="100%"> 
        <div class="gray_td"> 
          <h1><a style="text-decoration: none;" href="/8days/index.php"><font color="#ff0000">АКЦИИ</font></a></h1>
         </div>
       </td></tr>
   
    <tr><td><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"akcii_list2",
	Array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "14",
		"NEWS_COUNT" => "50",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",2=>"",),
		"PROPERTY_CODE" => array(0=>"flashno",1=>"href",2=>"pictorflash",3=>"",),
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N"
	)
);?> </td></tr>
   
    <tr><td> 
        <br />
       
        <br />
       
        <div class="gray_td"> 
          <h1>НОВОСТИ </h1>
         </div>
       </td></tr>
   
    <tr><td><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"news_main_list",
	Array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "13",
		"NEWS_COUNT" => "6",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",2=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"",),
		"DETAIL_URL" => "/news/news_#ELEMENT_ID#.html",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "Y"
	)
);?> </td></tr>
   
    <tr><td align="right" class="big_red_link"><a href="/news/">Показать все новости</a> </td></tr>
   
    <tr><td width="100%"> 
        <div class="gray_td"> 
          <h1> </h1>
         </div>
       
        <br />
       </td></tr>
   
    <tr><td> 
        <br />
       
        <br />
       
        <ul> 
          <div class="gray_td_left" width="100%"><a style="text-decoration: none;" href="/tipy_mehanizmov/divan_delfiny.php"> 
              <h1>ДИВАНЫ ДЕЛЬФИН</h1>
             </a></div>
         
          <div class="gray_td_left" width="100%"><a style="text-decoration: none;" href="/tipy_mehanizmov/divan_knizhka.php"> 
              <h1>ДИВАНЫ КНИЖКА</h1>
             </a></div>
         
          <div class="gray_td_left" width="100%"><a style="text-decoration: none;" href="/tipy_mehanizmov/divany_evroknizhki.php"> 
              <h1>ДИВАНЫ ЕВРОКНИЖКА</h1>
             </a></div>
         
          <div class="gray_td_left" width="100%"><a style="text-decoration: none;" href="/divani_i_kresla/kreslo_krovat.php"> 
              <h1>КРЕСЛО-КРОВАТЬ</h1>
             </a></div>
         
          <div class="gray_td_left" width="100%"><a style="text-decoration: none;" href="/section.php"> 
              <h1>ДИВАНЫ ПО ТИПАМ</h1>
             </a></div>
         </ul>
       </td></tr>
   	 
    <tr><td> 
        <br />
       
        <br />
       
        <div class="gray_td"> 
          <h1>ПОЛЕЗНОЕ</h1>
         </div>
       </td></tr>
   
    <tr><td><?$APPLICATION->IncludeComponent("bitrix:news.list", "template1", Array(
	"IBLOCK_TYPE" => "article",	// Тип информационного блока (используется только для проверки)
	"IBLOCK_ID" => "27",	// Код информационного блока
	"NEWS_COUNT" => "3",	// Количество новостей на странице
	"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
	"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
	"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
	"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
	"FILTER_NAME" => "",	// Фильтр
	"FIELD_CODE" => array(	// Поля
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(	// Свойства
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
	"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"AJAX_OPTION_SHADOW" => "Y",	// Включить затенение
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "3600",	// Время кеширования (сек.)
	"CACHE_FILTER" => "N",	// Кэшировать при установленном фильтре
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
	"DISPLAY_PANEL" => "N",	// Добавлять в админ. панель кнопки для данного компонента
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",	// Включать инфоблок в цепочку навигации
	"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
	"PARENT_SECTION" => "",	// ID раздела
	"PARENT_SECTION_CODE" => "",	// Код раздела
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
	"DISPLAY_BOTTOM_PAGER" => "N",	// Выводить под списком
	"PAGER_TITLE" => "Новости",	// Название категорий
	"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
	"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
	"DISPLAY_DATE" => "Y",	// Выводить дату элемента
	"DISPLAY_NAME" => "Y",	// Выводить название элемента
	"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
	"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);?> </td></tr>
   
    <tr><td align="right" class="big_red_link"><a href="/article/">Показать все статьи</a> </td></tr>
   </tbody>
 </table>
