<?
/**************************************************************************
Component "Catalog item in details".

This component is intended for displaying the complete information on the catalog element.

Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/element.php", Array(
	"IBLOCK_TYPE"		=> "catalog",
	"IBLOCK_ID"			=> "21",
	"ELEMENT_ID"		=> $_REQUEST["ID"],
	"SECTION_URL"		=> "/catalog/phone/section.php?",
	"LINK_IBLOCK_TYPE"	=> "catalog",
	"LINK_IBLOCK_ID"	=> "22",
	"LINK_PROPERTY_SID"	=> "PHONE_ID",
	"LINK_ELEMENTS_URL"	=> "/catalog/accessory/byphone.php?",
	"arrFIELD_CODE"		=> Array(
		"DETAIL_TEXT",
		"DETAIL_PICTURE"
		),
	"arrPROPERTY_CODE"	=> Array(
		"YEAR",
		"STANDBY_TIME",
		"TALKTIME",
		"WEIGHT",
		"STANDART",
		"SIZE",
		"BATTERY",
		"SCREEN",
		"WAP",
		"VIBRO",
		"VOICE",
		"PC",
		"MORE_PHOTO",
		"MANUAL",
		),
	"CACHE_TIME"		=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID
ELEMENT_ID - Element ID
SECTION_URL - URL that leads to the list of elements for the group
LINK_IBLOCK_TYPE - Type of the Information block with linked elements
LINK_IBLOCK_ID - ID of the Information block with linked elements
LINK_PROPERTY_SID - Mnemonic code of the property where Linkage information is stored
LINK_ELEMENTS_URL - URL of the page where the linked elements will be displayed
arrFIELD_CODE - array of the field identifiers, the following values can be used:

	DETAIL_PICTURE - picture for the element detailed description
	DETAIL_TEXT - text for the element detailed description
	PREVIEW_PICTURE - picture for the element anounce
	PREVIEW_TEXT - text for the element anounce

arrPROPERTY_CODE - array of selected property mnemonic codes; predefined properties:

	MORE_PHOTO - if property is specified the table of the additional photos for the element will be displayed
	MANUAL - if property is specified the link to the downloadable file will be displayed

CACHE_TIME - (sec.) time for caching (0 - do not cache)

***************************************************************************/

