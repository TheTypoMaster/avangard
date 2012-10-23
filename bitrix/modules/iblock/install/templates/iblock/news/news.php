<?
/**************************************************************************
Component News List.

This component is intended for displaying the list of the news from one, several or just from all information blocks.

Sample of usage:

$APPLICATION->IncludeFile("iblock/news/news.php", Array(
	"ID"	=>	1,
	"IBLOCK_TYPE"	=> "news",
	"SECTION_ID"	=> $_REQUEST["SECTION_ID"],
	"arrPROPERTY_CODE"		=> Array(
		"AUTHOR",
		"SOURCE"
		),
	"NEWS_COUNT"	=> "10",
	"SORT_BY1"	=> "ACTIVE_FROM",
	"SORT_ORDER1"	=> "DESC",
	"SORT_BY2"	=> "SORT",
	"SORT_ORDER2"	=> "ASC",
	"DETAIL_PAGE_URL"	=> "",
	"FILTER"		=> $FILTER,
	"CACHE_TIME"	=> "0"
	));

Parameters:

	ID - Information block ID
	IBLOCK_TYPE - Information block type (will be used for check purposes only)
	SECTION_ID - Information block section ID
	arrPROPERTY_CODE - array of property mnemonic codes
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
	DETAIL_PAGE_URL - Detail Page URL
	FILTER - name of the received array with filter values
	CACHE_TIME - (sec.) time for cacheing (0 - do not cache)

**************************************************************************/

IncludeTemplateLangFile(__FILE__);

$ID = (isset($ID) ? $ID : $_REQUEST["ID"]);

$SECTION_ID = (isset($SECTION_ID) ? $SECTION_ID : $_REQUEST["SECTION_ID"]);
if ($SECTION_ID == 0) $SECTION_ID = false;

$IBLOCK_TYPE = (isset($IBLOCK_TYPE) ? $IBLOCK_TYPE : "news");
if($IBLOCK_TYPE=="-")
	$IBLOCK_TYPE = "";

$NEWS_COUNT = (strlen($NEWS_COUNT)>0 ? intval($NEWS_COUNT) : "20");

$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "ACTIVE_FROM");
$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "SORT");
$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "ASC");

$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);
$FILTER = (isset($FILTER) ? $FILTER : array());
$arrPROPERTY_CODE = is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array();

$INCLUDE_IBLOCK_INTO_CHAIN = ($INCLUDE_IBLOCK_INTO_CHAIN == "N" ? $INCLUDE_IBLOCK_INTO_CHAIN : "Y");

$CACHE_TIME = intval($CACHE_TIME);
$CACHE_ID = SITE_ID."|".$APPLICATION->GetCurPage()."|".md5(serialize($arParams))."|".$USER->GetGroups();

$APPLICATION->SetTitle(GetMessage("T_NEWS_NEWS_TITLE"));

