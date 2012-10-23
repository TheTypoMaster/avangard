<?
// $FID - forum code
// $TID - topic code
// $MID - message code
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="forumtopmenubody">
  <tr>
	<td width="100%" height="22">
		<nobr>
		&nbsp;&nbsp;<a href="index.php" title="Список форумов" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_flist_d.gif" width="16" height="16" border="0" alt="Список форумов" name="search" align="absmiddle"> Форумы</a>
		<?
		if (strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="LIST"
			|| strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="READ"):
			?>
			&nbsp;&nbsp;<a href="list.php?FID=<?echo $FID; ?>" title="Список тем форума" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_tlist_d.gif" width="16" height="16" border="0" alt="Список тем форума" name="search" align="absmiddle"> Темы</a>
			<?
		endif;

		if (CModule::IncludeModule("search")):
			?>
			&nbsp;&nbsp;<a href="search.php" title="Поиск по форуму" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_search_d.gif" width="16" height="16" border="0" alt="Поиск по форуму" name="search" align="absmiddle"> Поиск</a>
			<?
		endif;
		?>
		&nbsp;&nbsp;<a href="help.php" title='Помощь' class="forumtopmenu" target="_blank"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_help_d.gif" width="16" height="16" border="0" alt="Помощь" name="help" align="absmiddle"> Помощь</a>
		<?
		if ($USER->IsAuthorized()):
			?>
			&nbsp;&nbsp;<a href="profile.php" title="Изменить профиль" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_profile_d.gif" width="15" height="15" border="0" alt="Изменить профиль" name="profile" align="absmiddle"> Профиль</a>
			&nbsp;&nbsp;<a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array("login", "logout", "register", "forgot_password", "change_password"));?>" title="Выйти" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_logout_d.gif" width="16" height="16" border="0" alt="Выйти" name="logout" align="absmiddle"> Выйти</a>
			<?
		else:
			?>
			&nbsp;&nbsp;<a href="forum_auth.php?back_url=<?echo urlencode($APPLICATION->GetCurPageParam("a", array("login", "logout", "register", "forgot_password", "change_password")));?>" title="Войти" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_login_d.gif" width="16" height="16" border="0" alt="Войти" name="login" align="absmiddle"> Войти</a>
			&nbsp;&nbsp;<a href="forum_auth.php?register=yes&back_url=<?echo urlencode($APPLICATION->GetCurPageParam("a", array("login", "register", "logout", "forgot_password", "change_password")));?>" title="Зарегистрироваться" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_reg_d.gif" width="16" height="16" border="0" alt="Зарегистрироваться" name="logout" align="absmiddle"> Зарегистрироваться</a>
			<?
		endif;

		if ($USER->IsAuthorized() 
			&&
			(strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="LIST"
			|| strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="READ")):
			?>
			&nbsp;&nbsp;<a href="<?echo $APPLICATION->GetCurPageParam("FID=".$FID."&ACTION=FORUM_SUBSCRIBE", array("FID", "ACTION", "login", "register", "logout"));?>" title="Подписаться на новые сообщения форума" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_subscribe_d.gif" width="16" height="16" border="0" alt="Подписаться на новые сообщения форума" name="profile" align="absmiddle"> Подписаться</a>&nbsp;&nbsp;
			<?
		endif;
		?>
		</nobr>
	</td>
	<td nowrap>&nbsp;</td>
  </tr>
</table>
<br>