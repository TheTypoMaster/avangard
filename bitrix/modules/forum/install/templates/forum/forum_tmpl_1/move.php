<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
//*******************************************************
$strErrorMessage = "";
$strOKMessage = "";
$bVarsFromForm = false;
$FID = IntVal($_REQUEST["FID"]);
$newFID = IntVal($_REQUEST["newFID"]);
$arForum = CForumNew::GetByID($FID);
if (!$arForum)
{
	LocalRedirect("index.php");
	die();
}
$topics = ForumMessageExistInArray($_REQUEST["TID"]);
$arTopic = array();
$arTopicID = array();
if ($topics)
{
	$arFilter = array("@ID" => implode(",", $topics), "FORUM_ID" => $FID);
	if (!$USER->IsAdmin())
		$arFilter["PERMISSION_STRONG"] = true;
		
	$db_res = CForumTopic::GetListEx(array(), $arFilter);
	if ($db_res && $res = $db_res->Fetch())
	{
		do
		{
			$arTopic[] = $res;
			$arTopicID[] = $res["ID"];
		}while ($res = $db_res->Fetch());
	}
}
if (count($arTopic) <= 0)
{
	LocalRedirect("list.php?FID=".$FID);
	die();
}
define("FORUM_MODULE_PAGE", "MOVE");
if (ForumCurrUserPermissions($FID)<"Q")
	$APPLICATION->AuthForm(GetMessage("FM_NO_FPERMS"));

if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["action"]=="move" && check_bitrix_sessid())
{
	if (IntVal($newFID)<=0)
		$strErrorMessage .= GetMessage("FM_EMPTY_DEST_FORUM").". \n";

	if (strlen($strErrorMessage)<=0)
	{
		$NewForum = CForumNew::GetByIDEx($newFID);
		if ((ForumCurrUserPermissions($newFID)<"Q") && ($NewForum["ALLOW_MOVE_TOPIC"]!="Y"))
			$strErrorMessage .= GetMessage("FM_NO_DEST_FPERMS").". \n";
	}
	if (strlen($strErrorMessage)<=0)
	{
		foreach ($arTopic as $Topic)
		{
			$res = CForumTopic::MoveTopic2Forum($Topic["ID"], $newFID);
			if (!$res)
			{
				$strErrorMessage .= GetMessage("FM_ERR_MOVE_TOPIC").". \n";
			}
//			elseif ($MakeNew)
//			{
//				foreach ($res as $MID)
//				{
//					CForumMessage::SendMailMessage($MID, array(), false, "NEW_FORUM_MESSAGE");
//				}
//			}
		}
	}
	
	if (strlen($strErrorMessage)>0)
		$bVarsFromForm = true;
	else
		LocalRedirect("list.php?FID=".$newFID);
}

$APPLICATION->AddChainItem($arForum["NAME"], "read.php?FID=".$FID."&TID=".$TID);
$APPLICATION->SetTitle(GetMessage("FM_MOVE_TITLE"));
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));
echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="forumborder">
<tr><td>
	<table width="100%" border="0" cellspacing="1" cellpadding="4">
		<form method="POST">
			<input type="hidden" name="action" value="move">
			<input type="hidden" name="FID" value="<?=$FID; ?>">
			<input type="hidden" name="TID" value="<?=implode(",", $arTopicID);?>">
			<?=bitrix_sessid_post()?>
			
		<tr>
			<td class="forumhead" colspan="2" align="center"><font class="forumheadtext"><b><?echo GetMessage("FM_TOPIC_MOVEMENT")?></b></font></td>
		</tr>
		<tr>
			<td class="forumbody" align="right" width="40%"><font class="forumheadtext"><?echo GetMessage("FM_MOVE_TOPIC")?></font></td>
			<td class="forumbody" align="left" width="60%"><font class="forumbodytext"><?
				$arFilter = array();
				if (!$USER->IsAdmin())
				{
					$arFilter["!ID"] = intVal($FID);
					$arFilter["PERMS"] = array($USER->GetGroups(), "ALLOW_MOVE_TOPIC");
					$arFilter["ACTIVE"] = "Y";
					$arFilter["SITE_ID"] = SITE_ID;
				}
				$db_Forum = CForumNew::GetListEx(array("NAME"=>"ASC"), $arFilter);
				
				if ($db_Forum && ($ar_Forum = $db_Forum->Fetch()))
				{
					?><select name="newFID" class="inputselect"><?
					do {
						if ($FID != $ar_Forum["ID"]):
							?><option value="<?=$ar_Forum["ID"]; ?>" <?if ($newFID==IntVal($ar_Forum["ID"])) echo "selected";?>><?=$ar_Forum["NAME"]; ?></option><?
						endif;
					}while ($ar_Forum = $db_Forum->Fetch());
				?></select><?
				}
				// вывод тем
				?></font></td>
		</tr>
	</table>
