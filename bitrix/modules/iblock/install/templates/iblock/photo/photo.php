<?
/**************************************************************************
Component "Detailed photo".

This component is intended for displaying complete information for each single photo. Additional links "Next" and "Previous" are also displayed for the photo.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/photo/photo.php", Array(
	"IBLOCK_TYPE"			=> "photo",
	"IBLOCK_ID"				=> "8",
	"ELEMENT_ID"			=> $_REQUEST["ID"],
	"SECTION_URL"			=> "/about/gallery/section.php?",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - information block type
IBLOCK_ID - Information block ID
ELEMENT_ID - Photo ID
SECTION_URL - URL to the page with Photogallery elements list
ELEMENT_SORT_FIELD - By which fields the photos will be sorted (for the Next and Previous links), following values can be used:

	shows - average number of photo views (popularity)
	sort - by sorting index 
	timestamp_x - by modification date
	name - by title
	id - by element ID
	active_from - by activity date FROM
	active_to - by activity date TILL

ELEMENT_SORT_ORDER - photos sorting order (for the Next and Previous links), following values can be used:

	asc - in ascending order
	desc - in descending order

CACHE_TIME - (sec.) time for caching values selected from database

***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):
	
	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Processing of the received parameters
	*************************************************************************/

	$ELEMENT_ID = intval($ELEMENT_ID);

	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	/*************************************************************************
								Work with cache
	*************************************************************************/
	$found = "N";
	$obCache = new CPHPCache;
	$CACHE_ID = __FILE__.md5(serialize($arParams).$USER->GetGroups());
	if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		$arVars = $obCache->GetVars();
		$arElement = $arVars["arElement"];
		$arProperty = $arVars["arProperty"];
		$arSection = $arVars["arSection"];
		$arrPath = $arVars["arrPath"];
		$found = "Y";
	}
	else
	{
		if ($ELEMENT_ID>0)
		{
			$arSelect = array(
				"ID",
				"NAME",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"PROPERTY_*",
				"DETAIL_PICTURE",
				"PREVIEW_PICTURE",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"PREVIEW_TEXT",
				"SECTION_PAGE_URL",
				"PREVIEW_TEXT_TYPE",
			);
			if($rsElement = GetIBlockElementListEx($IBLOCK_TYPE, false, false, array(), false, array("ID" => $ELEMENT_ID), $arSelect))
			{
				if ($obElement = $rsElement->GetNextElement()) 
				{
					$found = "Y";
					$arElement = $obElement->GetFields();			
					$arProperty = $obElement->GetProperties();
				}
			}

			if($rsSection = CIBlockSection::GetList(Array(),Array("IBLOCK_ID"=>$IBLOCK_ID,"ID"=>$arElement["IBLOCK_SECTION_ID"],"ACTIVE"=>"Y")))
			{
				$arSection = $rsSection->GetNext();
			}

			$arrPath = array();
			$rsPath = GetIBlockSectionPath($IBLOCK_ID, $arElement["IBLOCK_SECTION_ID"]);
			while($arPath=$rsPath->GetNext()) $arrPath[] = array("ID" => $arPath["ID"], "NAME" => $arPath["NAME"]);
		}
	}

	// if element has been found then
	if ($found=="Y"):

		$ELEMENT_NAME = $arElement["NAME"];
		$IBLOCK_ID = $arElement["IBLOCK_ID"];
		$SECTION_ID = $arElement["IBLOCK_SECTION_ID"];
		$KEYWORDS = $arProperty["KEYWORDS"]["VALUE"];
		$DESCRIPTION = $arProperty["DESCRIPTION"]["VALUE"];
		$SECTION_NAME = is_array($arSection)?$arSection["NAME"]:"";

		$APPLICATION->SetPageProperty("keywords", $KEYWORDS);
		$APPLICATION->SetPageProperty("description", $DESCRIPTION);
		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, $ELEMENT_ID, $SECTION_ID, $IBLOCK_TYPE);
		$APPLICATION->SetTitle($ELEMENT_NAME);
		if (is_array($arrPath))
		{
			while(list($key, $arS) = each($arrPath))
			{
				if ($SECTION_ID==$arS["ID"]) $SECTION_NAME = $arS["NAME"];
				$APPLICATION->AddChainItem($arS["NAME"], $SECTION_URL."&SECTION_ID=".$arS["ID"]);
			}
		}
		CIBlockElement::CounterInc($ELEMENT_ID);

		if($obCache->StartDataCache()):

			// get the values for the Next and Previous links
			$arSelect = array(
				"ID",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"DETAIL_PAGE_URL"
				);
			if ($rs = GetIBlockElementList($IBLOCK_ID, $SECTION_ID, array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "ASC"), 0, array("ACTIVE" => "Y"), $arSelect))
			{
				while($ar = $rs->GetNext())
				{
					if ($end=="Y")
					{
						$next_url = $ar["DETAIL_PAGE_URL"];
						break;
					}
					if ($ar["ID"]==$ELEMENT_ID) $end = "Y";
					else $prev_url = $ar["DETAIL_PAGE_URL"];
				}
			}
			?><p><font class="smalltext"><?
			if (strlen($prev_url)>0):
				?><font class="smalltext">&lt;&lt;&nbsp;</font><a class="smalltext" href="<?=$prev_url?>"><?=GetMessage("PHOTO_PREV")?></a><?
			endif;
			if (strlen($prev_url)>0 && strlen($next_url)>0)
				echo "<font class=\"smalltext\">&nbsp;|&nbsp;</font>";
			if (strlen($next_url)>0):
				?><a class="smalltext" href="<?=$next_url?>"><?=GetMessage("PHOTO_NEXT")?></a><font class="smalltext">&nbsp;&gt;&gt;</font><?
			endif;
			?></font></p><?

			/****************************************************************
									HTML form
			****************************************************************/
			?>

			<table width="70%" border="0" cellspacing="0" cellpadding="7">
				<tr>
					<td width="0%" valign="top" nowrap><?
						$image = intval($arElement["DETAIL_PICTURE"])<=0 ? $arElement["PREVIEW_PICTURE"] : $arElement["DETAIL_PICTURE"];
						echo ShowImage($image, 400, 400, "hspace='0' vspace='0' border='0'", "", true, GetMessage("PHOTO_ENLARGE"));
					?></td>
				</tr>
			</table>
			<?if (strlen($arElement["PREVIEW_TEXT"])>0):?>
				<font class="text"><p><?=$arElement["PREVIEW_TEXT"]?></p></font>
			<?endif;?>
			<?if (strlen($arElement["DETAIL_TEXT"])>0):?>
				<font class="text"><p><?
					echo ($arElement["DETAIL_TEXT_TYPE"]=="html") ? $arElement["DETAIL_TEXT"] : TxtToHTML($arElement["~DETAIL_TEXT"]);
				?></p></font>
			<?endif;?>

			<font class="text"><a href="<?=is_array($arSection)?$arSection["SECTION_PAGE_URL"]:""?>"><?=GetMessage("PHOTO_BACK")?></a></font>
			<?

			$obCache->EndDataCache(array(
				"arElement"	=> $arElement,
				"arProperty"	=> $arProperty,
				"arSection"	=> $arSection,
				"arrPath"	=> $arrPath,
				));
		endif;
	else:
		ShowError(GetMessage("PHOTO_ELEMENT_NOT_FOUND"));
		@define("ERROR_404", "Y");
	endif;
endif;
?>