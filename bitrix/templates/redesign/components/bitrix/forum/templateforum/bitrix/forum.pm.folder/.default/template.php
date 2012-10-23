<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if (!empty($arResult["ERROR_MESSAGE"]) || !empty($arResult["OK_MESSAGE"])):
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
	?><?=ShowNote($arResult["OK_MESSAGE"])?><br /><?
endif;

?><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="right">
	<div class="out"><div class="in" style="width:<?=$arResult["count"]?>%">&nbsp;</div></div>
	<div class="out1"><div class="in1"><?=GetMessage("PM_POST_FULLY")." ".$arResult["count"]?>%</div></div>
</td></tr></table><?
?><div class="forum-br"></div><?

if (($arParams["mode"] == "new") || ($arParams["mode"] == "edit"))
{
	?><form action="<?=$APPLICATION->GetCurPageParam()?>" method="get" class="forum-form">
		<input type="hidden" name="PAGE_NAME" value="pm_folder"/>
		<input type="hidden" name="action" value="<?=$arResult["action"]?>"/>
		<input type="hidden" name="mode" value="<?=$arParams["mode"]?>"/>
		<input type="hidden" name="FID" value="<?=$arParams["FID"]?>"/>
	<table border="0" cellpadding="0" cellspacing="">
	<tr><td>
		<table class="forum-main">
			<tr><th><b><?=GetMessage("PM_HEAD_FOLDER")?></b></th></tr>
			<tr><td><input type="text" name="FOLDER_TITLE" size="40" maxlength="64" value="<?=$arResult["POST_VALUES"]["FOLDER_TITLE"]?>" tabindex="1"/></td></tr>
			<tr><td align="center"><input type="submit" name="SAVE" value="<?=$arParams["mode"] == "new" ? GetMessage("PM_ACT_ADD") : GetMessage("PM_ACT_SAVE")	?>" tabindex="2"/></td></tr>
		</table>
	</td></tr>
	</table>
	</form>		
	<br/><?
	
}
else 
{
		?><a href="<?=$arResult["create_new_folder"]?>"><?=GetMessage("PM_HEAD_NEW_FOLDER")?></a><br/><br/><?
	?><table class="forum-main">
		<tr>
			<th width="80%"><?=GetMessage("PM_HEAD_TITLE")?></th>
			<th width="20%"><?=GetMessage("PM_HEAD_MESSAGE")?></th>
			<th width="0%" <?=(($arResult["SHOW_USER_FOLDER"] == "Y") ?  " colspan=\"3\"": "")?>><?=GetMessage("PM_HEAD_ACTION")?></th>
		</tr><?
	for ($ii = 1; $ii <= $arResult["FORUM_SystemFolder"]; $ii++)
	{
		if ($arParams["version"] == 2 && $ii == 2)
			continue;
		?><tr><td align="left"><a href="<?=$arResult["SYSTEM_FOLDER"][$ii]["pm_list"]?>"><?=GetMessage("PM_FOLDER_ID_".$ii)?></td>
			<td align="center"><?=$arResult["SYSTEM_FOLDER"][$ii]["cnt"]?></td><?
		if ($arResult["SHOW_USER_FOLDER"] == "Y"):
			?><td align="center">&nbsp;</td><?
		endif;
			?><td align="center"><a href="<?=$arResult["SYSTEM_FOLDER"][$ii]["remove"]?>"><?=GetMessage("PM_ACT_REMOVE")?></a></td><?
		if ($arResult["SHOW_USER_FOLDER"] == "Y"):
			?><td align="center">&nbsp;</td><?
		endif;
		?></tr><?	
	}
	foreach ($arResult["USER_FOLDER"] as $res)
	{
		?><tr><td align="left"><a href="<?=$res["pm_list"]?>"><?=$res["TITLE"]?></td><?
			?><td align="center"><?=$res["CNT"]?></td><?
			?><td align="center"><a href="<?=$res["edit"]?>"><?=GetMessage("PM_ACT_EDIT")?></a></td><?
			?><td align="center"><a href="<?=$res["remove"]?>"><?=GetMessage("PM_ACT_REMOVE")?></a></td><?
			?><td align="center"><a href="<?=$res["delete"]?>"><?=GetMessage("PM_ACT_DELETE")?></a></td><?
		?></tr><?
	}
	?></table><?
}
	// GetMessage("PM_FOLDER_ID_1");
	// GetMessage("PM_FOLDER_ID_2");
	// GetMessage("PM_FOLDER_ID_3");
	// GetMessage("PM_FOLDER_ID_4");
?>