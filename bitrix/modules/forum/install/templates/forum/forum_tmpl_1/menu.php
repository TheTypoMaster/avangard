<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
// $FID - forum code
// $TID - topic code
// $MID - message code

$sSection = strtoupper(basename($APPLICATION->GetCurPage(), ".php"));
?><table width="100%" border="0" cellspacing="0" cellpadding="0" class="forumtoolblock">
	<tr>
		<td width="100%" class="forumtoolbar">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><div class="forumtoolsection"></div></td>
					<td><div class="forumtoolsection"></div></td>
					<td><a href="index.php" title="<?echo GetMessage("FMI_2TOPIC_LIST")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_flist_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_2TOPIC_LIST")?>" hspace="4"></a></td>
					<td><a href="index.php" title="<?echo GetMessage("FMI_2TOPIC_LIST")?>" class="forumtoolbutton"><?echo GetMessage("FMI_FORUM_LIST")?></a></td>
					<td><div class="forumtoolsection"></div></td>
					<td><a href="active.php" title="<?echo GetMessage("FMI_2TOPIC_LIST")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_active.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_ACTIVE")?>" hspace="4"></a></td>
					<td><a href="active.php" title="<?echo GetMessage("FMI_2TOPIC_LIST")?>" class="forumtoolbutton"><?echo GetMessage("FMI_FORUM_ACTIVE")?></a></td>
					<?if ($sSection=="READ" || $sSection == "NEW_TOPIC"):?>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="list.php?FID=<?echo $FID; ?>" title="<?echo GetMessage("FMI_2TOPICS")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_tlist_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_2TOPICS")?>" hspace="4"></a></td>
						<td><a href="list.php?FID=<?echo $FID; ?>" title="<?echo GetMessage("FMI_2TOPICS")?>" class="forumtoolbutton"><?echo GetMessage("FMI_2TOPICS_LIST1")?></a></td>
					<?endif?>
					<?if (CModule::IncludeModule("search")):?>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="search.php<?if($FID>0) echo "?FORUM_ID=".$FID?>" title="<?echo GetMessage("FMI_FORUM_SEARCH")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_search_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_SEARCH")?>" hspace="4"></a></td>
						<td><a href="search.php<?if($FID>0) echo "?FORUM_ID=".$FID?>" title="<?echo GetMessage("FMI_FORUM_SEARCH")?>" class="forumtoolbutton"><?echo GetMessage("FMI_FORUM_SEARCH")?></a></td>
					<?endif;?>
					<td><div class="forumtoolseparator"></div></td>
					<td><a href="help.php" title="<?echo GetMessage("FMI_FORUM_HELP")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_help_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_HELP")?>" hspace="4"></a></td>
					<td><a href="help.php" title="<?echo GetMessage("FMI_FORUM_HELP")?>" class="forumtoolbutton"><?echo GetMessage("FMI_FORUM_HELP")?></a></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td width="100%" class="forumtoolbar">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><div class="forumtoolsection"></div></td>
					<td><div class="forumtoolsection"></div></td>
					<?
					if ($USER->IsAuthorized()):
						$CUID = $USER->GetID();
