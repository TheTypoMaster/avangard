<?
//*****************************************************************************************************************
//	Список тем форума. Публичная часть.
//*****************************************************************************************************************
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*****************************************************************************************************************
//*************************Let's init $FID (forum id) with actual and coordinated value****************************
	define("FORUM_MODULE_PAGE", "LIST");
	
	$FID = IntVal($_REQUEST["FID"]);
	ForumSetLastVisit($FID);
	$arForum = CForumNew::GetByID($FID);
	if (!$arForum)
	{
		LocalRedirect("index.php");
		die();
	}
//*************************!***************************************************************************************
//*************************!Let's check if current user can can view this forum************************************
if (!CForumNew::CanUserViewForum($FID, $USER->GetUserGroupArray()))
	$APPLICATION->AuthForm(GetMessage("FL_NO_FPERMS"));
if ($USER->IsAdmin())
	$PermissionUser = "Y";
else 
	$PermissionUser = ForumCurrUserPermissions($arForum["ID"]);

//*************************!Let's init read labels*****************************************************************
	
//*************************!ACTIONS: subscribe*********************************************************************
	$strErrorMessage = ""; $strOKMessage = "";
	$arError = array();
	$action = strToUpper($_REQUEST["ACTION"]);
	switch ($action)
	{
		case "FORUM_SUBSCRIBE":
		case "FORUM_SUBSCRIBE_TOPICS":
			if ($FID>0)
			{
				if (ForumSubscribeNewMessagesEx($FID, 0, (($_GET["ACTION"]=="FORUM_SUBSCRIBE_TOPICS")?"Y":"N"), $strErrorMessage, $strOKMessage))
				{
					LocalRedirect("subscr_list.php?FID=".$FID);
				}
			}
		break;
		case "SET_BE_READ":
			if ($FID>0)
			{
				ForumSetAllMessagesRead($FID);
			}
		break;
		case "SET_ORDINARY":
		case "SET_TOP":
			if (check_bitrix_sessid())
			{
				if ($action == "SET_ORDINARY")
				{
					$action = "ORDINARY";
					$sort = "150";
				}
				else 
				{
					$action = "TOP";
					$sort = "100";
				}
				ForumTopOrdinaryTopic($_REQUEST["TID_ARRAY"], $action, $strErrorMessage, $strOKMessage);
			}
			break;
		case "MOVE_TOPIC":
			// Здесь необходима проверка.
			$topic = explode(",", $_REQUEST["TID_ARRAY"]);
			$topic = ForumMessageExistInArray($topic);
			if (check_bitrix_sessid() && $topic)
			{
				LocalRedirect("move.php?FID=".$FID."&TID=".implode(",", $topic));
			}
			else 
			{
				$arError[] = GetMessage("FMT_NO_TOPICS");
			}
		break;
		case "DEL_TOPIC":
			if (check_bitrix_sessid())
			{
				ForumDeleteTopic($_REQUEST["TID_ARRAY"], $strErrorMessage, $strOKMessage);
			}
		break;
		case "STATE_Y":
		case "STATE_N":
			if ($action == "STATE_Y")
			{
				$action = "OPEN";
				$state = "Y";
			}
			else 
			{
				$action = "CLOSE";
				$state = "N";
			}
			if (check_bitrix_sessid())
			{
				ForumOpenCloseTopic($_REQUEST["TID_ARRAY"], $action, $strErrorMessage, $strOKMessage);
			}
		break;
	}
	
	if ($_GET["TID"]=="Y")
		$strOKMessage = GetMessage("FL_TO_VIEW_TOPIC");
		
//*************************!End of ACTIONS*************************************************************************

//$APPLICATION->AddChainItem($arForum["NAME"], "list.php?FID=".$FID);

$APPLICATION->SetTitle(GetMessage("FL_FORUM_PREF")." &laquo;".htmlspecialchars($arForum["NAME"])."&raquo;");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", array("FID"=>$FID));
ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));
ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));



InitSorting();
global $by, $order;
if (!$by)
{
	$by = "LAST_POST_DATE";
	$order = "DESC";
}
$arFilter = array("FORUM_ID"=>$FID);
if ($USER->IsAuthorized())
	$arFilter["USER_ID"] = $USER->GetID();
if ($PermissionUser<"Q")
	$arFilter["APPROVED"] = "Y";
//else
//	$arFilter["COUNT_UNREAD_MESSAGE"] = "Y";
	
$FORUM_NEW_TOPIC = false;
$db_Topic = CForumTopic::GetListEx(array("SORT"=>"ASC", $by=>$order), $arFilter, false, false, array("bDescPageNumbering"=>false, "nPageSize"=>$GLOBALS["FORUM_TOPICS_PER_PAGE"], "bShowAll" => false));
$db_Topic->NavStart($GLOBALS["FORUM_TOPICS_PER_PAGE"]);
?><script language="Javascript"><?
	if ($strJSPath = $APPLICATION->GetTemplatePath("forum/forum_tmpl_1/forum_js.php"))
	include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
