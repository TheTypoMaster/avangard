<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
//*******************************************************

define("FORUM_MODULE_PAGE", "SUBSCRIBE");

$UID = IntVal($_REQUEST["UID"]);
if (!$USER->IsAdmin() || $UID<=0)
{
	$UID = IntVal($USER->GetParam("USER_ID"));
}

$bUserFound = False;
$db_userX = CUser::GetByID($UID);
if ($ar_userX = $db_userX->Fetch())
{
	$bUserFound = True;

	while (list($key, $val) = each($ar_userX))
		${"f_".$key} = htmlspecialchars($val);
}


$strErrorMessage = "";
$strOKMessage = "";
$bVarsFromForm = false;

if ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="DEL" && IntVal($_GET["ID"])>0)
{
	if (CForumSubscribe::CanUserDeleteSubscribe(IntVal($_GET["ID"]), $USER->GetUserGroupArray(), $USER->GetID()))
	{
		CForumSubscribe::Delete(IntVal($_GET["ID"]));
	}
	else
	{
		$strErrorMessage .= GetMessage("FSL_NO_SPERMS").". \n";
	}
}

$APPLICATION->AddChainItem(GetMessage("FSL_PROFILE"), "profile.php");
$APPLICATION->SetTitle(GetMessage("FSL_FTITLE"));
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");


$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

if (!$bUserFound)
	$strErrorMessage .= str_replace("#UID#", $UID, GetMessage("FSL_NO_DUSER")).". \n";
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<?
$db_res = CForumSubscribe::GetList(array("FORUM_ID"=>"ASC", "TOPIC_ID"=>"ASC", "START_DATE"=>"ASC"), array("USER_ID"=>$UID));
?>

<font class="forumbodytext">
<?
$FID = IntVal($_REQUEST["FID"]);
$TID = IntVal($_REQUEST["TID"]);
$strBackPath = "";
if ($TID>0)
	$strBackPath = "read.php?FID=".$FID."&TID=".$TID."";
elseif ($FID>0)
	$strBackPath = "list.php?FID=".$FID."";

if (strlen($strBackPath)>0)
{
	?><a href="<?echo $strBackPath; ?>"><?echo GetMessage("FSL_BACK")?></a><br><br><?
}
?>
</font>

<?
if ($res = $db_res->Fetch()):
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" class="forumtitle">
				<?echo GetMessage("FSL_SUBSCR_MANAGE")?>
			</td>
		</tr>
	</table>

	<font style="font-size:4px;">&nbsp;<br></font>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
	<table border="0" cellpadding="3" cellspacing="1" width="100%">
		<tr>
			<td class="forumhead" align="center"><font class="forumheadtext"><b><?echo GetMessage("FSL_FORUM_NAME")?></b></font></td>
			<td class="forumhead" align="center"><font class="forumheadtext"><b><?echo GetMessage("FSL_TOPIC_NAME")?></b></font></td>
			<td class="forumhead" align="center"><font class="forumheadtext"><b><?echo GetMessage("FSL_SUBSCR_DATE")?></b></font></td>
			<td class="forumhead" align="center"><font class="forumheadtext"><b><?echo GetMessage("FSL_LAST_SENDED_MESSAGE")?></b></font></td>
			<td class="forumhead" align="center"><font class="forumheadtext"><b><?echo GetMessage("FSL_ACTIONS")?></b></font></td>
		</tr>
		<?
		do
		{
			$arForum_tmp = CForumNew::GetByID($res["FORUM_ID"]);
			$arTopic_tmp = CForumTopic::GetByID($res["TOPIC_ID"]);
			?>
			<tr>
				<td class="forumbody"><font class="forumbodytext"><a href="list.php?FID=<?echo $res["FORUM_ID"];?>"><?echo $arForum_tmp["NAME"];?></a></font></td>
				<td class="forumbody"><font class="forumbodytext"><?
						if (IntVal($res["TOPIC_ID"])>0)
						{
							echo "<a href=\"read.php?FID=".$res["FORUM_ID"]."&TID=".$res["TOPIC_ID"]."\">".$arTopic_tmp["TITLE"]."</a>";
						}
						else
						{
							if ($res["NEW_TOPIC_ONLY"]=="Y")
								echo GetMessage("FSL_NEW_TOPICS");
							else
								echo GetMessage("FSL_ALL_MESSAGES");
						}
				?></font></td>
				<td class="forumbody"><font class="forumbodytext"><?echo $res["START_DATE"];?></font></td>				
				<td class="forumbody" align="center"><font class="forumbodytext"><?if (IntVal($res["LAST_SEND"])>0):
					?><a href="read.php?MID=<?echo $res["LAST_SEND"];?>#message<?echo $res["LAST_SEND"];?>"><?echo GetMessage("FSL_HERE")?></a><?
				endif;?></font></td>
				<td class="forumbody"><font class="forumbodytext"><a href="subscr_list.php?ID=<?echo $res["ID"];?>&ACTION=DEL"><?echo GetMessage("FSL_DELETE")?></a></font></td>
			</tr>
			<?
		}
		while ($res = $db_res->Fetch());
		?>
	</table>
	<td><tr></table>
	<?
else:
	?>
	<font class="forumbodytext"><?echo GetMessage("FSL_NOT_SUBCRIBED")?></font>
	<?
endif;
?>


<br><br><br>
<?
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

//*******************************************************
	else:
		?>
		<font class="text"><b><?echo GetMessage("FSL_NO_MODULE")?></b></font>
		<?
	endif;
else:
	?>
	<font class="text"><b><?echo GetMessage("FSL_NO_AUTHORIZE")?></b></font>
	<?
endif;
?>