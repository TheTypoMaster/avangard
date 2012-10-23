<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
IncludeTemplateLangFile(__FILE__);
CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!$APPLICATION->GetTitle())
	$APPLICATION->SetTitle(GetMessage("BLOG_MAIN_SEARCH_TITLE"));

if(!CModule::IncludeModule("blog"))
{
	ShowError(GetMessage("BLOG_MAIN_SEARCH_NOT_INSTALL"));
	return;
}
if(!CModule::IncludeModule("search"))
{
	return;
}
/*************************************************************************
				Component parameters
*************************************************************************/

$PAGE_RESULT_COUNT	= IntVal($$PAGE_RESULT_COUNT)>0 ? IntVal($PAGE_RESULT_COUNT): 20;	// number of results displayed on each page
$is404 = ($is404=='N') ? false: true;

$q = trim($_REQUEST["q"]);
$where = trim($_REQUEST["where"]);
$how = trim($_REQUEST["how"]);
if($how<>"d") $how="";
?>
<form method="get" action="<?=$SEARCH_PAGE?>">
<table cellspacing="2" cellpadding="0" border="0">
	<tr>
	<td class="tablebodytext"><?=GetMessage("BLOG_MAIN_SEARCH_SEARCH")?></td>
	<td><input type="text" name="q" size="20" class="inputtext" value="<?echo htmlspecialcharsex($q)?>"></td>
	<td>
		<select name="where" class="inputselect">
			<option value="POST"<?=$where=="POST"?" selected":""?>><?=GetMessage("BLOG_MAIN_SEARCH_SEARCH_POST")?></option>
			<option value="BLOG"<?=$where=="BLOG"?" selected":""?>><?=GetMessage("BLOG_MAIN_SEARCH_SEARCH_BLOG")?></option>
			<option value="USER"<?=$where=="USER"?" selected":""?>><?=GetMessage("BLOG_MAIN_SEARCH_SEARCH_USER")?></option>
		</select>
	</td>
	<td><input type="submit" value="&nbsp;&nbsp;OK&nbsp;&nbsp;" class="inputbutton"></td>
	</tr>
</table>
<?if($how=="d"):?>
	<input type="hidden" name="how" value="d">
<?endif;?>
</form>
<?
if($q=="")
	return;
//RegisterModuleDependences("search", "OnReindex", "blog", "CBlogSearch", "OnSearchReindex");
//UnRegisterModuleDependences("search", "OnReindex", "iblock", "CIBlock", "OnSearchReindex");
$arFilter = array(
	"SITE_ID"	=> SITE_ID,
	"QUERY"		=> $q,
	"MODULE_ID"	=> "blog",
	"PARAM1"	=> $where,
	"CHECK_DATES"	=> "Y",
);
if($how=="d")
	$aSort=array("DATE_CHANGE"=>"DESC", "CUSTOM_RANK"=>"DESC", "RANK"=>"DESC");
else
	$aSort=array("CUSTOM_RANK"=>"DESC", "RANK"=>"DESC", "DATE_CHANGE"=>"DESC");
$obSearch = new CSearch();
$obSearch->Search($arFilter, $aSort);
if($obSearch->errorno==0):
	$obSearch->NavStart($PAGE_RESULT_COUNT, false);
	if($arResult = $obSearch->GetNext()):
		if($arResult["PARAM1"]=="POST")
		{
			$Blog = CBlog::GetByID($arResult["PARAM2"]);
			$arResult["PARAM2"] = $Blog["OWNER_ID"];
			$urlToAuthor = CBlog::PreparePath($Blog["URL"], SITE_ID, $is404);
		}
		else
		{
			$urlToAuthor = $arResult["URL"];
		}
		if($where!="USER")
		{
			$BlogUser=CBlogUser::GetByID($arResult["PARAM2"], BLOG_BY_USER_ID);
			$dbUser = CUser::GetByID($arResult["PARAM2"]);
			$arUser = $dbUser->Fetch();
			$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
		}
		$obSearch->NavPrint(GetMessage("BLOG_MAIN_SEARCH_RESULT"));
		?><br><hr><?
			do {//print_r($arResult);
			?>
			<font class="text">
			<a href="<?echo $arResult["URL"]?>"><?echo $arResult["TITLE_FORMATED"]?></a><br>
			<?echo $arResult["BODY_FORMATED"]?><?if($where!="USER"):?><br>
			<img src="/bitrix/images/1.gif" width="1" height="5"><br>
			<font class="smalltext"><?=GetMessage("BLOG_MAIN_SEARCH_AUTHOR")?> <a href="<?=$urlToAuthor?>"><?=htmlspecialcharsex($AuthorName)?></a> - <?=$arResult["FULL_DATE_CHANGE"]?></font>
			<?endif;?><hr></font><?
			} while($arResult = $obSearch->GetNext());
		$obSearch->NavPrint(GetMessage("BLOG_MAIN_SEARCH_RESULT"));
		?><br>
		<img src="/bitrix/images/1.gif" width="1" height="5"><br>
		<font class="text">
		<?if($how=="d"):?>
		<a href="<?=$APPLICATION->GetCurPage()."?q=".urlencode($q)."&where=".urlencode($where)?>"><?=GetMessage("BLOG_MAIN_SEARCH_SORT_RELEVATION")?></a>&nbsp;|&nbsp;<b><?=GetMessage("BLOG_MAIN_SEARCH_SORTED_DATE")?></b>
		<?else:?>
		<b><?=GetMessage("BLOG_MAIN_SEARCH_SORTED_RELEVATION")?></b>&nbsp;|&nbsp;<a href="<?=$APPLICATION->GetCurPage()."?q=".urlencode($q)."&where=".urlencode($where)."&how=d"?>"><?=GetMessage("BLOG_MAIN_SEARCH_SORT_DATE")?></a>
		<?endif;?>
		</font>
	<?else:
		echo ShowNote(GetMessage("BLOG_MAIN_SEARCH_NOTHING_FOUND"));
	endif;
endif;
?>