</td></tr></table>
<font style="font-size:4px;">&nbsp;<br></font>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" class="forumtitle">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<td width="99%" class="forumtitletext"><?=GetMessage("FL_FORUM")?> <a href="list.php?FID=<?=$arForum["ID"]?>"><b><?=htmlSpecialCharsEx($arForum["NAME"])?></b></a></td>
					<td nowrap width="1%" align="right" class="forumtitletext"><?=GetMessage("FL_TOPICS_N")?> <?=$arForum["TOPICS"]; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<font style="font-size:4px;">&nbsp;<br></font>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder">
  <tr>
	<td>
				  <table width="100%" border="0" cellspacing="1" cellpadding="4">
					<tr class="forumhead">
						<td align="center" nowrap class="forumheadtext">&nbsp;</td>
						<td align="center" nowrap class="forumheadtext">&nbsp;</td>
						<td width="45%" nowrap class="forumheadtext" align="center"><?=GetMessage("FL_TOPIC_NAME")?></td>
						<td width="14%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_AUTHOR")?></td>
						<td width="7%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_POSTS")?></td>
						<td width="7%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_VIEWS")?></td>
						<td width="27%" nowrap align="center" class="forumheadtext"><?=GetMessage("FL_TOPIC_LAST_MESS")?></td>
					</tr><?
				foreach ($arTopic as $Topic)
				{
					
					?>
					<tr class="forumbody">
						<td align="center" class="forumbodytext">&nbsp;
							<?
							$strClosed = "";
							if ($Topic["STATE"]!="Y") 
								$strClosed = "closed_";
							if ($Topic["APPROVED"]!="Y"):
								?><font color="#FF0000"><b>NA</b></font><?
							else:
								?><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_<?=$strClosed; ?>norm_no.gif" width="18" height="12" alt="" border="0"><?
							endif;
							?>
						</td>
						<td align="center" class="forumbodytext">
							<?if (strlen($Topic["IMAGE"])>0):?>
								<img src="/bitrix/images/forum/icon/<?=$Topic["IMAGE"];?>" alt="<?=$Topic["IMAGE_DESCR"];?>" border="0" width="15" height="15">
							<?endif;?>
						</td>
						<td class="forumbodytext">
							<?=(IntVal($Topic["SORT"])!=150) ? "<b>".GetMessage("FL_PINNED").":</b> ":""?>
							<a href="read.php?FID=<?=$Topic["FORUM_ID"];?>&TID=<?=$Topic["ID"]?>" title="<?=GetMessage("FL_TOPIC_START")?> <?=$Topic["START_DATE"]?>"><?=htmlspecialcharsEx($Topic["TITLE"]) ?></a><br><?=htmlspecialcharsEx($Topic["DESCRIPTION"]) ?></td>
						<td align="center" class="forumbodytext"><?=htmlspecialcharsEx($Topic["USER_START_NAME"]) ?></td>
						<td align="center" class="forumbodytext"><?=$Topic["POSTS"]?></td>
						<td align="center" class="forumbodytext"><?=$Topic["VIEWS"]?></td>
						<td class="forumbodytext"><?=$Topic["LAST_POST_DATE"]?><br><a href="read.php?FID=<?=$Topic["FORUM_ID"];?>&TID=<?=$Topic["ID"]?>&MID=<?=$Topic["LAST_MESSAGE_ID"]?>#message<?=$Topic["LAST_MESSAGE_ID"]?>"><?=GetMessage("FL_TOPIC_AUTHOR1")?></a>
							<b><a href="read.php?FID=<?=$Topic["FORUM_ID"];?>&TID=<?=$Topic["ID"]?>&MID=<?=$Topic["LAST_MESSAGE_ID"]?>#message<?=$Topic["LAST_MESSAGE_ID"]?>"><?=htmlspecialcharsEx($Topic["LAST_POSTER_NAME"]) ?></a></b>
						</td>
					</tr>
					<?
				}
				?>
				  </table>			
		</td>
	</tr>
</table>
<font style="font-size:4px;">&nbsp;<br></font>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="forumborder">
<tr><td>
	<table width="100%" border="0" cellspacing="1" cellpadding="4">
		<tr>
			<td class="forumhead" colspan="2" align="center"><font class="forumheadtext"><input type="submit" value="<?echo GetMessage("FM_MOVE")?>" class="inputbutton"></font></td>
		</tr>
	</table>
</td></tr>
</table>
</form>
<br><br><br>
<?
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

{
if ($SHOW_FORUM_DEBUG_INFO)
{
	for ($i = 0; $i < count($arForumDebugInfo); $i++)
		echo $arForumDebugInfo[$i];
}
}

//*******************************************************
	else:
		?>
		<font class="text"><b><?echo GetMessage("FM_NO_MODULE")?></b></font>
		<?
	endif;
else:
	?>
	<font class="text"><b><?echo GetMessage("FM_NO_AUTHORIZE")?></b></font>
	<?
endif;
?>