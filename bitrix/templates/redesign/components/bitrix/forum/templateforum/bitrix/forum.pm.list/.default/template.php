<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/forum.interface/templates/.default/script.js"></script>', true);

if (!empty($arResult["ERROR_MESSAGE"])):
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
endif;
if (!empty($arResult["OK_MESSAGE"])):
	?><?=ShowNote($arResult["OK_MESSAGE"])?><?
endif;

?><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="right">
	<div class="out"><div class="in" style="width:<?=$arResult["count"]?>%">&nbsp;</div></div>
	<div class="out1"><div class="in1"><?=GetMessage("PM_POST_FULLY")." ".$arResult["count"]?>%</div></div>
</td></tr></table><?

?>
<div class="forum-title"><?=$arResult["FolderName"]?></div>
<div class="forum-br"></div>
<?

if (($arResult["MESSAGE"] != "N") && is_array($arResult["MESSAGE"]))
{
?>
<form action="<?=$APPLICATION->GetCurPageParam()?>" name="REPLIER" id="REPLIER" method="GET" class="forum-form">
	<input type="hidden" name="action" value="" />
	<input type="hidden" name="folder_id" value="" />
	<input type="hidden" name="FID" value="<?=$arResult["FID"]?>" />
	<input type="hidden" name="PAGE_NAME" value="pm_list" />
	<?=$arResult["sessid"]?>
<table class="forum-main">
	<tr>
		<th width="0%"><input type="checkbox" name="all_message__" onclick="FSelectAll(this, 'message[]');" /></th>
		<th width="0%">&nbsp;</th>
		<th width="80%"><?=GetMessage("PM_HEAD_SUBJ")?><br/><?=$arResult["SortingEx"]["POST_SUBJ"]?></th>
		<th width="20%"><?
		if ($arResult["StatusUser"] == "RECIPIENT"):
			?><?=GetMessage("PM_HEAD_RECIPIENT")?><?
		elseif ($arResult["StatusUser"] == "SENDER"):
			?><?=GetMessage("PM_HEAD_SENDER")?><?
		else:
			?><?=GetMessage("PM_HEAD_AUTHOR")?><?
		endif;
		?><br /><?=$arResult["SortingEx"]["AUTHOR_NAME"]?></th>
		<th width="0%"><?=GetMessage("PM_HEAD_DATE")?><br/><?=$arResult["SortingEx"]["POST_DATE"]?></th>
	</tr><?
		foreach ($arResult["MESSAGE"] as $res)
		{
			?><tr onmouseup="OnRowClick(<?=$res["ID"]?>, this);" id="message_row_<?=$res["ID"]?>">
				<td align="center">
					<input type=checkbox name="message[]" id="message_id_<?=$res["ID"]?>" value="<?=$res["ID"]?>" <?
						?><?=$res["checked"]?> onclick="OnInputClick(this);" />
				</td>
				<td align="center">
					<a href="<?=$res["pm_read"]?>" onmouseup="FCancelBubble(event);">
				<?if($res["IS_READ"] == "Y"):
					?><div class="icon-no-message" title="<?=GetMessage("PM_HAVE_MESS")?>"></div>					
				<?else:
					?><div class="icon-new-message" title="<?=GetMessage("PM_HAVE_NEW_MESS")?>"></div>
				<?endif?>
				</a></td>
				<td align="left"><a href="<?=$res["pm_read"]?>" onmouseup="FCancelBubble(event);"><?=$res["POST_SUBJ"]?></a></td>
				<td align="center"><a href="<?=$res["profile_view"]?>" onmouseup="FCancelBubble(event)"><?=$res["SHOW_NAME"]?></a></td>
				<td align="center" nowrap="nowrap"><?=$res["POST_DATE"]?></td>
			</tr><?
		}
		?>
<tr valign="bottom"><th colspan="5">

<table cellpadding="0" cellspacing="0" border="0" class="clear"><tr>
	<td style="padding-right:15px;"><input type="button" name="action_delete" value="<?=GetMessage("PM_ACT_DELETE")?>" onclick="ChangeAction('delete', this);" /></td>
	<td><?=GetMessage("PM_ACT_MOVE")?> <?=GetMessage("PM_ACT_IN")?>
		<select name="folder_id_move"><?
		for ($ii = 1; $ii <= $arResult["SystemFolder"]; $ii++)
		{
			if (($arResult["version"] == 2 && $ii==2) || $arParams["FID"] == $ii)
				continue;
			?><option value="<?=$ii?>"><?=getMessage("PM_FOLDER_ID_".$ii)?></option><?
		}
		if (($arResult["UserFolder"] != "N") && is_array($arResult["UserFolder"]))
		{
			foreach ($arResult["UserFolder"] as $res)
			{
				if ($arParams["FID"] == $res["ID"])
					continue;
			?><option value="<?=$res["ID"]?>"><?=$res["TITLE"]?></option><?
			}
		}
		?></select> <input type="button" name="button_move" value="<?=GetMessage("PM_OK")?>" onclick="ChangeAction('move', this)" /></td>
<?/*?>
	<td><?=GetMessage("PM_ACT_COPY")?> <?=GetMessage("PM_ACT_IN")?>
		<select name="folder_id_copy"><?
		for ($ii = 1; $ii <= $arResult["SystemFolder"]; $ii++)
		{
			if ($arResult["version"] == 2 && $ii==2)
				continue;
			?><option value="<?=$ii?>"><?=getMessage("PM_FOLDER_ID_".$ii)?></option><?
		}
		if (($arResult["UserFolder"] != "N") && is_array($arResult["UserFolder"]))
		{
			foreach ($arResult["UserFolder"] as $res)
			{
			?><option value="<?=$res["ID"]?>"><?=$res["TITLE"]?></option><?
			}
		}
		?></select> <input type="button" name="button_copy" value="<?=GetMessage("PM_OK")?>" onclick="ChangeAction('move', this)" /></td>
<?
*/
?>
</tr></table>
</th></tr></table>
</form>

<script>
if (typeof oText != "object")
	var oText = {};
oText['no_data'] = '<?=CUtil::addslashes(GetMessage('JS_NO_MESSAGES'))?>';
oText['del_message'] = '<?=CUtil::addslashes(GetMessage("JS_DEL_MESSAGE"))?>';
</script>
<?

	if (!empty($arResult["NAV_STRING"])):
		?><div class="forum-navigation"><?=$arResult["NAV_STRING"]?></div><?
	endif;
}
else 
{
	?><table class="forum-main">
		<tr><td><?=GetMessage("PM_EMPTY_FOLDER")?></td></tr>
	</table><?
}
?>