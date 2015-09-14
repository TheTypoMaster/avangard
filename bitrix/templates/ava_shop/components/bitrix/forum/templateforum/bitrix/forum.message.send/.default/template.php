<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
	if ($arResult["SHOW_USER"] == "Y")
	{
		?><div class="forum-title"><?=GetMessage("F_TITLE")?></div><?
		?><div class="forum-br"></div><?
		?><form action="<?=POST_FORM_ACTION_URI?>" method="post" name="REPLIER" class="forum-form">
		<input type="hidden" name="PAGE_NAME" value="message_send" />
		<input type="hidden" name="ACTION" value="SEND" />
		<input type="hidden" name="TYPE" value="<?=$arParams["TYPE"]?>" />
		<input type="hidden" name="UID" value="<?=$arParams["UID"]?>" />
		<table class="forum-main">
			<tr><th colspan="2" class="left"><?=$arResult["strTextType"]?> <?=GetMessage("F_TO")?></th></tr>
			<tr>
				<td><?=GetMessage("F_NAME")?></td>
				<td width="100%"><a href="<?=$arResult["profile_view"]?>"><?=$arResult["ShowName"]?></a></td>
			</tr>
			<tr>
				<th colspan="2" class="left"><?=$arResult["strTextType"]?> <?=GetMessage("F_FROM")?></td>
			</tr>
			<tr>
				<td><b><font class="starrequired">*</font></b><?=GetMessage("F_NAME")?></td>
				<td>
				<?if ($arResult["IsAuthorized"] == "Y"):?>
						<?=$arResult["ShowMyName"]?>
				<?else:?>
					<input type="text" name="NAME" value="<?=$arResult["AuthorName"]?>" size="35" />
				<?endif;?>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><b><font class="starrequired">*</font></b><?
					if ($arParams["TYPE"] == "ICQ") 
					{
						?><?=GetMessage("F_ICQ")?><?
					}
					else 
					{
						?><?=GetMessage("F_EMAIL")?><?
					}
				?></td>
				<td><?
				if (!empty($arResult["AuthorContacts"]))
				{
					?><?=$arResult["AuthorContacts"]?><?
				}
				else 
				{
					?><input type="text" name="EMAIL" value="<?=$arResult["AuthorMail"]?>" size="35" /><?
				}
				?></td>
			</tr>
			<tr>
				<th colspan="2" class="left"><?=GetMessage("F_MESSAGE")?></td>
			</tr>
			<tr>
				<td><b><font class="starrequired">*</font></b><?=GetMessage("F_TOPIC")?></td>
				<td><input type="text" name="SUBJECT" value="<?=$arResult["MailSubject"]?>" size="47" maxlength="50" /></td>
			</tr>
			<tr>
				<td><b><font class="starrequired">*</font></b><?=GetMessage("F_TEXT")?></td>
				<td><textarea cols="47" rows="12" wrap="soft" name="MESSAGE"><?=$arResult["MailMessage"]?></textarea><br />
				<?if (!empty($arResult["CAPTCHA_CODE"])):?>
					<b><?=GetMessage("F_CAPTCHA_TITLE")?></b><br />
					<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" /><br />
					<?=GetMessage("F_CAPTCHA_PROMT")?> <input type="text" size="10" name="captcha_word" /><br />
				<?endif;?>
				<input type="submit" value="<?=GetMessage("F_SEND")?> <?=$arResult["strTextType"]?>" /></td>
			</tr>
		</table>
	</form><?
	}
?>