?></script><?	
?><table width="100%" border="0">
	<tr>
		<td align="left" width="99%"><?=$db_Topic->NavPrint(GetMessage("FL_TOPICS"))?></td>
		<form action="new_topic.php" method="GET">
		<td align="right" width="1%">
			<?if (CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())):?>
				<input type="hidden" name="FID" value="<?=$FID;?>">
				<input type="submit" value="<?=GetMessage("FL_CREATE_NEW_TOPIC")?>" title="<?=GetMessage("FL_CREATE_NEW_TOPIC_T")?>" class="forumnewtopic_button">
			<?endif;?>
		</td>
		</form>
	</tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" class="forumtitle">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<?if ($PermissionUser>="Q"):?>
						<td width="1%" class="forumtitletext"><input type="checkbox" name="topc_all" value="Y" id="topic_all" onclick="SelectAllCheckBox('FORUM_TOPICS', 'topic_id[]', 'topic_all');">&nbsp;</td>
					<?endif;?>
					<td width="99%" class="forumtitletext"><?=GetMessage("FL_FORUM")?> <b><?=htmlSpecialCharsEx($arForum["NAME"])?></b></td>
					<td nowrap width="1%" align="right" class="forumtitletext"><?=GetMessage("FL_TOPICS_N")?> <?=$arForum["TOPICS"]; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<font style="font-size:4px;">&nbsp;<br></font>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder">
<form name="FORUM_TOPICS" id="FORUM_TOPICS" method="get">
  <tr>
	<td>
	  <table width="100%" border="0" cellspacing="1" cellpadding="4">
		<tr class="forumhead">
			<td align="center" nowrap class="forumheadtext"></td>
			<?if ($PermissionUser>="Q"):?>
				<td align="center" nowrap class="forumheadtext">&nbsp;</td>
				<td align="center" nowrap class="forumheadtext">
					<img src='/bitrix/templates/.default/forum/forum_tmpl_1/images/icon_exclaim.gif' width=16 height=16 alt="<?=GetMessage("FL_MESSAGE_NOT_APPROVED");?>" title="<?=GetMessage("FL_MESSAGE_NOT_APPROVED");?>">
				</td>
			<?endif;?>
			<td align="center" nowrap class="forumheadtext"></td>
			<td width="45%" nowrap class="forumheadtext" align="center"><?=GetMessage("FL_TOPIC_NAME")?><br><?=SortingEx("TITLE")?></td>
			<td width="14%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_AUTHOR")?><br><?=SortingEx("USER_START_NAME")?></td>
			<td width="7%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_POSTS")?><br><?=SortingEx("POSTS")?></td>
			<td width="7%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_VIEWS")?><br><?=SortingEx("VIEWS")?></td>
			<td width="27%" nowrap align="center" class="forumheadtext"><?=GetMessage("FL_TOPIC_LAST_MESS")?><br><?=SortingEx("LAST_POST_DATE")?></td>
		</tr><?
	while ($ar_Topic = $db_Topic->Fetch()):
		?>
		<tr class="forumbody">
			<td align="center" class="forumbodytext">&nbsp;
				<?
				$strClosed = "";
				if ($ar_Topic["STATE"]!="Y") 
					$strClosed = "closed_";
					
				if ($ar_Topic["APPROVED"]!="Y" && $PermissionUser>="Q"):
					?><font color="#FF0000"><b>NA</b></font><?
				elseif (NewMessageTopic($ar_Topic["FORUM_ID"], $ar_Topic["ID"], $ar_Topic["LAST_POST_DATE"], $ar_Topic["LAST_VISIT"])):
					?><a href="read.php?FID=<?=$ar_Topic["FORUM_ID"];?>&TID=<?=$ar_Topic["ID"]?>&MID=unread_mid"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_<?=$strClosed; ?>norm.gif" width="18" height="12" alt="<?=GetMessage("FL_HAVE_NEW_MESS")?>" border="0"></a><?
				else:
					?><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_<?=$strClosed; ?>norm_no.gif" width="18" height="12" alt="<?=GetMessage("FL_NO_NEW_MESS")?>" border="0"><?
				endif;
				?>
			</td>
			<?if($PermissionUser>="Q"):?>
				<td align="center" class="forumbodytext"><input type="checkbox" name="topic_id[]" value="<?=$ar_Topic["ID"]?>" id="topic_id[<?=$ar_Topic["ID"]?>]" onclick="SelectCheckBox('topic_all');"></td>
				<td align="center" class="forumbodytext"><?
				$mCnt = CForumMessage::GetList(array(), array("TOPIC_ID"=>$ar_Topic["ID"], "APPROVED"=>"N"), true);
				if(intVal($mCnt) > 0):
					?><a href="appr_message.php?FID=<?=$ar_Topic["FORUM_ID"];?>&TID=<?=$ar_Topic["ID"]?>" title="<?=GetMessage("FL_MESSAGE_NOT_APPROVED")?>"><?=$mCnt?></a>
				<?endif;?>
				&nbsp;</td>
			<?endif;?>
			<td align="center" class="forumbodytext">
				<?if (strlen($ar_Topic["IMAGE"])>0):?>
					<img src="/bitrix/images/forum/icon/<?=$ar_Topic["IMAGE"];?>" alt="<?=$ar_Topic["IMAGE_DESCR"];?>" border="0" width="15" height="15">
				<?endif;?>
			</td>
			<td class="forumbodytext">
				<?=(IntVal($ar_Topic["SORT"])!=150) ? "<b>".GetMessage("FL_PINNED").":</b> ":""?>
				<a href="read.php?FID=<?=$ar_Topic["FORUM_ID"];?>&TID=<?=$ar_Topic["ID"]?>" title="<?=GetMessage("FL_TOPIC_START")?> <?=$ar_Topic["START_DATE"]?>"><?=htmlspecialcharsEx($ar_Topic["TITLE"]) ?></a>
				<?
				$numMessages = $ar_Topic["POSTS"]+1;
				if ($PermissionUser>="Q")
				{
					$numMessages = CForumMessage::GetList(array(), array("TOPIC_ID"=>$ar_Topic["ID"]), true);
				}
				echo ForumShowTopicPages($numMessages, "read.php?FID=".$ar_Topic["FORUM_ID"]."&TID=".$ar_Topic["ID"]."", "PAGEN_1");
				?><br><?=htmlspecialcharsEx($ar_Topic["DESCRIPTION"]) ?></td>
			<td align="center" class="forumbodytext"><?=htmlspecialcharsEx($ar_Topic["USER_START_NAME"]) ?></td>
			<td align="center" class="forumbodytext"><?=$ar_Topic["POSTS"]?></td>
			<td align="center" class="forumbodytext"><?=$ar_Topic["VIEWS"]?></td>
			<td class="forumbodytext"><?=$ar_Topic["LAST_POST_DATE"]?><br><a href="read.php?FID=<?=$ar_Topic["FORUM_ID"];?>&TID=<?=$ar_Topic["ID"]?>&MID=<?=$ar_Topic["LAST_MESSAGE_ID"]?>#message<?=$ar_Topic["LAST_MESSAGE_ID"]?>"><?=GetMessage("FL_TOPIC_AUTHOR1")?></a>
				<b><a href="read.php?FID=<?=$ar_Topic["FORUM_ID"];?>&TID=<?=$ar_Topic["ID"]?>&MID=<?=$ar_Topic["LAST_MESSAGE_ID"]?>#message<?=$ar_Topic["LAST_MESSAGE_ID"]?>"><?=htmlspecialcharsEx($ar_Topic["LAST_POSTER_NAME"]) ?></a></b>
			</td>
		</tr>
		<?
	endwhile;
	?>
	  </table>
	</td>
  </tr>
