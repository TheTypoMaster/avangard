<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<br />
<div class="search-page">
<b>¬ведите название мебели, частично или полностью:</b></br></br>
<form action="" method="get">
<?if($arResult["REQUEST"]["HOW"]=="d"):?>
	<input type="hidden" name="how" value="d" />
<?endif;?>

<table width="400" height="36" cellspacing="0" cellpadding="2">
	<tr>
		<td width="240" valign="middle" align="left"  style="BACKGROUND:
url(/bitrix/templates/avangard/images/search_bg.jpg) no-repeat left top">


	<input type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="25" style="border: 0px; width: 220px; padding: 5px 5px 5px 5px" />

		</td>
		<td align="right">
	&nbsp;<input  type="image" src="/bitrix/templates/avangard/images/search_go.jpg" type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
		</td>
	</tr>
</table>
</form>
<?if($arResult["REQUEST"]["QUERY"] === false):?>
<?elseif($arResult["ERROR_CODE"]!=0):?>
	<p><?=GetMessage("SEARCH_ERROR")?></p>
	<?ShowError($arResult["ERROR_TEXT"]);?>
	<p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p>
	<br /><br />
	<p><?=GetMessage("SEARCH_SINTAX")?><br /><b><?=GetMessage("SEARCH_LOGIC")?></b></p>
	<table border="0" cellpadding="5">
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_OPERATOR")?></td><td valign="top"><?=GetMessage("SEARCH_SYNONIM")?></td>
			<td><?=GetMessage("SEARCH_DESCRIPTION")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_AND")?></td><td valign="top">and, &amp;, +</td>
			<td><?=GetMessage("SEARCH_AND_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_OR")?></td><td valign="top">or, |</td>
			<td><?=GetMessage("SEARCH_OR_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_NOT")?></td><td valign="top">not, ~</td>
			<td><?=GetMessage("SEARCH_NOT_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top">( )</td>
			<td valign="top">&nbsp;</td>
			<td><?=GetMessage("SEARCH_BRACKETS_ALT")?></td>
		</tr>
	</table>
<?elseif(count($arResult["SEARCH"])>0):?>
	<?//=$arResult["NAV_STRING"]?>
	<hr />
	<?foreach($arResult["SEARCH"] as $arItem):?>
		<a href="<?echo $arItem["URL"]?>"><?echo $arItem["TITLE_FORMATED"]?></a>
		<p><?echo $arItem["BODY_FORMATED"]?></p>
		<small><?=GetMessage("SEARCH_MODIFIED")?> <?=$arItem["DATE_CHANGE"]?></small><br /><?
		if($arItem["CHAIN_PATH"]):?>
			<small><?=GetMessage("SEARCH_PATH")?>&nbsp;<?=$arItem["CHAIN_PATH"]?></small><?
		endif;
		?><hr />
	<?endforeach;?>
	<?=$arResult["NAV_STRING"]?>
	<br />
	<p>
	<?if($arResult["REQUEST"]["HOW"]=="d"):?>
		<a href="<?=$arResult["URL"]?>"><?=GetMessage("SEARCH_SORT_BY_RANK")?></a>&nbsp;|&nbsp;<b><?=GetMessage("SEARCH_SORTED_BY_DATE")?></b>
	<?else:?>
		<b><?=GetMessage("SEARCH_SORTED_BY_RANK")?></b>&nbsp;|&nbsp;<a href="<?=$arResult["URL"]?>&amp;how=d"><?=GetMessage("SEARCH_SORT_BY_DATE")?></a>
	<?endif;?>
	</p>
<?else:?>
	<?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
<?endif;?>
</div>