<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

$strErrorMessage = "";

ForumSetLastVisit();

define("FORUM_MODULE_PAGE", "VIEW_PROFILE");

$UID = trim($_REQUEST["UID"]);
if ((strLen($UID) <= 0 ) && $USER->IsAuthorized())
{
	$UID = intVal($USER->getId());
}


$bUserFound = False;
if (intVal($UID)>0)
{
	$db_res = CUser::GetByID(IntVal($UID));
	if ($ar_res = $db_res->Fetch())
	{
		$UID = IntVal($UID);
		$bUserFound = True;

		while (list($key, $val) = each($ar_res))
			${"f_".$key} = htmlspecialcharsex(trim($val));
	}
}

if (!$bUserFound)
{
	$db_res = CUser::GetByLogin($UID);
	if ($ar_res = $db_res->Fetch())
	{
		while (list($key, $val) = each($ar_res))
			${"f_".$key} = htmlspecialcharsex(trim($val));

		$UID = IntVal($f_ID);
		$bUserFound = True;
	}
}

// ********************  VOTINGS  ************************
if ($_GET["VOTE_USER"]=="Y" && $UID>0 && $bUserFound && $USER->IsAuthorized())
{
	ForumVote4User($UID, $_GET["VOTES"], (strlen($_GET["CANCEL_VOTE"])>0 ? True : False), $strErrorMessage, $strOKMessage);
}
// ********************  END OF VOTINGS  *****************

$APPLICATION->SetTitle(GetMessage("FV_FTITLE"));
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

if (!$bUserFound)
{
	if (strLen($UID) <= 0)
		$strErrorMessage .= GetMessage("FV_NO_UID").". \n";
	else 
		$strErrorMessage .= str_replace("#UID#", $UID, GetMessage("FV_NO_DUSER")).". \n";
}
?>
<?
echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));
echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));
?>

<?
$FID = IntVal($_REQUEST["FID"]);
$TID = IntVal($_REQUEST["TID"]);
$MID = IntVal($_REQUEST["MID"]);
if ($FID>0 || $TID>0 || $MID>0)
{
	$strLink = "";
	if ($FID>0) $strLink .= "&FID=".$FID;
	if ($TID>0) $strLink .= "&TID=".$TID;
	if ($MID>0) $strLink .= "&MID=".$MID."#message".$MID;
	$strLink = substr($strLink, 1);
	?><a href="read.php?<?=$strLink ?>"><?=GetMessage("FV_BACK")?></a><br><br><?
}
?>

<?
// ********************  UTIL FUNCTIONS  ************************
function ForumUrlExtractTmp($s)
{
	$x = 0;
	while (strpos(",}])>.", substr($s, -1, 1))!==false)
	{
		$s2 = substr($s, -1, 1);
		$s = substr($s, 0, strlen($s)-1);
	}
	return "<a href=\"".$s."\" target=\"_blank\">".$s."</a>".$s2;
}

// ********************  END UTIL FUNCTIONS  ************************

