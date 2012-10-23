<?
/**************************************************************************
Component Detailed News

This component is intended for displaying the detailed information for every news.

Sample of usage:

$APPLICATION->IncludeFile("iblock/sale_content/detail.php", Array(
	"ID"	=>	$_REQUEST["ID"],
	"IBLOCK_TYPE"	=> "news",
	"GROUP_PERMISSIONS" => Array(),
	"CACHE_TIME"	=> "0"
	));

Parameters:

	ID - Element ID
 	IBLOCK_TYPE - Information block type (will be used for check purposes only)
	CACHE_TIME - (sec.) time for cacheing (0 - do not cache)

**************************************************************************/

IncludeTemplateLangFile(__FILE__);

$ID = (isset($ID) ? $ID : $_REQUEST["ID"]);
$IBLOCK_TYPE = (isset($IBLOCK_TYPE) ? $IBLOCK_TYPE : "news");
if($IBLOCK_TYPE=="-")
	$IBLOCK_TYPE = "";

$CACHE_TIME = intval($CACHE_TIME);
$CACHE_ID = SITE_ID."|".$APPLICATION->GetCurPage()."|".md5(serialize($arParams))."|".$USER->GetGroups();

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

if ($bUserHaveAccess)
{
	if(CModule::IncludeModule("iblock"))
		CIBlockElement::CounterInc($ID);

	$cache = new CPHPCache;
	if($cache->InitCache($CACHE_TIME, $CACHE_ID))
	{
		$vars = $cache->GetVars();
		CIBlock::ShowPanel($vars["IBLOCK_ID"], $ID);
		$APPLICATION->SetTitle($vars["NAME"]);
		$APPLICATION->AddChainItem($vars["IBLOCK_NAME"], $vars["LIST_PAGE_URL"]);
		$cache->Output();
	}
	else
	{
		if(CModule::IncludeModule("iblock")):
			if($arIBlockElement = GetIBlockElement($ID, $IBLOCK_TYPE)):
				CIBlock::ShowPanel($arIBlockElement["IBLOCK_ID"], $ID, 0, $IBLOCK_TYPE);
				$APPLICATION->SetTitle($arIBlockElement["NAME"]);
				$APPLICATION->AddChainItem($arIBlockElement["IBLOCK_NAME"], $arIBlockElement["LIST_PAGE_URL"]);
				$cache->StartDataCache();
			?>
				<?if ($arIBlockElement["ACTIVE_FROM"]):?><font class="newsdata"><?echo $arIBlockElement["ACTIVE_FROM"]?>&nbsp;</font><?endif;?>
				<font class="text">
				<?if ($arIBlockElement["DETAIL_PICTURE"]):?>
					<table cellpadding="0" cellspacing="0" border="0" align="left">
						<tr>
							<td><?echo ShowImage($arIBlockElement["DETAIL_PICTURE"], 250, 250, "hspace='0' vspace='2' align='left' border='0'", "", true, GetMessage("T_NEWS_DETAIL_ENLARGE_IMG"));?></td>
							<td valign="top" width="0%"><img src="/bitrix/images/1.gif" width="10" height="1"></td>
						</tr>
					</table>
				<?endif;?>
				<?echo $arIBlockElement["DETAIL_TEXT"];?>
				<p><a href="<?echo $arIBlockElement["LIST_PAGE_URL"]?>"><?echo GetMessage("T_NEWS_DETAIL_BACK")?></a></p>
				</font>
			<?
				$vars = Array(
					"IBLOCK_ID"=>$arIBlockElement["IBLOCK_ID"],
					"NAME"=>$arIBlockElement["NAME"],
					"IBLOCK_NAME"=>$arIBlockElement["IBLOCK_NAME"],
					"LIST_PAGE_URL"=>$arIBlockElement["LIST_PAGE_URL"]
					);

				$cache->EndDataCache($vars);
			else:
				echo ShowError(GetMessage("T_NEWS_DETAIL_NF"));
				@define("ERROR_404", "Y");
			endif;
		else:
			echo ShowError(GetMessage("T_NEWS_DETAIL_MODULE_NA"));
		endif;
	}
}
else
{
	$APPLICATION->SetTitle(GetMessage("T_NEWS_DETAIL_ERROR"));
	echo ShowError(GetMessage("T_NEWS_DETAIL_PERM_DEN"));
}
?>