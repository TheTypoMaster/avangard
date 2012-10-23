<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Виды обивок");
$APPLICATION->AddHeadScript("/basket/basket.js");
$res = CIBlockSection::GetByID($_GET["SECTION_ID"]);
$ar_sect = $res->Fetch();

/*echo "<pre>ar_sect - ";
print_r($_GET);
echo "</pre>";*/
$dop_url = "";
if ($_GET['key']){
	$dop_url = "?key=".$_GET['key']."&num=".$_GET[num];
}

$srcSect="";
if ($ar_sect[PICTURE]) {
	$resFileSect = CFile::GetByID($ar_sect[PICTURE]);
	$arFileSect = $resFileSect -> Fetch();
	$srcSect = '/upload/'.$arFileSect[SUBDIR].'/'.$arFileSect[FILE_NAME];
}				
?>
    
	
<div id="extra_controls" class="gray_td">
	<h1 class="itemtitle">ВИДЫ ОБИВОК<font style="font-weight: normal; color: rgb(0, 0, 0); margin-left: 22px;">Раздел:</font> <?=$ar_sect[NAME]?></h1>
</div>


<table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr>
<td width="99">
<?
	$arFilter = Array('IBLOCK_ID'=>20, 'GLOBAL_ACTIVE'=>'Y', array());
	  $db_list = CIBlockSection::GetList(array(), $arFilter, true);
	  while($ar_result = $db_list->GetNext()){
			/*echo "<pre>ar_result - ";
			print_r($ar_result);
			echo "</pre>";*/
			$src=$url=""; 
			if ($ar_result[PICTURE]) {
				$resFile = CFile::GetByID($ar_result[PICTURE]);
				$arFile = $resFile -> Fetch();
				$src = '/upload/'.$arFile[SUBDIR].'/'.$arFile[FILE_NAME];
			}
			$url = "/texture/section_".$ar_result['ID'].".html".$dop_url;
	  
		  ?>
	<table cellspacing="0" cellpadding="0" class="catNavs">
	<tbody><tr><td align="center"><? if ($src) { ?><a href="<?=$url?>"><img alt="<?=$ar_result['NAME']?>" src="<?=$src?>"><br></a><? } ?></td></tr>
	<tr><td style="font-size: 10px;" class="link"><a href="<?=$url?>"><?=$ar_result['NAME']?></a></td></tr>
	</tbody></table>
	<?
	}
	?>
</td>
<td>
	<? if ($srcSect) { ?> <img width="160" height="96" border="0" style="float: left; margin: 0px 10px 5px 0px;" alt="" src="<?=$srcSect?>"> <? } ?>
	<div style="font-size: 11px;">Для получения подробной информации о материале, кликните на маленький квадрат. Чем выше ценовая категория обивки, тем дороже изделие мягкой мебели. Количество дней на заказ означает срок изготовление мягкой мебели из этой обивки.</div>
	<br>
	
	<table width="100%" cellspacing="10" cellpadding="0" class="topnews"><tbody><tr><td class="zag" style="border-bottom: 1px solid rgb(9, 36, 87);">
	Образцы обивочных материалов</td></tr></tbody></table>
	
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "texture_in_sect", array(
	"IBLOCK_TYPE" => "catalogue",
	"IBLOCK_ID" => "20",
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "ID",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "DAYS",
		1 => "PRESENCE",
		2 => "PICTURE",
		3 => "PRICE_CAT",
		4 => "",
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
	"PARENT_SECTION" => $_GET[SECTION_ID],
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
</td>
</tr></tbody></table>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>