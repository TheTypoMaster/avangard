<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile(
	"blog/blog/user_settings.php", 
	Array(
		"BLOG_URL" => $arFolders[0]
	)
);
*/

if (CModule::IncludeModule("blog")):
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

$BLOG_URL = Trim($BLOG_URL);
$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);
$is404 = ($is404=='N') ? false: true;

if (StrLen($BLOG_URL) > 0)
{
	$APPLICATION->SetTitle(GetMessage("B_B_US_TITLE"));
	$dbBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID", "NAME"));
	if ($arBlog = $dbBlog->Fetch())
	{
		$APPLICATION->SetTitle(str_replace("#NAME#", $arBlog["NAME"], GetMessage("B_B_US_TITLE_BLOG")));
		$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
		$strPath = $arPath["PATH"];

		if (CBlog::CanUserManageBlog($arBlog["ID"], ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )))
		{
			$errorMessage = "";
			$okMessage = "";

			if (IntVal($GLOBALS["del_id"]) > 0)
			{
				CBlogUser::AddToUserGroup($GLOBALS["del_id"], $arBlog["ID"], array(), "", BLOG_BY_USER_ID, BLOG_CHANGE);

				$dbCandidate = CBlogCandidate::GetList(
					array(),
					array("BLOG_ID" => $arBlog["ID"], "USER_ID" => IntVal($GLOBALS["del_id"]))
				);
				if ($arCandidate = $dbCandidate->Fetch())
					CBlogCandidate::Delete($arCandidate["ID"]);

				$okMessage = GetMessage("B_B_US_DELETE_OK");
			}

			if ((!IsSet($sessKey) || StrLen($sessKey) <= 0)
				&& (IsSet($GLOBALS["sessKey"]) && StrLen($GLOBALS["sessKey"]) > 0))
				$sessKey = $GLOBALS["sessKey"];

			if (IsSet($sessKey) && StrLen($sessKey) > 0
				&& IsSet($_SESSION[$sessKey]) && is_array($_SESSION[$sessKey]))
			{
				if (IsSet($_SESSION[$sessKey]["ERROR"]) && StrLen($_SESSION[$sessKey]["ERROR"]) > 0)
					$errorMessage .= $_SESSION[$sessKey]["ERROR"];
				if (IsSet($_SESSION[$sessKey]["OK"]) && StrLen($_SESSION[$sessKey]["OK"]) > 0)
					$okMessage .= $_SESSION[$sessKey]["OK"];
				unset($_SESSION[$sessKey]);
			}

			if (StrLen($errorMessage) > 0)
				echo "<div class=\"blogError\">".$errorMessage."</div>";
			if (StrLen($okMessage) > 0)
				echo "<div class=\"blogOK\">".$okMessage."</div>";

			$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
			$strPath = $arPath["PATH"];

			$canUseAlias = COption::GetOptionString("blog", "allow_alias", "Y");
			if ($canUseAlias == "Y")
				$arOrderBy = array("ALIAS" => "ASC", "USER_LAST_NAME" => "ASC", "USER_NAME" => "ASC");
			else
				$arOrderBy = array("USER_LAST_NAME" => "ASC", "USER_NAME" => "ASC");

			$dbUsers = CBlogCandidate::GetList(
				$arOrderBy,
				array("BLOG_ID" => $arBlog["ID"]),
				false,
				false,
				array("ID", "USER_ID", "BLOG_USER_ALIAS", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME")
			);

			if ($arUsers = $dbUsers->Fetch())
			{
				?>
				<h2><?=GetMessage("B_B_US_LIST_WANTED")?></h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="blogtableborder"><tr><td>
				<table cellpadding="3" cellspacing="1" border="0" width="100%">
					<tr>
						<td class="blogtablehead">
							<font class="blogheadtext"><?=GetMessage("B_B_US_VISIT")?></font>
						</td>
						<td class="blogtablehead">
							<font class="blogheadtext"><?=GetMessage("B_B_US_ACTIONS")?></font>
						</td>
					</tr>
					<?
					do
					{
						?>
						<tr>
							<td class="blogtablebody" valign="top">
								<font class="blogtext">
									<a href="<?= CBlogUser::PreparePath($arUsers["USER_ID"], false, $is404) ?>"><?= htmlspecialcharsex(CBlogUser::GetUserName($arUsers["BLOG_USER_ALIAS"], $arUsers["USER_NAME"], $arUsers["USER_LAST_NAME"], $arUsers["USER_LOGIN"])) ?></a>
								</font>
							</td>
							<?
								if($is404)
								{
									$pathToEdit = CBlog::PreparePath($BLOG_URL)."user_settings_edit.php?user_id=".$arUsers["USER_ID"];
									$pathToDel = CBlog::PreparePath($BLOG_URL)."user_settings.php?del_id=".$arUsers["USER_ID"];
								}
								else
								{
									$pathToEdit = $strPath."/user_settings_edit.php?user_id=".$arUsers["USER_ID"]."&blog=".htmlspecialchars($BLOG_URL);
									$pathToDel = $strPath."/user_settings.php?del_id=".$arUsers["USER_ID"]."&blog=".htmlspecialchars($BLOG_URL);
								}
							?>
							<td class="blogtablebody" valign="top">
								<font class="blogtext">
									<a href="<?=$pathToEdit?>"><?=GetMessage("B_B_US_EDIT")?></a><br>
									<a href="<?=$pathToDel?>"><?=GetMessage("B_B_US_DELETE")?></a><br>
								</font>
							</td>
						</tr>
						<?
					}
					while ($arUsers = $dbUsers->Fetch());
					?>
				</table>
				</td></tr></table>
				<?
			}

			$dbUsers = CBlogUser::GetList(
				$arOrderBy,
				array("GROUP_BLOG_ID" => $arBlog["ID"]),
				array("ID", "USER_ID", "ALIAS", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME")
			);
			if ($arUsers = $dbUsers->Fetch())
			{
				?>
				<h2><?=GetMessage("B_B_US_EDIT_FR_LIST")?></h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="blogtableborder"><tr><td>
				<table cellpadding="3" cellspacing="1" border="0" width="100%">
					<tr>
						<td class="blogtablehead">
							<font class="blogheadtext"><?=GetMessage("B_B_US_FR_VISITOR")?></font>
						</td>
						<td class="blogtablehead">
							<font class="blogheadtext"><?=GetMessage("B_B_US_FR_GROUPS")?></font>
						</td>
						<td class="blogtablehead">
							<font class="blogheadtext"><?=GetMessage("B_B_US_FR_ACTIONS")?></font>
						</td>
					</tr>
					<?
					do
					{
						?>
						<tr>
							<td class="blogtablebody" valign="top">
								<font class="blogtext">
									<a href="<?= CBlogUser::PreparePath($arUsers["USER_ID"], false, $is404) ?>"><?= htmlspecialcharsex(CBlogUser::GetUserName($arUsers["ALIAS"], $arUsers["USER_NAME"], $arUsers["USER_LAST_NAME"], $arUsers["USER_LOGIN"])) ?></a>
								</font>
							</td>
							<td class="blogtablebody" valign="top">
								<font class="blogtext">
									<?
									$dbUserGroups = CBlogUserGroup::GetList(
										array(),
										array(
											"USER2GROUP_USER_ID" => $arUsers["USER_ID"],
											"BLOG_ID" => $arBlog["ID"]
										),
										false,
										false,
										array("ID", "NAME")
									);
									$bNeedComa = False;
									while ($arUserGroups = $dbUserGroups->Fetch())
									{
										if ($bNeedComa)
											echo ", ";
										echo htmlspecialchars($arUserGroups["NAME"]);
										$bNeedComa = True;
									}
									?>
								</font>
							</td>
							<?	
								if($is404)
								{
									$pathToEdit = CBlog::PreparePath($BLOG_URL)."user_settings_edit.php?user_id=".$arUsers["USER_ID"];
									$pathToDel = CBlog::PreparePath($BLOG_URL)."user_settings.php?del_id=".$arUsers["USER_ID"];
								}
								else
								{
									$pathToEdit = $strPath."/user_settings_edit.php?user_id=".$arUsers["USER_ID"]."&blog=".htmlspecialchars($BLOG_URL);
									$pathToDel = $strPath."/user_settings.php?del_id=".$arUsers["USER_ID"]."&blog=".htmlspecialchars($BLOG_URL);
								}
							?>	
							<td class="blogtablebody" valign="top">
								<font class="blogtext">
									<a href="<?=$pathToEdit?>"><?=GetMessage("B_B_US_EDIT")?></a><br>
									<a href="<?=$pathToDel?>"><?=GetMessage("B_B_US_DELETE")?></a><br>
								</font>
							</td>
						</tr>
						<?
					}
					while ($arUsers = $dbUsers->Fetch());
					?>
				</table>
				</td></tr></table>
				<?
			}
			?>

			<script language="JavaScript">
			var user_count = 1;

			function addField()
			{
				var bl_name = "add_friend_";
				var new_field = false;
				user_count++;

				var current = document.getElementById(bl_name+user_count);
				if (!current)
					return false;

				var parent = current.parentNode;
				if (!parent)
					return false;

				var add_block = document.createElement("div");
				add_block.id = bl_name+user_count;    
				add_block.innerHTML = 
					"<table cellpadding='0' cellspacing='0' border='0' width='100%'>" +
					"<tr>" +
					"<td class=\"blogtext\"><b>" + user_count + ".</b>&nbsp;&nbsp;</td>" +
					"<td width='100%' style=\"padding-right:7px\"><input type=\"text\" name=\"add_friend[]\" value=\"\" size='40'></td>" +
					"</tr>" +
					"</table>" +
					"<img src='/images/1.gif' width='1' height='4'><br>";
				parent.replaceChild(add_block, current);

				var num = user_count + 1;
				add_block = document.createElement("div");
				add_block.id = bl_name + num;
				add_block.innerHTML = 
					"<table cellpadding='0' cellspacing='0' border='0' width='100%'>" +
					"<tr>" +
					"<td class=\"blogtext\"><b>" + num + ".</b>&nbsp;&nbsp;<a onclick=\"return addField();\" href=\"\"><?=GetMessage("B_B_US_1_M_F")?></a></td>" +
					"</tr>" +
					"</table>";
				parent.appendChild(add_block);

				return false;
			}
			</script>

			<h2><?=GetMessage("B_B_US_AD_NEW_FR")?></h2>
			<div class="blogtext"><?=GetMessage("B_B_US_AD_NEW_FR_BY")?></div>
			<form name="add_friends" action="<?= $strPath ?>/add_friends.php" method="post">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr valign="top">
						<td width="40%" valign="top">
							<div>
								<div id="add_friend_1">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td class="blogtext"><b>1.</b>&nbsp;&nbsp;</td>
											<td width="100%" style="padding-right:7px"><input type="text" name="add_friend[]" size="40" value=""></td>
										</tr>
									</table>
									<img src='/images/1.gif' width='1' height='4'><br>
								</div>
								<div id="add_friend_2">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td class="blogtext"><b>2.</b>&nbsp;&nbsp;<a onclick="return addField();" href=""><?=GetMessage("B_B_US_1_M_F")?></a></td>
										</tr>
									</table>
									<img src='/images/1.gif' width='1' height='4'><br>
								</div>
							</div>
							<br>
							<input type="submit" value="<?=GetMessage("B_B_US_ADD")?>" class="inputbutton">
							<input type="hidden" name="BLOG_URL" value="<?= htmlspecialchars($BLOG_URL) ?>">
						</td>
					</tr>
				</table>
			</form>

			<?
		}
		else
		{
			$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
			$strPath = $arPath["PATH"];
			LocalRedirect($strPath."/blog_auth.php?back_url=".UrlEncode(CBlog::PreparePath($BLOG_URL)."user_settings.php"));

			?><div class="blogError"><?=GetMessage("B_B_US_NO_RIGHT")?></div><?
		}
	}
	else
	{
		?><div class="blogError"><?=GetMessage("B_B_US_NO_BLOG")?></div><?
	}
}
else
{
	?><div class="blogError"><?=GetMessage("B_B_US_NO_BLOG")?></div><?
}

//*******************************************************
endif;
?>