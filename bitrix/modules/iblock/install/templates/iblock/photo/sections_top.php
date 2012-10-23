<?
/**************************************************************************
Component "Groups with top selected elements of the Photogallery".

This component is intended for displaying selected top elements for each group in the Photogallery in table order. Mainly is used on the main page in Photogallery site section.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/photo/sections_top.php", Array(
	"IBLOCK_TYPE"			=> "photo",
	"IBLOCK_ID"				=> "8",
	"PARENT_SECTION_ID"		=> "0",
	"SECTION_SORT_FIELD"	=> "sort",
	"SECTION_SORT_ORDER"	=> "asc",
	"SECTION_COUNT"			=> "20",
	"SECTION_URL"			=> "/about/gallery/section.php?",
	"ELEMENT_COUNT"			=> "9",
	"LINE_ELEMENT_COUNT"	=> "3",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"FILTER_NAME"			=> "arrFilter",
	"CACHE_FILTER"			=> "N", 
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information Block type
IBLOCK_ID - Information Block ID
PARENT_SECTION_ID - Parent group ID
SECTION_SORT_FIELD - By which field the groups will be sorted, the following values can be used:

	sort - by sorting index value
	timestamp_x - by modification date of the group parameters
	name - by the group title
	id - by group ID
	depth_level - depth level for the group

SECTION_SORT_ORDER - group sorting order, the following values can be used:

	asc - in ascending order
	desc - in descending order

SECTION_COUNT - maximum number of the displayed groups
SECTION_URL - URL that leads to the page with a group elements
ELEMENT_COUNT - maximum number of elements displayed for each group
LINE_ELEMENT_COUNT - number of elements displayed in every table row
ELEMENT_SORT_FIELD - by which fields the elements will be sorted, the following values can be used:

	shows - average number of element views (element popularity)
	sort - by sorting index
	timestamp_x - by modification date
	name - by title
	id - by element ID
	active_from - by activity date FROM
	active_to - by activity date TILL

ELEMENT_SORT_ORDER - element sorting order, following values can be used:

	asc - in ascending order
	desc - in descending order

FILTER_NAME - name of an array with values for elements filtering
CACHE_FILTER - [Y|N] use or not use cache for values selected from database if filter was set with this values?
CACHE_TIME - (сек.) time for caching values selected from database

***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):
	
	IncludeTemplateLangFile(__FILE__);
	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	if ($bDisplayPanel)
		CIBlock::ShowPanel($IBLOCK_ID, 0, 0, $IBLOCK_TYPE);
	
	/*************************************************************************
						Processing of received parameters
	*************************************************************************/

	$LINE_ELEMENT_COUNT = intval($LINE_ELEMENT_COUNT);
	global $$FILTER_NAME;
	$arrFilter = ${$FILTER_NAME};
	$CACHE_FILTER = ($CACHE_FILTER=="Y") ? "Y" : "N";
	if ($CACHE_FILTER=="N" && count($arrFilter)>0) $CACHE_TIME = 0;

	/*************************************************************************
								Work with cache
	*************************************************************************/

	$CACHE_ID = __FILE__.md5(serialize($arParams).serialize($arrFilter).$USER->GetGroups());
	$obCache = new CPHPCache;
	if($obCache->StartDataCache($CACHE_TIME, $CACHE_ID, "/")):

		/************************************
						Groups
		************************************/

		$rsSections = GetIBlockSectionList($IBLOCK_ID, $PARENT_SECTION_ID, array($SECTION_SORT_FIELD => $SECTION_SORT_ORDER, "ID" => "ASC"), $SECTION_COUNT, array("ACTIVE"=>"Y"));
		$arrProp = "";
		while ($arSection = $rsSections->GetNext()) :

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

			// adding values to the filter
			$arrFilter["ACTIVE"] = "Y";
			$arrFilter["IBLOCK_ID"] = $IBLOCK_ID;
			$arrFilter["SECTION_ID"] = $arSection["ID"];

			if ($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, false, false, array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "ASC"), array("nTopCount"=>$ELEMENT_COUNT), $arrFilter, $arSelect)):
				$rsElements->NavStart($ELEMENT_COUNT);
				$count = intval($rsElements->SelectedRowsCount());
				if ($count>0):

		/****************************************************************
								HTML form
		****************************************************************/
		?>
		<a class="subtitletext" href="<?=$arSection["SECTION_PAGE_URL"]?>"><?echo htmlspecialchars($arSection["NAME"])?></a><br><img height="10" src="/bitrix/images/1.gif" width="1"><br>
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
					?>
					<td>&nbsp;</td>
					<?
				endwhile;
				?>
			</tr>
		</table>
		<hr><img src="/bitrix/images/1.gif" width="1" height="15" border="0" title=""><br>
		<?
				endif; // if ($count>0):
			endif; // if ($rsElements = GetIBlockElementListEx
		endwhile;
		$obCache->EndDataCache();
	endif;
endif;
?>