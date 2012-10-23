<?
/**************************************************************************
Component News List.

This component is intended for displaying the list of the news from one, several or just from all information blocks.

Sample of usage:

$APPLICATION->IncludeFile("iblock/sale_content/news.php", Array(
	"ID"	=>	Array("1"),
	"IBLOCK_TYPE"	=> "news",
	"NEWS_COUNT"	=> "10",
	"GROUP_PERMISSIONS" => Array(),
	"SORT_BY1"	=> "ACTIVE_FROM",
	"SORT_ORDER1"	=> "DESC",
	"SORT_BY2"	=> "SORT",
	"SORT_ORDER2"	=> "ASC",
	"CACHE_TIME"	=> "0"
	));

Parameters:

	ID - Information block ID
	IBLOCK_TYPE - Information block type (will be used for check purposes only)
	NEWS_COUNT - Number of the news on page
	GROUP_PERMISSIONS - User groups that can read selling content
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

$ID = (strlen($ID)>0 ? intval($ID) : "1");

$IBLOCK_TYPE = (isset($IBLOCK_TYPE) ? $IBLOCK_TYPE : "catalog");
if($IBLOCK_TYPE=="-")
	$IBLOCK_TYPE = "";

$NEWS_COUNT = (strlen($NEWS_COUNT)>0 ? intval($NEWS_COUNT) : "20");

$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "ACTIVE_FROM");
$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "SORT");
$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "ASC");

$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);

$bUserHaveAccess = False;
if (isset($GROUP_PERMISSIONS) && is_array($GROUP_PERMISSIONS)
	&& isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
	for ($i = 0; $i < count($GROUP_PERMISSIONS); $i++)
	{
		if (in_array($GROUP_PERMISSIONS[$i], $arUserGroupArray))
		{
			$bUserHaveAccess = True;
			break;
		}
	}
}

$CACHE_TIME = intval($CACHE_TIME);
$CACHE_ID = SITE_ID."|".($bUserHaveAccess ? "TRUE" : "FALSE")."|".$APPLICATION->GetCurPage()."|".md5(serialize($arParams))."|".$USER->GetGroups();

$APPLICATION->SetTitle(GetMessage("T_NEWS_NEWS_TITLE"));

$cache = new CPHPCache;
if($cache->InitCache($CACHE_TIME, $CACHE_ID))
{
	$vars = $cache->GetVars();
	$APPLICATION->SetTitle($vars["NAME"]);
	$APPLICATION->AddChainItem($vars["NAME"]);
	if(CModule::IncludeModule("iblock"))
		CIBlock::ShowPanel($ID, 0, 0, $vars["IBLOCK_TYPE_ID"]);
	$cache->Output();
}
else
{
	if(CModule::IncludeModule("iblock") && ($arIBlock = GetIBlock($ID, $IBLOCK_TYPE))):
		$APPLICATION->SetTitle($arIBlock["NAME"]);
		$APPLICATION->AddChainItem($arIBlock["NAME"]);
		CIBlock::ShowPanel($ID, 0, 0, $arIBlock["IBLOCK_TYPE_ID"]);

		$cache->StartDataCache();

		$items = GetIBlockElementList($ID, false, $SORT, $NEWS_COUNT);
		$items->NavPrint(GetMessage("T_NEWS_NEWS_NAVIG"));
		?><table cellpadding="0" cellspacing="10" border="0"><?
		while($obItem = $items->GetNextElement()):
			$arItem = $obItem->GetFields();
			//$arProp = $obItem->GetProperties();
			?>
			<tr><td>
			<font class="text">
				<?echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100, "hspace='5' vspace='5' align='left' border='0'", ($bUserHaveAccess ? $arItem["DETAIL_PAGE_URL"] : ""));?>
				<?
				if (strlen($arItem["DATE_ACTIVE_FROM"]) > 0):
					?><font class="newsdata"><?= $arItem["DATE_ACTIVE_FROM"]?><br></font><?
				endif;
				if ($bUserHaveAccess):
					?><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?
				endif;
				?><b><?= $arItem["NAME"]?></b><?
				if ($bUserHaveAccess):
					?></a><?
				endif;
				?><br>
				<?echo $arItem["PREVIEW_TEXT"];?>
			</font>
			</td></tr>
			<?
		endwhile;
		?></table><?
		$items->NavPrint(GetMessage("T_NEWS_NEWS_NAVIG"));

		$vars = Array("NAME"=>$arIBlock["NAME"]);
		$cache->EndDataCache($vars);
	else:
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
	endif;
}
?>
