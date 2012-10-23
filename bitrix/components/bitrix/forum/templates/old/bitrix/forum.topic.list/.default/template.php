<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$arParams["PATH_TO_ICON"] = (empty($arParams["PATH_TO_ICON"]) ? $templateFolder."/images/icon/" : $arParams["PATH_TO_ICON"]);
$arParams["PATH_TO_ICON"] = str_replace("//", "/", $arParams["PATH_TO_ICON"]."/");
$iCount = 1;
?><div id="forum_errors_top" class="forum-error"><?
if (!empty($arResult["ERROR_MESSAGE"])):
?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
endif;
?></div>
<div id="forum_notes_top" class="forum-note"><?
if (!empty($arResult["OK_MESSAGE"])):
?><?=ShowNote($arResult["OK_MESSAGE"])?><?
endif;
?></div><?
// *****************************************************************************************
?>
<table width="100%" class="clear"><tr valign="bottom">
	<td width="99%"><?=$arResult["NAV_STRING"]?></td>
<?
if ($arResult["CanUserAddTopic"]):
?>
	<td width="1%">
		<a href="<?=$arResult["topic_new"]?>" title="<?=GetMessage("F_CREATE_NEW_TOPIC_T")?>" class="forum-button">
			<?=GetMessage("F_CREATE_NEW_TOPIC")?></a>
	</td>
<?
endif;
?>
</tr></table>
<div class="forum-br"></div>
<div class="forum-title"><span><?=GetMessage("F_FORUM");?></span> <span class="forum"><?=$arResult["FORUM"]["NAME"]?></span></div>
<div class="forum-br"></div>
<table width="100%" class="forum-main">
<thead>
	<tr class="forum-row">
<?
if ($arResult["UserPermission"] >= "Q"):
		?><th class="td-moderate"><input type="checkbox" name="topc_all" value="Y" id="topic_all" onclick="SelectTopics(this)" /></th>
		<th class="td-moderate"><div class="icon-attention" title="<?=GetMessage("F_MESSAGE_NOT_APPROVED");?>"></div></th><?
endif;
		?><th class="td-status"><div class="td-status"></div></th>
		<th class="td-topic-name"><?=GetMessage("F_TOPIC_NAME")?><br /></th>
		<th class="td-topic-author"><?=GetMessage("F_TOPIC_AUTHOR")?><br /></th>
		<th class="td-posts <?=("POSTS" == strtoupper($by) ? " selected" : "")?>">
			<?=GetMessage("F_TOPIC_POSTS")?><div class="forum-sort"><?=$arResult["SortingEx"]["POSTS"]?></div></th>
		<th class="td-views <?=("VIEWS" == strtoupper($by) ? " selected" : "")?>">
			<?=GetMessage("F_TOPIC_VIEWS")?><div class="forum-sort"><?=$arResult["SortingEx"]["VIEWS"]?></div></th>
		<th class="td-lm <?=("LAST_POST_DATE" == strtoupper($by) ? " selected" : "")?>">
			<?=GetMessage("F_TOPIC_LAST_MESS")?><div class="forum-sort"><?=$arResult["SortingEx"]["LAST_POST_DATE"]?></div></th>
	</tr>
</thead>
<tbody>
<?
		foreach ($arResult["Topics"] as $res):
		$iCount++;
	?><tr class="forum-row <?=($iCount%2 == 0 ? " selected" : "")?>"><?
	
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
		<td class="td-topic-name"><?=(intVal($res["SORT"])!=150) ? "<span class='forum-topic-pinned'>".GetMessage("F_PINNED").":</span> ":""?>
<?
			if (strLen($res["IMAGE"]) > 0):
			?><img src="<?=$arParams["PATH_TO_ICON"].$res["IMAGE"];?>" alt="<?=$res["IMAGE_DESCR"];?>" border="0" width="15" height="15"/><?
			endif;
?>		
			<a href="<?=$res["read"]?>" class="forum-topic" title="<?=GetMessage("F_TOPIC_START")?> <?=$res["START_DATE"]?>"><?=$res["TITLE"]?></a><?
			if (($res["TopicStatus"] == "NEW") && (strLen(trim($arParams["TMPLT_SHOW_ADDITIONAL_MARKER"])) >0)):
				?><a href="<?=$res["read"]?>" class="forum-topic forum-attention"><?=$arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]?></a><?
			endif;
			?><br /><?=$res["DESCRIPTION"]?> <?=$res["pages"]?></td>
		<td class="td-topic-author">
			<div class="f-author"><?
			
			if ($res["USER_START_ID"] > 0):
				?><a href="<?=$res["USER_START_HREF"]?>" class="forum-user"><?=$res["USER_START_NAME"]?></a><?
			else:
				?><span class="forum-user"><?=$res["USER_START_NAME"]?></span><?
			endif;
			
			?></div></td>
		<td class="td-posts"><?=$res["POSTS"]?></td>
		<td class="td-views"><?=$res["VIEWS"]?></td>
		<td class="td-lm">
			<a href="<?=$res["read_last_message"]?>" class="forum-topic"><?=$res["LAST_POST_DATE"]?></a><br />
			<div class="f-author"><?=GetMessage("F_TOPIC_AUTHOR1")?> <?
			
			if ($res["LAST_POSTER_ID"] > 0):
				?><a href="<?=$res["LAST_POSTER_HREF"]?>" class="forum-user"><?=$res["LAST_POSTER_NAME"]?></a><?
			else:
				?><span class="forum-user"><?=$res["LAST_POSTER_NAME"]?></span><?
			endif;
			?></div></td>
	</tr>
<?
		endforeach;
?>
	</tbody>
</table>

<div class="forum-br"></div>
<table width="100%" class="clear">
		<tr valign="top"><td width="100%"><?=$arResult["NAV_STRING"]?></td>
		<?if ($arResult["CanUserAddTopic"]):?>
		<td nowrap="nowrap">
			<a href="<?=$arResult["topic_new"]?>" title="<?=GetMessage("F_CREATE_NEW_TOPIC_T")?>" class="forum-button"><?=GetMessage("F_CREATE_NEW_TOPIC")?></a></td>
		<?endif;?>
		</tr>
</table>
<?
?><div id="forum_errors_bottom" class="forum-error"><?
if (!empty($arResult["ERROR_MESSAGE"])):
?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
endif;
?></div>
<div id="forum_notes_bottom" class="forum-note"><?
if (!empty($arResult["OK_MESSAGE"])):
?><?=ShowNote($arResult["OK_MESSAGE"])?><?
endif;
?></div><?
?>