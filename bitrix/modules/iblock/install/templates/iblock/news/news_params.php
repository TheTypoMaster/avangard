<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);

$ID = (isset($ID) ? $ID : $_REQUEST["ID"]);
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
$CACHE_ID = SITE_ID."|".$APPLICATION->GetCurPage()."|".md5(serialize($arParams))."|".$USER->GetGroups();

$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;
$bDisplayDate = ($DISPLAY_DATE == "Y") ? True : False;
$bDisplayPreviewPicture = ($DISPLAY_PREVIEW_PICTURE == "Y") ? True : False;
$bDisplayName = ($DISPLAY_NAME == "Y") ? True : False;
$bDisplayPreviewText = ($DISPLAY_PREVIEW_TEXT == "Y") ? True : False;
$bPageTitle = ($DISPLAY_PAGE_TITLE == "Y") ? True : False;

if($bPageTitle)
	$APPLICATION->SetTitle(GetMessage("T_NEWS_NEWS_TITLE"));

$cache = new CPHPCache;
if($cache->InitCache($CACHE_TIME, $CACHE_ID))
{
	$vars = $cache->GetVars();
	if($bPageTitle)
		$APPLICATION->SetTitle($vars["NAME"]);
	$APPLICATION->AddChainItem($vars["NAME"]);
	if ($bDisplayPanel && CModule::IncludeModule("iblock"))	
		CIBlock::ShowPanel($ID, 0, 0, $vars["IBLOCK_TYPE_ID"]);
	$cache->Output();
}
else
{
	if(CModule::IncludeModule("iblock") && ($arIBlock = GetIBlock($ID, $IBLOCK_TYPE))):
		if($bPageTitle)
			$APPLICATION->SetTitle($arIBlock["NAME"]);
		$APPLICATION->AddChainItem($arIBlock["NAME"]);
		if ($bDisplayPanel)
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
				<?if ($bDisplayPreviewPicture):?>
					<?echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100, "hspace='5' vspace='5' align='left' border='0'", $arItem["DETAIL_PAGE_URL"]);?>
				<?endif;?>	
				<?if(strlen($arItem["DATE_ACTIVE_FROM"])>0 && $bDisplayDate):?>
					<font class="newsdata"><?echo $arItem["DATE_ACTIVE_FROM"]?><br></font><?endif?>
				<?if ($bDisplayName):?>
					<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br>
				<?endif;?>
				<?if ($bDisplayPreviewText):?>				
					<?echo $arItem["PREVIEW_TEXT"];?>
				<?endif;?>				
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
