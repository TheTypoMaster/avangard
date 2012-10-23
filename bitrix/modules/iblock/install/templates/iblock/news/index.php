<?
/**************************************************************************
Component "All news".

This component is intended for displaying the list of the news from all the information blocks of the specified type. Element are grouped by information blocks.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/news/index.php", Array(
	"IBLOCK_TYPE"		=> "news",
	"IBLOCK_SORT_BY"	=> "SORT",
	"IBLOCK_SORT_ORDER"	=> "ASC",
	"NEWS_COUNT"		=> "5",
	"SORT_BY1"		=> "ACTIVE_FROM",
	"SORT_ORDER1"		=> "DESC",
	"SORT_BY2"		=> "SORT",
	"SORT_ORDER2"		=> "ASC",
	"CACHE_TIME"		=> "0"
	));



Parameters:

	IBLOCK_TYPE - Information block type
	IBLOCK_SORT_BY - Field for sorting the information blocks
		sort - by sorting index
		id - by ID
		name - by title
	IBLOCK_SORT_ORDER - Sorting order for information blocks
		asc - in ascending order
		desc - in descending order
	NEWS_COUNT - Number of the news to be displayed for each information block
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
	CACHE_TIME - (sec.) time for caching (0 - do not cache)
**************************************************************************/

IncludeTemplateLangFile(__FILE__);

$IBLOCK_TYPE = (strlen($IBLOCK_TYPE)>0 ? $IBLOCK_TYPE : "news");
$IBLOCK_SORT_BY = (isset($IBLOCK_SORT_BY) ? $IBLOCK_SORT_BY : "SORT");
$IBLOCK_SORT_ORDER = (isset($IBLOCK_SORT_ORDER) ? $IBLOCK_SORT_ORDER : "ASC");
$IBLOCK_SORT = Array($IBLOCK_SORT_BY=>$IBLOCK_SORT_ORDER);
$NEWS_COUNT = (strlen($NEWS_COUNT)>0 ? intval($NEWS_COUNT) : "5");

$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "ACTIVE_FROM");
$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "SORT");
$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "ASC");

$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);

$CACHE_TIME = intval($CACHE_TIME);
$CACHE_ID = SITE_ID."|".$APPLICATION->GetCurPage()."|".md5(serialize($arParams))."|".$USER->GetGroups();

$APPLICATION->SetTitle(GetMessage("T_NEWS_INDEX_TITLE"));

$cache = new CPageCache;
if($cache->StartDataCache($CACHE_TIME, $CACHE_ID))
{
	if(!CModule::IncludeModule("iblock")):
		echo ShowError(GetMessage("T_NEWS_INDEX_MODULE_NA"));
	else: //if(!CModule::IncludeModule("iblock")):
		$iblocks = GetIBlockList($IBLOCK_TYPE, Array(), Array(), $IBLOCK_SORT);
		while($arIBlock = $iblocks->GetNext()):
			?>
			<font class="text">
			<a href="<?echo $arIBlock["LIST_PAGE_URL"]?>"><b><?echo $arIBlock["NAME"]?></b></a><br><br></font>
			<?$items = GetIBlockElementList($arIBlock["ID"], false, $SORT, $NEWS_COUNT);?>
			<table cellpadding="2" cellspacing="0" border="0" width="80%">
				<?while($obItem = $items->GetNextElement()):?>
					<tr>
						<td width="100%">
							<?
							$arItem = $obItem->GetFields();
							$arProps = $obItem->GetProperties();
							$arLinkProp = $arProps["DOC_LINK"];
							?>
							<font class="newsdata"><?echo $arItem["ACTIVE_FROM"]?></font><?
								if (strlen($arItem["ACTIVE_FROM"])>0):?>&nbsp;|&nbsp;<?endif;?>
								<font class="text">
								<?if ($arItem["DETAIL_TEXT"] && !$arLinkProp["VALUE"]):?><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?elseif ($arLinkProp["VALUE"]):?><a href="<?echo $arLinkProp["VALUE"]?>"><?endif;?><?=$arItem["NAME"]?><?if ($arItem["DETAIL_TEXT"] || $arLinkProp["VALUE"]):?></a><?endif;?>
								</font>
							<br>
						</td>
					</tr>
					<tr>
					<td valign="top" width="100%">
						<?if ($arItem["PREVIEW_PICTURE"]):?>
						<table cellpadding="0" cellspacing="0" border="0" align="left">
							<tr>
							<td valign="top"><?echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100, "hspace='2' vspace='2' align='left' border='0'", $arItem["DETAIL_PAGE_URL"]);?></td>
							<td valign="top"><img src="/bitrix/images/1.gif" width="10" height="1"></td>
							</tr>
						</table>
						<?endif;?>
						<font class="text"><?if ($arItem["PREVIEW_TEXT"]):?><?echo $arItem["PREVIEW_TEXT"];?><br><?endif;?></font>
					</td>
					</tr>
					<tr>
						<td width="100%"><img src="/bitrix/images/1.gif" width="10" height="3"></td>
					</tr>
				<?endwhile;?>
				<tr>
					<td width="100%"><img src="/bitrix/images/1.gif" width="15" height="5" alt="" border="0"></td>
				</tr>
			</table>
		<?endwhile;?>
	<?endif //if(!CModule::IncludeModule("iblock")):?>
	<?
	$cache->EndDataCache();
} //if($cache->StartDataCache($CACHE_TIME, SITE_ID."|".$APPLICATION->GetCurPage()))
?>
