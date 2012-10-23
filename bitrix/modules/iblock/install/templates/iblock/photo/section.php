<?
/**************************************************************************
Component "Photos of the group".

This component is intended for displaying photos for one of the group in table order. Mainly used on the page that displays conent of some photogallery groups.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/photo/section.php", Array(
	"IBLOCK_TYPE"			=> "photo",
	"IBLOCK_ID"				=> "8",
	"SECTION_ID"			=> $_REQUEST["SECTION_ID"],
	"PAGE_ELEMENT_COUNT"	=> "50",
	"LINE_ELEMENT_COUNT"	=> "3",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"FILTER_NAME"			=> "arrFilter",
	"CACHE_FILTER"			=> "N", 
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information Block type
IBLOCK_ID - Inf. block ID
SECTION_ID - Group ID
PAGE_ELEMENT_COUNT - Number of photos on the page
LINE_ELEMENT_COUNT - Number of photos displayed in each table row 
ELEMENT_SORT_FIELD - by which field the elements will be sorted, the following values can be used:

	shows - average number of photo views (popularity)
	sort - by sorting index
	timestamp_x - by modification date
	name - by title
	id - by element ID
	active_from - by activity date FROM
	active_to - by activity date TILL

ELEMENT_SORT_ORDER - element sorting order, following values can be used:

	asc - in ascending order
	desc - in descending order

arrPROPERTY_CODE - array of mnemonic codes for the information block properties
PRICE_CODE- mnemonic code of the price type
BASKET_URL - URL to the page with the customer's basket
FILTER_NAME - name of an array with values for filtering of the photos 
CACHE_FILTER - [Y|N] cache or not cache the values selected from the database if filter was set with the use of them?
CACHE_TIME - (sec.) time for caching of the values selected from database

***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):
	
	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Processing of received parameters
	*************************************************************************/

	$LINE_ELEMENT_COUNT = intval($LINE_ELEMENT_COUNT);
	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	$SECTION_ID = (intval($SECTION_ID)>0 ? intval($SECTION_ID) : false);
	global $$FILTER_NAME;
	$arrFilter = ${$FILTER_NAME};
	$CACHE_FILTER = ($CACHE_FILTER=="Y") ? "Y" : "N";
	if ($CACHE_FILTER=="N" && count($arrFilter)>0) $CACHE_TIME = 0;

	/*************************************************************************
								Work with cache
	*************************************************************************/

	$CACHE_ID = __FILE__.md5(serialize($arParams).serialize($arrFilter).$USER->GetGroups().CDBResult::NavStringForCache($PAGE_ELEMENT_COUNT));
	$obCache = new CPHPCache;
	if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		$arVars = $obCache->GetVars();

		$SECTION_ID		= $arVars["SECTION_ID"];
		$SECTION_NAME	= $arVars["SECTION_NAME"];
		$IBLOCK_ID		= $arVars["IBLOCK_ID"];
		$IBLOCK_TYPE	= $arVars["IBLOCK_TYPE"];
		$ELEMENT_NAME	= $arVars["ELEMENT_NAME"];
	}
	else
	{
		//$arSection = GetIblockSection($SECTION_ID);
		$res = CIBlockSection::GetList(Array(),Array("IBLOCK_ID"=>$IBLOCK_ID,"ID"=>$SECTION_ID,"ACTIVE"=>"Y"));
		$arSection = $res->GetNext();

		$arIBlock = GetIBlock($arSection["IBLOCK_ID"]);
		$arIblockType = CIBlockType::GetByIDLang($arIBlock["IBLOCK_TYPE_ID"], LANGUAGE_ID);

		$SECTION_ID		= $arSection["ID"];
		$SECTION_NAME	= $arSection["NAME"];
		$IBLOCK_ID		= $arSection["IBLOCK_ID"];
		$IBLOCK_TYPE	= $arIBlock["IBLOCK_TYPE_ID"];
		$ELEMENT_NAME	= $arIblockType["ELEMENT_NAME"];
		
	}
	if (intval($SECTION_ID)>0) : 
		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, 0, $SECTION_ID, $IBLOCK_TYPE);
		$APPLICATION->SetTitle($SECTION_NAME);
		$APPLICATION->AddChainItem($SECTION_NAME, $APPLICATION->GetCurPage()."?SECTION_ID=".$SECTION_ID);

		if($obCache->StartDataCache()):

			/************************************
						Elements
			************************************/

			// list of the element fields that will be used in selection
			$arSelect = array(
				"ID",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"NAME", 
				"PREVIEW_PICTURE",
				"DETAIL_PICTURE",
				"DETAIL_PAGE_URL"
				);

			// adding the filter with some values
			$arrFilter["ACTIVE"] = "Y";
			$arrFilter["SECTION_ID"] = $SECTION_ID;

			if ($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, $IBLOCK_ID, false, array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "ASC"), false, $arrFilter, $arSelect)):
				$rsElements->NavStart($PAGE_ELEMENT_COUNT);
				$count = intval($rsElements->SelectedRowsCount());

				/****************************************************************
										HTML form
				****************************************************************/

		?>
		<?if ($count>0):?><p><?echo $rsElements->NavPrint($ELEMENT_NAME)?></p><?endif;?>
		<table cellpadding="10" cellspacing="0" border="0" width="80%">	
			<tr>
				<?
				$n=1;
				$cell = 0;
				while ($obElement = $rsElements->GetNextElement()):
					$cell++;
					$arElement = $obElement->GetFields();
					$image1 = intval($arElement["PREVIEW_PICTURE"])<=0 ? $arElement["DETAIL_PICTURE"] : $arElement["PREVIEW_PICTURE"];
					$image2 = intval($arElement["DETAIL_PICTURE"])<=0 ? $arElement["PREVIEW_PICTURE"] : $arElement["DETAIL_PICTURE"];
				?>
				<td valign="top" width="<?=(100/$LINE_ELEMENT_COUNT)?>%">
					<table cellpadding="2" cellspacing="0" border="0">
						<tr>
							<td valign="top"><?echo CFile::Show2Images($image1, $image2, 150, 150, "hspace='0' vspace='0' border='0' title='".$arElement["NAME"]."'", true);?></td>
						</tr>
						<tr>
							<td valign="top"><font class="text"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></font></td>
						</tr>
					</table></td>
					<?
					if($n%$LINE_ELEMENT_COUNT == 0):
						$cell = 0;
					?>
			</tr>
			<tr>
			<?
					endif; // if($n%$LINE_ELEMENT_COUNT == 0):
					$n++;
				endwhile; // while ($obElement = $rsElements->GetNextElement()):

				while ($cell<$LINE_ELEMENT_COUNT):
					$cell++;
					?><td>&nbsp;</td><?
				endwhile;
				?>
			</tr>
		</table>
		<?if ($count>0):?><p><?echo $rsElements->NavPrint($ELEMENT_NAME)?></p><?endif;?>
		<?
			endif; // if ($rsElements = GetIBlockElementListEx
			$obCache->EndDataCache(array(
				"SECTION_ID"	=> $SECTION_ID,
				"SECTION_NAME"	=> $SECTION_NAME,
				"IBLOCK_ID"		=> $IBLOCK_ID,
				"IBLOCK_TYPE"	=> $IBLOCK_TYPE,
				"ELEMENT_NAME"	=> $ELEMENT_NAME
				));
		endif; // if($obCache->StartDataCache()):

	else:
			ShowError(GetMessage("PHOTO_SECTION_NOT_FOUND"));
	endif;
endif;
?>