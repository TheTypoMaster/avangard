<?
/**************************************************************************
Component Subscribtion News List.

This component is intended for displaying the list of the news from one, several or just from all information blocks for newsletter issue.

Sample of usage:

$APPLICATION->IncludeFile("subscribe/subscr_news.php", Array(
	"IBLOCK_TYPE"	=> "news"
	));

Parameters:

	IBLOCK_TYPE - Information block type (will be used for check purposes only)
**************************************************************************/
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!IsModuleInstalled("iblock") || !CModule::IncludeModule("iblock"))
	return;

IncludeTemplateLangFile(__FILE__);

$SITE_ID = (isset($SITE_ID) ? $SITE_ID : $SUBSCRIBE_TEMPLATE_RUBRIC["SITE_ID"]);
$IBLOCK_TYPE = (isset($IBLOCK_TYPE) ? $IBLOCK_TYPE : "news");
$ID = (!isset($ID) || ($ID=="-")? "" : $ID);
global $USER;
$SAVED_USER=$USER; $USER = new CUser;
global $SUBSCRIBE_TEMPLATE_RUBRIC;
$rsSite = CSite::GetByID($SITE_ID);
$arSite = $rsSite->Fetch();

$SORT_BY = (isset($SORT_BY) ? $SORT_BY : "SORT");
if($SORT_BY != "ACTIVE_FROM")
	$SORT_BY = "SORT";

$SORT_ORDER = (isset($SORT_ORDER) ? $SORT_ORDER : "ASC");
if($SORT_ORDER != "DESC")
	$SORT_ORDER = "ASC";

$news_counter=0;
$rsIBlock = CIBlock::GetList(
	Array($SORT_BY=>$SORT_ORDER),
	Array(
		'ID'=>$ID,
		'TYPE'=>$IBLOCK_TYPE,
		'SITE_ID'=>$SITE_ID,
		'ACTIVE'=>'Y'
	));
?>
<table cellpadding="0" cellspacing="10" border="0">
<?
while($arIBlock = $rsIBlock->Fetch()):
	$rsNews = CIBlockElement::GetList(
		Array($SORT_BY=>$SORT_ORDER),
		Array(
			'IBLOCK_ID'=>$arIBlock["ID"],
			'>DATE_ACTIVE_FROM'=>$SUBSCRIBE_TEMPLATE_RUBRIC["START_TIME"],
			'<=DATE_ACTIVE_FROM'=>$SUBSCRIBE_TEMPLATE_RUBRIC["END_TIME"]
		));
	if($arNews = $rsNews->GetNextElement()):
?>
        <tr><td><h1><?=$arIBlock['NAME']?></h1></td></tr>
<?
	do
	{
		$news_counter++;
		$arItem = $arNews->GetFields();
		if(strpos($arItem["DETAIL_PAGE_URL"],"http")!==0)
			$arItem["DETAIL_PAGE_URL"]="http://".$arSite["SERVER_NAME"].$arItem["DETAIL_PAGE_URL"];
?>
	<tr><td>
		<font class="text">
		<?echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100, "hspace='5' vspace='5' align='left' border='0'", $arItem["DETAIL_PAGE_URL"]);?>
		<?if(strlen($arItem["DATE_ACTIVE_FROM"])>0):?>
			<font class="newsdata"><?echo $arItem["DATE_ACTIVE_FROM"]?></font><br>
		<?endif;?>
		<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br>
		<?echo $arItem["PREVIEW_TEXT"];?>
		</font>
	</td></tr>
<?
	}
	while($arNews = $rsNews->GetNextElement());
	endif;
?>
<?endwhile?>
</table>

<?
$USER=$SAVED_USER;
global $SUBSCRIBE_TEMPLATE_RESULT;
if($news_counter>0)
	$SUBSCRIBE_TEMPLATE_RESULT=true;
?>