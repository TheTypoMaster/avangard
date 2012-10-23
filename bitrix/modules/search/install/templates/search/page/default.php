<?
/**************************************************************************
				Component for displaying Search page
***************************************************************************/

IncludeTemplateLangFile(__FILE__);

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!$APPLICATION->GetTitle())
	$APPLICATION->SetTitle(GetMessage("SEARCH_PAGE_TITLE"));

if(!CModule::IncludeModule("search"))
{
	ShowError(GetMessage("SEARCH_MODULE_UNAVAILABLE"));
	return;
}

/*************************************************************************
				Component parameters
*************************************************************************/
/*
$arrWHERE		= $arParams["arrWHERE"];		// values for drop-down list "where to search"
$PAGE_RESULT_COUNT	= $arParams["PAGE_RESULT_COUNT"];	// number of results displayed on each page
$CHAIN_TEMPLATE_PATH	= $arParams["CHAIN_TEMPLATE_PATH"];	// template for Navigation chain in search results  ("Path:")
$CACHE_TIME		= $arParams["CACHE_TIME"];		// time to cache values of the drop-down list (sec.)
*/

if($CHECK_DATES<>"Y" && $CHECK_DATES<>"N")
	$CHECK_DATES="N";
if($SHOW_WHERE<>"Y" && $SHOW_WHERE<>"N")
	$SHOW_WHERE="Y";
$arrWHERE = (is_array($arrWHERE)) ? $arrWHERE : array();
$CHAIN_TEMPLATE_PATH = strlen($CHAIN_TEMPLATE_PATH)>0 ? $CHAIN_TEMPLATE_PATH : false;
$arrDropdown = array();
$q = trim($_REQUEST["q"]);
$where = ($SHOW_WHERE=="Y"?trim($_REQUEST["where"]):"");
$how = trim($_REQUEST["how"]);
if($how<>"d") $how="";
if($how=="d")
	$aSort=array("DATE_CHANGE"=>"DESC", "CUSTOM_RANK"=>"DESC", "RANK"=>"DESC");
else
	$aSort=array("CUSTOM_RANK"=>"DESC", "RANK"=>"DESC", "DATE_CHANGE"=>"DESC");
$exFILTER=array();

if(is_array($arrFILTER))
{
	foreach($arrFILTER as $strFILTER)
	{
		if($strFILTER=="main" && is_array(${"arrFILTER_".$strFILTER}))
		{
			$arURL=array();
			foreach(${"arrFILTER_".$strFILTER} as $strURL)
				$arURL[]=$strURL."%";
			$exFILTER[]=array(
				"MODULE_ID"=>"main"
				,"URL"=>$arURL
			);
		}
		elseif($strFILTER=="forum" && CModule::IncludeModule("forum"))
		{
			$arForum=array();
			foreach(${"arrFILTER_".$strFILTER} as $strForum)
				if($strForum<>"-")
					$arForum[]=intval($strForum);
			if(count($arForum)>0)
				$exFILTER[]=array(
					"MODULE_ID"=>"forum"
					,"PARAM1"=>$arForum
				);
			else
				$exFILTER[]=array(
					"MODULE_ID"=>"forum"
				);
		}
		elseif(strpos($strFILTER,"iblock_")===0)
		{
			$arIBlock=array();
			foreach(${"arrFILTER_".$strFILTER} as $strIBlock)
				if($strIBlock<>"-")
					$arIBlock[]=intval($strIBlock);
			if(count($arIBlock)>0)
				$exFILTER[]=array(
					"MODULE_ID"=>"iblock"
					,"PARAM1"=>substr($strFILTER, 7)
					,"PARAM2"=>$arIBlock
				);
			else
				$exFILTER[]=array(
					"MODULE_ID"=>"iblock"
					,"PARAM1"=>substr($strFILTER, 7)
				);
		}
		elseif($strFILTER=="blog")
		{
			$exFILTER[] = array(
				"MODULE_ID"	=> "blog",
			);
		}
	}
}

/*************************************************************************
			Operations with cache
*************************************************************************/
$obCache = new CPHPCache;
$CACHE_ID = __FILE__.serialize($arParams);
if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
{
	$arVars = $obCache->GetVars();
	$arrDropdown = $arVars["arrDropdown"];
}
else
{
	if (CModule::IncludeModule("iblock"))
	{
		// Getting of the Information block types
		$rsType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
		while ($arr=$rsType->Fetch())
		{
			if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
				$arIblockType[$arr["ID"]] = $ar["NAME"];
		}
	}
	// Creating of an array for drop-down list
	foreach($arrWHERE as $code)
	{
		list($module_id, $part_id) = explode("_",$code,2);
		switch ($module_id)
		{
			case "forum":
				$arrDropdown[$code] = GetMessage("SEARCH_FORUM");
				break;
			case "blog":
				$arrDropdown[$code] = GetMessage("SEARCH_BLOG");
				break;
			case "iblock":
				// if there is additional information specified besides ID then
				if(is_array($arIblockType) && array_key_exists($part_id, $arIblockType))
					$arrDropdown[$code] = $arIblockType[$part_id];
				break;
		}
	}
}
// save cache to the disk
if($obCache->StartDataCache())
	$obCache->EndDataCache(Array("arrDropdown" => $arrDropdown));

/****************************************************************
				HTML form
****************************************************************/

