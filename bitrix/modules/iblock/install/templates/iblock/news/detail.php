<?
/**************************************************************************
Component Detailed News

This component is intended for displaying the detailed information for every news.

Sample of usage:

$APPLICATION->IncludeFile("iblock/news/detail.php", Array(
	"ID"	=>	$_REQUEST["ID"],
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"1",
	"arrPROPERTY_CODE"	=>	Array(
					"AUTHOR",
					"SOURCE"
				),
	"LIST_PAGE_URL"	=> "",
	"CACHE_TIME"	=>	"0",
	));

Parameters:

	ID - Element ID
 	IBLOCK_TYPE - Information block type (will be used for check purposes only)
	IBLOCK_ID - Information block ID
	arrPROPERTY_CODE - array of property mnemonic codes
	CACHE_TIME - (sec.) time for cacheing (0 - do not cache)

**************************************************************************/

IncludeTemplateLangFile(__FILE__);
global $USER, $APPLICATION;

if(CModule::IncludeModule("iblock")):

	$ID = (isset($ID) ? $ID : $_REQUEST["ID"]);
	$IBLOCK_TYPE = (isset($IBLOCK_TYPE) ? $IBLOCK_TYPE : "news");
	if($IBLOCK_TYPE=="-")
		$IBLOCK_TYPE = "";

	$INCLUDE_IBLOCK_INTO_CHAIN = ($INCLUDE_IBLOCK_INTO_CHAIN == "N" ? $INCLUDE_IBLOCK_INTO_CHAIN : "Y");

	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	$found = "N";
	$WF_SHOW_HISTORY = "N";

	$CACHE_TIME = intval($CACHE_TIME);
	$CACHE_ID = SITE_ID."|".$APPLICATION->GetCurPage()."|".md5(serialize($arParams))."|".$USER->GetGroups()."_".(($_REQUEST["show_workflow"]=="Y") ? "Y" : "N");

	if(CModule::IncludeModule("iblock"))
		CIBlockElement::CounterInc($ID);

	$cache = new CPHPCache;
	if($cache->InitCache($CACHE_TIME, $CACHE_ID))
	{
		$vars = $cache->GetVars();

		$IBLOCK_ID = $vars["IBLOCK_ID"];
		$NAME = $vars["NAME"];
		$CHAIN_NAME = $vars["CHAIN_NAME"];
		$ItemListPageURL = $vars["ItemListPageURL"];
		$META_KEYWORDS = $vars["META_KEYWORDS"];
		$META_DESCRIPTION = $vars["META_DESCRIPTION"];
		$arElement	= $vars["arElement"];
		$arProperty	= $vars["arProperty"];

		$WF_SHOW_HISTORY = $vars["WF_SHOW_HISTORY"];

		$found = "Y";
	}
	else
	{
		if($ID>0)
		{
			$WF_SHOW_HISTORY = "N";
			if (($_REQUEST["show_workflow"] == "Y") && CModule::IncludeModule("workflow"))
			{
				$WF_ID = CIBlockElement::WF_GetLast($ID);

				$WF_STATUS_ID = CIBlockElement::WF_GetCurrentStatus($WF_ID, $WF_STATUS_TITLE);
				$WF_STATUS_PERMISSION = CIBlockElement::WF_GetStatusPermission($WF_STATUS_ID);

				if ($WF_STATUS_ID == 1 || $WF_STATUS_PERMISSION < 1)
					$WF_ID = $ID;
				else
					$WF_SHOW_HISTORY = "Y";

				$ID = $WF_ID;
			}

			$arSelect = array(
				"ID",
				"NAME",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"DETAIL_TEXT_TYPE",
				"DETAIL_TEXT",
				"DETAIL_PICTURE",
				"ACTIVE_FROM",
				"LIST_PAGE_URL",
			);

			$arFilter = array(
				"ID" => $ID,
				"IBLOCK_LID" => SITE_ID,
				"IBLOCK_ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"IBLOCK_TYPE" => $IBLOCK_TYPE,
				"SHOW_HISTORY" => $WF_SHOW_HISTORY
			);

			if($rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect))
			{
				if($obElement = $rsElement->GetNextElement())
				{
					$found = "Y";
					$arElement = $obElement->GetFields();
					$arProperty = $obElement->GetProperties();

					$arIBlock = GetIBlock($arElement["IBLOCK_ID"], $IBLOCK_TYPE);

					$IBLOCK_ID = $arElement["IBLOCK_ID"];
					$NAME = $arElement["NAME"];
					$CHAIN_NAME = $arIBlock["NAME"];

					//$LIST_PAGE_URL = $arElement["LIST_PAGE_URL"];

					if (strlen(trim($LIST_PAGE_URL)) > 0)
						$ItemListPageURL = CIBlock::ReplaceDetailUrl($LIST_PAGE_URL, $arElement, true);
					else
						$ItemListPageURL = $arElement["LIST_PAGE_URL"];

					$META_KEYWORDS = $arProperty[$META_KEYWORDS]["VALUE"];
					$META_DESCRIPTION = $arProperty[$META_DESCRIPTION]["VALUE"];
				}
			}
		}
	}

	if ($found == "Y")
	{
		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, $ID);

		$APPLICATION->SetTitle($NAME);

		if ($INCLUDE_IBLOCK_INTO_CHAIN == "Y")
			$APPLICATION->AddChainItem($CHAIN_NAME, $LIST_PAGE_URL);

		if ($META_KEYWORDS && $META_KEYWORDS!="-")
			$APPLICATION->SetPageProperty("keywords", $META_KEYWORDS);

		if ($META_DESCRIPTION && $META_DESCRIPTION!="-")
			$APPLICATION->SetPageProperty("description", $META_DESCRIPTION);

		if($WF_SHOW_HISTORY == "Y" || $cache->StartDataCache()):

			if ($arElement["ACTIVE_FROM"]):?><font class="newsdata"><?echo $arElement["ACTIVE_FROM"]?>&nbsp;</font><?endif;?>
			<font class="text">
			<?if ($arElement["DETAIL_PICTURE"]):?>
				<table cellpadding="0" cellspacing="0" border="0" align="left">
					<tr>
						<td><?echo ShowImage($arElement["DETAIL_PICTURE"], 250, 250, "hspace='0' vspace='2' align='left' border='0'", "", true, GetMessage("T_NEWS_DETAIL_ENLARGE_IMG"));?></td>
						<td valign="top" width="0%"><img src="/bitrix/images/1.gif" width="10" height="1"></td>
					</tr>
				</table>
			<?endif;?>
			<?echo $arElement["DETAIL_TEXT"];?>
			</font>
			<?
			if (is_array($arrPROPERTY_CODE) && count($arrPROPERTY_CODE)>0):
				reset($arrPROPERTY_CODE);
				?><table cellpadding="1" cellspacing="0" border="0"><?
				foreach($arrPROPERTY_CODE as $pid):
					if ((is_array($arProperty[$pid]["VALUE"]) && count($arProperty[$pid]["VALUE"])>0) || (!is_array($arProperty[$pid]["VALUE"]) && strlen($arProperty[$pid]["VALUE"])>0)):
						?>
						<tr>
							<td valign="top" nowrap><font class="smalltext"><?=$arProperty[$pid]["NAME"]?>:&nbsp;</font></td>
							<td valign="top" nowrap><font class="smalltextblack"><? if(is_array($arProperty[$pid]["VALUE"]))
								echo implode("<br>",$arProperty[$pid]["VALUE"]);
							else{
								if(strpos($arProperty[$pid]["VALUE"], "http")!==false || strpos($arProperty[$pid]["VALUE"], "www")!==false){
									if(strpos($arProperty[$pid]["VALUE"], "http") === false)
										$site = "http://".$arProperty[$pid]["VALUE"];
									else
										$site = $arProperty[$pid]["VALUE"]; 
		
									if(IsModuleInstalled("statistic")){
										?><a href="/bitrix/redirect.php?event1=news_out&amp;event2=<?=$site;?>&amp;event3=<?=$arElement["NAME"]?>&amp;goto=<?=$site;?>" target="blank"><? echo $site;?></a><?
									}else{
										?><a href="<?=$site?>" target="blank"><? echo $site;?></a><?
									}
								}else{
									echo $arProperty[$pid]["VALUE"];
								}
							}
							?></font></td>
						</tr>
						<?
					endif;
				endforeach;
				?></table><?
			endif;
			?><p class="text"><a href="<?echo $ItemListPageURL?>"><?echo GetMessage("T_NEWS_DETAIL_BACK")?></a></p><?
			if ($WF_SHOW_HISTORY != "Y")
			{
				$cache->EndDataCache(array(
					"IBLOCK_ID"=>$IBLOCK_ID,
					"NAME"=>$NAME,
					"CHAIN_NAME"=>$CHAIN_NAME,
					"ItemListPageURL"=> $ItemListPageURL,
					"META_KEYWORDS" =>$META_KEYWORDS,
					"META_DESCRIPTION" => $META_DESCRIPTION,
					"arElement"	=> $arElement,
					"arProperty"	=> $arProperty,
					"WF_SHOW_HISTORY" => $WF_SHOW_HISTORY
				));
			}
		endif;
	}
	else
	{
		echo ShowError(GetMessage("T_NEWS_DETAIL_NF"));
		@define("ERROR_404", "Y");
	}
else:
	echo ShowError(GetMessage("T_NEWS_DETAIL_MODULE_NA"));
endif;
?>