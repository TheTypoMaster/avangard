<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table border="0" cellpadding="4" cellspacing="0" width="75%" class="blog-groups">
<?
foreach($arResult["GROUPS_TABLE"] as $row)
{
        if(is_array($row))
	{
		?><tr><?
		foreach($row as $item)
		{
			?><td nowrap><a href="<?=$item["URL"]?>"><img src="<?=$templateFolder?>/images/folder.gif" width="17" height="17" alt="" border="0" align="absmiddle"></a>&nbsp;&nbsp;<a href="<?=$item["URL"]?>"><?echo $item["NAME"]?></a></td><?
		}
		?></tr><?
	}
}
?>
</table>