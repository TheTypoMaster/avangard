<?
//*****************************************************************************************************************
//	Список форумов. Публичная часть.
//*****************************************************************************************************************
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
	//*******************************************************
	
	if ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="SET_BE_READ")
		ForumSetAllMessagesRead(false);
		
	ForumSetLastVisit(0);
/*	?><pre><?print_r($_SESSION["FORUM"])?></pre><?*/
/*	?><pre><?=date("Y-m-d H:i:s", $_SESSION["FORUM"]["LAST_VISIT_FORUM_0"]);?></pre><?*/
	define("FORUM_MODULE_PAGE", "INDEX");
	
	$arFilter = array();
	if (!$USER->IsAdmin())
		$arFilter = array("LID" => LANG, "PERMS" => array($USER->GetGroups(), 'A'), "ACTIVE" => "Y");
	$db_Forum = CForumNew::GetListEx(array("FORUM_GROUP_SORT"=>"ASC", "FORUM_GROUP_ID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
	$db_Forum->NavStart($GLOBALS["FORUMS_PER_PAGE"]);
	$GlobPerm = false;
	
	
	ob_start();
	?><p><font class="forumbodytext"><?echo $db_Forum->NavPrint(GetMessage("FI_FORUM"))?></font></p>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" class="forumtitle">
					<?echo GetMessage("FI_FORUM_LIST")?>
				</td>
			</tr>
		</table>
		<font style="font-size:4px;">&nbsp;<br></font>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder">
		  <tr>
			<td>
			  <table width="100%" border="0" cellspacing="1" cellpadding="4">
				<tr class="forumhead">
				<!-- head !-->
					<td align="center" nowrap class="forumheadtext">
					</td>
					<td width="45%" nowrap class="forumheadtext">
						<?echo GetMessage("FI_FORUM_NAME")?>
					</td>
					<td width="14%" align="center" nowrap class="forumheadtext">
						<?echo GetMessage("FI_FORUM_TOPICS")?>
					</td>
					<td width="7%" align="center" nowrap class="forumheadtext">
						<?echo GetMessage("FI_FORUM_MESS")?>
					</td>
					<td width="27%" nowrap class="forumheadtext">
						<?echo GetMessage("FI_FORUM_LAST_MESS")?>
					</td>
				</tr>
			<?
			$currentGroupID = -1;
			while ($ar_Forum = $db_Forum->Fetch())
			{
				if ($USER->IsAdmin())
					$perm = "Y";
				else 
					$perm = ForumCurrUserPermissions($ar_Forum["ID"]);
					
				if ($currentGroupID != IntVal($ar_Forum["FORUM_GROUP_ID"]))
				{
					if (IntVal($ar_Forum["FORUM_GROUP_ID"])>0)
					{
						$arCurForumGroup = CForumGroup::GetLangByID($ar_Forum["FORUM_GROUP_ID"], LANGUAGE_ID);
						?>
						<tr class="forumbody">
							<td class="forumbodytext" colspan="6">
								<b><?echo htmlspecialcharsEx($arCurForumGroup["NAME"]);?></b>
								<?if (strlen($arCurForumGroup["DESCRIPTION"])>0):?>
									<br><?echo htmlspecialcharsEx($arCurForumGroup["DESCRIPTION"]);?>
								<?endif;?>
							</td>
						</tr>
						<?
					}
					$currentGroupID = IntVal($ar_Forum["FORUM_GROUP_ID"]);
				}
				?><tr class="forumbody"><?
				if ($perm>="Q"):
					$GlobPerm = true;
					?><td align="center" class="forumbodytext" valign="top">&nbsp;<?$mCnt = CForumMessage::GetList(array(), array("FORUM_ID"=>$ar_Forum["ID"], "APPROVED"=>"N"), true);
					if($mCnt>0):
						?><a href="appr_message.php?FID=<?=$ar_Forum["ID"];?>" title="<?=GetMessage("FL_MESSAGE_NOT_APPROVED")?>">(<?echo $mCnt?>)</a><?
					endif;
					?></td><?
				else :
					?><!-- !--><?
				endif;
					?><td align="center" class="forumbodytext" valign="top">
						&nbsp;<?
						
						if (NewMessageForum($ar_Forum["ID"], $ar_Forum["LAST_POST_DATE"]))
						{
							?><a href="read.php?FID=<?echo $ar_Forum["ID"];?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_norm.gif" width="18" height="12" alt="<?echo GetMessage("FI_HAVE_NEW_MESS")?>" border="0"></a><?
						}
						else
						{
							?><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_norm_no.gif" width="18" height="12" alt="<?echo GetMessage("FI_NO_NEW_MESS")?>" border="0"><?
						}
						?>
					</td>
					
					<td class="forumbodytext" valign="top">
						<a href="list.php?FID=<?echo $ar_Forum["ID"];?>"><?echo htmlspecialcharsEx($ar_Forum["NAME"]);?></a><br>
						<?echo htmlspecialcharsEx($ar_Forum["DESCRIPTION"])?>
					</td>
					<td align="center" class="forumbodytext" valign="top">
						<?echo $ar_Forum["TOPICS"]?>
					</td>
					<td align="center" class="forumbodytext" valign="top">
						<?echo $ar_Forum["POSTS"]?> 
					</td>
					<td class="forumbodytext" valign="top">
						<?if (strlen($ar_Forum["LAST_POST_DATE"])>0) echo $ar_Forum["LAST_POST_DATE"]."<br>";?>
						<?if (strlen($ar_Forum["TITLE"])>0):?>
							<?echo GetMessage("FI_TOPIC")?> <a href="read.php?FID=<?echo $ar_Forum["ID"];?>&TID=<?echo $ar_Forum["TID"];?>&MID=<?echo $ar_Forum["MID"];?>#message<?echo $ar_Forum["MID"];?>"><?echo (strlen($ar_Forum["TITLE"])>23) ? htmlspecialcharsEx(substr($ar_Forum["TITLE"], 0, 20))."..." : htmlspecialcharsEx($ar_Forum["TITLE"]);?></a><br>
						<?endif;?>
						<?if (strlen($ar_Forum["LAST_POSTER_NAME"])>0):?>
							<?echo GetMessage("FI_AUTHOR")?> <?echo (IntVal($ar_Forum["LAST_POSTER_ID"])>0)?"<a href=\"view_profile.php?UID=".$ar_Forum["LAST_POSTER_ID"]."\">":""?><?= htmlspecialcharsEx($ar_Forum["LAST_POSTER_NAME"])?><?echo (IntVal($ar_Forum["LAST_POSTER_ID"])>0)?"</a>":""?>
						<?endif;?>
					</td>
				</tr>
				<?
			}
			?>
		
			  </table>
			</td>
		  </tr>
		</table>
		
	<table width="100%" border="0">
		<tr>
			<td align="left">
				<font class="forumbodytext">
				<?echo $db_Forum->NavPrint(GetMessage("FI_FORUM"))?>
				</font>
			</td>
		</tr>
	</table>
	
	<br>
	<? // show online forum users list ?><?
	$UserOnLine = ShowActiveUser(array("PERIOD" => 600, "TITLE" => GetMessage("FI_USER_PROFILE")));
	?><table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td><table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class="forumhead"><td valign="top" class="forumtitletext"><?=GetMessage("FI_NOW_ONLINE")." ".$UserOnLine["HEAD"]?></td></tr>
		<tr class="forumbody"><td valign="top" class="forumbodytext"><?=$UserOnLine["BODY"]?></td></tr>
	</table></td></tr></table><?
	?><br><?
	?><? // show list of users, who have a birthday today ?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class="forumhead">
			<td valign="top" class="forumtitletext">
				<?echo GetMessage("FI_TODAY_BIRTHDAY")?>
			</td>
		</tr>
		<tr class="forumbody">
			<td valign="top" class="forumbodytext">
			<?
			$boundary_date = Date("m-d");
			$db_cur_users = CForumUser::GetList(array(), array("PERSONAL_BIRTHDAY_DATE" => $boundary_date, ">=USER_ID" => 1));
			$b_need_comma = False;
			while ($ar_cur_users = $db_cur_users->Fetch())
			{
				if ($b_need_comma)
					echo ", ";
	
				$str_cur_name = "";
				if ($ar_cur_users["SHOW_NAME"]=="Y")
				{
					$str_cur_name = Trim($ar_cur_users["NAME"]);
					if (strlen($ar_cur_users["LAST_NAME"])>0)
					{
						if (strlen($str_cur_name)>0)
							$str_cur_name .= " ";
						$str_cur_name .= Trim($ar_cur_users["LAST_NAME"]);
					}
				}
	
				if (strlen($str_cur_name)<=0)
					$str_cur_name = $ar_cur_users["LOGIN"];
	
				?><a href="view_profile.php?UID=<?echo $ar_cur_users["USER_ID"] ?>" title="<?echo GetMessage("FI_USER_PROFILE")?>"><?
				echo htmlspecialcharsEx($str_cur_name);
				?></a><?
				$b_need_comma = True;
			}
			if (!$b_need_comma)
			{
				?><?echo GetMessage("FI_NONE")?><?
			}
			?>
			</td>
		</tr>
	</table>
	</td></tr></table>
	<br>
	<center><font class="forumbodytext">
	<a href="index.php?ACTION=SET_BE_READ" title="<?echo GetMessage("FI_MARK_AS_READED")?>"><?echo GetMessage("FI_MARK_AS_READED_DO")?></a>
	</font></center><br><br><br><?
	$buf = ob_get_clean();
	if ($GlobPerm):
		$buf = str_replace(
			"<!-- head !-->", 
			"<td align='center' nowrap class='forumheadtext'><img src='/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_exclaim.gif' width=16 height=16 alt=".GetMessage("FL_MESSAGE_NOT_APPROVED")." title=".GetMessage("FL_MESSAGE_NOT_APPROVED")."></td>",
			$buf);
		$buf = str_replace(
			"<!-- !-->", 
			"<td class='forumbodytext' valign='top'>&nbsp;</td>",
			$buf);
	endif;
	
	$APPLICATION->SetTitle(GetMessage("FI_FORUM"));
	$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
	echo $buf;
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
	
	//*******************************************************
else:
	?>
	<font class="text"><b><?echo GetMessage("FI_NO_MODULE")?></b></font>
	<?
endif;
?>