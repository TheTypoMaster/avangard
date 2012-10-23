<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("blog")):
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

$arMenuTypes = array("MAIN", "USER", "GROUP", "POST_FORM", "POST", "BLOG");
$is404 = ($is404=='N') ? false: true;
$bEditPage = ($EditPage == "Y") ? true: false;
$MENU_TYPE = strtoupper(Trim($MENU_TYPE));
if (!in_array($MENU_TYPE, $arMenuTypes))
	$MENU_TYPE = "MAIN";

if (strlen($BLOG_URL) > 0)
{
	$BLOG_URL = Trim($BLOG_URL);
	$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);
}

$ID = IntVal($ID);

CBlogUser::SetLastVisit();
$userId = $USER->GetID();
/*
if(StrLen($BLOG_URL) > 0 && $_GET["friend"]=="Y")
{
	$dbCurrentBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID", "NAME", "URL", "OWNER_ID"));
	if ($arCurrentBlog = $dbCurrentBlog->Fetch())
	{
		$arBlog = CBlog::GetByOwnerId($userId);
		if(!CBlogCandidate::Add(Array("BLOG_ID"=>$arBlog["ID"], "USER_ID"=>$arCurrentBlog["OWNER_ID"])))
			echo ShowError(GetMessage("BLOG_MENU_BLOG_ERROR_FRIEND"));
	}
	else
		echo ShowError(GetMessage("BLOG_MENU_BLOG_NOT_FOUND"));
}
*/
$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
$strPath = $arPath["PATH"];
?><table width="100%" border="0" cellspacing="0" cellpadding="0" class="blogtoolblock">
	<tr>
		<td width="100%" class="blogtoolbar">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><div class="blogtoolsection"></div></td>
					<td><div class="blogtoolsection"></div></td>
					<td><a href="<?= $strPath ?>/" title="<?=GetMessage("BLOG_MENU_BLOGS_LIST_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_blog_list.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_BLOGS_LIST_TITLE")?>" hspace="4"></a></td>
					<td><a href="<?= $strPath ?>/" title="<?=GetMessage("BLOG_MENU_BLOGS_LIST_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_BLOGS_LIST")?></a></td>
					<?
					
					$arCurrentBlog = False;
					if (StrLen($BLOG_URL) > 0)
					{
						$dbCurrentBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID", "NAME", "URL", "OWNER_ID"));
						if ($arCurrentBlog = $dbCurrentBlog->Fetch())
						{
							?>
							<td><a href="<?= CBlog::PreparePath($BLOG_URL, false, $is404) ?>" title="<?= str_replace("#NAME#", htmlspecialchars($arCurrentBlog["NAME"]), GetMessage("BLOG_MENU_CURRENT_BLOG_TITLE")) ?>"><img src="/bitrix/templates/.default/blog/images/icon_current_blog.gif" width="16" height="16" border="0" title="<?= str_replace("#NAME#", htmlspecialchars($arCurrentBlog["NAME"]), GetMessage("BLOG_MENU_CURRENT_BLOG_TITLE")) ?>" hspace="4"></a></td>
							<td><a href="<?= CBlog::PreparePath($BLOG_URL, false, $is404) ?>" title="<?= str_replace("#NAME#", htmlspecialchars($arCurrentBlog["NAME"]), GetMessage("BLOG_MENU_CURRENT_BLOG_TITLE")) ?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_CURRENT_BLOG")?></a></td>
							<?
							/*
							if($userId>0 && $userId != $arCurrentBlog["OWNER_ID"])
							{
								$UBlog = CBlog::GetByOwnerID($userId);
							
								$isFriend = true;
								$dbFriends = CBlogUser::GetUserFriends($userId, False);
								while ($arFriends = $dbFriends->Fetch())
								{
									if($UBlog == $arFriends["ID"])
									{
										$isFriend = false;
										break;
									}
								}
								
								if($isFriend)
								{
									?>
									<td><a href="<?=$APPLICATION->GetCurPageParam("friend=Y", Array("friend"))?>" title="<?=GetMessage("BLOG_MENU_ADD_FRIEND")?>"><img src="/bitrix/templates/.default/blog/images/icon_profile_d.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_ADD_FRIEND")?>" hspace="4"></a></td>
									<td><a href="<?=$APPLICATION->GetCurPageParam("friend=Y", Array("friend"))?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_ADD_FRIEND")?></a></td><?
								}
							}
							*/
						}
					}

					$arMyBlog = False;
					if ($GLOBALS["USER"]->IsAuthorized())
					{
						?>
						<td><div class="blogtoolseparator"></div></td>
						<?
						$arMyBlog = CBlog::GetByOwnerID($GLOBALS["USER"]->GetID());
						if ($arMyBlog)
						{
							?>
							<td><a href="<?= CBlog::PreparePath($arMyBlog["URL"], false, $is404) ?>" title="<?=GetMessage("BLOG_MENU_MY_BLOG_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_my_blog.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_MY_BLOG_TITLE")?>" hspace="4"></a></td>
							<td><a href="<?= CBlog::PreparePath($arMyBlog["URL"], false, $is404) ?>" title="<?=GetMessage("BLOG_MENU_MY_BLOG_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_MY_BLOG")?></a></td>
							<?
						}

						?>
						<td><a href="<?= CBlogUser::PreparePath($GLOBALS["USER"]->GetID(), false, $is404) ?>" title="<?=GetMessage("BLOG_MENU_PROFILE_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_my_profile.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_PROFILE_TITLE")?>" hspace="4"></a></td>
						<td><a href="<?= CBlogUser::PreparePath($GLOBALS["USER"]->GetID(), false, $is404) ?>" title="<?=GetMessage("BLOG_MENU_PROFILE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_PROFILE")?></a></td>
						<?
						if($is404)
							$pathToFriends = $strPath."/users/friends/".$GLOBALS["USER"]->GetID().".php";
						else
							$pathToFriends = $strPath."/friends.php?user_id=".$GLOBALS["USER"]->GetID();
						?>
						<td><a href="<?= $pathToFriends ?>" title="<?=GetMessage("BLOG_MENU_FRIENDS_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_friendlenta.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_FRIENDS_TITLE")?>" hspace="4"></a></td>
						<td><a href="<?= $pathToFriends?>" title="<?=GetMessage("BLOG_MENU_FRIENDS_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_FRIENDS")?></a></td>
						<?
					}

					if ($USER->IsAuthorized())
					{
						?>
						<td><div class="blogtoolseparator"></div></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array("login", "logout", "register", "forgot_password", "change_password"));?>" title="<?=GetMessage("BLOG_MENU_LOGOUT_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_login_d.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_LOGOUT_TITLE")?>" hspace="4"></a></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array("login", "logout", "register", "forgot_password", "change_password"));?>" title="<?=GetMessage("BLOG_MENU_LOGOUT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_LOGOUT")?></a></td>
						<?
					}
					else
					{
						?>
						<td><div class="blogtoolseparator"></div></td>
						<td><a href="<?= $strPath ?>/blog_auth.php?back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "logout", "register", "forgot_password", "change_password")));?>" title="<?=GetMessage("BLOG_MENU_LOGIN_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_login_d.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_LOGIN_TITLE")?>" hspace="4"></a></td>
						<td><a href="<?= $strPath ?>/blog_auth.php?back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "logout", "register", "forgot_password", "change_password")));?>" title="<?=GetMessage("BLOG_MENU_LOGIN_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_LOGIN")?></a></td>

						<td><a href="<?= $strPath ?>/blog_auth.php?register=yes&back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "register", "logout", "forgot_password", "change_password")));?>" title="<?=GetMessage("BLOG_MENU_REGISTER_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_reg_d.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_REGISTER_TITLE")?>" hspace="4"></a></td>
						<td><a href="<?= $strPath ?>/blog_auth.php?register=yes&back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "register", "logout", "forgot_password", "change_password")));?>" title="<?=GetMessage("BLOG_MENU_REGISTER_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_REGISTER")?></a></td>
						<?
					}
					?>
				</tr>
			</table>
		</td>
	</tr>

	<?
	if ($MENU_TYPE == "POST_FORM" || $MENU_TYPE == "POST" || $MENU_TYPE == "BLOG")
	{
		if (strlen($BLOG_URL) > 0)
		{
			if ($arCurrentBlog)
			{
				$bCanUserPostBlog = (CBlog::GetBlogUserPostPerms($arCurrentBlog["ID"], ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )) >= BLOG_PERMS_WRITE);
				$bCanUserEditPost = ($ID > 0 && ($MENU_TYPE == "POST_FORM" || $MENU_TYPE == "POST") && CBlogPost::CanUserEditPost($ID, ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )));
				$bCanUserDeletePost = ($ID > 0 && ($MENU_TYPE == "POST_FORM" || $MENU_TYPE == "POST") && CBlogPost::CanUserDeletePost($ID, ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )));

				if ($bCanUserPostBlog || $bCanUserEditPost || $bCanUserDeletePost)
				{
					?>
					<tr>
						<td width="100%" class="blogtoolbar">
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td><div class="blogtoolsection"></div></td>
									<td><div class="blogtoolsection"></div></td>
									<?
									if ($bCanUserPostBlog)
									{
										if($is404)
										{
											$prePathEdit = CBlog::PreparePath($BLOG_URL)."post_edit.php";
											$prePathDraft = CBlog::PreparePath($BLOG_URL)."draft.php";
										}
										else
										{
											$prePathEdit = $strPath."/post_edit.php?blog=".htmlspecialchars($BLOG_URL);
											$prePathDraft = $strPath."/draft.php?blog=".htmlspecialchars($BLOG_URL);
										}
										?>
										<td><a href="<?=$prePathEdit ?>" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_new_message.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>" hspace="4"></a></td>
										<td><a href="<?=$prePathEdit ?>" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_ADD_MESSAGE")?></a></td>

										<td><div class="blogtoolseparator"></div></td>
										<td><a href="<?=$prePathDraft ?>" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_draft_messages.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>" hspace="4"></a></td>
										<td><a href="<?=$prePathDraft ?>" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_DRAFT_MESSAGES")?></a></td>
										<?
									}
									
									if (($bCanUserEditPost || $bCanUserDeletePost) && $bCanUserPostBlog)
									{
										?>
										<td><div class="blogtoolseparator"></div></td>
										<?
									}
									if ($bCanUserEditPost && !$bEditPage)
									{
										if($is404)
											$prePathEdit = CBlog::PreparePath($BLOG_URL)."post_edit.php?ID=".$ID;
										else
											$prePathEdit = $strPath."/post_edit.php?blog=".htmlspecialchars($BLOG_URL)."&post_id=".$ID;
									?>
										<td><a href="<?=$prePathEdit?>" title="<?=GetMessage("BLOG_MENU_EDIT_MESSAGE_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_mes_edit.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_EDIT_MESSAGE_TITLE")?>" hspace="4"></a></td>
										<td><a href="<?=$prePathEdit?>" title="<?=GetMessage("BLOG_MENU_EDIT_MESSAGE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_EDIT_MESSAGE")?></a></td>
										<?
									}
									if ($bCanUserEditPost)
									{
										if($is404)
											$pathToDel = CBlog::PreparePath($BLOG_URL, false, $is404)."?";
										else
											$pathToDel = CBlog::PreparePath($BLOG_URL, false, $is404)."&";
										?>
										<td><a href="<?=$pathToDel?>del_id=<?= $ID ?>&<?=bitrix_sessid_get()?>" title="<?=GetMessage("BLOG_MENU_DELETE_MESSAGE_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_mes_delete.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_DELETE_MESSAGE_TITLE")?>" hspace="4"></a></td>
										<td><a href="<?=$pathToDel?>del_id=<?= $ID ?>&<?=bitrix_sessid_get()?>" title="<?=GetMessage("BLOG_MENU_DELETE_MESSAGE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_DELETE_MESSAGE")?></a></td>
										<?
									}
									?>
									
								</tr>
							</table>
						</td>
					</tr>
					<?
				}
			}
		}
	}
	?>

	<?
	if ($MENU_TYPE == "POST_FORM" || $MENU_TYPE == "POST" || $MENU_TYPE == "BLOG")
	{
		if (CBlog::CanUserManageBlog($arCurrentBlog["ID"], ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )))
		{
			
			// $arMyBlog && $arMyBlog["URL"] == $BLOG_URL
			?>
			<tr>
				<td width="100%" class="blogtoolbar">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><div class="blogtoolsection"></div></td>
							<td><div class="blogtoolsection"></div></td>
							<?
							if($is404)
							{
								$pathToBlog = CBlog::PreparePath($arCurrentBlog["URL"]);
								$pathUSet = $pathToBlog."user_settings.php";
								$pathGEdit = $pathToBlog."group_edit.php";
								$pathBEdit = $pathToBlog."blog_edit.php";
								$pathCEdit = $pathToBlog."category_edit.php";
							}
							else
							{
								$pathUSet = $strPath."/user_settings.php?blog=".htmlspecialchars($arCurrentBlog["URL"]);
								$pathGEdit = $strPath."/group_edit.php?blog=".htmlspecialchars($arCurrentBlog["URL"]);
								$pathBEdit = $strPath."/blog_edit.php?blog=".htmlspecialchars($arCurrentBlog["URL"]);
								$pathCEdit = $strPath."/category_edit.php?blog=".htmlspecialchars($arCurrentBlog["URL"]);
							}
							
							?>
							<td><a href="<?=$pathUSet?>" title="<?=GetMessage("BLOG_MENU_USER_SETTINGS_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_user_settings.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_USER_SETTINGS_TITLE")?>" hspace="4"></a></td>
							<td><a href="<?=$pathUSet?>" title="<?=GetMessage("BLOG_MENU_USER_SETTINGS_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_USER_SETTINGS")?></a></td>

							<td><a href="<?=$pathGEdit?>" title="<?=GetMessage("BLOG_MENU_GROUP_EDIT_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_group_settings.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_GROUP_EDIT_TITLE")?>" hspace="4"></a></td>
							<td><a href="<?=$pathGEdit?>" title="<?=GetMessage("BLOG_MENU_GROUP_EDIT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_GROUP_EDIT")?></a></td>

							<td><a href="<?=$pathCEdit?>" title="<?=GetMessage("BLOG_MENU_CATEGORY_EDIT_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_category_settings.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_CATEGORY_EDIT_TITLE")?>" hspace="4"></a></td>
							<td><a href="<?=$pathCEdit?>" title="<?=GetMessage("BLOG_MENU_CATEGORY_EDIT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_CATEGORY_EDIT")?></a></td>

							<td><a href="<?=$pathBEdit?>" title="<?=GetMessage("BLOG_MENU_BLOG_EDIT_TITLE")?>"><img src="/bitrix/templates/.default/blog/images/icon_blog_settings.gif" width="16" height="16" border="0" title="<?=GetMessage("BLOG_MENU_BLOG_EDIT_TITLE")?>" hspace="4"></a></td>
							<td><a href="<?=$pathBEdit?>" title="<?=GetMessage("BLOG_MENU_BLOG_EDIT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_BLOG_EDIT")?></a></td>
						</tr>
					</table>
				</td>
			</tr>
			<?
		}
	}
	?>

</table>
<br>
<?
//*******************************************************
endif;
?>
