<?
// $FID - forum code
// $TID - topic code
// $MID - message code
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="forumtopmenubody">
  <tr>
	<td width="100%" height="22">
		<nobr>
		&nbsp;&nbsp;<a href="index.php" title="������ �������" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_flist_d.gif" width="16" height="16" border="0" alt="������ �������" name="search" align="absmiddle"> ������</a>
		<?
		if (strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="LIST"
			|| strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="READ"):
			?>
			&nbsp;&nbsp;<a href="list.php?FID=<?echo $FID; ?>" title="������ ��� ������" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_tlist_d.gif" width="16" height="16" border="0" alt="������ ��� ������" name="search" align="absmiddle"> ����</a>
			<?
		endif;

		if (CModule::IncludeModule("search")):
			?>
			&nbsp;&nbsp;<a href="search.php" title="����� �� ������" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_search_d.gif" width="16" height="16" border="0" alt="����� �� ������" name="search" align="absmiddle"> �����</a>
			<?
		endif;
		?>
		&nbsp;&nbsp;<a href="help.php" title='������' class="forumtopmenu" target="_blank"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_help_d.gif" width="16" height="16" border="0" alt="������" name="help" align="absmiddle"> ������</a>
		<?
		if ($USER->IsAuthorized()):
			?>
			&nbsp;&nbsp;<a href="profile.php" title="�������� �������" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_profile_d.gif" width="15" height="15" border="0" alt="�������� �������" name="profile" align="absmiddle"> �������</a>
			&nbsp;&nbsp;<a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array("login", "logout", "register", "forgot_password", "change_password"));?>" title="�����" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_logout_d.gif" width="16" height="16" border="0" alt="�����" name="logout" align="absmiddle"> �����</a>
			<?
		else:
			?>
			&nbsp;&nbsp;<a href="forum_auth.php?back_url=<?echo urlencode($APPLICATION->GetCurPageParam("a", array("login", "logout", "register", "forgot_password", "change_password")));?>" title="�����" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_login_d.gif" width="16" height="16" border="0" alt="�����" name="login" align="absmiddle"> �����</a>
			&nbsp;&nbsp;<a href="forum_auth.php?register=yes&back_url=<?echo urlencode($APPLICATION->GetCurPageParam("a", array("login", "register", "logout", "forgot_password", "change_password")));?>" title="������������������" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_reg_d.gif" width="16" height="16" border="0" alt="������������������" name="logout" align="absmiddle"> ������������������</a>
			<?
		endif;

		if ($USER->IsAuthorized() 
			&&
			(strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="LIST"
			|| strtoupper(basename($APPLICATION->GetCurPage(), ".php"))=="READ")):
			?>
			&nbsp;&nbsp;<a href="<?echo $APPLICATION->GetCurPageParam("FID=".$FID."&ACTION=FORUM_SUBSCRIBE", array("FID", "ACTION", "login", "register", "logout"));?>" title="����������� �� ����� ��������� ������" class="forumtopmenu"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/icon_subscribe_d.gif" width="16" height="16" border="0" alt="����������� �� ����� ��������� ������" name="profile" align="absmiddle"> �����������</a>&nbsp;&nbsp;
			<?
		endif;
		?>
		</nobr>
	</td>
	<td nowrap>&nbsp;</td>
  </tr>
</table>
<br>