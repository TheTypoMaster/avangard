<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

$INQUERY_FORUM_ID = IntVal($INQUERY_FORUM_ID);

$strOKMessage = "";
$strErrorMessage = "";

if ($INQUERY_FORUM_ID<=0)
	$strErrorMessage .= GetMessage("TFC_NO_FORUM_CODE").". ";

if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["comment_send"])>0)
{
	if (strlen($_POST["comment_author"])<=0)
		$strErrorMessage .= GetMessage("TFC_NO_NAME").". ";

	if (strlen($_POST["comment_body"])<=0)
		$strErrorMessage .= GetMessage("TFC_NO_BODY").". ";

	$sTransStarted = False;
	if (strlen($strErrorMessage)<=0)
	{
		$strDescription = $_POST["comment_body"]."\n";
		if (strlen($_POST["comment_phone"])>0)
			$strDescription .= "\n".GetMessage("COMM_PHONE")." ".$_POST["comment_phone"];
		if (strlen($_POST["comment_email"])>0)
			$strDescription .= "\n".GetMessage("COMM_EMAIL")." ".$_POST["comment_email"];

		$DB->StartTransaction();
		$sTransStarted = True;

		$arFields = Array(
			"TITLE" => TruncateText($strDescription, 40),
			"FORUM_ID" => $INQUERY_FORUM_ID,
			"USER_START_NAME" => $_POST["comment_author"],
			"LAST_POSTER_NAME" => $_POST["comment_author"]
		);

		$TID1 = CForumTopic::Add($arFields);
		if (IntVal($TID1)<=0)
			$strErrorMessage .= GetMessage("ADDMESS_ERROR_ADD_TOPIC").". \n";
	}

	if (strlen($strErrorMessage)<=0)
	{
		$AUTHOR_IP = ForumGetRealIP();
		$AUTHOR_IP_tmp = $AUTHOR_IP;
		$AUTHOR_REAL_IP = $_SERVER['REMOTE_ADDR'];
		$AUTHOR_IP = @gethostbyaddr($AUTHOR_IP);
		if ($AUTHOR_IP_tmp==$AUTHOR_REAL_IP)
			$AUTHOR_REAL_IP = $AUTHOR_IP;
		else
			$AUTHOR_REAL_IP = @gethostbyaddr($AUTHOR_REAL_IP);

		$arFields = Array(
			"POST_MESSAGE"	=> $strDescription,
			"AUTHOR_NAME" => $_POST["comment_author"],
			"AUTHOR_EMAIL" => $_POST["comment_email"],
			"FORUM_ID" => $INQUERY_FORUM_ID,
			"TOPIC_ID" => $TID1,
			"AUTHOR_IP" => ($AUTHOR_IP!==False) ? $AUTHOR_IP : "<no address>",
			"AUTHOR_REAL_IP" => ($AUTHOR_REAL_IP!==False) ? $AUTHOR_REAL_IP : "<no address>",
			"NEW_TOPIC" => "Y",
			"GUEST_ID" => $_SESSION["SESS_GUEST_ID"]
		);

		$MID1 = CForumMessage::Add($arFields);
		if (IntVal($MID1)<=0)
		{
			$strErrorMessage .= GetMessage("ADDMESS_ERROR_ADD_MESSAGE").". \n";
			CForumTopic::Delete($TID1);
			$TID1 = 0;
		}
	}

	if (strlen($strErrorMessage)<=0)
	{
		$DB->Commit();
		$strOKMessage .= GetMessage("COMM_COMMENT_OK").". ";
	}
	else
	{
		if ($sTransStarted)
			$DB->Rollback();
	}
}
?>

<br><a name="cm">
<table border="0" cellspacing="0" cellpadding="3">
	<form action="<?= $APPLICATION->GetCurPage()."#cm" ?>" method="POST">
	<?if (strlen($strErrorMessage)>0 || strlen($strOKMessage)>0):?>
		<tr>
			<td align="center" colspan="2" valign="top">
				<p><font class="tablebodytext"><b><?
				echo ShowError($strErrorMessage);
				echo ShowNote($strOKMessage);
				?></b></font></p>
			</td>
		</tr>
	<?endif;?>
	<tr> 
		<td align="left">
			<font class="text"><?= GetMessage("TFC_NAME") ?>:</font>
		</td>
		<td>
			<input name="comment_author" type="text" value="<?= htmlspecialchars($comment_author) ?>" style="WIDTH: 260px;" size="30">
		</td>
	</tr>
	<tr> 
		<td align="left">
			<font class="text">E-Mail:</font>
		</td>
		<td>
			<input type="text" style="WIDTH: 260px" size="30" name="comment_email" value="<?= htmlspecialchars($comment_email) ?>">
		</td>
	</tr>
	<tr>
		<td align="left">
			<font class="text"><?= GetMessage("TFC_PHONE") ?>:</font>
		</td>
		<td>
			<input type="text" style="WIDTH: 260px" size="30" name="comment_phone" value="<?= htmlspecialchars($comment_phone) ?>">
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			<font class="text"><?= GetMessage("TFC_COMMENT") ?>:</font>
		</td>
		<td>
			<textarea name="comment_body" style="WIDTH: 260px" cols="30" rows="8"><?= htmlspecialchars($comment_body) ?></textarea>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="hidden" name="comment_send" value="Y">
			<input type="submit" value="<?=GetMessage("COMM_SEND")?>">
		</td>
	</tr>
</form>
</table>

<?
//*******************************************************
endif;
?>