?>
<form action="<?=$APPLICATION->GetCurPage()?>" method="GET">
<?if($how=="d"):?>
	<input type="hidden" name="how" value="d">
<?endif;?>
	<input class="inputtext" type="text" name="q" value="<?echo htmlspecialcharsex($q)?>" size="40">
<?if($SHOW_WHERE=="Y"):?>
	&nbsp;<?
	echo SelectBoxFromArray(
		"where",
		array(
			"reference"	=> array_values($arrDropdown),
			"reference_id"	=> array_keys($arrDropdown)
		),
		htmlspecialchars($where),
		GetMessage("SEARCH_ALL"),
		"class='inputselect'"
		);
	?>
<?endif;?>
	&nbsp;<input type="submit" class="inputbutton" value="<?=GetMessage("SEARCH_GO")?>">
</form><?
if(strlen($q)>0):
	$arFilter = array("SITE_ID" => SITE_ID, "QUERY" => $q);
	if ($where!="NOT_REF" && strlen($where)>0)
	{
		list($module_id, $part_id) = explode("_",$where,2);
		$arFilter["MODULE_ID"] = $module_id;
		if (strlen($part_id)>0) $arFilter["PARAM1"] = $part_id;
	}
	if($CHECK_DATES=="Y")
		$arFilter["CHECK_DATES"]="Y";
	$obSearch = new CSearch();
	$obSearch->Search($arFilter, $aSort, $exFILTER);
	if($obSearch->errorno!=0):
	?>
		<font class="text"><?=GetMessage("SEARCH_ERROR")?></font>
		<?echo ShowError($obSearch->error);?>
		<font class="text"><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></font>
		<br><br>
		<font class="text"><?=GetMessage("SEARCH_SINTAX")?><br><b><?=GetMessage("SEARCH_LOGIC")?></b></font>
		<table border="0" cellpadding="5">
			<tr>
				<td align="center" valign="top"><font class="text"><?=GetMessage("SEARCH_OPERATOR")?></font></td><td valign="top"><font class="text"><?=GetMessage("SEARCH_SYNONIM")?></font></td>
				<td><font class="text"><?=GetMessage("SEARCH_DESCRIPTION")?></font></td>
			</tr>
			<tr>
				<td align="center" valign="top"><font class="text"><?=GetMessage("SEARCH_AND")?></font></td><td valign="top"><font class="text">and, &amp;, +</font></td>
				<td><font class="text"><?=GetMessage("SEARCH_AND_ALT")?></font></td>
			</tr>
			<tr>
				<td align="center" valign="top"><font class="text"><?=GetMessage("SEARCH_OR")?></font></td><td valign="top"><font class="text">or, |</font></td>
				<td><font class="text"><?=GetMessage("SEARCH_OR_ALT")?></font></td>
			</tr>
			<tr>
				<td align="center" valign="top"><font class="text"><?=GetMessage("SEARCH_NOT")?></font></td><td valign="top"><font class="text">not, ~</font></td>
				<td><font class="text"><?=GetMessage("SEARCH_NOT_ALT")?></font></td>
			</tr>
			<tr>
				<td align="center" valign="top"><font class="text">( )</font></td>
				<td valign="top"><font class="text">&nbsp;</font></td>
				<td><font class="text"><?=GetMessage("SEARCH_BRACKETS_ALT")?></font></td>
			</tr>
		</table><?

	else:

		$obSearch->NavStart($PAGE_RESULT_COUNT, false);
		if($arResult = $obSearch->GetNext()):
			$obSearch->NavPrint(GetMessage("SEARCH_RESULTS"));
			?><br><hr><?
				do {
				?><font class="text"><a href="<?echo $arResult["URL"]?>"><?echo $arResult["TITLE_FORMATED"]?></a><br>
				<?echo $arResult["BODY_FORMATED"]?><br>
				<img src="/bitrix/images/1.gif" width="1" height="5"><br>
				<font class="smalltext"><?=GetMessage("SEARCH_MODIFIED")?> <?=$arResult["DATE_CHANGE"]?></font><?
				if(strlen($ch = $APPLICATION->GetNavChain($arResult["URL"], 0, $CHAIN_TEMPLATE_PATH))>0)
					echo "<br><font class='smalltext'>".GetMessage("SEARCH_PATH")." </font>".$ch."<br>";
			?><hr></font><?
				} while($arResult = $obSearch->GetNext());
			$obSearch->NavPrint(GetMessage("SEARCH_RESULTS"));
			?><br>
			<img src="/bitrix/images/1.gif" width="1" height="5"><br>
			<font class="text">
			<?if($how=="d"):?>
			<a href="<?=$APPLICATION->GetCurPage()."?q=".urlencode($q)."&where=".urlencode($where)?>"><?=GetMessage("SEARCH_SORT_BY_RANK")?></a>&nbsp;|&nbsp;<b><?=GetMessage("SEARCH_SORTED_BY_DATE")?></b>
			<?else:?>
			<b><?=GetMessage("SEARCH_SORTED_BY_RANK")?></b>&nbsp;|&nbsp;<a href="<?=$APPLICATION->GetCurPage()."?q=".urlencode($q)."&where=".urlencode($where)."&how=d"?>"><?=GetMessage("SEARCH_SORT_BY_DATE")?></a>
			<?endif;?>
			</font>
		<?else:
			echo ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));
		endif;
	endif;
endif;
?>