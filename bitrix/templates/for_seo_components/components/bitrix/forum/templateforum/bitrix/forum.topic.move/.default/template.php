<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
	?><?=ShowNote($arResult["OK_MESSAGE"])?><?
	
	?><div class="forum-title"><span class="forum-title-views"><?=GetMessage("FL_TOPICS_N")?> <?=count($arResult["TOPIC"])?></span>
			<span><?=GetMessage("FL_FORUM")?></span> <a href="<?=$arResult["list"]?>"><?=$arResult["FORUM"]["NAME"]?></a></div><?
	?><div class="forum-br"></div><?
	?><form action="<?=$APPLICATION->GetCurPageParam()?>" method="get" onsubmit="this.form_topics_submit.disabled=true;" class="forum-form">
		<input type="hidden" name="PAGE_NAME" value="topic_move"/>
		<input type="hidden" name="action" value="move"/>
		<input type="hidden" name="FID" value="<?=$arParams["FID"]?>"/>
		<input type="hidden" name="TID" value="<?=$arParams["TID"]?>"/>
		<?=$arResult["sessid"]?>
	<table class="forum-main">
		<tr>
			<th class="td-status">&nbsp;</th>
			<th class="td-icon">&nbsp;</th>
			<th class="td-topic-name"><?=GetMessage("FL_TOPIC_NAME")?></th>
			<th class="td-topic-author"><?=GetMessage("FL_TOPIC_AUTHOR")?></th>
			<th class="td-posts"><?=GetMessage("FL_TOPIC_POSTS")?></th>
			<th class="td-views"><?=GetMessage("FL_TOPIC_VIEWS")?></th>
			<th class="td-lm"><?=GetMessage("FL_TOPIC_LAST_MESS")?></th>
		</tr><?
		foreach ($arResult["TOPIC"] as $Topic)
		{
		?><tr>
			<td align="center">
				<?
				$strClosed = "";
				if ($Topic["STATE"]!="Y") 
					$strClosed = "closed_";
				if ($Topic["APPROVED"]!="Y"):
					?><div class="icon-na" title="N/A"></div><?
				else:
					?><div class="icon<?=$res["closed"]?>-no-message"></div><?
				endif;
				?>
			</td>
			<td align="center">
				<?if (strLen($Topic["IMAGE"])>0):
					if (strLen(trim($arParams["PATH_TO_ICON"])) <= 0)
					{
						$arParams["PATH_TO_ICON"] = $templateFolder."/images/icon/";
					}
				?><img src="<?=$arParams["PATH_TO_ICON"].$Topic["IMAGE"];?>" alt="<?=$Topic["IMAGE_DESCR"];?>" border="0" width="15" height="15"/>
				<?endif;?>
			</td>
			<td><?=(intVal($Topic["SORT"])!=150) ? "<b>".GetMessage("FL_PINNED").":</b> ":""?>
				<a href="<?=$Topic["read"]?>" title="<?=GetMessage("FL_TOPIC_START")?> <?=$Topic["START_DATE"]?>"><?=$Topic["TITLE"]?></a><br/><?=$Topic["DESCRIPTION"]?></td>
			<td align="center">
				<div class="f-author">
				<?if (intVal($Topic["USER_START_ID"]) > 0):?>
					<a href="<?=$Topic["USER_START_HREF"]?>"><?=$Topic["USER_START_NAME"]?></a>
				<?else:?>
					<?=$Topic["USER_START_NAME"]?>
				<?endif;?>
				</div>
			</td>
			<td align="center"><?=$Topic["POSTS"]?></td>
			<td align="center"><?=$Topic["VIEWS"]?></td>
			<td>
				<a href="<?=$Topic["read_last_message"]?>"><?=$Topic["LAST_POST_DATE"]?></a>
				<div class="f-author">
				<?=GetMessage("FL_TOPIC_AUTHOR1")?>
				<?if (intVal($Topic["LAST_POSTER_ID"]) > 0):?>
					<a href="<?=$Topic["LAST_POSTER_HREF"]?>"><?=$Topic["LAST_POSTER_NAME"]?></a>
				<?else:?>
					<?=$Topic["LAST_POSTER_NAME"]?>
				<?endif;?>
				</div>
			</td>
		</tr><?
		}
		?><tr><th colspan="7" nowrap="nowrap">
		<label for="leaveLink"><?=GetMessage("FM_LEAVE_LINK")?></label> <input type="checkbox" id="leaveLink" name="leaveLink" value="Y" checked="checked" /><br />
		<?=GetMessage("FM_MOVE_TOPIC")?> 
		<select name="newFID">
			<option value="">&nbsp;</option><?
		foreach ($arResult["arForum"]["data"] as $res):
			if ($res["ID"] != $arParams["FID"]):
			?><option value="<?=$res["ID"]?>" <?=(($res["ID"] == $arResult["arForum"]["active"]) ? " selected " : "")?>><?=$res["NAME"];?></option><?
			endif;
		endforeach;
		?></select><br />
	<input type="submit" id="form_topics_submit" value="<?=GetMessage("FM_MOVE")?>"/></th></tr></table>
</form>