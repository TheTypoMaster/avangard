<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table cellspacing="0" cellpadding="0" width="182" border="0">
  <tbody>
    <tr><td width="100%">
        <div class="gray_td">
          <h1><a style="TEXT-DECORATION: none" href="/8days/index.php" ><font color="#ff0000">АКЦИИ</font></a></h1>
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
  
    <tr><td class="big_red_link" align="right"><a href="/news/" >Показать все новости</a> </td></tr>
  
    <tr><td width="100%">
        <div class="gray_td">
          <h1 />
        </div>
      
        <br />
      </td></tr>
  
    <tr><td>
        <br />
      
        <br />
      
        <ul>
                  
          <div class="gray_td_left" width="100%"><a style="TEXT-DECORATION: none" href="/tipy_mehanizmov/divan_delfiny.php">
              <h1>ДИВАНЫ ДЕЛЬФИН</h1>
            </a></div>
        
          <div class="gray_td_left" width="100%"><a style="TEXT-DECORATION: none" href="/tipy_mehanizmov/divan_knizhka.php">
              <h1>ДИВАНЫ КНИЖКА</h1>
            </a></div>
        
          <div class="gray_td_left" width="100%"><a style="TEXT-DECORATION: none" href="/divani_i_kresla/kreslo_krovat.php">
              <h1>КРЕСЛО-КРОВАТЬ</h1>
            </a></div>
        </ul>
      </td></tr>
  </tbody>
</table>
