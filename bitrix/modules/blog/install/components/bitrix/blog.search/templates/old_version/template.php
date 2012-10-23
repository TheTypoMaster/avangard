<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form method="get" action="<?=$arParams["SEARCH_PAGE"]?>">
<input type="hidden" name="<?=$arParams["PAGE_VAR"]?>" value="search">
<table cellspacing="2" cellpadding="0" border="0" class="blog-search">
	<tr>
	<td><span class="blogtext"><?=GetMessage("BLOG_MAIN_SEARCH_SEARCH")?></span></td>
	<td><input type="text" name="q" size="20" value="<?=$arResult["q"]?>"></td>
	<td>
		<select name="where">
		<?foreach($arResult["WHERE"] as $k => $v)
		{
			?><option value="<?=$k?>"<?=$k==$arResult["where"]?" selected":""?>><?=$v?></option><?
		}
		?>
		</select>
	</td>
	<td><input type="submit" value="&nbsp;&nbsp;OK&nbsp;&nbsp;"></td>
	</tr>
</table>
<?if($arResult["how"]=="d"):?>
	<input type="hidden" name="how" value="d">
<?endif;?>
</form>

<?
if(strlen($arResult["ERROR_MESSAGE"])<=0)
{
	?><span class="blogtext"><?
	foreach($arResult["SEARCH_RESULT"] as $v)
	{
		?>
		<div class="blog-line"></div>
		<a href="<?echo $v["URL"]?>"><?echo $v["TITLE_FORMATED"]?></a>
		<?if(strlen($v["BODY_FORMATED"])>0)
		{
			?>
			<br />
			<?=$v["BODY_FORMATED"]?>
			<?
		}
		?>
		<br clear="left" />
		<?if(strlen($v["AuthorName"])>0 && strlen($v["BLOG_URL"])>0)
		{
			?>
			<a href="<?=$v["USER_URL"]?>" class="blog-user"></a>&nbsp;<a href="<?=$v["BLOG_URL"]?>"><?=$v["AuthorName"]?></a> - 
			<?
		}
		echo $v["FULL_DATE_CHANGE_FORMATED"];
	}
	if(strlen($arResult["NAV_STRING"]) > 0):
		?><p><?=$arResult["NAV_STRING"]?></p><?
	endif;
		
	if(strlen($arResult["ORDER_LINK"])>0)
	{
		if($arResult["how"]=="d"):
			?><p><a href="<?=$arResult["ORDER_LINK"]?>"><?=GetMessage("BLOG_MAIN_SEARCH_SORT_RELEVATION")?></a>&nbsp;|&nbsp;<b><?=GetMessage("BLOG_MAIN_SEARCH_SORTED_DATE")?></b></p><?
		else:
			?><p><b><?=GetMessage("BLOG_MAIN_SEARCH_SORTED_RELEVATION")?></b>&nbsp;|&nbsp;<a href="<?=$arResult["ORDER_LINK"]?>"><?=GetMessage("BLOG_MAIN_SEARCH_SORT_DATE")?></a></p><?
		endif;
	}
	?></span><?
}
else
	echo ShowError($arResult["ERROR_MESSAGE"]);
?>