<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/**************************************************************************
Component "Menu items".

This component is intended for adding section names to the menu.

Sample of usage:

$aMenuLinksNew=$APPLICATION->IncludeFile("iblock/menu/menu_items.php", Array(
	"ID"		=> $_REQUEST["ID"],
	"IBLOCK_TYPE"		=> "catalog",
	"IBLOCK_ID"		=> "21",
	"CACHE_TIME"		=> "3600",
	"SECTION_URL"		=> "/catalog/phone/section.php?",
	));
$aMenuLinks = array_merge($aMenuLinksNew, $aMenuLinks);

Parameters:

ID - Element ID used to select an menu item
IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID
CACHE_TIME - (sec.) time for caching (0 - do not cache)
SECTION_URL - URL to the page with the group contents

***************************************************************************/
if($GLOBALS["APPLICATION"]->GetShowIncludeAreas())
	echo "<br>";

$ID = intval($ID);
$IBLOCK_ID = intval($IBLOCK_ID);

$aMenuLinksNew = array();
if (CModule::IncludeModule("iblock")):
	$CACHE_ID = __FILE__.$IBLOCK_ID;
	$obMenuCache = new CPHPCache;
	// если массив закэширован то
	if($obMenuCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		// берем данные из кэша
		$arVars = $obMenuCache->GetVars();
		$arSections = $arVars["arSections"];
		$arElementLinks = $arVars["arElementLinks"];
	}
	else
	{
		// иначе собираем разделы
		$rsSections = GetIBlockSectionList($IBLOCK_ID, 0, array("SORT" => "ASC", "ID" => "ASC"), false, array("ACTIVE"=>"Y"));
		$arSections = array();
		while ($arSection = $rsSections->Fetch())
		{
			$arSections[]=$arSection;
			$arElementLinks[$arSection["ID"]] = array();
		}
	}

	if($ID>0)
	{
		$arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
		$arFilter = array("ID"=>$ID, "ACTIVE" => "Y", "IBLOCK_ID" => $IBLOCK_ID);
		if($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, false, false, array(), false, $arFilter, $arSelect))
		{
			while($arElement = $rsElements->GetNext())
				$arElementLinks[$arElement["IBLOCK_SECTION_ID"]][] = $arElement["DETAIL_PAGE_URL"];
		}
	}
	
	foreach($arSections as $arSection)
	{
		// пройдемся по элементам раздела
		$aMenuLinksNew[] = array(
			$arSection["NAME"],
			SITE_DIR.$SECTION_URL."SECTION_ID=".$arSection["ID"],
			$arElementLinks[$arSection["ID"]]);
	}
	
	// сохраняем данные в кэше
	if($obMenuCache->StartDataCache())
	{
		$obMenuCache->EndDataCache(Array("arSections" => $arSections, "arElementLinks"=>$arElementLinks));
	}

endif;

return $aMenuLinksNew;
?>