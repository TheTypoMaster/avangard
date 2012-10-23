<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> 
<div class="rc_h3"><a href="/8days/index.php" >АКЦИИ</a></div>
 
<div id="rc_img"> <?$APPLICATION->IncludeComponent("bitrix:news.list", "akcii_list2", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "14",
	"NEWS_COUNT" => "50",
	"SORT_BY1" => "SORT",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "href",
		1 => "flashno",
		2 => "pictorflash",
		3 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
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
	"DISPLAY_PREVIEW_TEXT" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?> </div>
 
<div class="rc_h3"> 
  <h3>НОВОСТИ</h3>
 </div>
 
<div id="rc_news_block"> <?$APPLICATION->IncludeComponent(
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
);?> </div>
 
<div class="rc_link"><a href="/news/" >Показать все новости</a></div>
 
<div class="rc_h3"></div>
 
<div id="rc_menu"> 
  <ul> 
    <li><a href="/tipy_mehanizmov/divan_delfiny.php" >ДИВАНЫ ДЕЛЬФИН</a></li>
   
    <li><a href="/tipy_mehanizmov/divan_knizhka.php" >ДИВАНЫ КНИЖКА</a></li>
   
    <li><a href="/tipy_mehanizmov/divany_evroknizhki.php" >ДИВАНЫ ЕВРОКНИЖКА</a></li>
   
    <li><a href="/divani_i_kresla/kreslo_krovat.php" >КРЕСЛО-КРОВАТЬ</a></li>
   
    <li><a href="/section.php" >ДИВАНЫ ПО ТИПАМ</a></li>
   </ul>
 </div>
 
