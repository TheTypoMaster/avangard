<?
/**************************************************************************
Component News Line

This component is intended for displaying the list of news without separating them by information blocks.
There can be selected news from one, from several or just from all information blocks.
 
Sample of usage:
$APPLICATION->IncludeFile("iblock/news/news_line.php", Array(
	"IBLOCK_TYPE"	=> "news",
	"IBLOCK"		=> Array("1"),
	"NEWS_COUNT"	=> "20",
	"SORT_BY1"	=> "ACTIVE_FROM",
	"SORT_ORDER1"	=> "DESC",
	"SORT_BY2"	=> "SORT",
	"SORT_ORDER2"	=> "ASC",
	"CACHE_TIME"	=> "0"
	));

Parameters:

	IBLOCK_TYPE - Information block type (will be used for check purposes only)
	IBLOCK - Information block ID
	NEWS_COUNT - Number of the news on page
	SORT_BY1 - Field for the first news sort
		sort - by sorting index
		timestamp_x - by modification date
		name - by title
		active_from - by activity date FROM
	SORT_ORDER1 - Sorting order for the first news sort
		asc - in ascending order
		desc - in descending order
	SORT_BY2 - Field for the second news sort
		sort - by sorting index
		timestamp_x - by modification date
		name - by title
		active_from - by activity date FROM
	SORT_ORDER2 - Sorting order for the second news sort
		asc - in ascending order
		desc - in descending order
	CACHE_TIME - (sec.) time for cacheing (0 - do not cache)

**************************************************************************/

IncludeTemplateLangFile(__FILE__);

$IBLOCK = (isset($IBLOCK) ? $IBLOCK : "");
$IBLOCK_TYPE = (isset($IBLOCK_TYPE) ? $IBLOCK_TYPE : "news");
if($IBLOCK_TYPE=="-")
	$IBLOCK_TYPE = "";

$NEWS_COUNT = (strlen($NEWS_COUNT)>0 ? intval($NEWS_COUNT) : "20");

$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "ACTIVE_FROM");
$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "SORT");
$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "ASC");

$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);

$CACHE_TIME = intval($CACHE_TIME);
$CACHE_ID = SITE_ID."|".__FILE__."|".md5(serialize($arParams))."|".$USER->GetGroups();
$cache = new CPageCache;
if(CModule::IncludeModule("iblock")):
	if($cache->StartDataCache($CACHE_TIME, $CACHE_ID)):
		$arSelect = array("ACTIVE_FROM", "DETAIL_PAGE_URL", "NAME");
		$items = GetIBlockElementListEx($IBLOCK_TYPE, $IBLOCK, Array(), $SORT, Array("nTopCount"=>$NEWS_COUNT), array(), $arSelect);
		if($arItem = $items->GetNext()):
		?>
		<table border="0" cellspacing="0" cellpadding="2">
			<?do{?>
			<tr>
				<td valign="top"><font class="newsdata"><?echo $arItem["ACTIVE_FROM"]?>&nbsp;&nbsp;</font><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="newstext"><?echo $arItem["NAME"]?></a></td>
			</tr>
			<?}while($arItem = $items->GetNext());?>
			</table>
		<?
		endif;
		$cache->EndDataCache();
	endif;
endif;
?>
