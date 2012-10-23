<?
/*************************************************************************************************
Component "Two level groups structure (with element count for each group)".

This component is intended for displaying the list of catalog groups with nested sub levels with element count for each level. Mainly used on the catalog main page.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/sections_top_2.php", Array(
	"IBLOCK_TYPE"			=> "catalog",
	"IBLOCK_ID"				=> "22",
	"SECTION_SORT_FIELD"	=> "sort",
	"SECTION_SORT_ORDER"	=> "asc",
	"SECTION_URL"			=> "/catalog/accessory/section.php?",
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID
SECTION_SORT_FIELD - by which field the groups will be sorted, can be used the following values:

	sort - by sorting index
	timestamp_x - by modification date
	name - by group title
	id - by group ID
	depth_level - nesting lebvel of the group

SECTION_SORT_ORDER - Sorting order for information block groups, following values can be used:

		asc - in ascending order
		desc - in descending order

SECTION_URL - URL to the page with the group contents
CACHE_TIME - (sec.) time to cache the values selected from database

*************************************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):
	
	IncludeTemplateLangFile(__FILE__);

	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;
	if ($bDisplayPanel)
		CIBlock::ShowPanel($IBLOCK_ID, 0, 0, $IBLOCK_TYPE);

	/*************************************************************************
								Work with cache	
	*************************************************************************/

	$CACHE_ID = __FILE__.md5(serialize($arParams).serialize($arrFilter).$USER->GetGroups());
	$obCache = new CPHPCache;
	if($obCache->StartDataCache($CACHE_TIME, $CACHE_ID, "/")):

		if ($rsSections_top=GetIBlockSectionListWithCnt($IBLOCK_ID, 0, Array($SECTION_SORT_FIELD=>$SECTION_SORT_ORDER, "ID" => "DESC"), false, array("DEPTH_LEVEL" => 1))) :

			/****************************************************************
									HTML form
			****************************************************************/

			?>
			<table width="100%" border="0" cellspacing="0" cellpadding="4">
				<?
				while ($arSection_top=$rsSections_top->GetNext()) :
					$URL = $SECTION_URL."SECTION_ID=".$arSection_top["ID"];
				?>
				<tr>
					<td width="0%" valign="top"><?=ShowImage($arSection_top["PICTURE"], 100, 100, "hspace='2' vspace='2' align='left' border='0'", $URL);?></td>
					<td width="100%" valign="middle"><font class="tablebodytext"><a class="tablebodylink" href="<?=$URL?>"><b><?=$arSection_top["NAME"]?></b></a>&nbsp;(<?echo $arSection_top["ELEMENT_CNT"]?>)&nbsp;</font><br><font class="tablebodytext"><?
					if ($rsSections=GetIBlockSectionListWithCnt($IBLOCK_ID, $arSection_top["ID"], Array($SECTION_SORT_FIELD=>$SECTION_SORT_ORDER), false, array("DEPTH_LEVEL" => 2))) :
						$i=0;
						while ($arSection=$rsSections->GetNext()):
							$URL = $SECTION_URL."SECTION_ID=".$arSection["ID"];
							if ($i>0) echo ", ";
						?><a class="tablebodylink" href="<?=$URL?>"><?=$arSection["NAME"]?></a>&nbsp;(<?=$arSection["ELEMENT_CNT"]?>)<?
							$i++;
						endwhile;
					endif;
					?></font></td>
				</tr>
				<?
				endwhile;
				?>
			</table>
		<?
		endif;
		$obCache->EndDataCache(); 
	endif; 
endif;
?>