<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
	?><?=ShowNote($arResult["OK_MESSAGE"])?><?
?><div class="forum-title"><?=GetMessage("FSL_SUBSCR_MANAGE")?></div>
<div class="forum-br"></div><?
if ($arResult["SHOW_SUBSCRIBE_LIST"] == "Y")
{
?><table class="forum-main">
	<tr>
		<th><?=GetMessage("FSL_FORUM_NAME")?></th>
		<th><?=GetMessage("FSL_TOPIC_NAME")?></th>
		<th><?=GetMessage("FSL_SUBSCR_DATE")?></th>
		<th><?=GetMessage("FSL_LAST_SENDED_MESSAGE")?></th>
		<th><?=GetMessage("FSL_ACTIONS")?></th>
	</tr>
	<?
	
	foreach ($arResult["SUBSCRIBE_LIST"] as $res)
	{
		?>
		<tr>
			<td><a href="<?=$res["list"]?>"><?=$res["FORUM_INFO"]["NAME"]?></a></td>
			<td><?
		if ($res["SUBSCRIBE_TYPE"] == "TOPIC")
		{
				?><a href="<?=$res["read"]?>"><?=$res["TOPIC_INFO"]["TITLE"]?></a><?
		}
		elseif ($res["SUBSCRIBE_TYPE"] == "NEW_TOPIC_ONLY")
		{
				?><?=GetMessage("FSL_NEW_TOPICS")?><?
		}
		else 
		{
				?><?=GetMessage("FSL_ALL_MESSAGES")?><?
		}
			?></td>
			<td><?=$res["START_DATE"]?></td>				
			<td align="center"><?
		if ($res["LAST_SEND"] > 0):
				?><a href="<?=$res["read_last_send"]?>"><?=GetMessage("FSL_HERE")?></a><?
		endif;
			?></td>
			<td align="center"><a href="<?=$res["subscr_delete"]?>"><?=GetMessage("FSL_DELETE")?></a></td>
		</tr>
		<?
	}
?></table><?
	if (!empty($arResult["NAV_STRING"]))
	{
?><?=$arResult["NAV_STRING"]?><?
	}
}
else 
{
?><table class="forum-main">
	<tr>
		<td><?=GetMessage("FSL_NOT_SUBCRIBED")?></td>
	</tr>
</table><?
}
?>