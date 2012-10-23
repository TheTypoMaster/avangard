<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Виды обивок");
?>

<div id="extra_controls" class="gray_td">
	<h1 class="itemtitle">Выбор обивочных материалов</h1>
</div>

<?
if (!$_GET['key']) { // если выбор материалов из корзины, то не выводим фильтр ?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.filter", "filter_texture", Array(
		"IBLOCK_TYPE" => "catalogue",	// Тип инфо-блока
		"IBLOCK_ID" => "20",	// Инфо-блок
		"FILTER_NAME" => "arrFilter",	// Имя выходящего массива для фильтрации
		"FIELD_CODE" => array(	// Поля
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(	// Свойства
			0 => "PRESENCE",
			1 => "SHADE",
			2 => "PICTURE",
			3 => "TEXTURE",
			4 => "PRICE_CAT",
			5 => "",
		),
		"LIST_HEIGHT" => "5",	// Высота списков множественного выбора
		"TEXT_WIDTH" => "20",	// Ширина однострочных текстовых полей ввода
		"NUMBER_WIDTH" => "5",	// Ширина полей ввода для числовых интервалов
		"CACHE_TYPE" => "N",	// Тип кеширования
		"CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"CACHE_GROUPS" => "N",	// Учитывать права доступа
		"SAVE_IN_SESSION" => "N",	// Сохранять установки фильтра в сессии пользователя
		"PRICE_CODE" => "",	// Тип цены
		),
		false
	);?>
	<?
	}
/*echo "<pre>";
print_r($_GET);
echo "</pre>";
$arFilter = $_GET[arrFilter_pf];
$arFilter["PROPERTY_PRICE_CAT"] = $_GET[arrFilter_pf][PRICE_CAT];

echo "<pre>";
print_r($arrFilter);
echo "</pre>";*/
if ($_GET && !$_GET[key]) { // если есть фильтр, то выводим материалы ?>
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "texture_in_sect", array(
		"IBLOCK_TYPE" => "catalogue",
		"IBLOCK_ID" => "20",
		"NEWS_COUNT" => "20",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "arrFilter",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "ARTICUL",
			1 => "DAYS",
			2 => "PRESENCE",
			3 => "SHADE",
			4 => "PICTURE",
			5 => "TEXTURE",
			6 => "PRICE_CAT",
			7 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "news_detail.php?ID=#ELEMENT_ID#",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "N",
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
		"DISPLAY_BOTTOM_PAGER" => "Y",
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
		"AJAX_OPTION_ADDITIONAL" => ""
		),
		false
	);?>
	<?
}
else { // фильтра нет, выводим разделы
	$res = CIBlockSection::GetByID($_GET["SECTION_ID"]);
	$ar_sect = $res->Fetch();
	$dop_url = "";
	if ($_GET[key]){
		$dop_url = "?key=".$_GET['key']."&num=".$_GET[num];
	} ?>
	<table width="773" height="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr>
	<td>
	<img width="1" height="100%" src="/allegro/img/gif.gif"><br>
	</td>
	<td class="body">
	
	 <?
	$arFilter = Array('IBLOCK_ID'=>20, 'GLOBAL_ACTIVE'=>'Y', array());
	$db_list = CIBlockSection::GetList(array("SORT" => "asc"), $arFilter, true, Array("UF_PREVIEW_TEXT"));
 
	while($ar_result = $db_list->GetNext()){
		/*echo "<pre>ar_result - ";
		print_r($ar_result);
		echo "</pre>";*/
		$src=""; 
		if ($ar_result[PICTURE]) {
			$resFile = CFile::GetByID($ar_result[PICTURE]);
			$arFile = $resFile -> Fetch();
			$src = '/upload/'.$arFile[SUBDIR].'/'.$arFile[FILE_NAME];
		}
		
		$url = '/texture/section_'.$ar_result[ID].'.html'.$dop_url;
		?>
		<table cellspacing="0" cellpadding="8">
		  <tbody>
			<tr>
			  <td valign="top" rowspan="2" style="text-align: center; padding: 5px 0px 0px; height: 101px;">
			  <? if ($src) { ?><a href="<?=$url?>"><img style="width: 160px; height: 96px;" src="<?=$src?>"><br>
			  </a> <? } ?>
			  </td>
			  <td class="acenter"><div style="text-align: left; padding: 2px;"><a style="font-weight: bold; font-size: 14px;" href="<?=$url?>"><?=$ar_result[NAME]?></a></div></td>
			</tr>
			<tr>
			  <td valign="top" style="padding: 5px; font-size: 12px;"><?=$ar_result[UF_PREVIEW_TEXT]?></td>
			</tr>
		  </tbody>
		</table><?
	} ?>
	</td></tr></tbody></table> <?
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>