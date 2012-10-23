<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
IncludeAJAX();
// *****************************************************************************************
$_REQUEST["ACTION"] = ($_REQUEST["ACTION"] == "MOVE_TO_NEW" ? "MOVE_TO_NEW" : "MOVE_TO_TOPIC");
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] == "Y" ? "Y" : "N");
// *****************************************************************************************
if (!empty($arResult["ERROR_MESSAGE"])):
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
endif;
if (!empty($arResult["OK_MESSAGE"])):
	?><?=ShowError($arResult["OK_MESSAGE"])?><?
endif;
	
?><div class="forum-title"><span><?=GetMessage("F_TITLE_FROM_TOPIC")?></span> &laquo;<a href="<?=$arResult["TOPIC"]["read"]?>"><?=$arResult["TOPIC"]["TITLE"]?></a><?=($arParams["TOPIC"]["DESCRIPTION"] ? ", ".$arParams["TOPIC"]["DESCRIPTION"] : "")?>&raquo; <span><?=GetMessage("F_TITLE_ON_FORUM")?>:<a href="<?=$arResult["FORUM"]["list"]?>"><?=$arResult["FORUM"]["NAME"]?></a></span></div>
	<div class="forum-br"></div>
	<form method="POST" name="MESSAGES" id="MESSAGES" action="<?=POST_FORM_ACTION_URI?>" onsubmit="this.send_form.disabled=true; return true;" class="forum-form">
		<input type="hidden" name="PAGE_NAME" value="message_move" />
		<?=$arResult["sessid"]?>
		<input type="hidden" name="TID" value="<?=$arParams["TID"]?>" />
		<input type="hidden" name="FID" value="<?=$arParams["FID"]?>" />
		<input type="hidden" name="step" value="1" />
