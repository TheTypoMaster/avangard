<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER, $APPLICATION, $strError, $DB;
IncludeTemplateLangFile(__FILE__);
$is404 = ($is404=='N') ? false: true;

if (CModule::IncludeModule("blog"))
{
	$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");
	$errorMessage = "";
	$bVarsFromForm = false;
	$arr = CBlogSitePath::GetBySiteID(SITE_ID);
	$sBlogPath = $arr['PATH'];
	
	if (!$USER->IsAuthorized()) // Не авторизован
	{
		if ($_REQUEST["do_authorize"] == "Y")
		{
			$USER_LOGIN = $_REQUEST["USER_LOGIN"];
			if (strlen($USER_LOGIN) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_AUTH_LOGIN").".<br>";

			$USER_PASSWORD = $_REQUEST["USER_PASSWORD"];

			if (strlen($errorMessage) <= 0)
			{
				$arAuthResult = $GLOBALS["USER"]->Login($USER_LOGIN, $USER_PASSWORD, "N");
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$errorMessage .= GetMessage("STOF_ERROR_AUTH").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : ".<br>" );
			}
		}
		elseif ($_REQUEST["do_register"] == "Y")
		{
			$NEW_NAME = $_REQUEST["NEW_NAME"];
			if (strlen($NEW_NAME) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_REG_NAME").".<br>";

			$NEW_LAST_NAME = $_REQUEST["NEW_LAST_NAME"];
			if (strlen($NEW_LAST_NAME) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_REG_LASTNAME").".<br>";

			$NEW_EMAIL = $_REQUEST["NEW_EMAIL"];
			if (strlen($NEW_EMAIL) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_REG_EMAIL").".<br>";
			elseif (!check_email($NEW_EMAIL))
				$errorMessage .= GetMessage("STOF_ERROR_REG_BAD_EMAIL").".<br>";

			$NEW_LOGIN = $_REQUEST["NEW_LOGIN"];
			if (strlen($NEW_LOGIN) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_REG_FLAG").".<br>";

			$NEW_PASSWORD = $_REQUEST["NEW_PASSWORD"];
			if (strlen($NEW_PASSWORD) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_REG_FLAG1").".<br>";

			$NEW_PASSWORD_CONFIRM = $_REQUEST["NEW_PASSWORD_CONFIRM"];
			if (strlen($NEW_PASSWORD_CONFIRM) <= 0)
				$errorMessage .= GetMessage("STOF_ERROR_REG_FLAG1").".<br>";

			if (strlen($NEW_PASSWORD) > 0
				&& strlen($NEW_PASSWORD_CONFIRM) > 0
				&& $NEW_PASSWORD != $NEW_PASSWORD_CONFIRM)
				$errorMessage .= GetMessage("STOF_ERROR_REG_PASS").".<br>";

			if (strlen($errorMessage) <= 0)
			{
				$arAuthResult = $GLOBALS["USER"]->Register($NEW_LOGIN, $NEW_NAME, $NEW_LAST_NAME, $NEW_PASSWORD, $NEW_PASSWORD_CONFIRM, $NEW_EMAIL, LANG, $_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]);
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$errorMessage .= GetMessage("STOF_ERROR_REG").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : ".<br>" );
				else
					if ($GLOBALS["USER"]->IsAuthorized())
						CUser::SendUserInfo($GLOBALS["USER"]->GetID(), SITE_ID, GetMessage("INFO_REQ"));
			}
		}

		if (!$USER->IsAuthorized())
		{
			$GLOBALS["APPLICATION"]->SetTitle(GetMessage('BLOG_REGISTER'));
			?>
			<?= ShowError($errorMessage) ?>
			<table border="0" cellspacing="0" cellpadding="1">
				<tr valign=top>
					<td width="45%" valign="top">
						<font class="blogheadtext">
						<b><?echo GetMessage("STOF_2REG")?></b>
						</font>
					</td>
					<td width="10%">
						&nbsp;
					</td>
					<td width="45%" valign="top">
						<font class="blogheadtext">
						<b><?echo GetMessage("STOF_2NEW")?></b>
						</font>
					</td>
				</tr>
				<tr valign=top>
					<td valign="top">
						<table border="0" cellspacing="0" cellpadding="1"><tr valign=top><td class=blog_input_form>
						<table border="0" cellspacing="0" cellpadding="3" width="100%">
						<form method="post" action="<?=$sBlogPath?>/blog_edit.php" name="blog_auth_form">
							<tr valign=top>
									<td>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_LOGIN_PROMT")?>
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td nowrap>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_LOGIN")?> <font color="#FF0000">*</font><br>
										<input type="text" name="USER_LOGIN" maxlength="30" size="30" value="<?= ((strlen($USER_LOGIN) > 0) ? htmlspecialchars($USER_LOGIN) : htmlspecialchars(${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"})) ?>" class="inputtext">&nbsp;&nbsp;&nbsp;
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td nowrap>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_PASSWORD")?> <font color="#FF0000">*</font><br>
										<input type="password" name="USER_PASSWORD" maxlength="30" size="30" class="inputtext">&nbsp;&nbsp;&nbsp;
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td nowrap>
										<font class="blogheadtext">
										<a href="auth.php?forgot_password=yes&back_url=<?= urlencode($ORDER_PAGE); ?>"><?echo GetMessage("STOF_FORGET_PASSWORD")?></a>
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td align="center">
										<font class="blogheadtext">
										<input type="submit" value="<?echo GetMessage("STOF_NEXT_STEP")?>" class="inputbuttonflat">
										<input type="hidden" name="do_authorize" value="Y">
										</font>
									</td>
								</tr>
							</form>
						</table>
						</td></tr></table>
					</td>
					<td>
						&nbsp;
					</td>
					<td valign="top">
						<table border="0" cellspacing="0" cellpadding="1"><tr valign=top><td class=blog_input_form>
						<table border="0" cellspacing="0" cellpadding="3" width="100%">
							<form method="post" action="<?=$sBlogPath?>/blog_edit.php" name="blog_reg_form">
								<tr valign=top>
									<td nowrap>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_NAME")?> <font color="#FF0000">*</font><br>
										<input type="text" name="NEW_NAME" size="40" value="<?= htmlspecialchars($NEW_NAME) ?>" class="inputtext">&nbsp;&nbsp;&nbsp;
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td nowrap>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_LASTNAME")?> <font color="#FF0000">*</font><br>
										<input type="text" name="NEW_LAST_NAME" size="40" class="inputtext" value="<?= htmlspecialchars($NEW_LAST_NAME) ?>">&nbsp;&nbsp;&nbsp;
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td nowrap>
										<font class="blogheadtext">
										E-Mail <font color="#FF0000">*</font><br>
										<input type="text" name="NEW_EMAIL" size="40" class="inputtext" value="<?= htmlspecialchars($NEW_EMAIL) ?>">&nbsp;&nbsp;&nbsp;
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_LOGIN")?> <font color="#FF0000">*</font><br>
										<input type="text" name="NEW_LOGIN" size="40" class="inputtext" value="<?= htmlspecialchars($NEW_LOGIN) ?>">
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_PASSWORD")?> <font color="#FF0000">*</font><br>
										<input type="password" name="NEW_PASSWORD" size="40" class="inputtext">
										</font>
									</td>
								</tr>
								<tr valign=top>
									<td>
										<font class="blogheadtext">
										<?echo GetMessage("STOF_RE_PASSWORD")?> <font color="#FF0000">*</font><br>
										<input type="password" name="NEW_PASSWORD_CONFIRM" size="40" class="inputtext">
										</font>
									</td>
								</tr>

								<?
								/* CAPTCHA */
								if (COption::GetOptionString("main", "captcha_registration", "N") == "Y")
								{
									?>
									<tr valign=top>
										<td><br>
											<font class="blogheadtext"><b><?=GetMessage("CAPTCHA_REGF_TITLE")?></b></font>
										</td>
									</tr>
									<tr valign=top>
										<td>
											<?
											$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();
											?>
											<input type="hidden" name="captcha_sid" value="<?= htmlspecialchars($capCode) ?>">
											<img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialchars($capCode) ?>" width="180" height="40">
										</td>
									</tr>
									<tr valign="middle">
										<td>
											<font class="starrequired">*</font><font class="blogheadtext"><?=GetMessage("CAPTCHA_REGF_PROMT")?>:</font><br>
											<input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext">
										</td>
									</tr>
									<?
								}
								/* CAPTCHA */
								?>

								<tr valign=top>
									<td align="center">
										<font class="blogheadtext">
										<input type="submit" value="<?echo GetMessage("STOF_NEXT_STEP")?>" class="inputbuttonflat">
										<input type="hidden" name="do_register" value="Y">
										</font>
									</td>
								</tr>
						</table>
						</td></tr></table>
					</td>
				</tr>
			</table>

			<font class="blogheadtext">
			<br><br>
			<?echo GetMessage("STOF_REQUIED_FIELDS_NOTE")?><br><br>
			<?echo GetMessage("STOF_EMAIL_NOTE")?><br><br>
			<?echo GetMessage("STOF_PRIVATE_NOTES")?>
			</font>
			<?
		}
	} 

	if ($USER->IsAuthorized()) // АВТОРИЗОВАН но неизвестно, есть ли у него блог//
	{
		if(CBlog::CanUserCreateBlog($USER->GetID()))
		{
			$USER_ID = intval($USER->GetID());
			$BLOG_ID = intval($BLOG_ID);

			if ($BLOG_ID) // Попали сюда по посту
				$arBlog = CBlog::GetByID($BLOG_ID);
			elseif ($OWNER) // Попали сюда с 404й страницы
			{
				$res = CBlog::GetList(array(),array("URL" => $OWNER));
				$arBlog = $res->Fetch();
				$BLOG_ID = intval($arBlog['ID']);
			}
			else
				$arBlog = CBlog::GetByOwnerID($USER_ID);

			if (!$OWNER && $arBlog && !$BLOG_ID)
			{	
				if($is404)
					$page = CBlog::PreparePath($arBlog['URL'])."blog_edit.php";
				else
				{
					$arSitePath = CBlogSitePath::GetBySiteID(SITE_ID);
					$page = $arSitePath["PATH"]."/blog_edit.php?blog=".htmlspecialchars($arBlog['URL']);
				}
				
				LocalRedirect($page);
				die();
			} 
			elseif (!$OWNER || $arBlog)
			{
				if ($BLOG_ID==0 || CBlog::CanUserManageBlog($BLOG_ID, ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )))
				{
					$bBlockURL = COption::GetOptionString("blog", "block_url_change", "N") == 'Y' ? true : false;
					$arSitePath = CBlogSitePath::GetBySiteID(SITE_ID);
					if ($_POST['reset'])
					{
						LocalRedirect(CBlog::PreparePath($arBlog['URL'], false, $is404));
					}
					elseif ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['do_blog'] == "Y")
					{
						if ($_POST['perms_p'][1] > BLOG_PERMS_READ)
							$_POST['perms_p'][1] = BLOG_PERMS_READ;
						if ($_POST['perms_c'][1] > BLOG_PERMS_WRITE)
							$_POST['perms_c'][1] = BLOG_PERMS_WRITE;

						$arFields = array(
							"NAME" => $_POST['NAME'],
							"DESCRIPTION" => $_POST['DESCRIPTION'],
							"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
							"GROUP_ID" => $_POST['GROUP_ID'],
							"ENABLE_IMG_VERIF" => (($_POST['ENABLE_IMG_VERIF'] == "Y") ? "Y" : "N"),
							"EMAIL_NOTIFY" => (($_POST['EMAIL_NOTIFY'] == "Y") ? "Y" : "N"),
							"ENABLE_RSS" => "Y",
							"PERMS_POST" => $_POST['perms_p'],
							"PERMS_COMMENT" => $_POST['perms_c']
						);

						if (!$bBlockURL || $USER->IsAdmin() || $BLOG_ID==0)
							$arFields["URL"] = $_POST['URL'];

						if ($arBlog)
						{
							if (is_array($_POST['group']))
								$arFields["AUTO_GROUPS"] = serialize(array_keys($_POST['group']));
							else
								$arFields["AUTO_GROUPS"] = "";
							$newID = CBlog::Update($arBlog["ID"], $arFields);
						}
						else
						{
							$arFields["=DATE_CREATE"] = $DB->CurrentTimeFunction();
							$arFields["ACTIVE"] = "Y";
							$arFields["OWNER_ID"] = $USER->GetID();

							$newID = CBlog::Add($arFields);
							if ($newID)
							{
								BXClearCache(True, "/".SITE_ID."/blog/new_blogs/");
								BXClearCache(True, "/".SITE_ID."/blog/groups/".$arBlog['GROUP_ID']."/");
								BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']);
								CBlogUserGroup::Add(array("BLOG_ID"=>$newID,"NAME"=>GetMessage('BLOG_FRIENDS')));
							}
						}

							
						if ($newID && !$errorMessage)
						{
							$arBlog = CBlog::GetByID($newID);
							if ($_POST['apply'])
							{
								if($is404)
									LocalRedirect(CBlog::PreparePath($arBlog['URL'], false, $is404)."blog_edit.php");
								else
									$page = $arSitePath["PATH"]."/blog_edit.php?blog=".htmlspecialchars($arBlog['URL']);
							}
							else
								LocalRedirect(CBlog::PreparePath($arBlog['URL'], false, $is404));
						}
						else
						{
							$bVarsFromForm = true;
							if ($ex = $APPLICATION->GetException())
								$errorMessage .= $ex->GetString().".<br>";
							else
								$errorMessage .= GetMessage('BLOG_ERR_SAVE').".<br>";
						}
					}
			
			
					if ($arBlog)
						$APPLICATION->SetTitle(str_replace("#BLOG#", htmlspecialchars($arBlog["NAME"]), GetMessage('BLOG_TOP_TITLE')));
					else
						$APPLICATION->SetTitle(GetMessage('BLOG_NEW_BLOG'));
					?>
					<?= ShowError($errorMessage) ?>
				<form method="post" action="<?=$sBlogPath?>/blog_edit.php">
				<input type=hidden name=BLOG_ID value="<?=$BLOG_ID?>">
				<input type=hidden name=blog value="<?=$arBlog["URL"]?>">
					<table border=0 cellspacing=1 cellpadding=3  class="blogtableborder">
							<tr valign=top>
								<td nowrap align="right" valign="top" class="blogtablehead">
									<font class="blogheadtext">
									<font color="#FF0000">*</font> <b><?=GetMessage('BLOG_TITLE')?></b>
									</font>
								</td>
								<td class="blogtablebody">
									<?$val = htmlspecialchars($bVarsFromForm ? $_POST['NAME'] : ($arBlog ? $arBlog["NAME"] : ""));?>
									<input type="text" name="NAME" maxlength="100" size="40" value="<?= $val ?>" class="inputtext" style="width:100%">
								</td>
								<td class="blogtablebody" valign="top">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_TITLE_DESCR')?>
									</font>
								</td>
							</tr>
							<tr valign=top>
								<td nowrap valign="top" align="right" class="blogtablehead">
									<font class="blogheadtext">
									<b><?=GetMessage('BLOG_DESCR')?></b>
								</td>
								<td class="blogtablebody">
									<?$val = htmlspecialchars($bVarsFromForm ? $_POST['DESCRIPTION'] : ($arBlog ? $arBlog["DESCRIPTION"] : ""));?>
									<textarea name="DESCRIPTION" rows="5" cols="40" class="inputtextarea messageareawidth" style="width:100%"><?= $val ?></textarea>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_DESCR_TITLE')?>
									</font>
								</td>
							</tr>
							<tr valign=top>
								<td nowrap valign="top" align="right" class="blogtablehead">
									<font class="blogheadtext">
									<font color="#FF0000">*</font> <b><?=GetMessage('BLOG_URL')?></b>
								</td>
								<td class="blogtablebody">
									<?$val = htmlspecialchars($bVarsFromForm ? $_POST['URL'] : ($arBlog ? $arBlog["URL"] : ""));?>
									<?
									if ($bBlockURL && !$USER->IsAdmin() && strlen($arBlog['URL'])>0)
										print "<font class=blogtext>$val</font>";
									else
										print "<input type=\"text\" name=\"URL\" maxlength=\"100\" size=\"40\" value=\"$val\" class=\"inputtext\" style=\"width:100%\">";
									?>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage("BLOG_URL_TITLE")?>
									</font>
								</td>
							</tr>
							<tr valign=top>
								<td nowrap valign="top" align="right" class="blogtablehead">
									<font class="blogheadtext">
									<font color="#FF0000">*</font> <b><?=GetMessage('BLOG_GRP')?></b>
									</font>
								</td>
								<td class="blogtablebody">
									<select name="GROUP_ID" class="inputselect">
										<?
										$val = IntVal($bVarsFromForm ? $_POST['GROUP_ID'] : ($arBlog ? $arBlog["GROUP_ID"] : 0));

										$dbBlogGroup = CBlogGroup::GetList(
											array("NAME" => "ASC"),
											array("SITE_ID" => SITE_ID)
										);
										while ($arBlogGroup = $dbBlogGroup->Fetch())
										{
											?><option value="<?=$arBlogGroup["ID"]?>"<?if ($val == IntVal($arBlogGroup["ID"])) echo " selected";?>><?= htmlspecialchars($arBlogGroup["NAME"]) ?></option><?
										}
										?>
									</select>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_GRP_TITLE')?>
									</font>
								</td>
							</tr>
							<tr valign=top>
								<td nowrap align="right" valign="top" class="blogtablehead">
									<font class=blogheadtext>
									<b><?=GetMessage('BLOG_AUTO_MSG')?></b>
									</font>
								</td>
								<td class="blogtablebody">
									<?$val = htmlspecialchars($bVarsFromForm ? $_POST['ENABLE_IMG_VERIF'] : ($arBlog ? $arBlog["ENABLE_IMG_VERIF"] : "Y"));?>
									<input id=img_verif type="checkbox" name="ENABLE_IMG_VERIF" value="Y"<?if ($val == "Y") echo " checked";?>>
									<label for=img_verif class=blogtext>
									<?=GetMessage('BLOG_AUTO_MSG_TITLE')?>
									</label>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_CAPTHA')?>
									</font>
								</td>
							</tr>
							<tr valign=top>
								<td nowrap align="right" valign="top" class="blogtablehead">
									<font class=blogheadtext>
									<b><?=GetMessage('BLOG_EMAIL_NOTIFY')?></b>
									</font>
								</td>
								<td class="blogtablebody">
									<?$val = htmlspecialchars($bVarsFromForm ? $_POST['EMAIL_NOTIFY'] : ($arBlog ? $arBlog["EMAIL_NOTIFY"] : "Y"));?>
									<input id=EMAIL_NOTIFY type="checkbox" name="EMAIL_NOTIFY" value="Y"<?if ($val == "Y") echo " checked";?>>
									<label for=EMAIL_NOTIFY class=blogtext>
									<?=GetMessage('BLOG_EMAIL_NOTIFY_TITLE')?>
									</label>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_EMAIL_NOTIFY_HELP')?>
									</font>
								</td>
							</tr>
						<?
						$res=CBlogUserGroup::GetList($arOrder = Array("ID" => "ASC"), $arFilter = Array("BLOG_ID" => $BLOG_ID));
						if ($arBlog && $arUGroup=$res->Fetch())
						{
						?>
							<tr valign=top>
								<td nowrap valign="top" align="right" class="blogtablehead">
									<font class="blogheadtext">
									<b><?=GetMessage('BLOG_OPENED_GRPS')?></b>
									</font>
								</td>
								<td class="blogtablebody">
									<?
									$arAutoGroups = unserialize($arBlog["AUTO_GROUPS"]);
									do
									{
										if (is_array($arAutoGroups) || $bVarsFromForm)
											$val = ($bVarsFromForm ? $_POST['group'][$arUGroup['ID']] : in_array($arUGroup['ID'],$arAutoGroups));
										else
											$val = false;
										print "
									<input id=group_$arUGroup[ID] type=checkbox name=group[{$arUGroup['ID']}] ".($val ? ' checked' : '').">
									<label for=group_$arUGroup[ID] class=blogtext>".htmlspecialcharsex($arUGroup[NAME])."</label>
									<br>
										";
									}
									while ($arUGroup=$res->Fetch())
									?>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_OPENED_TITLE')?>
									</font>
								</td>
							</tr>
							
						<?	
						}
						?>
							<?
							function ShowSelectPerms($type,$id,$def)
							{
								if ($type=='p')
									$arr = $GLOBALS["AR_BLOG_POST_PERMS"];
								else
									$arr = $GLOBALS["AR_BLOG_COMMENT_PERMS"];

								$res = "<select name='perms_{$type}[{$id}]' class=inputselect>";
								while(list(,$key)=each($arr))
									if ($id > 1 || ($type=='p' && $key <= BLOG_PERMS_READ) || ($type=='c' && $key <= BLOG_PERMS_WRITE))
										$res.= "<option value='$key'".($key==$def?' selected':'').">".$GLOBALS["AR_BLOG_PERMS"][$key]."</option>";
								$res.= "</select>";
								return $res;
							}

							if ($bVarsFromForm)
							{
								$arUGperms_p = $_POST['perms_p'];
								$arUGperms_c = $_POST['perms_c'];
							}
							elseif ($arBlog)
							{
								$res=CBlogUserGroupPerms::GetList(array("ID" => "DESC"),array("BLOG_ID" => $arBlog['ID'], "POST_ID" => 0));
								while($arPerms = $res->Fetch())
								{
									if ($arPerms['PERMS_TYPE']=='P')
										$arUGperms_p[$arPerms['USER_GROUP_ID']] = $arPerms['PERMS'];
									elseif ($arPerms['PERMS_TYPE']=='C')
										$arUGperms_c[$arPerms['USER_GROUP_ID']] = $arPerms['PERMS'];
								}
							}
							else
							{
								$arUGperms_p[1] = BLOG_PERMS_READ;
								$arUGperms_p[2] = BLOG_PERMS_READ;
								$arUGperms_c[1] = BLOG_PERMS_WRITE;
								$arUGperms_c[2] = BLOG_PERMS_WRITE;
							}
								
							?>
							<tr valign=top>
								<td nowrap valign="top" align="right" class="blogtablehead">
									<font class="blogheadtext">
									<b><?=GetMessage('BLOG_DEF_PERMS')?></b>
									</font>
								</td>
								<td class="blogtablebody">
									<table cellspacing=0 cellpadding=5 class=blogtext>
										<tr valign=top>
											<td><b><?=GetMessage('BLOG_GROUPS')?></b></td>
											<td><b><?=GetMessage('BLOG_MESSAGES')?></b></td>
											<td><b><?=GetMessage('BLOG_COMMENTS')?></b></td>
										</tr>
										<tr valign=top>
											<td><?=GetMessage('BLOG_ALL_USERS')?></td>
											<td><?=ShowSelectPerms('p',1,$arUGperms_p[1])?></td>
											<td><?=ShowSelectPerms('c',1,$arUGperms_c[1])?></td>
										</tr>
										<tr valign=top>
											<td><?=GetMessage('BLOG_REGISTERED')?></td>
											<td><?=ShowSelectPerms('p',2,$arUGperms_p[2])?></td>
											<td><?=ShowSelectPerms('c',2,$arUGperms_c[2])?></td>
										</tr>
										
										<?
									if ($BLOG_ID)
									{
										$res=CBlogUserGroup::GetList(array(),$arFilter=array("BLOG_ID" => $BLOG_ID));
										while ($aUGroup=$res->Fetch())
											print "
											<tr valign=top>
												<td>".htmlspecialcharsex($aUGroup['NAME'])."</td>
												<td>".ShowSelectPerms('p',$aUGroup['ID'],$arUGperms_p[$aUGroup['ID']])."</td>
												<td>".ShowSelectPerms('c',$aUGroup['ID'],$arUGperms_c[$aUGroup['ID']])."</td>
											</tr>";
									}
										?>
									</table>
								</td>
								<td valign="top" class="blogtablebody">
									<font class="blogtext"><small>
									<?=GetMessage('BLOG_PERMS_TITLE')?>
									</font>
								</td>
							</tr>
					</table>
					<br>

					<input type="submit" name=save value="<?= ($arBlog ? GetMessage('BLOG_SAVE') : GetMessage('BLOG_CREATE')) ?>" class="inputbutton">
					<?
					if ($arBlog)
						print '
						<input type="submit" name=apply value="'.GetMessage('BLOG_APPLY').'" class="inputbutton">
						<input type="submit" name=reset value="'.GetMessage('BLOG_CANCEL').'" class="inputbutton">
						';
					?>
					<input type="hidden" name="do_blog" value="Y">
				</form>
				<font class="blogheadtext">
				<br><br>
				<?echo GetMessage("STOF_REQUIED_FIELDS_NOTE")?><br><br>
				</font>
					<?
				}
				else
					ShowError($errorMessage .= GetMessage('BLOG_ERR_NO_RIGHTS'));
			}
			else
				ShowError($errorMessage .= GetMessage('BLOG_ERR_NOT_FOUND'));
		}
		else
			echo ShowError(GetMessage("BLOG_NOT_RIGHTS_TO_CREATE"));
	}
}
else
	ShowError($errorMessage .= GetMessage('BLOG_ERR_NOT_INSTALLED'));
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
