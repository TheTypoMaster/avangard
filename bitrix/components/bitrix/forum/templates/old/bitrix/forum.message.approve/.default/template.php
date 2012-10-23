<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/forum.interface/templates/.default/script.js"></script>', true);
$arResult["SHOW_ICQ"] = (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") != "Y") ? "N" : ($arParams["SEND_ICQ"] > "A" ? "Y" : "N");
$arParams["SHOW_MAIL"] = (($arParams["SEND_MAIL"] <= "A" || ($arParams["SEND_MAIL"] <= "E" && !$GLOBALS['USER']->IsAuthorized())) ? "N" : "Y");
$arParams["HIDE_USER_ACTION"] = ($arParams["HIDE_USER_ACTION"] == "Y" ? "Y" : "N");
// *****************************************************************************************
if (!empty($arResult["ERROR_MESSAGE"])):
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
endif;
if (!empty($arResult["OK_MESSAGE"])):
	?><?=ShowError($arResult["OK_MESSAGE"])?><?
endif;

if (!empty($arResult["NAV_STRING"])):?>
	<?=$arResult["NAV_STRING"]?>
	<div class="forum-br"></div><?
endif;

?><div class="forum-title">
	<input type="checkbox" name="message_all" value="Y" id="message_all" onclick="SelectAllCheckBox('FORUM_MESSAGES', 'message_id[]', 'message_all');" checked="checked" />
	<span><?=GetMessage("F_TITLE")?></span> 
	<?if ($arParams["TID"] > 0):?>
		&laquo;<a href="<?=$arResult["read"]?>"><?=$arResult["TOPIC"]["TITLE"]?></a>&raquo;<span>, </span>
	<?endif;?>
	<span><?=GetMessage("F_FORUM")?> <a href="<?=$arResult["list"]?>"><?=$arResult["FORUM"]["NAME"]?></a></span>
</div>
<div class="forum-br"></div><?

if (empty($arResult["MESSAGE_LIST"])):
	?><table class="forum-main"><tr><th align="left" width="100%"><?=GetMessage("F_EMPTY_RESULT")?></th></tr></table><?
	return false;
endif;

?><form name="FORUM_MESSAGES" id="FORUM_MESSAGES" action="<?=$APPLICATION->GetCurPageParam()?>" class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="message_approve">
	<?=$arResult["sessid"]?>
<table class="forum-message" width="100%" cellpadding="0" cellspacing="0" border="0"><?
foreach ($arResult["MESSAGE_LIST"] as $res):
?><tr valign="top">
	<td class="forum-message-user-info">
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
						"ONCLICK" => "jsUtils.Redirect([], '".$res["URL"]["AUTHOR"]."');"
						));
						
				if ($arParams["SHOW_MAIL"] == "Y" && strlen($res["EMAIL"]) > 0):
					$arUserInfo["email"] = array(
						"TITLE" => GetMessage("F_EMAIL_AUTHOR"),
						"CONTENT" => array(
							"<div class=\"forum-user email\"></div>",
							"E-Mail"),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["URL"]["AUTHOR_EMAIL"]."');"
						);
				endif;
				if ((strLen($res["PERSONAL_ICQ"])>0) && ($arResult["SHOW_ICQ"] == "Y")):
					$arUserInfo["icq"] = array(
						"TITLE" => GetMessage("F_ICQ_AUTHOR"),
						"CONTENT" => array(
							"<div class=\"forum-user icq\"></div>",
							"ICQ"),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["URL"]["AUTHOR_ICQ"]."');"
						);
				endif;
				
				if ($USER->IsAuthorized()):
					$arUserInfo["pm"] = array(
						"TITLE" => GetMessage("F_PRIVATE_MESSAGE"),
						"CONTENT" => array(
							"<div class=\"forum-user pm\"></div>",
							GetMessage("F_PRIVATE_MESSAGE")),
						"ONCLICK" => "jsUtils.Redirect([], '".$res["URL"]["AUTHOR_PM"]."');"
						);
				endif;
				
				$APPLICATION->IncludeComponent(
					"bitrix:forum.interface", "popup", 
					array("DATA" => $arUserInfo), 
					$component, 
					array("HIDE_ICONS" => "Y"));
				endif;
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
	?></td>
	<td>
		<table class="clear" width="100%">
			<tr><td width="100%">
				<input type="checkbox" name="message_id[]" value="<?=$res["ID"]?>" id="message_id_<?=$res["ID"]?>_" checked="checked" />
				<label for="message_id_<?=$res["ID"]?>_"><span class="forum-message-datecreate"><?=GetMessage("F_DATE_CREATE")?></span> 
				<?=$res["POST_DATE"]?></label></td>
				<td><a title="" href="<?=$res["URL"]["MESSAGE"]?>" class="forum-button-small"><?=GetMessage("F_GO_TO_MESSAGE")?></a></td></tr></table>
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
	?></td>
</tr>
<tr><td colspan="2" class="clear"><div class="forum-br"></div></td></tr><?
endforeach;
?></table>
</form>
<?

if (!empty($arResult["NAV_STRING"])):?>
	<div class="forum-br"></div>
	<?=$arResult["NAV_STRING"]?><?
endif;
?>