<table class="forum-main">
	<thead><tr>
		<th align="right" width="10%"><?=GetMessage("F_HEAD_MOVE_MESSAGE")?></th>
		<td><select name="ACTION" onchange="document.getElementById('MOVE_TO_TOPIC').style.display='none'; document.getElementById('MOVE_TO_NEW').style.display='none'; document.getElementById(this.value).style.display = '';">
			<option value="MOVE_TO_TOPIC" <?=($_REQUEST["ACTION"] == "MOVE_TO_TOPIC" ? "selected='selected'" : "")?>><?=
				GetMessage("F_HEAD_TO_EXIST_TOPIC")?></option>
			<option value="MOVE_TO_NEW" <?=($_REQUEST["ACTION"] == "MOVE_TO_NEW" ? "selected='selected'" : "")?>><?=
				GetMessage("F_HEAD_TO_NEW_TOPIC")?></option>
			</select></td></tr>
	</thead>
	<tbody id="MOVE_TO_TOPIC" <?=($_REQUEST["ACTION"] == "MOVE_TO_NEW" ? "style='display:none;'" : "")?>>
		<tr>
			<th align="right"><font class="starrequired">*</font><?=GetMessage("F_HEAD_TOPIC")?>: </th>
			<td>
				<input type="text" name="newTID" id="newTID" value="<?=intVal($_REQUEST["newTID"])?>" onfocus="ForumSearchTopic(this, 'Y');" onblur="ForumSearchTopic(this, 'N');" style="width:30px;" />
				<input type="button" name="search" value="..." onClick="window.open('<?=CUtil::addslashes(htmlspecialchars($arResult["topic_search"]))?>', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));"  style="width:30px;" />
				<span id="TOPIC_INFO"><?
		if (!empty($arResult["NEW_TOPIC"]["TOPIC"])):
			?>&laquo;<?=$arResult["NEW_TOPIC"]["TOPIC"]["TITLE"]?>&raquo; ( <?=GetMessage("F_TITLE_ON_FORUM")?>: <?=$arResult["NEW_TOPIC"]["FORUM"]["NAME"]?>)<?
		elseif (intVal($_REQUEST["newTID"]) > 0):
			?><?=GetMessage("F_BAD_TOPIC")?><?
		else:
			?><font class="starrequired"><?=GetMessage("F_BAD_NEW_TOPIC")?></font><?
		endif;
				?></span>
			</td>
		</tr>
	</tbody>
	<tbody id="MOVE_TO_NEW" <?=($_REQUEST["ACTION"] == "MOVE_TO_NEW" ? "" : "style='display:none;'")?>>
		<tr>
			<th align="right"><font class="starrequired">*</font><?=GetMessage("F_HEAD_TOPIC_NAME")?>:</th>
			<td><input type="text" name="TITLE" value="<?=htmlSpecialChars($_REQUEST["TITLE"])?>"/></td>
		</tr>
		<tr>
			<th align="right"><?=GetMessage("F_HEAD_TOPIC_DESCR")?>:</th>
			<td><input type="text" name="DESCRIPTION" value="<?=htmlSpecialChars($_REQUEST["DESCRIPTION"])?>" /></td>
		</tr><?
	if ($arParams["SHOW_TAGS"] == "Y"):?>
		<tr>
			<th align="right"><?=GetMessage("F_HEAD_TOPIC_TAGS")?>:</th>
			<td><?
		if (IsModuleInstalled("search")):
		$APPLICATION->IncludeComponent(
			"bitrix:search.tags.input", 
			"", 
			array(
				"VALUE" => htmlSpecialChars($_REQUEST["TAGS"]), 
				"NAME" => "TAGS"),
			$component,
			array("HIDE_ICONS" => "Y"));
		else:
			?><input name="TAGS" type="text" value="<?=htmlSpecialChars($_REQUEST["TAGS"])?>" /><?
		endif;
	?></td></tr><?
	endif;
		?>
		<tr>
			<th align="right"><?=GetMessage("F_HEAD_TOPIC_ICON")?>:</th>
			<td><?=$arResult["ForumPrintIconsList"]?></tr>
	</tbody>

	<tfoot>
		<tr>
			<th colspan="2" nowrap="nowrap">
				<input type="submit" name="send_form" value="<?=GetMessage("F_BUTTON_MOVE")?>" />
			</th>
		</tr>
	</tfoot>
	</table>
	<br />
	<table class="forum-message" width="100%" cellpadding="0" cellspacing="0" border="0">
	<?foreach ($arResult["MESSAGE"] as $res):?>
		<tr valign="top">
			<td class="forum-message-user-info">
				<a name="message<?=$res["ID"];?>"></a>
			<div class="forum-user name forum-message-name"><?
			if ($res["AUTHOR_ID"] > 0):
				?><a href="<?=$res["URL"]["AUTHOR"]?>" class="forum-user name" title="<?=GetMessage("F_AUTHOR_PROFILE")?>"><?=$res["AUTHOR_NAME"]?></a><?
			else:
				?><?=$res["AUTHOR_NAME"]?><?
			endif;
			?></div><?
			
			if (is_array($res["AVATAR"]) && (strLen($res["AVATAR"]["HTML"]) > 0)):
				?><a href="<?=$res["URL"]["AUTHOR"]?>" title="<?=GetMessage("F_AUTHOR_PROFILE")?>" class="forum-user avatar"><?=$res["AVATAR"]["HTML"]?></a><?
			else:
				?><a href="<?=$res["URL"]["AUTHOR"]?>" title="<?=GetMessage("F_AUTHOR_PROFILE")?>" class="forum-user avatar"><div class="no-avatar"></div></a><?
			endif;
			
			if (intVal($res["NUM_POSTS"]) > 0):
				?><div class="forum-user posts forum-message-posts"><span><?=GetMessage("F_NUM_MESS")?></span> <?=$res["NUM_POSTS"];?></div><?
			endif;
			
			if (strlen($res["~DATE_REG"]) > 0):
				?><div class="forum-user datereg forum-message-datereg"><span><?=GetMessage("F_DATE_REGISTER")?></span> <?=$res["DATE_REG"];?></div><?
			endif;
			
			if (strlen($res["DESCRIPTION"]) > 0):
			?><div class="forum-user description forum-message-description"><?=$res["DESCRIPTION"]?></div><?
			endif;
			?>
			</td>
			<td>
				<table class="clear" width="100%"><tr><td>
					<input type="checkbox" checked="checked" name="MID[]" value="<?=$res["ID"]?>" id="MID_<?=$res["ID"]?>_" />&nbsp;
					<label for="MID_<?=$res["ID"]?>_"><span class="forum-message-datecreate"><?=GetMessage("F_DATE_CREATE")?></span>
						<?=$res["POST_DATE"]?></label>
				</td></tr></table>
				<div class="forum-hr"></div>
			<div id='message_post_<?=$res["ID"]?>'><?
				?><div class="forum-message-text" id="message_text_<?=$res["ID"]?>"><?=$res["POST_MESSAGE_TEXT"]?></div><?
				foreach ($res["FILES"] as $arFile): 
				?><div class="forum-message-img"><?
					?><?$GLOBALS["APPLICATION"]->IncludeComponent(
						"bitrix:forum.interface", "show_file",
						Array(
							"FILE" => $arFile,
							"WIDTH" => $arResult["PARSER"]->image_params["width"],
							"HEIGHT" => $arResult["PARSER"]->image_params["height"],
							"CONVERT" => "N",
							"FAMILY" => "FORUM",
							"SINGLE" => "Y",
							"RETURN" => "N",
							"SHOW_LINK" => "Y"),
						null,
						array("HIDE_ICONS" => "Y"));
				?></div><?
				endforeach;
			?></div><?
							
			if (strLen($res["SIGNATURE"]) > 0):
			?><div class="forum-user signature forum-message-signature"><div class="forum-hr"></div><?=$res["SIGNATURE"]?></div><?
			endif;
			?>
			</td>
		</tr>
		<tr><td class="clear" colspan="2"><div class="forum-br"></div></td></tr>
	<?endforeach;?>
	</table>
</form>
<script>
if (typeof oForum != "object")
	var oForum = {};
oForum['topic_search'] = {
	'url' : '<?=CUtil::JSEscape($arResult["topic_search"])?>',
	'object' : false,
	'value' : '<?=intVal($arResult["newTID"])?>', 
	'action' : 'search', 
	'fined' : {}};

if (typeof oText != "object")
		var oText = {};
oText['topic_not_found'] = '<?=CUtil::addslashes(GetMessage("F_BAD_TOPIC"))?>';
oText['topic_bad'] = '<?=CUtil::addslashes('<font class="starrequired">'.GetMessage("F_BAD_NEW_TOPIC").'</font>')?>';
oText['topic_wait'] = '<?=CUtil::addslashes('<i>'.GetMessage("FORUM_MAIN_WAIT").'</i>')?>';
</script>
