<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$arParams["PATH_TO_ICON"] = (empty($arParams["PATH_TO_ICON"]) ? $templateFolder."/images/icon/" : $arParams["PATH_TO_ICON"]);
$arParams["PATH_TO_ICON"] = str_replace("//", "/", $arParams["PATH_TO_ICON"]."/");

// *****************************************************************************************
?><div id="forum_errors_top" class="forum-error"><?=ShowError($arResult["ERROR_MESSAGE"])?></div><?
?><div id="forum_notes_top" class="forum-note"><?=ShowNote($arResult["OK_MESSAGE"])?></div><?
// *****************************************************************************************
?><table width="100%" class="clear"><tr valign="bottom"><td width="99%"><?=$arResult["NAV_STRING"]?></td><?
if ($arResult["CanUserAddTopic"]):
	?><td width="1%"><a href="<?=$arResult["topic_new"]?>" title="<?=GetMessage("F_CREATE_NEW_TOPIC_T")?>" class="forum-button"><?=GetMessage("F_CREATE_NEW_TOPIC")?></a></td><?
endif;
?></tr></table><?
?><div class="forum-br"></div><?
?><div class="forum-title"><span><?=GetMessage("F_FORUM");?></span> &laquo;<?=$arResult["FORUM"]["NAME"]?>&raquo;</div><?
?><div class="forum-br"></div><?

?><table width="100%" class="forum-main">
	<tr><?
	if ($arResult["UserPermission"] >= "Q"):
		?><th class="td-moderate"><input type="checkbox" name="topc_all" value="Y" id="topic_all" onclick="SelectTopics(this)" /></th>
		<th class="td-moderate"><div class="icon-attention" title="<?=GetMessage("F_MESSAGE_NOT_APPROVED");?>"></div></th><?
	endif;
		?><th class="td-status"><div class="td-status"></div></th>
		<th class="td-icon"><div class="div-icon"></div></th>
		<th class="td-topic-name"><?=GetMessage("F_TOPIC_NAME")?><br /></th>
		<th class="td-topic-author"><?=GetMessage("F_TOPIC_AUTHOR")?><br /></th>
		<th class="td-posts"><?=GetMessage("F_TOPIC_POSTS")?><br /><?=$arResult["SortingEx"]["POSTS"]?></th>
		<th class="td-views"><?=GetMessage("F_TOPIC_VIEWS")?><br /><?=$arResult["SortingEx"]["VIEWS"]?></th>
		<th class="td-lm"><?=GetMessage("F_TOPIC_LAST_MESS")?><br /><?=$arResult["SortingEx"]["LAST_POST_DATE"]?></th>
	</tr><?
	
	if (is_array($arResult["Topics"]) && !empty($arResult["Topics"]))
	{
		foreach ($arResult["Topics"] as $res):
	?><tr><?
	
			if($arResult["UserPermission"] >= "Q"):
		?><td class="td-moderate"><input type="checkbox" name="topic_id[]" value="<?=$res["ID"]?>" id="topic_id_<?=$res["ID"]?>_" /></td>
		<td class="td-moderate"><?
				if(intVal($res["mCnt"]) > 0):
			?><div class="icon-attention" title="<?=GetMessage("F_MESSAGE_NOT_APPROVED")?> (<?=$res["mCnt"]?>)" onclick="window.location='<?=$res["mCntURL"]?>'"></div><?
				endif;
		?></td><?
			endif;
			
			
		?><td class="td-status"><?
			$res["closed"] = "";
			if (($res["STATE"]!="Y") && ($res["STATE"]!="L"))
					$res["closed"] = "-closed";
			if ($res["TopicStatus"] == "NA"):
				?><div class="icon-na" title="N/A"></div><?
			elseif ($res["TopicStatus"] == "NEW"):
				?><div class="icon<?=$res["closed"]?>-new-message" title="<?=(!empty($res["closed"]) ? GetMessage("F_TOPIC_CLOSE") : "").GetMessage("F_HAVE_NEW_MESS")?>" onclick="location.href='<?=$res["read_last_unread"]?>'; return false"></div><?
			elseif ($res["TopicStatus"] == "MOVED"):
				?><div class="icon-moved" title="<?=GetMessage("F_TOPIC_MOVED")?>" onclick="location.href='<?=$res["read"]?>'; return false"></div><?
			else:
				?><div class="icon<?=$res["closed"]?>-no-message" title="<?=(!empty($res["closed"]) ? GetMessage("F_TOPIC_CLOSE") : "").GetMessage("F_NO_NEW_MESS")?>"></div><?
			endif;
			
		?></td>
		<td class="td-icon"><?
			if (strLen($res["IMAGE"]) > 0):
			?><img src="<?=$arParams["PATH_TO_ICON"].$res["IMAGE"];?>" alt="<?=$res["IMAGE_DESCR"];?>" border="0" width="15" height="15"/><?
			endif;
		?></td>
		<td class="td-topic-name"><?=(intVal($res["SORT"])!=150) ? "<b>".GetMessage("F_PINNED").":</b> ":""?>
			<a href="<?=$res["read"]?>" title="<?=GetMessage("F_TOPIC_START")?> <?=$res["START_DATE"]?>"><?=$res["TITLE"]?></a><?
			if (($res["TopicStatus"] == "NEW") && (strLen(trim($arParams["TMPLT_SHOW_ADDITIONAL_MARKER"])) >0)):
				?><a href="<?=$res["read"]?>" class="forum-attention"><?=$arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]?></a><?
			endif;
			?><br /><?=$res["DESCRIPTION"]?> <?=$res["pages"]?></td>
		<td class="td-topic-author">
			<div class="f-author"><?
			
			if ($res["USER_START_ID"] > 0):
				?><a href="<?=$res["USER_START_HREF"]?>"><?=$res["USER_START_NAME"]?></a><?
			else:
				?><?=$res["USER_START_NAME"]?><?
			endif;
			
			?></div></td>
		<td class="td-posts"><?=$res["POSTS"]?></td>
		<td class="td-views"><?=$res["VIEWS"]?></td>
		<td class="td-lm">
			<a href="<?=$res["read_last_message"]?>"><?=$res["LAST_POST_DATE"]?></a><br />
			<div class="f-author"><?=GetMessage("F_TOPIC_AUTHOR1")?><?
			
			if ($res["LAST_POSTER_ID"] > 0):
				?><a href="<?=$res["LAST_POSTER_HREF"]?>"><?=$res["LAST_POSTER_NAME"]?></a><?
			else:
				?><?=$res["LAST_POSTER_NAME"]?><?
			endif;
			
			?></div></td>
	</tr><?
		endforeach;
	}
		?></table><?
	?><div class="forum-br"></div><?
	?><table width="100%" class="clear">
		<tr valign="top"><td width="100%"><?=$arResult["NAV_STRING"]?></td>
		<?if ($arResult["CanUserAddTopic"]):?>
		<td nowrap="nowrap">
			<a href="<?=$arResult["topic_new"]?>" title="<?=GetMessage("F_CREATE_NEW_TOPIC_T")?>" class="forum-button"><?=GetMessage("F_CREATE_NEW_TOPIC")?></a></td>
		<?endif;?>
		</tr>
	</table>