<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> 
<div class="rc_h3">
  <h3>КАТАЛОГ ТОВАРОВ</h3>
</div>
<ul class="lc_catalog_list">
	<li><a href="/divani_i_kresla/">Диваны и кресла</a></li>
	<li><a href="/uglovie_divani/">Угловые диваны</a></li>
	<li><a href="/modulnie_sistemi/">Модульные системы</a></li>
	<li><a href="/tip_divanov/kreslo-krovati.php">Кресла-кровати</a></li>
	<li><a href="/krovati/index.php">Кровати</a></li>
	<li><a href="/tipy_mehanizmov/divan_akkordeon.php">Диваны-аккордеоны</a></li>
	<li><a href="/tipy_mehanizmov/divan_knizhka.php">Диваны-книжка</a></li>
	<li><a href="/tipy_mehanizmov/vykatnye_divany.php">Диваны выкатные</a></li>
	<li><a href="/modulnie_sistemi/mjagkaja_mebel-transformer.php">Диваны-трансформеры</a></li>
	<li><a href="/accessories/">Аксессуары</a></li>
</ul>
 		
<div class="discount_banner">
	<a href="http://www.avangard.biz/8days/sale_centers.php">
		<img src="/images/discount_banner.png"/>
	</a>
</div>

<div class="discount_banner">
	<a href="#">
		<img src="/images/dostavka_banner.png"/>
	</a>
</div>

<div class="discount_banner">
	<a href="/information/expert-advice.php">
		<img src="/images/doctor_banner2.png"/>
	</a>
</div>

<div class="left_news">
	<h3 class="title">НОВОСТИ</h3>
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "news_main_list", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "13",
	"NEWS_COUNT" => "4",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "/news/news_#ELEMENT_ID#.html",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "N",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "N",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
	<div class="more_link"><a href="/news/">Показать все новости</a></div>
</div>

<div class="rc_h3">
	<h3>ПОЛЕЗНОЕ</h3>
</div>
<div id="rc_txt" class="left_useful">
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "template1", Array(
		"IBLOCK_TYPE" => "article",	
		"IBLOCK_ID" => "27",	
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(	
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(	
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",	
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",	
		"AJAX_OPTION_JUMP" => "N",	
		"AJAX_OPTION_STYLE" => "Y",	
		"AJAX_OPTION_HISTORY" => "N",	
		"CACHE_TYPE" => "A",	
		"CACHE_TIME" => "3600",	
		"CACHE_FILTER" => "N",	
		"CACHE_GROUPS" => "Y",	
		"PREVIEW_TRUNCATE_LEN" => "",	
		"ACTIVE_DATE_FORMAT" => "d.m.Y",	
		"DISPLAY_PANEL" => "N",	
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "N",	
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",	
		"ADD_SECTIONS_CHAIN" => "Y",	
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",	
		"PARENT_SECTION_CODE" => "",	
		"DISPLAY_TOP_PAGER" => "N",	
		"DISPLAY_BOTTOM_PAGER" => "N",	
		"PAGER_TITLE" => "Новости",	
		"PAGER_SHOW_ALWAYS" => "N",	
		"PAGER_TEMPLATE" => "",	
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	
		"PAGER_SHOW_ALL" => "N",	
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",	
		"DISPLAY_PREVIEW_TEXT" => "Y",	
		"AJAX_OPTION_ADDITIONAL" => "",	
		),
		false
	);?>
	<div class="more_link"><a href="/article/">Показать все статьи</a></div>
</div>