if ($bUserFound)
{
	$ar_forum_user = CForumUser::GetByUSER_ID($UID);
	if ($ar_forum_user)
	{
		while (list($key, $val) = each($ar_forum_user))
			${"fu_".$key} = htmlspecialcharsex($val);
	}
	?>

	<h2>
		<?
		if (($fu_SHOW_NAME=="Y") && (strlen($f_NAME)>0 || strlen($f_LAST_NAME)>0))
			echo $f_NAME."&nbsp;".$f_LAST_NAME;
		else
			echo $f_LOGIN;
		?>
		<?if ($USER->IsAuthorized() 
			&& (IntVal($USER->GetID())==$UID || $USER->IsAdmin())):?>
			&nbsp;&nbsp;&nbsp;<small>[<a href="profile.php?ID=<?=$UID?>" title="<?=($USER->IsAdmin() && IntVal($USER->GetID())!=$UID) ? GetMessage("FV_EDIT_THIS_PROFILE") : GetMessage("FV_EDIT_YOUR_PROFILE") ?>"><?=GetMessage("FV_EDIT_PROFILE")?></a>]</small>
		<?endif;?>
	</h2><?

	// ********************  VOTINGS  ************************
	if (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y"
		&& $USER->IsAuthorized()
		&& ($USER->IsAdmin() || IntVal($USER->GetParam("USER_ID"))!=$UID))
	{
		$strNotesText = "";
		$bCanVote = False;
		$bCanUnVote = False;
		if ($USER->IsAdmin()) $bCanVote = True;

		$arUserRank = CForumUser::GetUserRank(IntVal($USER->GetParam("USER_ID")));

		$arUserPoints = CForumUserPoints::GetByID(IntVal($USER->GetParam("USER_ID")), $UID);
		if ($arUserPoints)
		{
			$bCanUnVote = True;
			$strNotesText .= str_replace("#POINTS#", $arUserPoints["POINTS"], str_replace("#END#", ForumNumberRusEnding($arUserPoints["POINTS"]), GetMessage("FV_ALREADY_VOTED1"))).". \n";

			if (IntVal($arUserPoints["POINTS"])<IntVal($arUserRank["VOTES"])
				&& !$USER->IsAdmin())
			{
				$bCanVote = True;
				$strNotesText .= str_replace("#POINTS#", (IntVal($arUserRank["VOTES"])-IntVal($arUserPoints["POINTS"])), str_replace("#END#", ForumNumberRusEnding((IntVal($arUserRank["VOTES"])-IntVal($arUserPoints["POINTS"]))), GetMessage("FV_ALREADY_VOTED3")));
			}
			elseif ($USER->IsAdmin())
			{
				$strNotesText .= GetMessage("FV_ALREADY_VOTED_ADMIN");
			}
		}
		else
		{
			if (IntVal($arUserRank["VOTES"])>0 || $USER->IsAdmin())
			{
				$bCanVote = True;
				$strNotesText .= GetMessage("FV_NOT_VOTED");
				if (!$USER->IsAdmin())
				{
					$strNotesText .= str_replace("#POINTS#", $arUserRank["VOTES"], str_replace("#END#", ForumNumberRusEnding($arUserRank["VOTES"]), GetMessage("FV_NOT_VOTED1"))).". \n";
				}
				elseif ($USER->IsAdmin())
				{
					$strNotesText .= GetMessage("FV_ALREADY_VOTED_ADMIN");
				}
			}
		}

		if (strlen($strNotesText)>0 || $bCanVote || $bCanUnVote)
		{
			?>
			<table border="0" cellpadding="3" cellspacing="1" class="forumbrd2" width="100%">
				<tr>
					<td valign="top" class="forumbodytext" >
						
						<?=$strNotesText?>
						
					</td>
					<?
					if ($bCanVote || $bCanUnVote)
					{
						?>
						<form method="GET" action="view_profile.php">
						<td valign="top" align="right" nowrap class="forumbodytext" >
							<?if ($USER->IsAdmin() && $bCanVote):?>
								<input type="text" name="VOTES" value="<?=IntVal($arUserRank["VOTES"])?>" size="5">
							<?endif;?>
							<input type="hidden" name="UID" value="<?=$UID ?>">
							<input type="hidden" name="FID" value="<?=IntVal($FID) ?>">
							<input type="hidden" name="TID" value="<?=IntVal($TID) ?>">
							<input type="hidden" name="MID" value="<?=IntVal($MID) ?>">
							<input type="hidden" name="VOTE_USER" value="Y">
							<?if ($bCanVote):?>
								<input type="submit" name="VOTE_BUTTON" value="<?=GetMessage("FV_DO_VOTE")?>" title="<?=GetMessage("FV_DO_VOTE_ALT")?>" class="forumnewtopic_button" style="width: 150px">
							<?endif;?>
							<?if ($bCanVote && $bCanUnVote):?>
								<br>
							<?endif;?>
							<?if ($bCanUnVote):?>
								<input type="submit" name="CANCEL_VOTE" value="<?=GetMessage("FV_UNDO_VOTE")?>" title="<?=GetMessage("FV_UNDO_VOTE_ALT")?>" class="forumnewtopic_button" style="width: 150px">
							<?endif;?>
						</td>
						</form>
						<?
					}
					?>
				</tr>
			</table>
			<br>
			<?
		}
	}
	// ********************  END OF VOTINGS  ************************

	?>
<table border="0" cellspacing="1" cellpadding="0" class="forumborder" width="100%">
  <tr>
	<td>
	<table width="100%" border="0"  cellspacing="1" cellpadding="4">
		<tr valign="top"  class="forumhead">
			<td class="forumheadtext" align="center" colspan="2"><b><?=GetMessage("FV_PRIVATE_DATA")?></b></td>
			<td class="forumheadtext" colspan="2" align="center"><b><?=GetMessage("FV_WORK_DATA")?></b></td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_BIRTHDATE")?></td>
			<td class="forumbodytext" width="30%"><?=$f_PERSONAL_BIRTHDAY ?>&nbsp;</td>
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_COMPANY")?></td>
			<td class="forumbodytext" width="30%"><?=$f_WORK_COMPANY ?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_WWW_PAGE")?></td>
			<td class="forumbodytext" width="30%"><?
				if (strlen($f_PERSONAL_WWW)>0 && $f_PERSONAL_WWW!="http://")
				{
					$strBValueTmp = substr($f_PERSONAL_WWW, 0, 6);
					if ($strBValueTmp!="http:/" && $strBValueTmp!="https:" && $strBValueTmp!="ftp://")
						$f_PERSONAL_WWW = "http://".$f_PERSONAL_WWW;

					echo "<a href=\"".$f_PERSONAL_WWW."\" target=\"_blank\">".$f_PERSONAL_WWW."</a>";
				}
			?>&nbsp;</td>
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_WWW_PAGE")?></td>
			<td class="forumbodytext" width="30%"><?
				if (strlen($f_WORK_WWW)>0 && $f_WORK_WWW!="http://")
				{
					$strBValueTmp = substr($f_WORK_WWW, 0, 6);
					if ($strBValueTmp!="http:/" && $strBValueTmp!="https:" && $strBValueTmp!="ftp://")
						$f_WORK_WWW = "http://".$f_WORK_WWW;

					echo "<a href=\"".$f_WORK_WWW."\" target=\"_blank\">".$f_WORK_WWW."</a>";
				}
			?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_SEX")?></td>
			<td class="forumbodytext" width="30%"><?
				$str_PERSONAL_GENDER = "";
				if ($f_PERSONAL_GENDER=="M")
					$str_PERSONAL_GENDER = GetMessage("FV_SEX_MALE");
				elseif ($f_PERSONAL_GENDER=="F")
					$str_PERSONAL_GENDER = GetMessage("FV_SEX_FEMALE");
				echo $str_PERSONAL_GENDER;
			?>&nbsp;</td>
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_SEX_DEPARTMENT")?></td>
			<td class="forumbodytext" width="30%"><?=$f_WORK_DEPARTMENT ?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_CONTACTS")?></td>
			<td class="forumbodytext" width="30%">
				<table border="0" width="100%" cellpadding="0" cellspacing="0">
					<tr class="forumbody">
						<?if ($USER->IsAuthorized()):?>
						<td class="forumbodytext" align="center"><a href="pm_message.php?mode=new&USER_ID=<?=$UID?>" title="<?=GetMessage("FV_SEND_PM_ALT")?>"><?=GetMessage("FV_SEND_PM")?></a></td>
						<?endif;?>
						<?if (strlen($f_EMAIL)>0):?>
						<td class="forumbodytext" align="center">
							<a href="send_message.php?TYPE=MAIL&UID=<?=$UID ?>" title="<?=GetMessage("FV_SEND_EMAIL_ALT")?>"><?=GetMessage("FV_SEND_EMAIL")?></a>
						</td>
						<?endif;?>
						<?if ((strlen($f_PERSONAL_ICQ)>0) && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y")):?>
						<td class="forumbodytext" align="center">
							<a href="send_message.php?TYPE=ICQ&UID=<?=$UID ?>" title="<?=GetMessage("FV_SEND_ICQ_ALT")?>"><?=GetMessage("FV_SEND_ICQ")?></a>
						</td>
						<?endif;?>
					</tr>
				</table><?
			?></td>
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_POST")?></td>
			<td class="forumbodytext" width="30%"><?=$f_WORK_POSITION ?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_LOCATION_PERS")?></td>
			<td class="forumbodytext" width="30%"><?
				$str_LOCATION = GetCountryByID($f_PERSONAL_COUNTRY);
				if (strlen($str_LOCATION)>0 && strlen($f_PERSONAL_CITY)>0)
					$str_LOCATION .= ", ";
				$str_LOCATION .= $f_PERSONAL_CITY;
				echo $str_LOCATION;
			?>&nbsp;</td>
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_LOCATION")?></td>
			<td class="forumbodytext" width="30%"><?
				$str_LOCATION = GetCountryByID($f_WORK_COUNTRY);
				if (strlen($str_LOCATION)>0 && strlen($f_WORK_CITY)>0)
					$str_LOCATION .= ", ";
				$str_LOCATION .= $f_WORK_CITY;
				echo $str_LOCATION;
			?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_INTERESTS")?></td>
			<td class="forumbodytext" width="30%"><?
				$fu_INTERESTS = preg_replace("'((http|https|ftp):\/\/[^ \t\r\n\"Р-пр-џ]+)'ies", "ForumUrlExtractTmp('\\1')", $fu_INTERESTS);
				$fu_INTERESTS = preg_replace("'(^|([\s\(\[\"<]+))([=-a-zA-Z0-9~][-_a-zA-Z0-9.+~\x02]*@([^Р-пр-џ\s\x01]+\.)+([-_A-Za-z0-9\x02]+))'is", '\1<a href="mailto:\3">\3</a>', $fu_INTERESTS);
				echo wordwrap(substr($fu_INTERESTS, 0, 5000), 50, "<WBR>", 1);
			?>&nbsp;</td>
			<td class="forumbodytext" style="" width="20%"><?=GetMessage("FV_ACTIVITY")?></td>
			<td class="forumbodytext" width="30%"><?=wordwrap(substr($f_WORK_PROFILE, 0, 5000), 50, "<WBR>", 1)?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumhead">
			<td class="forumbodytext" align="center" colspan="2"><b><?=GetMessage("FV_FORUM_INFO")?></b></td>
			<td class="forumbodytext" align="center"><b><?=GetMessage("FV_AVATAR")?></b></td>
			<td class="forumbodytext" align="center"><b><?=GetMessage("FV_PHOTO")?></b></td>
		</tr>
		<?
		?><tr valign="top" class="forumbody">
				<td class="forumbodytext" width="20%"><?=GetMessage("FV_VIEW_MESSAGE")?></td>
				<td class="forumbodytext" width="30%">
				<?if ($USER->IsAuthorized()):?>
					<a href="list_user.php?UID=<?=$UID?>&mode=lta" title="<?=GetMessage("FV_ALL_TOPICS_AUTHOR_ALT")?>"><?=GetMessage("FV_ALL_TOPICS_AUTHOR")?></a><br>
					<a href="list_user.php?UID=<?=$UID?>&mode=lt" title="<?=GetMessage("FV_ALL_TOPICS_ALT")?>"><?=GetMessage("FV_ALL_TOPICS")?></a><br>
					<a href="list_user.php?UID=<?=$UID?>&mode=all" title="<?=GetMessage("FV_ALL_MESSAGES_ALT")?>"><?=GetMessage("FV_ALL_MESSAGES")?></a><br>
				<?else:?>	
					&nbsp;
				<?endif;?>
				</td>
				<td class="forumbodytext" width="20%" rowspan="25"><?
				if (strlen($fu_AVATAR)>0):
					?><?=CFile::ShowImage($fu_AVATAR, 200, 200, "border=0", "", true)?><?
				endif;
				?>&nbsp;</td>
				<td class="forumbodytext" width="20%" rowspan="25"><?
				if (strlen($f_PERSONAL_PHOTO)>0):
					?><?=CFile::ShowImage($f_PERSONAL_PHOTO, 200, 200, "border=0", "", true)?><?
				endif;
				?>&nbsp;</td>
		</tr><?
		if (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y"):
			$arRank = CForumUser::GetUserRank($UID, LANG_ADMIN_LID);?>
			<tr valign="top" class="forumbody">
				<td class="forumbodytext" width="20%"><?=GetMessage("FV_ZVA")?></td>
				<td class="forumbodytext" width="30%"><?
					echo $arRank["NAME"];
					if ($USER->IsAuthorized()
						&& ($USER->IsAdmin() || IntVal($USER->GetParam("USER_ID"))==$UID))
					{
						echo "<br>".GetMessage("FV_NUM_POINTS").$fu_POINTS;
						echo "<br>".GetMessage("FV_NUM_VOTES").(IntVal($arRank["VOTES"])>0 ? IntVal($arRank["VOTES"]) : GetMessage("FV_NO_VOTES"));
					}
				?>&nbsp;</td>
			</tr>
		<?endif;?>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_NUM_MESSAGES")?></td>
			<td class="forumbodytext" width="30%"><?=$fu_NUM_POSTS ?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_DATE_REGISTER")?></td>
			<td class="forumbodytext" width="30%"><?=$fu_DATE_REG ?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%"><?=GetMessage("FV_DATE_VISIT")?></td>
			<td class="forumbodytext" width="30%"><?=$fu_LAST_VISIT; ?>&nbsp;</td>
		</tr>
		<tr valign="top" class="forumbody">
			<td class="forumbodytext" width="20%" style=""><?=GetMessage("FV_LAST_MESSAGE")?></td>
			<td class="forumbodytext" width="30%"><?
				$arFilter = array("AUTHOR_ID"=>$UID);
				$arTopic = CForumUser::UserAddInfo(array("ID"=>"DESC"), array("AUTHOR_ID"=>$UID), "topics");
				if ($arTopic)
				{
					if ($arTopic = $arTopic->GetNext())
					{
					?><?=$arTopic["LAST_POST_DATE"];?><br><?
					?><?=GetMessage("FV_IN_TOPIC");?>: <?
					?><a href="read.php?TID=<?=$arTopic["TOPIC_ID"] ?>&MID=<?=$arTopic["LAST_POST"]?>#message<?=$arTopic["LAST_POST"]?>">
						<b><?=$arTopic["TITLE"]?></b><?
					if (strlen($arTopic["DESCRIPTION"])>0)
						echo ", ".htmlspecialcharsex($arTopic["DESCRIPTION"]);
					?></a><?
					}
				}
			?>&nbsp;</td>
		</tr>
	</table>
</td>	
</tr>
</table>
	<?
	if ($USER->IsAuthorized()
		&& (IntVal($USER->GetID())==$UID || $USER->IsAdmin())):
		?><br><br><table border="0" width="100%">
			<tr><td class="forumbodytext"><?=GetMessage("FV_TO_CHANGE1")?> <?=($USER->IsAdmin() && IntVal($USER->GetID())!=$UID) ? GetMessage("FV_TO_CHANGE2") : GetMessage("FV_TO_CHANGE3") ?> <?=GetMessage("FV_TO_CHANGE4")?> 
		<a href="profile.php?ID=<?=$UID?>" title="<?=($USER->IsAdmin() && IntVal($USER->GetID())!=$UID) ? GetMessage("FV_EDIT_THIS_PROFILE") : GetMessage("FV_EDIT_YOUR_PROFILE") ?>"><?=GetMessage("FV_TO_CHANGE5")?></a>.
				</td></tr>
		</table><?
	endif;
}
?>

<br><br><br>
<?
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

//*******************************************************
else:
	?>
	<font class="text"><b><?=GetMessage("FV_NO_MODULE")?></b>
	<?
endif;

function ForumNumberRusEnding($num)
{
	if (LANGUAGE_ID=="ru")
	{
		if (strlen($num)>1 && substr($num, strlen($num)-2, 1)=="1")
		{
			return "ют";
		}
		else
		{
			$c = IntVal(substr($num, strlen($num)-1, 1));
			if ($c==0 || ($c>=5 && $c<=9))
				return "ют";
			elseif ($c==1)
				return "";
			else
				return "р";
		}
	}
	else
	{
		if (IntVal($num)>1)
			return "s";
		return "";
	}
}
?>