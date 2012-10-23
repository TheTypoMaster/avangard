<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="blogtoolblock">
	<tr>
		<td width="100%" class="blogtoolbar">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><div class="blogtoolsection"></div></td>
					<td><div class="blogtoolsection"></div></td>
					
					<td><a href="<?= $arParams["PATH_TO_BLOG_INDEX"]?>" title="<?=GetMessage("BLOG_MENU_BLOGS_LIST_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_blog_list.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_BLOGS_LIST_TITLE")?>" alt=""></a></td>
					<td><a href="<?=$arParams["PATH_TO_BLOG_INDEX"]?>" title="<?=GetMessage("BLOG_MENU_BLOGS_LIST_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_BLOGS_LIST")?></a></td>
					
					<?
					if(strlen($arResult["urlToCurrentBlog"])>0)
					{
						?>
						<td><a href="<?=$arResult["urlToCurrentBlog"]?>" title="<?= str_replace("#NAME#", $arResult["Blog"]["NAME"], GetMessage("BLOG_MENU_CURRENT_BLOG_TITLE")) ?>" ><img src="<?=$templateFolder?>/images/icon_current_blog.gif" class="blogmenuicon" border="0" alt=""></a></td>
						<td><a href="<?=$arResult["urlToCurrentBlog"]?>" title="<?= str_replace("#NAME#", $arResult["Blog"]["NAME"], GetMessage("BLOG_MENU_CURRENT_BLOG_TITLE")) ?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_CURRENT_BLOG")?></a></td>
						<?
					}
					?>
					
					<?
					if(strlen($arResult["urlToOwnBlog"])>0)
					{
						?>
						<td><div class="blogtoolseparator"></div></td>
						<td><a href="<?=$arResult["urlToOwnBlog"]?>" title="<?=str_replace("#NAME#", $arResult["OwnBlog"]["NAME"], GetMessage("BLOG_MENU_MY_BLOG_TITLE")) ?>"><img src="<?=$templateFolder?>/images/icon_my_blog.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_MY_BLOG_TITLE")?>" alt=""></a></td>
						<td><a href="<?=$arResult["urlToOwnBlog"]?>" title="<?=str_replace("#NAME#", $arResult["OwnBlog"]["NAME"], GetMessage("BLOG_MENU_MY_BLOG_TITLE")) ?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_MY_BLOG")?></a></td>
						<?
					}

					if(strlen($arResult["urlToUser"])>0)
					{
						?>
						<td><a href="<?=$arResult["urlToUser"]?>" title="<?=GetMessage("BLOG_MENU_PROFILE_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_my_profile.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_PROFILE_TITLE")?>" alt=""></a></td>
						<td><a href="<?=$arResult["urlToUser"]?>" title="<?=GetMessage("BLOG_MENU_PROFILE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_PROFILE")?></a></td>
						<?
					}
					
					if(strlen($arResult["urlToFriends"])>0)
					{
						?>					
						<td><a href="<?=$arResult["urlToFriends"]?>" title="<?=GetMessage("BLOG_MENU_FRIENDS_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_friendlenta.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_FRIENDS_TITLE")?>" alt=""></a></td>
						<td><a href="<?=$arResult["urlToFriends"]?>" title="<?=GetMessage("BLOG_MENU_FRIENDS_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_FRIENDS")?></a></td>
						<?
					}
					
					if (strlen($arResult["urlToLogout"])>0)
					{
						?>
						<td><div class="blogtoolseparator"></div></td>
						<td><a href="<?=$arResult["urlToLogout"]?>" title="<?=GetMessage("BLOG_MENU_LOGOUT_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_login_d.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_LOGOUT_TITLE")?>" alt=""></a></td>
						<td><a href="<?=$arResult["urlToLogout"]?>" title="<?=GetMessage("BLOG_MENU_LOGOUT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_LOGOUT")?></a></td>
						<?
					}
					
					if(strlen($arResult["urlToAuth"])>0)
					{
						?>
						<td><div class="blogtoolseparator"></div></td>
						<td><a href="<?=$arResult["urlToAuth"]?>" title="<?=GetMessage("BLOG_MENU_LOGIN_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_login_d.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_LOGIN_TITLE")?>" alt=""></a></td>
						<td><a href="<?=$arResult["urlToAuth"]?>" title="<?=GetMessage("BLOG_MENU_LOGIN_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_LOGIN")?></a></td>
						<?
					}
					
					if(strlen($arResult["urlToRegister"])>0)
					{
						?>
						<td><a href="<?=$arResult["urlToRegister"]?>" title="<?=GetMessage("BLOG_MENU_REGISTER_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_reg_d.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_REGISTER_TITLE")?>" alt=""></a></td>
						<td><a href="<?=$arResult["urlToRegister"]?>" title="<?=GetMessage("BLOG_MENU_REGISTER_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_REGISTER")?></a></td>
						<?
					}
					?>
				</tr>
			</table>
		</td>
	</tr>

	<?
	if ($arResult["SecondLine"] == "Y")
	{
		?>
		<tr>
			<td width="100%" class="blogtoolbar">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="blogtoolsection"></div></td>
						<td><div class="blogtoolsection"></div></td>
						<?
						if (strlen($arResult["urlToNewPost"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToNewPost"]?>" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_new_message.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>" alt=""></a></td>
							<td><a href="<?=$arResult["urlToNewPost"]?>" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_ADD_MESSAGE")?></a></td>
							<?
						}
						
						if(strlen($arResult["urlToDraft"])>0)
						{
							?>
							<td><div class="blogtoolseparator"></div></td>
							<td><a href="<?=$arResult["urlToDraft"]?>" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_draft_messages.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>" alt=""></a></td>
							<td><a href="<?=$arResult["urlToDraft"]?>" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_DRAFT_MESSAGES")?></a></td>
							<td><div class="blogtoolseparator"></div></td>
							<?
						}
						
						if(strlen($arResult["urlToBecomeFriend"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToBecomeFriend"]?>" class="blogtoolbutton" title="<?=GetMessage("BLOG_MENU_FR_B_F")?>"><img src="<?=$templateFolder?>/images/icon_to_friend.gif" class="blogmenuicon" border="0" alt=""></a></td>
							<td><a href="<?=$arResult["urlToBecomeFriend"]?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_FR_B_F")?></a></td>
							<td><div class="blogtoolseparator"></div></td>
							<?
						}
						if(strlen($arResult["urlToAddFriend"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToAddFriend"]?>" class="blogtoolbutton" title="<?=GetMessage("BLOG_MENU_FR_A_F")?>"><img src="<?=$templateFolder?>/images/icon_friend.gif" border="0" alt=""></a></td>
							<td><a href="<?=$arResult["urlToAddFriend"]?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_FR_A_F")?></a></td>
							<?
						}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<?
	}
	?>

	<?
	if ($arResult["ThirdLine"] == "Y")
	{
		?>
		<tr>
			<td width="100%" class="blogtoolbar">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="blogtoolsection"></div></td>
						<td><div class="blogtoolsection"></div></td>
						<?
						if(strlen($arResult["urlToUserSettings"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToUserSettings"]?>" title="<?=GetMessage("BLOG_MENU_USER_SETTINGS_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_user_settings.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_USER_SETTINGS_TITLE")?>" alt=""></a></td>
							<td><a href="<?=$arResult["urlToUserSettings"]?>" title="<?=GetMessage("BLOG_MENU_USER_SETTINGS_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_USER_SETTINGS")?></a></td>
							<?
						}
						if(strlen($arResult["urlToGroupEdit"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToGroupEdit"]?>" title="<?=GetMessage("BLOG_MENU_GROUP_EDIT_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_group_settings.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_GROUP_EDIT_TITLE")?>" alt=""></a></td>
							<td><a href="<?=$arResult["urlToGroupEdit"]?>" title="<?=GetMessage("BLOG_MENU_GROUP_EDIT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_GROUP_EDIT")?></a></td>
							<?
						}
						if(strlen($arResult["urlToCategoryEdit"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToCategoryEdit"]?>" title="<?=GetMessage("BLOG_MENU_CATEGORY_EDIT_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_category_settings.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_CATEGORY_EDIT_TITLE")?>" alt=""></a></td>
							<td><a href="<?=$arResult["urlToCategoryEdit"]?>" title="<?=GetMessage("BLOG_MENU_CATEGORY_EDIT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_CATEGORY_EDIT")?></a></td>
							<?
						}
						if(strlen($arResult["urlToBlogEdit"])>0)
						{
							?>
							<td><a href="<?=$arResult["urlToBlogEdit"]?>" title="<?=GetMessage("BLOG_MENU_BLOG_EDIT_TITLE")?>"><img src="<?=$templateFolder?>/images/icon_blog_settings.gif" class="blogmenuicon" border="0" title="<?=GetMessage("BLOG_MENU_BLOG_EDIT_TITLE")?>" alt=""></a></td>
							<td><a href="<?=$arResult["urlToBlogEdit"]?>" title="<?=GetMessage("BLOG_MENU_BLOG_EDIT_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_BLOG_EDIT")?></a></td>
							<?
						}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<?
	}
	?>
</table>
<br />