<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$arParams["AJAX_TYPE"] = ($arParams["AJAX_TYPE"] == "Y" ? "Y" : "N");
$arResult["SHOW_ICQ"] = (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") != "Y") ? "N" : ($arParams["SEND_ICQ"] > "A" ? "Y" : "N");
$arResult["SHOW_MAIL"] = ($arParams["SEND_MAIL"] > "A" ? "Y" : "N");
$arParams["HIDE_USER_ACTION"] = ($arParams["HIDE_USER_ACTION"] == "Y" ? "Y" : "N");

if ($arParams["AJAX_TYPE"] == "Y")
	IncludeAJAX();
$res = false;
// *****************************************************************************************
?><div id="forum_errors_top" class="forum-error"><?=ShowError($arResult["ERROR_MESSAGE"])?></div>
<div id="forum_notes_top" class="forum-note"><?=ShowNote($arResult["OK_MESSAGE"])?></div>
<table class="clear" width="100%"><tr><td><?=$arResult["NAV_STRING"]?></td><?
	if ($arResult["CanUserAddTopic"]):
		?><td><a href="<?=$arResult["topic_new"]?>" title="<?=GetMessage("F_CREATE_NEW_TOPIC_T")?>" class="forum-button"><?
			?><?=GetMessage("F_CREATE_NEW_TOPIC")?></a></td><?
	endif;
?></tr></table>
<div class="forum-br"></div>
<div class="forum-title"><span class="views"><?=GetMessage("F_ON_VIEWS")?> <?=$arResult["TOPIC"]["VIEWS"]?></span><?
	if ($arResult["UserPermission"]>="Q"){?><input type="checkbox" name="control_element" id="control_element" value="Y" onclick="SelectElements(this);" /><?}
	?><?=GetMessage("F_TOPIC")?><?
	if ($arResult["TOPIC"]["SORT"] != 150){?> <?=GetMessage("F_PINNED")?><?
		if ($arResult["TOPIC"]["STATE"] != "Y") {?> <?=GetMessage("F_AND")?><?}}
	if ($arResult["TOPIC"]["STATE"] != "Y"){?> <?=GetMessage("F_CLOSED")?><?}
	?>: &laquo;<b><?=trim($arResult["TOPIC"]["TITLE"])?></b><?
 		if (strlen($arResult["TOPIC"]["DESCRIPTION"])>0):
			?>, <?=trim($arResult["TOPIC"]["DESCRIPTION"])?><?
		endif;
	?>&raquo; <?=GetMessage("F_ON_FORUM")?>  <a href="<?=$arResult["list"]?>"><?=$arResult["FORUM"]["NAME"]?></a></div>
<div class="forum-br"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forum-message"><?
	foreach ($arResult["MESSAGE_LIST"] as $res):?>
	<tr valign="top">
		<td class="forum-message-user-info" rowspan="2">
			<a name="message<?=$res["ID"];?>"></a>
			<div class="forum-user name forum-message-name"><?
			if ($res["AUTHOR_ID"] > 0):
				if ($arParams["HIDE_USER_ACTION"] == "Y"):
				$arUserInfo = array(
					"profile" => array(
						"TITLE" => GetMessage("F_AUTHOR_PROFILE"),
						"CONTENT" => array(
							"<div class=\"forum-user profile\"></div>",
							GetMessage("F_PROFILE")),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["profile_view"]."');"
						));
						
				if (strlen($res["EMAIL"])>0 && ($arResult["SHOW_MAIL"] == "Y")):
					$arUserInfo["email"] = array(
						"TITLE" => GetMessage("F_EMAIL_AUTHOR"),
						"CONTENT" => array(
							"<div class=\"forum-user email\"></div>",
							"E-Mail"),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["email"]."');"
						);
				endif;
				if ((strLen($res["PERSONAL_ICQ"])>0) && ($arResult["SHOW_ICQ"] == "Y")):
					$arUserInfo["icq"] = array(
						"TITLE" => GetMessage("F_ICQ_AUTHOR"),
						"CONTENT" => array(
							"<div class=\"forum-user icq\"></div>",
							"ICQ"),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["icq"]."');"
						);
				endif;
				
				if ($USER->IsAuthorized()):
					$arUserInfo["pm"] = array(
						"TITLE" => GetMessage("F_PRIVATE_MESSAGE"),
						"CONTENT" => array(
							"<div class=\"forum-user pm\"></div>",
							GetMessage("F_PRIVATE_MESSAGE")),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["pm_edit"]."');"
						);
				endif;
				
				if (($res["VOTES"]["ACTION"] == "VOTE") || ($res["VOTES"]["ACTION"] == "UNVOTE")):
					$arUserInfo["vote"] = array(
						"TITLE" => (($res["VOTES"]["ACTION"] == "VOTE") ? GetMessage("F_NO_VOTE_DO") : GetMessage("F_NO_VOTE_UNDO")),
						"CONTENT" => array(
							"<div class=\"forum-user ".strtolower($res["VOTES"]["ACTION"])."\"></div>",
							(($res["VOTES"]["ACTION"] == "VOTE") ? GetMessage("F_NO_VOTE_DO") : GetMessage("F_NO_VOTE_UNDO"))),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["VOTES"]["link"]."');"
						);
				endif;
				
				
				$APPLICATION->IncludeComponent(
					"bitrix:forum.interface", "popup", 
					array("DATA" => $arUserInfo), 
					$component, 
					array("HIDE_ICONS" => "Y"));
				endif;
				?><a href="<?=$res["profile_view"]?>" class="forum-user name" title="<?=GetMessage("F_AUTHOR_PROFILE")?>"><?=$res["AUTHOR_NAME"]?></a><?
			else:
				?><?=$res["AUTHOR_NAME"]?><?
			endif;
			?></div><?
			
			if (is_array($res["AVATAR"]) && (strLen($res["AVATAR"]["HTML"]) > 0)):
				?><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("F_AUTHOR_PROFILE")?>" class="forum-user avatar"><?=$res["AVATAR"]["HTML"]?></a><?
			endif;
			
			if (strLen(trim($res["AUTHOR_STATUS"]))):
				?><div class="forum-user status forum-message-status "><?=$res["AUTHOR_STATUS"]?></div><?
			endif;

			if (($arParams["HIDE_USER_ACTION"] == "Y") && ($res["VOTES"]["ACTION"] == "VOTE") || ($res["VOTES"]["ACTION"] == "UNVOTE")):
				?><div class="forum-user voting forum-message-voting"><?
					?><a href="<?=$res["VOTES"]["link"]?>" class="forum-button-small forum-button-small-<?=strToLower($res["VOTES"]["ACTION"])?>"><?
						?><?=(($res["VOTES"]["ACTION"] == "VOTE") ? GetMessage("F_NO_VOTE_DO") : GetMessage("F_NO_VOTE_UNDO"));?></a></div><?
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
			
			
		?></td>
		<td class="border-bottom">
			<table class="clear" width="100%">
				<tr><td width="100%"><?
				if ($arResult["UserPermission"] >= "Q"):?>
					<input type="checkbox" name="message_id[]" value="<?=$res["ID"]?>" id="message_id_<?=$res["ID"]?>_" /><?
				endif;?> 
					<label for="message_id_<?=$res["ID"]?>_"><span class="forum-message-datecreate"><?=GetMessage("F_DATE_CREATE")?></span> 
					<?=$res["POST_DATE"]?></label></td>
				<td><a href="http://<?=$_SERVER["HTTP_HOST"]?><?=$res["MESSAGE_ANCHOR"]?>#message<?=$res["ID"]?>" <?
					?>onclick="prompt('<?=GetMessage("F_ANCHOR_TITLE")?>:', 'http://<?=$_SERVER["HTTP_HOST"]?><?
						?><?=$res["MESSAGE_ANCHOR"]?>#message<?=$res["ID"]?>'); return false;" <?
					?>title="<?=GetMessage("F_ANCHOR_TITLE")?>" class="forum-button-small">#<?=$res["NUMBER"]?></a></td><?

				if ($arResult["UserPermission"] >= "I" && $arResult["TOPIC"]["STATE"] == "Y"):
				?><td><div class="empty"></div></td><td><a href="#postform" onmousedown="reply2author('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>,')" <?
					?>title="<?=GetMessage("F_INSERT_NAME")?>" class="forum-button-small"><?=GetMessage("F_NAME")?></a></td><?
				endif;
				
				if ($arResult["UserPermission"] >= "I" && $arResult["TOPIC"]["STATE"] == "Y" && 
					$arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):
				?><td><div class="empty"></div></td><td><a href="#postform" <?
					?>onmousedown="if (window['quoteMessageEx']){quoteMessageEx('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>', 'message_text_<?=$res["ID"]?>')}" <?
					?>title="<?=GetMessage("F_QUOTE_HINT")?>" class="forum-button-small"><?=GetMessage("F_QUOTE")?></a></td><?
				endif;

				?></tr>
			</table>
			<div class="forum-hr"></div><?
			
			if (($res["MESSAGE_EDIT"]["ACTION"] == "EDIT") && ($arParams["AJAX_TYPE"] == "Y")):
				?><a href="<?=$res["MESSAGE_EDIT"]["link"]?>" <?
					?>class="forum-button-small forum-button-small-edit-ajax" <?
					?>onclick="ForumSendMessage('<?=$res["ID"]?>', this.href, '<?=$arParams["AJAX_TYPE"]?>'); return false;" <?
					?>title="<?=GetMessage("F_EDIT_MESS")?>" ></a><?
			endif;
			
			?><div id='message_post_<?=$res["ID"]?>'><?
				?><div class="forum-message-text" id="message_text_<?=$res["ID"]?>"><?=$res["POST_MESSAGE_TEXT"]?></div><?
			
				if (strLen($res["ATTACH_IMG"]) > 0):
				?><div class="forum-message-img"><?
					?><?$GLOBALS["APPLICATION"]->IncludeComponent(
						"bitrix:forum.interface",
						"show_file",
						Array(
							"FILE" => $res["~ATTACH_FILE"],
							
							"WIDTH"=> $arResult["PARSER"]->image_params["width"],
							"HEIGHT"=> $arResult["PARSER"]->image_params["height"],
							"CONVERT" => "N",
							"FAMILY" => "FORUM",
							"SINGLE" => "Y",
							"RETURN" => "N",
							"SHOW_LINK" => "Y"
						),
						null,
						array("HIDE_ICONS" => "Y"));
				?></div><?
				endif;
				if (!empty($res["EDITOR_NAME"])):
				?><div class="forum-message-edit">
					<div class="head"><span class="head"><?=GetMessage("F_EDIT_HEAD")?></span> <?
					?><span class="forum-user editor-name"><?
					if (!empty($res["EDITOR_LINK"])):
						?><a href="<?=$res["EDITOR_LINK"]?>"><?=$res["EDITOR_NAME"]?></a><?
					else:
						?><?=$res["EDITOR_NAME"]?><?
					endif;
					?></span><?
					?> <span class="edit-date"><?=$res["EDIT_DATE"]?></span></div><?
					?><div class="body"><?=$res["EDIT_REASON"]?></div><?
				?></div><?
				endif;
			?></div><?
			
			if (strLen($res["SIGNATURE"]) > 0):
				?><div class="forum-user signature forum-message-signature"><div class="forum-hr"></div><?=$res["SIGNATURE"]?></div><?
			endif;
		?></td>
	</tr>
	<tr valign="bottom">
		<td class="border-top">
			<div class="forum-hr"></div>
			<table class="clear" width="100%">
				<tr valign="top">
					<td><?
					if ($res["AUTHOR_ID"] > 0 && $arParams["HIDE_USER_ACTION"] != "Y"):?>
						<table class="clear">
							<tr>
								<td><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("F_AUTHOR_PROFILE")?>" class="forum-button-small"><?=GetMessage("F_PROFILE")?></a></td>
								<td><div class="empty"></div></td><?

						if (strlen($res["EMAIL"])>0 && ($arResult["SHOW_MAIL"] == "Y")):?>
								<td><a href="<?=$res["email"]?>" title="<?=GetMessage("F_EMAIL_AUTHOR")?>" class="forum-button-small">E-Mail</a></td>
								<td><div class="empty"></div></td><?
						endif;
						
						if ((strLen($res["PERSONAL_ICQ"])>0) && ($arResult["SHOW_ICQ"] == "Y")):?>
								<td><a href="<?=$res["icq"]?>" title="<?=GetMessage("F_ICQ_AUTHOR")?>" class="forum-button-small">ICQ</a></td>
								<td><div class="empty"></div></td><?
						endif;
						
						if ($USER->IsAuthorized()):?>
								<td><a href="<?=$res["pm_edit"]?>" title="<?=GetMessage("F_PRIVATE_MESSAGE")?>"  class="forum-button-small pm"><?=GetMessage("F_PRIVATE_MESSAGE")?></a></td>
								<td><div class="empty"></div></td><?
						endif;
						
						if (($res["VOTES"]["ACTION"] == "VOTE") || ($res["VOTES"]["ACTION"] == "UNVOTE")):?>
								<td><a href="<?=$res["VOTES"]["link"]?>" class="forum-button-small forum-button-small-<?=strToLower($res["VOTES"]["ACTION"])?>"><?=(($res["VOTES"]["ACTION"] == "VOTE") ? GetMessage("F_NO_VOTE_DO") : GetMessage("F_NO_VOTE_UNDO"));?></a></td>
								<td><div class="empty"></div></td><?
						endif;?>
							</tr>
						</table>
						<div class="forum-br"></div>
					<?endif;
					
					if ($res["SHOW_PANEL"] == "Y"):
						?><table class="clear">
							<tr><?
							
							if ($res["SHOW_HIDE"]["ACTION"] == "HIDE"):
								?><td><a href="<?=$res["SHOW_HIDE"]["link"]?>" title="<?=GetMessage("F_HIDE_MESS")?>" <?
									?>class="forum-button-small forum-button-small-hide"><?=GetMessage("F_HIDE")?></a></td><?
								?><td><div class="empty"></div></td><?
							elseif ($res["SHOW_HIDE"]["ACTION"] == "SHOW"):
								?><td><a href="<?=$res["SHOW_HIDE"]["link"]?>" title="<?=GetMessage("F_SHOW_MESS")?>" <?
									?>class="forum-button-small forum-button-small-show"><?=GetMessage("F_SHOW")?></a></td><?
								?><td><div class="empty"></div></td><?
							endif;
							
							if ($res["MESSAGE_EDIT"]["ACTION"] == "EDIT"):
								?><td><a href="<?=$res["MESSAGE_EDIT"]["link"]?>" title="<?=GetMessage("F_EDIT_MESS")?>" <?
									?>class="forum-button-small forum-button-small-edit"><?=GetMessage("F_EDIT")?></a><?
								?></td><td><div class="empty"></div></td><?
							endif;
							
							if ($res["MESSAGE_DELETE"]["ACTION"] == "DELETE"):
								?><td><a href="<?=$res["MESSAGE_DELETE"]["link"]?>" title="<?=GetMessage("F_DELETE_MESS")?>" <?
									?>class="forum-button-small forum-button-small-del" onclick="return confirm('<?=GetMessage("F_DELETE_CONFIRM");?>');"><?
								?><?=GetMessage("F_DELETE")?></a></td><td><div class="empty"></div></td><?
							endif;
							
							if($res["MESSAGE_SUPPORT"]["ACTION"] == "SUPPORT"):
							?><td><a href="<?=$res["MESSAGE_SUPPORT"]["link"]?>" title="<?=GetMessage("F_MOVE2SUPPORT")?>" <?
								?>class="forum-button-small forum-button-small-support"><?=GetMessage("F_2SUPPORT")?></a></td><?
							endif;
							
							?></tr>
						</table><?
					endif //SHOW_PANEL?>
				</td>
				<td><a href="#" onclick="scroll(0,0);" title="<?=GetMessage("F_2TOP")?>" class="forum-button-small forum-button-small-top"></a></td>
				</tr>
			</table><?
			
			if ($arResult["UserPermission"]>="Q"):
				?><div class="forum-br"></div><?
				if ($res["IP_IS_DIFFER"] == "Y"):
					?>IP<?=GetMessage("F_REAL_IP")?>: <?=$res["AUTHOR_IP"];?>&nbsp;/ <?=$res["AUTHOR_REAL_IP"];?><br /><?
				else:
					?>IP:<?=$res["AUTHOR_IP"];?><br /><?
				endif;
				
				if ($res["SHOW_STATISTIC"] == "Y"):
					?><?=GetMessage("F_USER_ID")?> <a href="/bitrix/admin/guest_list.php?lang=<?=LANGUAGE_ID?>&amp;find_id=<?=$res["GUEST_ID"]?>&amp;set_filter=Y"><?=$res["GUEST_ID"];?></a><br /><?
				endif;
					
				if ($res["SHOW_AUTHOR_ID"] == "Y"):
					?><?=GetMessage("F_USER_ID_USER")?> <a href="/bitrix/admin/user_edit.php?lang=<?=LANG_ADMIN_LID?>&amp;ID=<?=$res["AUTHOR_ID"]?>"><?=$res["AUTHOR_ID"];?></a><br /><?
				endif;
			endif;
		?></td>
	</tr>
	<tr><td colspan="2" class="clear"><div class="forum-br"></div></td></tr><?
	endforeach;
	
?></table>

<table class="clear" width="100%"><tr><td><?=$arResult["NAV_STRING"]?></td><?
	if ($arResult["CanUserAddTopic"]):
	?><td><a href="<?=$arResult["topic_new"]?>" title="<?=GetMessage("F_CREATE_NEW_TOPIC_T")?>" class="forum-button"><?=GetMessage("F_CREATE_NEW_TOPIC")?></a></td><?
	endif;
	?></tr></table>
<div id="forum_errors_bottom" class="forum-error"><?=ShowError($arResult["ERROR_MESSAGE"])?></div>
<div id="forum_notes_bottom" class="forum-note"><?=ShowNote($arResult["OK_MESSAGE"])?></div><?
	
// View new posts
if ($arResult["VIEW"] == "Y"):
?><table width="100%" border="0" cellpadding="0" cellspacing="0" class="forum-message">
	<tr><td class="forum-message-user-info" rowspan="2"><div class="forum-message-name"><?=GetMessage("F_VIEW")?></div></td>	<td class="border-bottom"><div class="forum-hr"></div><?=$arResult["POST_MESSAGE_VIEW"];?></td></tr><tr><td class="border-top"><div class="forum-hr"></div></td></tr></table><?
?><div class="forum-br"></div><?
endif;
	

?><script type="text/javascript">
<?if (intVal($arParams["MID"]) > 0):?>
	location.hash = 'message<?=$arParams["MID"]?>';
<?endif;?>

if (typeof oText != "object")
		var oText = {};
oText['del_topic'] = '<?=CUtil::addslashes(GetMessage("JS_DEL_TOPIC"))?>';
oText['del_messages'] = '<?=CUtil::addslashes(GetMessage("JS_DEL_MESSAGES"))?>';
oText['del_message'] = '<?=CUtil::addslashes(GetMessage("JS_DEL_MESSAGE"))?>';
oText['no_data'] = '<?=CUtil::addslashes((($arResult["sSection"] == "LIST") ? GetMessage('JS_NO_TOPICS') : GetMessage('JS_NO_MESSAGES')))?>';
oText['quote_text'] = '<?=CUtil::addslashes(GetMessage("JQOUTE_AUTHOR_WRITES"));?>';
oText['no_message'] = '<?=CUtil::addslashes(GetMessage("JERROR_NO_MESSAGE"));?>';

function reply2author(name)
{
	if (document.REPLIER.POST_MESSAGE)
	{
		document.REPLIER.POST_MESSAGE.value += <?=(($arResult["FORUM"]["ALLOW_BIU"] == "Y") ? "'[b]'+name+'[/b]'" : "name")?> + " \n";
	}
	return false;
}
</script>
<?
//GetMessage("F_TOP");
//GetMessage("F_LINK");
//GetMessage("F_ANCHOR");
//GetMessage("F_TOP");
?>