</form>
</table>
<br>
<table width="100%" border="0">
	<tr>
		<td align="left">
			<?=$db_Topic->NavPrint(GetMessage("FL_TOPICS"))?>
		</td>
		<form action="new_topic.php" method="GET">
		<td align="right">
			<?
			if (CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())):
				?>
				<input type="hidden" name="FID" value="<?=$FID;?>">
				<input type="submit" value="<?=GetMessage("FL_CREATE_NEW_TOPIC")?>" title="<?=GetMessage("FL_CREATE_NEW_TOPIC_T")?>" class="forumnewtopic_button">
				<?
			endif;
			?>
		</td>
		</form>
	</tr>
</table>
<br><?
	$UserOnLine = ShowActiveUser(array("PERIOD" => 600, "FORUM_ID" => $FID));
	?><table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td><table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class="forumhead"><td valign="top" class="forumtitletext"><?=GetMessage("FL_NOW_ONLINE")." ".$UserOnLine["HEAD"]?></td></tr>
		<tr class="forumbody"><td valign="top" class="forumbodytext"><?=$UserOnLine["BODY"]?></td></tr>
	</table></td></tr></table><br>
	<?
?><center><font class="forumbodytext">
<a href="list.php?FID=<?=$FID; ?>&ACTION=SET_BE_READ" title="<?=GetMessage("FL_TOPIC_MARK_READ")?>"><?=GetMessage("FL_TOPIC_MARK_READ_DO")?></a>
</font></center>
<br><br><br>
<?
if ($FORUM_NEW_TOPIC == false)
	ForumSetReader($FID);


$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", array("FID"=>$FID));
//*******************************************************
else:
	?>
	<font class="text"><b><?=GetMessage("FL_NO_MODULE")?></b></font>
	<?
endif;
?>