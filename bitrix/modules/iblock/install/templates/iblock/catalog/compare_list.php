<?
/**************************************************************************
Component "List of compared catalog items".

This component is intended for displaying the table containing the names of elements that are added to the Compare table. Enables to remove element from the Compare list or view the complete Compare table.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/compare_list.php", Array(
	"IBLOCK_TYPE"			=> "catalog",
	"IBLOCK_ID"				=> "21",
	"COMPARE_URL"			=> "/catalog/phone/compare.php",
	"NAME"					=> "CATALOG_COMPARE_LIST",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID
COMPARE_URL - URL that leads to the complete Compare table of the catalog items
NAME - unique identifier of the Compare list
ELEMENT_SORT_FIELD - Field for sorting the elements, following values can be used:

	shows - average number of element views
	sort - by sorting index
	timestamp_x - by modification date
	name - by title
	id - by element ID
	active_from - by activity date FROM
	active_to - by activity date TILL

ELEMENT_SORT_ORDER - Sorting order for information block elements, following values can be used:

		asc - in ascending order
		desc - in descending order

	CACHE_TIME - (sec.) time for caching (0 - do not cache)

***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):
	
	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Handling the Compare button
	*************************************************************************/

	if ($_REQUEST["action"]=="ADD_TO_COMPARE_LIST" && intval($_REQUEST["id"])>0) 
	{
		$_SESSION[$NAME][$IBLOCK_ID][$_REQUEST["id"]] = $_REQUEST["id"];
	}

	/*************************************************************************
						Handling the Remove link
	*************************************************************************/

	if ($_REQUEST["action"]=="DELETE_FROM_COMPARE_LIST" && intval($_REQUEST["id"])>0) 
	{
		unset($_SESSION[$NAME][$IBLOCK_ID][$_REQUEST["id"]]);
	}

	$arrCompareList = $_SESSION[$NAME][$IBLOCK_ID]; // array for storing the IDs of elements that are being campared

	?><a name="compare_list"></a><?
	if (is_array($arrCompareList) && count($arrCompareList)>0) :

		/*************************************************************************
									Work with cache
		*************************************************************************/

		$CACHE_ID = SITE_ID."|".__FILE__."|".md5(serialize($arParams)."|".serialize($arrCompareList)."|".$USER->GetGroups());
		$obCache = new CPHPCache;
		if($obCache->StartDataCache($CACHE_TIME, $CACHE_ID, "/")):

			foreach($arrCompareList as $eid) $aFilter["ID"][] = $eid;

			if ($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, false, false, array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "DESC"), false, $aFilter, array("ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL"))) :

				/****************************************************************
										HTML form
				****************************************************************/
				
				?>
				<?=ShowNote($strNote)?>
				<?=ShowError($strError)?>
				<table cellspacing=0 cellpadding=1 class="tableborder">
				<form action="<?=$COMPARE_URL?>" method="GET">
					<tr>
						<td><table cellspacing=0 cellpadding=4 class="tablebody" width="100%">
								<tr>
									<td class="tablehead" align="center" colspan="2"><font class="tableheadtext"><?=GetMessage("CATALOG_COMPARE_ELEMENTS")?></font></td>
								</tr>
								<?
								$count=0;
								while ($arElement = $rsElements->GetNext()):
									$count++;
								?>
								<tr>
									<td class="tablebody" nowrap><input type="hidden" name="ID[]" value="<?echo $arElement["ID"]?>"><font class="tablebodytext"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=htmlspecialchars($arElement["NAME"])?></a></font></td>
									<td><font class="tablebodytext"><a href="<?echo $APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_LIST&id=".$arElement["ID"], Array("action", "id"))?>#compare_list"><?=GetMessage("CATALOG_DELETE")?></a></font></td>
								</tr>
								<?
								endwhile;
								if ($count>=2):
								?>
								<tr>
									<td colspan="2"><font class="tablebodytext"><input class="inputbuttonflat" type="submit"  value="<?=GetMessage("CATALOG_COMPARE")?>"><input type="hidden" name="action" value="COMPARE"></font></td>
								</tr>
								<?endif;?>
							</table>
						</td>
					</tr>
				</form>
				</table>				
				<?
			endif;
			$obCache->EndDataCache();
		endif;
	endif;
endif;
?>