<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (!is_set($MESSAGE_TYPE) || (($MESSAGE_TYPE!="REPLY") && ($MESSAGE_TYPE!="EDIT"))) $MESSAGE_TYPE = "NEW";

$FID = IntVal($FID);
$TID = IntVal($TID);
$MID = IntVal($MID);
?><script language="Javascript"><?
	if ($strJSPath = $APPLICATION->GetTemplatePath("forum/forum_tmpl_1/forum_js.php"))
		include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
	?>
	function View()
	{
		var form_forum = document.REPLIER;
		form_forum.MESSAGE_MODE.value = 'VIEW';
		return;
	}
	</script><?

if (
	$MESSAGE_TYPE=="REPLY" && $TID>0 && CForumMessage::CanUserAddMessage($TID, $USER->GetUserGroupArray(), $USER->GetID())
	|| $MESSAGE_TYPE=="NEW" && $FID>0 && CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())
	|| $MESSAGE_TYPE=="EDIT" && $MID>0 && CForumMessage::CanUserUpdateMessage($MID, $USER->GetUserGroupArray(), IntVal($USER->GetID()))
	)
{
	
	$str_USE_SMILES = "Y";
	$str_AUTHOR_ID = IntVal($USER->GetParam("USER_ID"));
	
	if ($MESSAGE_TYPE=="EDIT")
	{
		$arMessage = CForumMessage::GetByID($MID);
		if ($arMessage)
		{
			$arTopic = CForumTopic::GetByID(intval($arMessage["TOPIC_ID"]), array("NoFilter" => 'true'));
			$str_AUTHOR_NAME = htmlspecialchars($arMessage["AUTHOR_NAME"]);
			$str_AUTHOR_EMAIL = htmlspecialchars($arMessage["AUTHOR_EMAIL"]);
			$str_TITLE = htmlspecialchars($arTopic["TITLE"]);
			$str_DESCRIPTION = htmlspecialchars($arTopic["DESCRIPTION"]);
			$str_POST_MESSAGE = htmlspecialchars($arMessage["POST_MESSAGE"]);
			$str_ICON_ID = IntVal($arTopic["ICON_ID"]);
			$str_USE_SMILES = ($arMessage["USE_SMILES"]=="Y") ? "Y" : "N";
			$str_AUTHOR_ID = IntVal($arMessage["AUTHOR_ID"]);
			$str_ATTACH_IMG = $arMessage["ATTACH_IMG"];
		}
	}

	if ($bVarsFromForm)
	{
		$str_AUTHOR_NAME = htmlspecialchars($AUTHOR_NAME);
		$str_AUTHOR_EMAIL = htmlspecialchars($AUTHOR_EMAIL);
		$str_TITLE = htmlspecialchars($TITLE);
		$str_DESCRIPTION = htmlspecialchars($DESCRIPTION);
		$str_POST_MESSAGE = htmlspecialchars($POST_MESSAGE);
		$str_ICON_ID = IntVal($ICON_ID);
		$str_USE_SMILES = ($USE_SMILES=="Y") ? "Y" : "N";
	}
	?>

	<form action="<?echo $APPLICATION->GetCurPage();?>?FID=<?echo $FID;?>&TID=<?echo $TID;?>#postform" method="post" name="REPLIER" id="REPLIER" enctype="multipart/form-data" onsubmit="return ValidateForm(this);">
		<input type="hidden" name="MID" value="<?echo $MID;?>">
		<input type="hidden" name="MESSAGE_TYPE" value="<?echo $MESSAGE_TYPE;?>">
		<input type="hidden" name="MESSAGE_MODE" value="">
		<input type="hidden" name="AUTHOR_ID" value="<?echo $str_AUTHOR_ID;?>">

		<?if (!$View):?>
			<a name="postform"></a>
		<?endif;?>
		<?if (strlen($strErrorMessage)>0 || strlen($strOKMessage)>0):?>
			<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
			<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>
		<?endif;?>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" class="forumtitle"><?
					if ($MESSAGE_TYPE=="NEW")
						echo GetMessage("FPF_CREATE_IN_FORUM")." \"".$arForum["NAME"]."\"";
					elseif ($MESSAGE_TYPE=="REPLY")
						echo GetMessage("FPF_REPLY_FORM");
					else
						echo GetMessage("FPF_EDIT_FORM");
				?></td>
			</tr>
		</table>

		<font style="font-size:4px;">&nbsp;<br></font>

		<table width="100%" border="0" cellspacing="0" cellpadding="5">
			
			<?if (($MESSAGE_TYPE=="NEW" || $MESSAGE_TYPE=="REPLY") && !$USER->IsAuthorized() || $MESSAGE_TYPE=="EDIT" && $str_AUTHOR_ID<=0):?>
				<tr class="forumhead">
					<td colspan="2" class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><?echo GetMessage("FPF_UNREG_USER_INFO")?></font></td>
				</tr>
				<tr valign="top" class="forumbody">
					<td class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><b><font class="starrequired">*</font></b><?echo GetMessage("FPF_TYPE_NAME")?></font></td>
					<td width="100%" class="forumbrd2" style="border-left:none;border-bottom:none;"><input type="text" name="AUTHOR_NAME" size="40" maxlength="64" value="<?echo (strlen($str_AUTHOR_NAME)>0) ? $str_AUTHOR_NAME : GetMessage("FPF_GUEST");?>" class="inputtext"></td>
				</tr>
				<?if ($arForum["ASK_GUEST_EMAIL"]=="Y"):?>
					<tr valign="top" class="forumbody">
						<td class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><?echo GetMessage("FPF_TYPE_EMAIL")?></font></td>
						<td width="100%" class="forumbrd2" style="border-bottom:none;border-left:none;"><input type="text" name="AUTHOR_EMAIL" size="40" maxlength="64" value="<?echo (strlen($str_AUTHOR_EMAIL)>0) ? $str_AUTHOR_EMAIL : "";?>" class="inputtext"></td>
					</tr>
				<?endif;?>
			<?endif;?>
			
			<?if ($MESSAGE_TYPE=="NEW" || $MESSAGE_TYPE=="EDIT" && CForumTopic::CanUserUpdateTopic($TID, $USER->GetUserGroupArray(), $USER->GetID())):?>
				<tr class="forumhead">
					<td colspan="2" class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><?echo GetMessage("FPF_TOPIC_PARAMS")?></font></td>
				</tr>
				<tr valign="top" class="forumbody">
					<td class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><b><font class="starrequired">*</font></b><?echo GetMessage("FPF_TOPIC_NAME")?></font></td>
					<td width="100%" class="forumbrd2" style="border-bottom:none;border-left:none;"><input type="text" name="TITLE" size="50" maxlength="70" value="<?echo (strlen($str_TITLE)>0) ? $str_TITLE : "";?>" class="inputtext"></td>
				</tr>
				<tr valign="top" class="forumbody">
					<td class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><?echo GetMessage("FPF_TOPIC_DESCR")?></font></td>
					<td width="100%" class="forumbrd2" style="border-bottom:none;border-left:none;"><input type="text" name="DESCRIPTION" size="50" maxlength="70" value="<?echo (strlen($str_DESCRIPTION)>0) ? $str_DESCRIPTION : "";?>" class="inputtext"></td>
				</tr>
				<tr valign="top" class="forumbody">
					<td class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><?echo GetMessage("FPF_TOPIC_ICON")?></font></td>
					<td width="100%" class="forumbrd2" style="border-bottom:none;border-left:none;"><?
						echo ForumPrintIconsList(7, "ICON_ID", $str_ICON_ID, GetMessage("FPF_NO_ICON"), LANGUAGE_ID);
					?></td>
				</tr>
			<?endif;?>
			
			<tr class="forumhead">
				<td colspan="2" class="forumbrd2" style="border-bottom:none;"><font class="forumheadtext"><?echo GetMessage("FPF_MESSAGE_TEXT")?></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td class="forumbrd2" style="border-bottom:none;">
					<?if ($arForum["ALLOW_SMILES"]=="Y"):?>
						<br>
						<table align="center" cellspacing="1" cellpadding="5" border="0" style="border-width:1px; border-color:#999999; border-style:solid;">
							<tr>
								<td colspan="3" align="center" style="border-bottom:1px; border-bottom-color:#999999; border-bottom-style:solid;">
									<font class="forumheadtext"><?echo GetMessage("FPF_SMILES")?></font>
								</td>
							</tr>
							<?
							echo ForumPrintSmilesList(3, LANGUAGE_ID);
							?>
						</table>
					<?endif;?>
				</td>
				<td width="100%" class="forumbrd2" style="border-left:none;border-bottom:none;">
					<table cellpadding='2' cellspacing='2' width='100%' align='center'>
						<tr>
							<td nowrap width='10%'><font class="text">
							<?if ($arForum["ALLOW_BIU"] == "Y"):?>
								<input type='button' accesskey='b' value=' B ' onClick='simpletag("B")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle; font-weight:bold" name='B' title="<?echo GetMessage("FPF_BOLD")?>" onMouseOver="show_hints('bold')">&nbsp;
								<input type='button' accesskey='i' value=' I ' onClick='simpletag("I")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle; font-style:italic" name='I' title="<?echo GetMessage("FPF_ITAL")?>" onMouseOver="show_hints('italic')">&nbsp;
								<input type='button' accesskey='u' value=' U ' onClick='simpletag("U")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle; text-decoration:underline" name='U' title="<?echo GetMessage("FPF_UNDER")?>" onMouseOver="show_hints('under')">&nbsp;
							<?endif;?>
							<?if ($arForum["ALLOW_FONT"] == "Y"):?>
								<select name='ffont' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" onchange="alterfont(this.options[this.selectedIndex].value, 'FONT')" onMouseOver="show_hints('font')">
									<option value='0'><?echo GetMessage("FPF_FONT")?></option>
									<option value='Arial' style='font-family:Arial'>Arial</option>
									<option value='Times' style='font-family:Times'>Times</option>
									<option value='Courier' style='font-family:Courier'>Courier</option>
									<option value='Impact' style='font-family:Impact'>Impact</option>
									<option value='Geneva' style='font-family:Geneva'>Geneva</option>
									<option value='Optima' style='font-family:Optima'>Optima</option>
								</select>&nbsp;
								<select name='fcolor' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" onchange="alterfont(this.options[this.selectedIndex].value, 'COLOR')" onMouseOver="show_hints('color')">
									<option value='0'><?echo GetMessage("FPF_COLOR")?></option>
									<option value='blue' style='color:blue'><?echo GetMessage("FPF_BLUE")?></option>
									<option value='red' style='color:red'><?echo GetMessage("FPF_RED")?></option>
									<option value='gray' style='color:gray'><?echo GetMessage("FPF_GRAY")?></option>
									<option value='green' style='color:green'><?echo GetMessage("FPF_GREEN")?></option>
								</select>&nbsp; 
							<?endif;?>
								<a href='javascript:closeall();' title="<?echo GetMessage("FPF_CLOSE_OPENED_TAGS")?>" onMouseOver="show_hints('close')"><?echo GetMessage("FPF_CLOSE_ALL_TAGS")?></a></font></td>
						</tr>
						<tr>
							<td align='left'>
							<?if ($arForum["ALLOW_ANCHOR"] == "Y"):?>
								<input type='button' accesskey='h' value=' http:// ' onClick='tag_url()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='url' title="<?echo GetMessage("FPF_HYPERLINK")?>" onMouseOver="show_hints('url')">
							<?endif;?>
							<?if ($arForum["ALLOW_IMG"] == "Y"):?>
								<input type='button' accesskey='g' value=' IMG ' onClick='tag_image()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='img' title="<?echo GetMessage("FPF_IMAGE")?>" onMouseOver="show_hints('img')">
							<?endif;?>
							<?if ($arForum["ALLOW_QUOTE"] == "Y"):?>
								<input type='button' accesskey='q' value=' QUOTE ' onClick='simpletag("QUOTE")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='QUOTE' title="<?echo GetMessage("FPF_QUOTE")?>" onMouseOver="show_hints('quote')">
							<?endif;?>
							<?if ($arForum["ALLOW_CODE"] == "Y"):?>
								<input type='button' accesskey='p' value=' CODE ' onClick='simpletag("CODE")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='CODE' title="<?echo GetMessage("FPF_CODE")?>" onMouseOver="show_hints('code')">
							<?endif;?>
							<?if ($arForum["ALLOW_LIST"] == "Y"):?>
								<input type='button' accesskey='l' value=' LIST ' onClick='tag_list()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name="LIST" title="<?echo GetMessage("FPF_LIST")?>" onMouseOver="show_hints('list')">
							<?endif;?>
								<?if (LANGUAGE_ID=="ru"):?>
									<input type='button' accesskey='t' value=' Транслит ' onClick='translit()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name="TRANSLIT" title="<?echo GetMessage("FPF_TRANSLIT")?>" onMouseOver="show_hints('translit')">
								<?endif;?>
							</td>
						</tr>
						<tr>
							<td align='left' valign='middle'>
								<font class="text">
								<?echo GetMessage("FPF_OPENED_TAGS")?><input type='text' name='tagcount' size='3' maxlength='3' style='font-size:10px;font-family:verdana,arial;border: 0 solid;font-weight:bold;' readonly class='forumbody' value="0">
								&nbsp;<input type='text' name='helpbox' size='50' maxlength='120' style='width:80%;font-size:10px;font-family:verdana,arial;border: 0 solid;' readonly class='forumbody' value="">
								</font>
							</td>
						</tr>
					</table>



					<textarea cols="55" rows="15" wrap="soft" name="POST_MESSAGE" tabindex="3" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" class="inputtextarea"><?echo (strlen($str_POST_MESSAGE)>0) ? $str_POST_MESSAGE: "";?></textarea>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td colspan="2">
								<font class="forumbodytext"><?echo GetMessage("FPF_TO_QUOTE_NOTE")?> <b><?echo GetMessage("FPF_TO_QUOTE_NOTE1")?></b>.<br><br>
							</td>
						</tr>
						<?if ($arForum["ALLOW_SMILES"]=="Y"):?>
							<tr>
								<td>
									<input type="checkbox" name="USE_SMILES" value="Y" <? echo ($str_USE_SMILES=="Y") ? "checked" : "";?> class="inputcheckbox">
								</td>
								<td width="100%">
									<font class="forumbodytext"><?echo GetMessage("FPF_WANT_ALLOW_SMILES")?></font>
								</td>
							</tr>
						<?endif;?>
						
						
						<?if ($USER->IsAuthorized() && (ForumCurrUserPermissions($FID) > "E")):
							$arFields = array(
								"USER_ID" => $USER->GetID(),
								"FORUM_ID" => $FID,
								"SITE_ID" => LANG
								);
							$db_res = CForumSubscribe::GetList(array(), $arFields);
							$topic_subscribe = false;
							$forum_subscribe = false;
							if ($db_res)
							{
								$res = array();
								while ($res = $db_res->Fetch())
								{
									if ((intVal($res["TOPIC_ID"]) <= 0) && ($res["NEW_TOPIC_ONLY"] != "Y"))
									{
										$forum_subscribe = true;
									}
									elseif($res["TOPIC_ID"] == $TID) 
									{
										$topic_subscribe = true;
									}
								}
							}
						?>
							<tr>
								<td>
									<input type="checkbox" name="TOPIC_SUBSCRIBE" value="Y" <?=$topic_subscribe? "checked disabled " : "";?> class="inputcheckbox">
								</td>
								<td width="100%">
									<font class="forumbodytext"><?=GetMessage("FPF_WANT_SUBSCRIBE_TOPIC")?></font>
								</td>
							</tr>
							<tr>
								<td>
									<input type="checkbox" name="FORUM_SUBSCRIBE" value="Y" <?=$forum_subscribe? "checked disabled " : "";?>class="inputcheckbox">
								</td>
								<td width="100%">
									<font class="forumbodytext"><?=GetMessage("FPF_WANT_SUBSCRIBE_FORUM")?></font>
								</td>
							</tr>
						<?endif;?>
						<?if ($arForum["ALLOW_UPLOAD"]=="Y" || $arForum["ALLOW_UPLOAD"]=="F" || $arForum["ALLOW_UPLOAD"]=="A"):?>
							<tr>
								<td colspan="2"><br>
									<font class="forumbodytext">
									<?echo GetMessage("FPF_LOAD")?> <?= ($arForum["ALLOW_UPLOAD"]=="Y") ? GetMessage("FPF_IMAGE1") : GetMessage("FPF_FILE1") ?> <?echo GetMessage("FPF_FOR_MESSAGE")?><br>
									<input name="ATTACH_IMG" size="40" type="file" class="inputfile"><br>
									<?if ($MESSAGE_TYPE=="EDIT"):?>
										<input type="checkbox" name="ATTACH_IMG_del" value="Y" class="inputcheckbox"> <?echo GetMessage("FPF_DELETE_FILE")?> 
									<?endif;?>
									<?if (strlen($str_ATTACH_IMG)>0):?>
										<br>
										<?echo CFile::ShowImage($str_ATTACH_IMG, 200, 200, "border=0", "", true)?>
									<?endif;?>
									</font>
								</td>
							</tr>
						<?endif;?>
						<?if (!$USER->IsAuthorized() && $arForum["USE_CAPTCHA"]=="Y"):?>
							<tr>
								<td colspan="2"><br>
									<font class="forumbodytext">
									<br><b><?echo GetMessage("FPF_CAPTCHA_TITLE")?></b><br><br>
									<?
									include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
									$cpt = new CCaptcha();
									$captchaPass = COption::GetOptionString("main", "captcha_password", "");
									if (strlen($captchaPass) <= 0)
									{
										$captchaPass = randString(10);
										COption::SetOptionString("main", "captcha_password", $captchaPass);
									}
									$cpt->SetCodeCrypt($captchaPass);
									?>
									<input type="hidden" name="captcha_code" value="<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>">
									<img src="/bitrix/tools/captcha.php?captcha_code=<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>"><br><br>
									<?echo GetMessage("FPF_CAPTCHA_PROMT")?> <input type="text" size="10" name="captcha_word" class="inputtext">
									</font>
								</td>
							</tr>
						<?endif;?>
					</table>
				</td>
			</tr>
			<tr class="forumbody">
				<td class="forumbrd2" style="border-right:none;">&nbsp;</td>
				<td class="forumbrd2" style="border-left:none;" nowrap>
					<input type="hidden" name="forum_post_action" value="save">
					<input type="submit" name="submit" value="<?
					if ($MESSAGE_TYPE=="NEW")
						echo GetMessage("FPF_SEND");
					elseif ($MESSAGE_TYPE=="REPLY")
						echo GetMessage("FPF_REPLY");
					else
						echo GetMessage("FPF_EDIT");
					?>" tabindex="4" class="inputbutton ">&nbsp;
					<input type="submit" name="submit_view" value="<?=GetMessage("FPF_VIEW")?>" tabindex="4" class="inputbutton" onclick="View();">
				</td>
			</tr>
		</table>
	</form>
	<?
}
?>