//						$db_res = CUser::GetByID($CUID);
//						$ar_res = $db_res->Fetch();
						?>
						<td><a href="view_profile.php?UID=<?=$CUID?>" title="<?echo GetMessage("FMI_FORUM_PROFILE")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_profile_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_PROFILE")?>" hspace="4"></a></td>
						<td><a href="view_profile.php?UID=<?=$CUID?>" title="<?echo GetMessage("FMI_FORUM_PROFILE")?>" class="forumtoolbutton"><?echo GetMessage("FMI_FORUM_PROFILE1")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="list_user.php" title="<?echo GetMessage("FMI_LIST_USER")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_profile_d.gif" width="16" height="16" border="0" title="<?=GetMessage("FMI_LIST_USER")?>" hspace="4"></a></td>
						<td><a href="list_user.php" title="<?=GetMessage("FMI_LIST_USER")?>" class="forumtoolbutton"><?=GetMessage("FMI_LIST_USER")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="subscr_list.php" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscribe_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>" hspace="4"></a></td>
						<td><a href="subscr_list.php" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>" class="forumtoolbutton"><?echo GetMessage("FMI_SUBSCR_CHANGE")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="subscr_list.php" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscribe_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>" hspace="4"></a></td>
						<td><a href="pm_message.php" title="<?echo GetMessage("FMI_PM_VIEW")?>" class="forumtoolbutton"><?
							echo GetMessage("FMI_PM_VIEW"); 
							$count = CForumPrivateMessage::GetNewPM();
							if (intVal($count["UNREAD_PM"]) > 0)
								echo "&nbsp;(".intVal($count["UNREAD_PM"]).")";
							?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<?
					endif;
					?>
					<?
					if ($USER->IsAuthorized()):
						?>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array("login", "logout", "register", "forgot_password", "change_password"));?>" title="<?echo GetMessage("FMI_LOGOUT")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_logout_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_LOGOUT")?>" hspace="4"></a></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array("login", "logout", "register", "forgot_password", "change_password"));?>" title="<?echo GetMessage("FMI_LOGOUT")?>" class="forumtoolbutton"><?echo GetMessage("FMI_OUT")?></a></td>
						<?
					else:
						?>
						<td><a href="forum_auth.php?back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "logout", "register", "forgot_password", "change_password")));?>" title="<?echo GetMessage("FMI_AUTHORIZE")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_login_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_AUTHORIZE")?>" hspace="4"></a></td>
						<td><a href="forum_auth.php?back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "logout", "register", "forgot_password", "change_password")));?>" title="<?echo GetMessage("FMI_AUTHORIZE")?>" class="forumtoolbutton"><?echo GetMessage("FMI_ENTER")?></a></td>
						
						<?if(COption::GetOptionString("main", "new_user_registration", "N")=="Y"):?>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="forum_auth.php?register=yes&back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "register", "logout", "forgot_password", "change_password")));?>" title="<?echo GetMessage("FMI_REGISTER")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_reg_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_REGISTER")?>" hspace="4"></a></td>
						<td><a href="forum_auth.php?register=yes&back_url=<?echo urlencode($APPLICATION->GetCurPageParam("", array("login", "register", "logout", "forgot_password", "change_password")));?>" title="<?echo GetMessage("FMI_REGISTER")?>" class="forumtoolbutton"><?echo GetMessage("FMI_REGISTER1")?></a></td>
						<?endif;
					endif;
					?>
					<td><div style="width:4px;"></div></td>
				</tr>
			</table>
		</td>
	</tr>


	<?if ($USER->IsAuthorized() && ($sSection=="LIST" || $sSection=="READ")):?>
		<tr>
			<td width="100%" class="forumtoolbar">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="forumtoolsection"></div></td>
						<td><div class="forumtoolsection"></div></td>
						<td class="forumtoolbutton"><?echo GetMessage("FMI_SUBSCRIBE_ON")?></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("FID=".$FID."&ACTION=FORUM_SUBSCRIBE&".bitrix_sessid_get(), array("FID", "ACTION", "login", "register", "logout"));?>" title="<?echo GetMessage("FMI_SUBSCRIBE_ON_ALL")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_SUBSCRIBE_ON_NEW")?>" hspace="4"></a></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("FID=".$FID."&ACTION=FORUM_SUBSCRIBE&".bitrix_sessid_get(), array("FID", "ACTION", "login", "register", "logout"));?>" title="<?echo GetMessage("FMI_SUBSCRIBE_ON_ALL")?>" class="forumtoolbutton"><?echo GetMessage("FMI_SUBSCRIBE_ON_NEW_MESS")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("FID=".$FID."&ACTION=FORUM_SUBSCRIBE_TOPICS&".bitrix_sessid_get(), array("FID", "ACTION", "login", "register", "logout"));?>" title="<?echo GetMessage("FMI_SUBSCRIBE_ON_NEW_TOPICS")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_new_topic.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_SUBSCRIBE_ON_NEW")?>" hspace="4"></a></td>
						<td><a href="<?echo $APPLICATION->GetCurPageParam("FID=".$FID."&ACTION=FORUM_SUBSCRIBE_TOPICS&".bitrix_sessid_get(), array("FID", "ACTION", "login", "register", "logout"));?>" title="<?echo GetMessage("FMI_SUBSCRIBE_ON_NEW_TOPICS")?>" class="forumtoolbutton"><?echo GetMessage("FMI_NEW_TOPICS")?></a></td>
						<?if ($sSection=="READ" && $arTopic["STATE"]=="Y"):?>
							<td><div class="forumtoolseparator"></div></td>
							<td><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $TID?>&ACTION=TOPIC_SUBSCRIBE&<?=bitrix_sessid_get()?>" title="<?echo GetMessage("FMI_NEW_MESS_THIS_TOPIC")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_topic.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_NEW_MESS_THIS_TOPIC1")?>" hspace="4"></a></td>
							<td><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $TID?>&ACTION=TOPIC_SUBSCRIBE&<?=bitrix_sessid_get()?>" title="<?echo GetMessage("FMI_NEW_MESS_THIS_TOPIC")?>" class="forumtoolbutton"><?echo GetMessage("FMI_THIS_TOPIC1")?></a></td>
						<?endif;?>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="subscr_list.php" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscribe_d.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>" hspace="4"></a></td>
						<td><a href="subscr_list.php" title="<?echo GetMessage("FMI_FORUM_SUBSCR_CHANGE")?>" class="forumtoolbutton"><?echo GetMessage("FMI_SUBSCR_CHANGE")?></a></td>
					</tr>
				</table>
			</td>
		</tr>
	<?endif?>
	<?if ((($sSection == "READ")||($sSection == "LIST")||($sSection == "APPR_MESSAGE"))  && $USER->IsAuthorized() && ForumCurrUserPermissions($FID)>="Q"):?>
		<?$index = rand(0, 99999);?>
		<tr>
			<td width="100%" class="forumtoolbar">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="forumtoolsection"></div></td>
						<td><div class="forumtoolsection"></div></td>
						<td class="forumtoolbutton"><?=GetMessage("FMI_MANAGE")?></td>
					<?if ($sSection == "READ"):?>
						<td><div class="forumtoolseparator"></div></td>
						<td><div style="width:4px;"></div></td>
						<form action="read.php" method="GET">
						<td><?=bitrix_sessid_post()?>
						<input type="hidden" name="FID" value="<?=$FID?>">
						<input type="hidden" name="TID" id="FORUM_TID" value="<?=$TID?>">
						<select name="ACTION" class="forumtoolbutton" style="width:100px;">
							<option value="move_topic"><?=GetMessage("FMI_TOPIC_MOVE1")?></option>
							<option value="<?echo (IntVal($arTopic["SORT"])!=150)?"SET_ORDINARY":"SET_TOP";?>"><?echo (IntVal($arTopic["SORT"])!=150)?GetMessage("FMI_TOPIC_UNPIN1"):GetMessage("FMI_TOPIC_PIN1");?></option>
							<option value="<?echo ($arTopic["STATE"]!="Y")?"STATE_Y":"STATE_N";?>"><?echo ($arTopic["STATE"]!="Y")?GetMessage("FMI_TOPIC_OPEN1"):GetMessage("FMI_TOPIC_CLOSE1");?></option>
						<?if (CForumTopic::CanUserDeleteTopic($TID, $USER->GetUserGroupArray(), $USER->GetID())):?>
							<option value="del_topic"><?echo GetMessage("FMI_TOPIC_DELETE1")?></option>
						<?endif;?>
						</select></td>
						<td><input type="submit" value="<?=GetMessage("FMI_TOPIC")?>" class="forumtoolbutton" style="width:50px;"></td>
						</form>
					<?endif;?>
					<?if ($sSection == "LIST"):?>
						<td><div class="forumtoolseparator"></div></td>
						<td><div style="width:4px;"></div></td>
						<form action="list.php?FID=<?=$FID?>" method="POST" onsubmit="return CheckForm('TID_ARRAY<?="".$index?>', 'FORUM_TOPICS', 'topic_id[]');">
						<td><?=bitrix_sessid_post()?>
						<input type="hidden" name="FID" value="<?=$FID?>">
						<input type="hidden" name="TID_ARRAY" id="TID_ARRAY<?="".$index?>" value="TEST">
						<select name="ACTION" class="forumtoolbutton" style="width:100px;">
							<option value="move_topic"><?=GetMessage("FMI_TOPIC_MOVE1")?></option>
							<option value="SET_ORDINARY"><?=GetMessage("FMI_TOPIC_UNPIN1")?></option>
							<option value="SET_TOP"><?=GetMessage("FMI_TOPIC_PIN1")?></option>
							<option value="STATE_Y"><?=GetMessage("FMI_TOPIC_OPEN1")?></option>
							<option value="STATE_N"><?=GetMessage("FMI_TOPIC_CLOSE1")?></option>
							<option value="del_topic"><?echo GetMessage("FMI_TOPIC_DELETE1")?></option>
						</select></td>
						<td><input type="submit" value="<?=GetMessage("FMI_TOPICS")?>" class="forumtoolbutton" style="width:50px;"></td>
						</form>
					<?endif;?>
					<?if (($sSection == "READ")||($sSection == "APPR_MESSAGE")):?>
						<td><div class="forumtoolseparator"></div></td>
						<td><div style="width:4px;"></div></td>
						<form action="<?=$APPLICATION->GetCurPage()?>?FID=<?=$FID?><?=(($TID>0)?"&TID=".$TID:"")?>" method="POST"  onsubmit="return CheckForm('MID_ARRAY<?="".$index?>', 'FORUM_MESSAGES', 'message_id[]');" name="FORUM_MESSAGES_GROUP_ACTION" id="FORUM_MESSAGES_GROUP_ACTION<?="".$index?>">
						<td><?=bitrix_sessid_post()?>
						<input type="hidden" name="FID" value="<?=$FID?>">
						<input type="hidden" name="TID" value="<?=$TID?>">
						<input type="hidden" name="MID" value="<?=$MID?>">
						<input type="hidden" name="MID_ARRAY" id="MID_ARRAY<?="".$index?>" value="">
						<select name="ACTION" class="forumtoolbutton" style="width:100px;">
							<option value="hide"><?=GetMessage("FMI_MESSAGE_HIDE1")?></option>
							<option value="show"><?=GetMessage("FMI_MESSAGE_SHOW1")?></option>
							<option value="del"><?=GetMessage("FMI_MESSAGE_DELETE1")?></option>
						<?if ($sSection == "READ"):?>
							<option value="move"><?=GetMessage("FMI_MESSAGE_MOVE1")?></option>
						<?endif;?>
						</select></td>
						<td><input type="submit" value="<?=GetMessage("FMI_MESSAGE")?>" class="forumtoolbutton"></td>
						</form>
					<?endif;?>
					</tr>
				</table>
			</td>
		</tr>
	<?endif;?>
	<?if ($USER->IsAuthorized() && (($sSection=="PM_MESSAGE")||($sSection=="PM_FOLDER"))):?>
		<tr>
			<td width="100%" class="forumtoolbar">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="forumtoolsection"></div></td>
						<td><div class="forumtoolsection"></div></td>
						<td><a href="pm_message.php?mode=new" title="<?echo GetMessage("FMI_PM_SENT")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_PM_SENT")?>" hspace="4"></a></td>
						<td><a href="pm_message.php?mode=new" title="<?echo GetMessage("FMI_PM_SENT")?>" class="forumtoolbutton"><?echo GetMessage("FMI_PM_SENT")?></a></td>
						<td><div class="forumtoolsection"></div></td>
						<td><a href="pm_message.php?FID=1&mode=list" title="<?echo GetMessage("FMI_PM_INBOX")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_PM_INBOX")?>" hspace="4"></a></td>
						<td><a href="pm_message.php?FID=1&mode=list" title="<?echo GetMessage("FMI_PM_INBOX")?>" class="forumtoolbutton"><?echo GetMessage("FMI_PM_INBOX")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="pm_message.php?FID=2&mode=list" title="<?echo GetMessage("FMI_PM_SEND")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_PM_SEND")?>" hspace="4"></a></td>
						<td><a href="pm_message.php?FID=2&mode=list" title="<?echo GetMessage("FMI_PM_SEND")?>" class="forumtoolbutton"><?echo GetMessage("FMI_PM_SEND")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="pm_message.php?FID=3&mode=list" title="<?echo GetMessage("FMI_PM_OUTBOX")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_PM_OUTBOX")?>" hspace="4"></a></td>
						<td><a href="pm_message.php?FID=3&mode=list" title="<?echo GetMessage("FMI_PM_OUTBOX")?>" class="forumtoolbutton"><?echo GetMessage("FMI_PM_OUTBOX")?></a></td>
						<td><div class="forumtoolseparator"></div></td>
						<td><a href="pm_message.php?FID=4&mode=list" title="<?echo GetMessage("FMI_PM_RECYLED")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_PM_RECYLED")?>" hspace="4"></a></td>
						<td><a href="pm_message.php?FID=4&mode=list" title="<?echo GetMessage("FMI_PM_RECYLED")?>" class="forumtoolbutton"><?echo GetMessage("FMI_PM_RECYLED")?></a></td>
						<td><div class="forumtoolsection"></div></td>
						<td><div class="forumtoolsection"></div></td>
						<td><a href="pm_folder.php" title="<?echo GetMessage("FMI_PM_FOLDER")?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?echo GetMessage("FMI_PM_FOLDER")?>" hspace="4"></a></td>
						<td><a href="pm_folder.php" title="<?echo GetMessage("FMI_PM_FOLDER")?>" class="forumtoolbutton"><?echo GetMessage("FMI_PM_FOLDER")?></a></td>
					</tr>
				</table>
			</td>
		</tr>
		<?$resFolder = CForumPMFolder::GetList(array(), array("USER_ID" => $USER->GetId()));
		if ($resF = $resFolder->Fetch()):
		$count = 0;?>
		<tr>
			<td width="100%" class="forumtoolbar">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="forumtoolsection"></div></td>
						<td><div class="forumtoolsection"></div></td><?
			do 
			{
				if ($count)
					echo "<td><div class=\"forumtoolseparator\"></div></td>";
				else
					$count = 1;
				
						?><td><a href="pm_message.php?FID=<?=intVal($resF["ID"])?>&mode=list" title="<?=htmlspecialcharsEx($resF["TITLE"])?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_subscr_forum.gif" width="16" height="16" border="0" title="<?=htmlspecialcharsEx($resF["TITLE"])?>" hspace="4"></a></td>
						<td><a href="pm_message.php?FID=<?=intVal($resF["ID"])?>&mode=list" title="<?=htmlspecialcharsEx($resF["TITLE"])?>" class="forumtoolbutton"><?=htmlspecialcharsEx($resF["TITLE"])?></a></td><?
			}while ($resF = $resFolder->Fetch());
					?></tr>
				</table>
			</td>
		</tr><?
		endif;
		?>
		
		
	<?endif?>

</table>
<br>