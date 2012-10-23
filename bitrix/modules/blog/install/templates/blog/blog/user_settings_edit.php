<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile(
	"blog/blog/user_settings_edit.php", 
	Array(
		"BLOG_URL" => $arFolders[0],
		"USER_ID" => IntVal($user_id)
	)
);
*/

if (CModule::IncludeModule("blog")):
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

$BLOG_URL = Trim($BLOG_URL);
$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);
$is404 = ($is404=='N') ? false: true;

$USER_ID = IntVal($USER_ID);

$APPLICATION->SetTitle(GetMessage("B_B_USE_TITLE"));
if ($USER_ID > 0)
{
	$dbUser = CUser::GetByID($USER_ID);
	if ($arUser = $dbUser->Fetch())
	{
		if (StrLen($BLOG_URL) > 0)
		{
			$dbBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID", "NAME"));
			if ($arBlog = $dbBlog->Fetch())
			{
				$APPLICATION->SetTitle(str_replace("#NAME#", $arBlog["NAME"], GetMessage("B_B_USE_TITLE_BLOG")));

				$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
				$strPath = $arPath["PATH"];
				if (CBlog::CanUserManageBlog($arBlog["ID"], ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )))
				{
					$arBlogUser = CBlogUser::GetByID($arUser["ID"], BLOG_BY_USER_ID);

					$errorMessage = "";
					$okMessage = "";
					if ($GLOBALS["user_action"] == "Y")
					{
						if (!$arBlogUser)
						{
							CBlogUser::Add(
								array(
									"USER_ID" => $arUser["ID"],
									"=LAST_VISIT" => $GLOBALS["DB"]->GetNowFunction(),
									"=DATE_REG" => $GLOBALS["DB"]->GetNowFunction(),
									"ALLOW_POST" => "Y"
								)
							);
						}

						CBlogUser::AddToUserGroup($arUser["ID"], $arBlog["ID"], $GLOBALS["add2groups"], "", BLOG_BY_USER_ID, BLOG_CHANGE);

						$dbCandidate = CBlogCandidate::GetList(
							array(),
							array("BLOG_ID" => $arBlog["ID"], "USER_ID" => $arUser["ID"])
						);
						if ($arCandidate = $dbCandidate->Fetch())
							CBlogCandidate::Delete($arCandidate["ID"]);

						if($is404)
							$pathToEdit = CBlog::PreparePath($BLOG_URL)."user_settings.php";
						else
							$pathToEdit = $strPath."/user_settings.php?blog=".htmlspecialchars($BLOG_URL);
						LocalRedirect($pathToEdit);
					}

					if (StrLen($errorMessage) > 0)
						echo "<div class=\"blogError\">".$errorMessage."</div>";
					if (StrLen($okMessage) > 0)
						echo "<div class=\"blogOK\">".$okMessage."</div>";
					if($is404)
						$pathToEdit = CBlog::PreparePath($BLOG_URL)."user_settings_edit.php";
					else
						$pathToEdit = $strPath."/user_settings_edit.php";

					?>
					<form action="<?=$pathToEdit?>" method="GET">
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="blogtableborder"><tr><td>
					<table cellpadding="3" cellspacing="1" border="0" width="100%">
						<tr>
							<td class="blogtablehead" align="center" colspan="2">
								<font class="blogheadtext">
								<?= str_replace("#NAME#", htmlspecialcharsex($arBlog["NAME"]), GetMessage("B_B_USE_TITLE_BLOG")) ?>
								</font>
							</td>
						</tr>
						<tr>
							<td class="blogtablebody" align="right" width="40%" valign="top">
								<font class="blogtext">
									<?=GetMessage("B_B_USE_USER")?>
								</font>
							</td>
							<td class="blogtablebody" width="60%" valign="top">
								<font class="blogtext">
									<a href="<?= CBlogUser::PreparePath($arUser["ID"], false, $is404) ?>"><?= htmlspecialcharsex(CBlogUser::GetUserName($arBlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"])) ?></a>
									<input type="hidden" name="user_id" value="<?= $arUser["ID"] ?>">
								</font>
							</td>
						</tr>
						<tr>
							<td class="blogtablebody" align="right" valign="top">
								<font class="blogtext">
									<?=GetMessage("B_B_USE_U_GROUPS")?>
								</font>
							</td>
							<td class="blogtablebody">
								<font class="blogtext" valign="top">
									<?
									$arUserGroups = CBlogUser::GetUserGroups($arUser["ID"], $arBlog["ID"], "", BLOG_BY_USER_ID);

									$dbBlogGroups = CBlogUserGroup::GetList(
										array("NAME" => "ASC"),
										array("BLOG_ID" => $arBlog["ID"]),
										false,
										false,
										array("ID", "NAME")
									);
									while ($arBlogGroups = $dbBlogGroups->Fetch())
									{
										?>
										<input type="checkbox" id="add2groups_<?= $arBlogGroups["ID"] ?>" name="add2groups[]" value="<?= $arBlogGroups["ID"] ?>"<?if (in_array($arBlogGroups["ID"], $arUserGroups)) echo " checked";?>>
										<label for="add2groups_<?= $arBlogGroups["ID"] ?>"><?= htmlspecialchars($arBlogGroups["NAME"]) ?></label><br>
										<?
									}
									?>
								</font>
							</td>
						</tr>
						<tr>
							<td class="blogtablebody" align="center" colspan="2">
								<font class="blogtext">
								<input type="submit" value="<?=GetMessage("B_B_USE_SAVE")?>" class="inputbutton">
								<input type="reset" value="<?=GetMessage("B_B_USE_CANCEL")?>" class="inputbutton">
								<input type="hidden" name="user_action" value="Y">
								<input type="hidden" name="blog" value="<?=$BLOG_URL?>">
								</font>
							</td>
						</tr>
					</table>
					</td></tr></table>
					</form>
					<?
				}
				else
				{
					LocalRedirect($strPath."/blog_auth.php?back_url=".UrlEncode(CBlog::PreparePath($BLOG_URL)."user_settings_edit.php?user_id=".$USER_ID));

					?><div class="blogError"><?=GetMessage("B_B_USE_NO_RIGHTS")?></div><?
				}
			}
			else
			{
				?><div class="blogError"><?=GetMessage("B_B_USE_NO_BLOG")?></div><?
			}
		}
		else
		{
			?><div class="blogError"><?=GetMessage("B_B_USE_NO_BLOG")?></div><?
		}
	}
	else
	{
		?><div class="blogError"><?=GetMessage("B_B_USE_NO_USER")?></div><?
	}
}
else
{
	?><div class="blogError"><?=GetMessage("B_B_USE_NO_USER")?></div><?
}


//*******************************************************
endif;
?>