$cache = new CPHPCache;
if($cache->InitCache($CACHE_TIME, $CACHE_ID))
{
	$vars = $cache->GetVars();
	$APPLICATION->SetTitle($vars["NAME"]);

	if ($INCLUDE_IBLOCK_INTO_CHAIN == "Y")
		$APPLICATION->AddChainItem($vars["NAME"]);

	if(CModule::IncludeModule("iblock"))
	{
		if ($bDisplayPanel)	
			CIBlock::ShowPanel($ID, 0, $SECTION_ID, $vars["IBLOCK_TYPE_ID"]);
	}
	$cache->Output();
}
else
{
	if(CModule::IncludeModule("iblock") && ($arIBlock = GetIBlock($ID, $IBLOCK_TYPE))):

		if (IntVal($SECTION_ID) > 0 && $arSection = GetIBlockSection($SECTION_ID, $arIBlock["IBLOCK_TYPE_ID"]))
			$PageTitle = $arSection["NAME"];
		else
			$PageTitle = $arIBlock["NAME"];

		$APPLICATION->SetTitle($PageTitle);

		if ($INCLUDE_IBLOCK_INTO_CHAIN == "Y")
			$APPLICATION->AddChainItem($PageTitle);

		if ($bDisplayPanel)	
			CIBlock::ShowPanel($ID, 0, $SECTION_ID, $arIBlock["IBLOCK_TYPE_ID"]);

		$cache->StartDataCache();

		$arSelect = array(
			"ID",
			"DETAIL_PAGE_URL",
			"PREVIEW_PICTURE",
			"DATE_ACTIVE_FROM",
			"NAME",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PROPERTY_*",
		);
		$items = GetIBlockElementList($ID, $SECTION_ID, $SORT, $NEWS_COUNT, $FILTER, $arSelect);
		$items->NavPrint(GetMessage("T_NEWS_NEWS_NAVIG"));
		?><table cellpadding="0" cellspacing="10" border="0"><?
		while($obItem = $items->GetNextElement()):
			$arItem = $obItem->GetFields();
			$arProp = $obItem->GetProperties();

			if (strlen(trim($DETAIL_PAGE_URL)) > 0)
				$ItemDetailPageURL = CIBlock::ReplaceDetailUrl($DETAIL_PAGE_URL, $arItem, true);
			else
				$ItemDetailPageURL = $arItem["DETAIL_PAGE_URL"];

			?>
			<tr><td>
			<font class="text">
				<?echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100, "hspace='5' vspace='5' align='left' border='0'", $ItemDetailPageURL);?>
				<?if(strlen($arItem["DATE_ACTIVE_FROM"])>0):?><font class="newsdata"><?echo $arItem["DATE_ACTIVE_FROM"]?><br></font><?endif?><a href="<?echo $ItemDetailPageURL?>"><b><?echo $arItem["NAME"]?></b></a><br>
				<?echo $arItem["PREVIEW_TEXT"];?>
			</font>
			<?
				if (is_array($arrPROPERTY_CODE) && count($arrPROPERTY_CODE)>0):
				reset($arrPROPERTY_CODE);
				?><table cellpadding="1" cellspacing="0" border="0"><?
				foreach($arrPROPERTY_CODE as $pid):
					if ((is_array($arProp[$pid]["VALUE"]) && count($arProp[$pid]["VALUE"])>0) || (!is_array($arProp[$pid]["VALUE"]) && strlen($arProp[$pid]["VALUE"])>0)):
						?>
						<tr>
							<td valign="top" nowrap><font class="smalltext"><?=$arProp[$pid]["NAME"]?>:&nbsp;</font></td>
							<td valign="top" nowrap><font class="smalltextblack"><? if(is_array($arProp[$pid]["VALUE"]))
								echo implode("<br>",$arProp[$pid]["VALUE"]);
							else{
								if(strpos($arProp[$pid]["VALUE"], "http")!==false || strpos($arProp[$pid]["VALUE"], "www")!==false){
									if(strpos($arProp[$pid]["VALUE"], "http") === false)
										$site = "http://".$arProp[$pid]["VALUE"];
									else
										$site = $arProp[$pid]["VALUE"]; 
									
									if(IsModuleInstalled("statistic")){
										?><a href="/bitrix/redirect.php?event1=news_out&amp;event2=<?=$site;?>&amp;event3=<?=$arIBlockElement["NAME"]?>&goto=<?=$site;?>" target="blank"><? echo $site;?></a><?
									}else{
										?><a href="<?=$site?>" target="blank"><? echo $site;?></a><?
									}
								}else{
									echo $arProp[$pid]["VALUE"];
								}

							}
								?></font></td>
						</tr>
						<?
					endif;
				endforeach;
				?></table><?
			endif;
			?>
			</td></tr>
			<?
		endwhile;
		?></table><?
		$items->NavPrint(GetMessage("T_NEWS_NEWS_NAVIG"));

		$vars = Array("NAME"=>$PageTitle, "IBLOCK_TYPE_ID"=>$arIBlock["IBLOCK_TYPE_ID"]);
		$cache->EndDataCache($vars);
	else:
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
	endif;
}
?>