global $USER, $APPLICATION;
if(CModule::IncludeModule("iblock")):

	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Processing of the received parameters
	*************************************************************************/

	$arrFIELD_CODE = (is_array($arrFIELD_CODE)) ? $arrFIELD_CODE : array("DETAIL_PICTURE", "DETAIL_TEXT", "DETAIL_TEXT_TYPE");
	$arrPROPERTY_CODE = (is_array($arrPROPERTY_CODE)) ? $arrPROPERTY_CODE : array();
	$ELEMENT_ID = intval($ELEMENT_ID);
	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	/*************************************************************************
								Work with cache
	*************************************************************************/

	$WF_SHOW_HISTORY = "N";
	$found = "N";
	$obCache = new CPHPCache;
	$CACHE_ID = __FILE__.md5(serialize($arParams).$USER->GetGroups())."_".(($_REQUEST["show_workflow"]=="Y") ? "Y" : "N");

	if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		$arVars = $obCache->GetVars();
		$ELEMENT_NAME	= $arVars["ELEMENT_NAME"];
		$IBLOCK_ID		= $arVars["IBLOCK_ID"];
		$SECTION_ID		= $arVars["SECTION_ID"];
		$SECTION_NAME	= $arVars["SECTION_NAME"];
		$KEYWORDS		= $arVars["KEYWORDS"];
		$DESCRIPTION	= $arVars["DESCRIPTION"];
		$arrPath		= $arVars["arrPath"];
		$arElement	= $arVars["arElement"];
		$arProperty	= $arVars["arProperty"];
		$WF_SHOW_HISTORY = $arVars["WF_SHOW_HISTORY"];
		$found = "Y";
	}
	else
	{
		if($ELEMENT_ID>0)
		{
			$WF_SHOW_HISTORY = "N";
			if (($_REQUEST["show_workflow"] == "Y") && CModule::IncludeModule("workflow"))
			{
				$WF_ELEMENT_ID = CIBlockElement::WF_GetLast($ELEMENT_ID);

				$WF_STATUS_ID = CIBlockElement::WF_GetCurrentStatus($WF_ELEMENT_ID, $WF_STATUS_TITLE);
				$WF_STATUS_PERMISSION = CIBlockElement::WF_GetStatusPermission($WF_STATUS_ID);

				if ($WF_STATUS_ID == 1 || $WF_STATUS_PERMISSION < 1)
					$WF_ELEMENT_ID = $ELEMENT_ID;
				else
					$WF_SHOW_HISTORY = "Y";

				$ELEMENT_ID = $WF_ELEMENT_ID;
			}

			$arSelect = array(
				"ID",
				"NAME",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"DETAIL_TEXT_TYPE",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_PICTURE",
				"PREVIEW_PICTURE",
				);
			$arSelect = array_merge($arSelect, $arrFIELD_CODE);

			$arFilter = array(
				"ID" => $ELEMENT_ID,
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
					$KEYWORDS = $arProperty["KEYWORDS"]["VALUE"];
					$DESCRIPTION = $arProperty["DESCRIPTION"]["VALUE"];
					$ELEMENT_NAME = $arElement["NAME"];
					$IBLOCK_ID = $arElement["IBLOCK_ID"];
					$SECTION_ID = $arElement["IBLOCK_SECTION_ID"];
					if($rsSection = CIBlockSection::GetByID($SECTION_ID))
					{
						$arSection = $rsSection->Fetch();
						$SECTION_NAME = $arSection["NAME"];
					}
				}
			}

			$arrPath = array();
			$rsPath = GetIBlockSectionPath($IBLOCK_ID, $SECTION_ID);
			while($arPath=$rsPath->GetNext()) $arrPath[] = array("ID" => $arPath["ID"], "NAME" => $arPath["NAME"]);
		}
	}

	// if element is found then
	if($found=="Y"):

		$APPLICATION->SetPageProperty("keywords", $KEYWORDS);
		$APPLICATION->SetPageProperty("description", $DESCRIPTION);
		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, $ELEMENT_ID, $SECTION_ID, $IBLOCK_TYPE);
		$APPLICATION->SetTitle($ELEMENT_NAME);
		if(is_array($arrPath))
		{
			while(list($key, $arS) = each($arrPath))
			{
				if($SECTION_ID==$arS["ID"]) $SECTION_NAME = $arS["NAME"];
				$APPLICATION->AddChainItem($arS["NAME"], $SECTION_URL."SECTION_ID=".$arS["ID"]);
			}
		}

		CIBlockElement::CounterInc($ELEMENT_ID);

		if($WF_SHOW_HISTORY == "Y" || $obCache->StartDataCache()):

			/****************************************************************
						HTML form
			****************************************************************/
			?>
			<table width="70%" border="0" cellspacing="0" cellpadding="7">
				<tr>
					<?
					if(in_array("PREVIEW_PICTURE", $arrFIELD_CODE))
					{
						$image = intval($arElement["PREVIEW_PICTURE"])>0 ? $arElement["PREVIEW_PICTURE"] : $arElement["DETAIL_PICTURE"];
					}
					if(in_array("DETAIL_PICTURE", $arrFIELD_CODE))
					{
						$image = intval($arElement["DETAIL_PICTURE"])>0 ? $arElement["DETAIL_PICTURE"] : $arElement["PREVIEW_PICTURE"];
					}

					if(strlen($LINK_PROPERTY_SID)>0 && strlen($LINK_IBLOCK_TYPE)>0 && intval($LINK_IBLOCK_ID)>0):
						if($rsLinkElements = GetIBlockElementListEx($LINK_IBLOCK_TYPE, $LINK_IBLOCK_ID, Array(), Array(), false, Array("PROPERTY_".$LINK_PROPERTY_SID=>$ELEMENT_ID), Array("ID"))):
							$linked_count = $rsLinkElements->SelectedRowsCount();
						endif;
					endif;

					if(intval($image)>0):
						?>
						<td width="0%" valign="top" nowrap><?
						echo ShowImage($image, 400, 400, "hspace='0' vspace='2' border='0'", "", true, GetMessage("CATALOG_ENLARGE"));

						// link "More photo"
						if(isset($arProperty["MORE_PHOTO"]["VALUE"]) && is_array($arProperty["MORE_PHOTO"]["VALUE"])) :
							?><br><font class="text"><a href="#more_photo"><?=GetMessage("CATALOG_MORE_PHOTO")?></a></font><?
						endif;

						?></td>
					<?endif;?>
					<td width="100%" valign="top"><?

					if(is_array($arrPROPERTY_CODE) && count($arrPROPERTY_CODE)>0):
						foreach($arrPROPERTY_CODE as $pid):
							if($pid!="MORE_PHOTO" && $pid!="FORUM_TOPIC_ID" && $pid!="KEYWORDS" && $pid!="DESCRIPTION"):
								if(is_array($arProperty[$pid]["VALUE"]) && count($arProperty[$pid]["VALUE"])>0 || !is_array($arProperty[$pid]["VALUE"]) && strlen($arProperty[$pid]["VALUE"])>0):
								?>
								<table cellpadding="1" cellspacing="0" border="0">
									<tr>
										<td valign="top" nowrap><font class="smalltext"><?=$arProperty[$pid]["NAME"]?>:&nbsp;</font></td>
										<td valign="top"><font class="smalltextblack"><?
											if  ($pid=="MANUAL") :
												?><a href="<?=$arProperty[$pid]["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
											else:
												echo (is_array($arProperty[$pid]["VALUE"])) ? implode("<br>",$arProperty[$pid]["VALUE"]) : $arProperty[$pid]["VALUE"];
											endif;
											?></font></td>
									</tr>
								</table>
								<?
								endif;
							endif;
						endforeach;
					endif;

					?></td>
				</tr>
			</table>
			<?
			// link to the linked elements from the other information block
			if($linked_count>0):
				?><font class="text"><b><a href="<?=$LINK_ELEMENTS_URL?>PARENT_ELEMENT_ID=<?=$ELEMENT_ID?>"><?=GetMessage("CATALOG_LINK_ELEMENTS")?></a></b> (<?=$linked_count?>)</font><?
			endif;

			if(strlen($arElement["PREVIEW_TEXT"])>0):?>
				<font class="text"><p><?=$arElement["PREVIEW_TEXT"]?></p></font>
			<?endif;?>
			<?if(strlen($arElement["DETAIL_TEXT"])>0):?>
				<font class="text"><p><?
					echo ($arElement["DETAIL_TEXT_TYPE"]=="html") ? $arElement["DETAIL_TEXT"] : TxtToHTML($arElement["~DETAIL_TEXT"]);
				?></p></font>
			<?endif;?>

			<?
			// additional photos
			$LINE_ELEMENT_COUNT = 2; // number of elements in a row
			if(isset($arProperty["MORE_PHOTO"]["VALUE"]) && is_array($arProperty["MORE_PHOTO"]["VALUE"])) :
				?><a name="more_photo"></a>
				<table cellpadding="20" cellspacing="0" border="0" width="0%">
					<tr>
						<?
						$n=1;
						$cell = 0;
						foreach ($arProperty["MORE_PHOTO"]["VALUE"] as $key=>$value):
							$cell++;
						?>
						<td valign="top" width="<?=(100/$LINE_ELEMENT_COUNT)?>%"><?echo ShowImage($value, 400, 400, "hspace='0' vspace='0' border='0' title=''", "", true);?></td>
						<?
							if($n%$LINE_ELEMENT_COUNT == 0):
								$cell = 0;
						?>
					</tr>
					<tr>
						<?
							endif;
							$n++;
						endforeach;

						while ($cell<$LINE_ELEMENT_COUNT):
							$cell++;
							?><td>&nbsp;</td><?
						endwhile;
						?>
					</tr>
				</table><?

			endif;

			?>
			<?if(strlen($SECTION_URL)>0 && intval($SECTION_ID)>0):?>
			<font class="text"><a href="<?=$SECTION_URL?>SECTION_ID=<?=$SECTION_ID?>"><?=GetMessage("CATALOG_BACK")?></a></font>
			<?endif?>
			<?
			if ($WF_SHOW_HISTORY != "Y")
			{
				$obCache->EndDataCache(array(
					"ELEMENT_NAME"	=> $ELEMENT_NAME,
					"IBLOCK_ID"		=> $IBLOCK_ID,
					"SECTION_ID"	=> $SECTION_ID,
					"SECTION_NAME"	=> $SECTION_NAME,
					"KEYWORDS"		=> $KEYWORDS,
					"DESCRIPTION"	=> $DESCRIPTION,
					"arrPath"		=> $arrPath,
					"arElement"	=> $arElement,
					"arProperty"	=> $arProperty,
					"WF_SHOW_HISTORY" => $WF_SHOW_HISTORY
					));
			}
		endif;
	else:
		ShowError(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
		@define("ERROR_404", "Y");
	endif;
endif;
?>
