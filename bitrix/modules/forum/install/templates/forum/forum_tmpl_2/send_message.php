<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

$UID = IntVal($_REQUEST["UID"]);
$bUserFound = False;

ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "SEND_MESSAGE");

$db_userX = CUser::GetByID($UID);
if ($ar_userX = $db_userX->Fetch())
{
	while (list($key, $val) = each($ar_userX))
		${"x_".$key} = htmlspecialchars($val);

	$bUserFound = True;

	$db_res = CForumUser::GetByUSER_ID($UID);
	if ($db_res)
	{
		while (list($key, $val) = each($db_res))
			${"xu_".$key} = htmlspecialchars($val);
	}

	$ShowName = "";
	if ($xu_SHOW_NAME=="Y") $ShowName = $x_NAME." ".$x_LAST_NAME;
	if (strlen($ShowName)<=0) $ShowName = $x_LOGIN;
}

if (($_REQUEST["TYPE"]!="ICQ") && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") != "Y"))
	$TYPE = "MAIL";
else
	$TYPE = "ICQ";

if ($TYPE=="ICQ")
	$strTextType = "ICQ";
else
	$strTextType = "E-Mail";

if ($USER->IsAuthorized())
{
	$db_userY = CUser::GetByID($USER->GetID());
	if ($ar_userY = $db_userY->Fetch())
	{
		while (list($key, $val) = each($ar_userY))
			${"y_".$key} = htmlspecialchars($val);

		$db_res = CForumUser::GetByUSER_ID($y_ID);
		if ($db_res)
		{
			while (list($key, $val) = each($db_res))
				${"yu_".$key} = htmlspecialchars($val);
		}

		$ShowMyName = "";
		if ($yu_SHOW_NAME=="Y") $ShowMyName = $USER->GetFullName();
		if (strlen($ShowMyName)<=0) $ShowMyName = $USER->GetLogin();
	}
}

$strErrorMessage = "";
$strOKMessage = "";

if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["ACTION"]=="SEND" && $bUserFound)
{
	if ($USER->IsAuthorized())
	{
		$NAME = $ShowMyName;
		$EMAIL = ($TYPE=="ICQ") ? $y_PERSONAL_ICQ : $y_EMAIL;
	}
	else
	{
		$NAME = $_POST["NAME"];
		$EMAIL = $_POST["EMAIL"];
	}

	if (strlen($NAME)<=0)
		$strErrorMessage .= "Укажите Ваше имя. \n";

	if (strlen($EMAIL)<=0)
		$strErrorMessage .= "Укажите Ваш ".(($TYPE=="ICQ") ? "номер ICQ" : "E-Mail адрес").". \n";
	elseif ($TYPE!="ICQ" && !check_email($EMAIL))
		$strErrorMessage .= "E-Mail адрес не верен. \n";

	if (strlen($_POST["SUBJECT"])<=0)
		$strErrorMessage .= "Укажите тему сообщения. \n";
	if (strlen($_POST["MESSAGE"])<=0)
		$strErrorMessage .= "Введите текст сообщения. \n";
	if ($TYPE=="ICQ" && strlen($x_PERSONAL_ICQ)<=0)
		$strErrorMessage .= "Не задан номер ICQ адресата. \n";
	if ($TYPE=="MAIL" && strlen($x_EMAIL)<=0)
		$strErrorMessage .= "Не задан E-Mail адрес адресата. \n";

	if (strlen($strErrorMessage)<=0)
	{
		if ($TYPE=="ICQ")
		{
			$body   = "From ".$NAME." (UIN ".$EMAIL.")\n";
			$body  .= "<br>-----<br>\n";
			$body  .= $_POST["SUBJECT"]."\n";
			$body  .= "<br>-----<br>\n";
			$body  .= $_POST["MESSAGE"]."\n";
			$from   = $NAME;
			$headers  = "Content-Type: text/plain; charset=windows-1254\n";
			$headers .= "From: $from\nX-Mailer: System33r";
			@mail($x_PERSONAL_ICQ."@pager.mirabilis.com", $_POST["SUBJECT"], $body, $headers);
			$strOKMessage = "Сообщение отправлено. \n";
		}
		else
		{
			$event = new CEvent;
			$arFields = Array(
				"FROM_NAME" => $NAME,
				"FROM_EMAIL" => $EMAIL,
				"TO_NAME" => $ShowName,
				"TO_EMAIL" => $x_EMAIL,
				"SUBJECT" => $_POST["SUBJECT"],
				"MESSAGE" => $_POST["MESSAGE"],
				"MESSAGE_DATE" => date("d.m.Y H:i:s")
			);
			$event->Send("NEW_FORUM_PRIV", SITE_ID, $arFields);
			$strOKMessage = "Сообщение отправлено. \n";
		}
	}
}

$APPLICATION->AddChainItem($ShowName, "view_profile.php?UID=".$UID);
$APPLICATION->SetTitle($strTextType);
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php");

if (!$bUserFound)
	$strErrorMessage .= "Пользователь с кодом $UID не найден. \n";
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<?
if ($bUserFound):
	?>
	<br>
	<form action="send_message.php" method="POST" name="REPLIER">
	<input type="hidden" name="ACTION" value="SEND">
	<input type="hidden" name="TYPE" value="<?echo $TYPE; ?>">
	<input type="hidden" name="UID" value="<?echo $UID; ?>">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>

	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr>
			<td colspan="2" class="forumhead">
				<font class="forumheadtext">&nbsp;<b><?echo $strTextType; ?> кому</b></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody"><font class="forumheadtext">&nbsp;Имя</font></td>
			<td class="forumbody">
				<font class="forumbodytext"><?echo $ShowName; ?></font>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="forumhead">
				<font class="forumheadtext">&nbsp;<b><?echo $strTextType; ?> от</b></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody"><font class="forumheadtext">&nbsp;Имя</font></td>
			<td class="forumbody"><font class="forumbodytext">
				<?if ($USER->IsAuthorized()):?>
					<?echo $ShowMyName; ?>
				<?else:?>
					<input type="text" name="NAME" value="<?echo htmlspecialchars($NAME); ?>" size="25">
				<?endif;?></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext"><nobr>&nbsp;<?
				if ($TYPE=="ICQ") echo "Номер ICQ";
				else echo "E-Mail адрес";
				?> </nobr></font>
			</td>
			<td class="forumbody"><font class="forumbodytext">
				<?
				if ($USER->IsAuthorized() && ($TYPE=="ICQ" && strlen($y_PERSONAL_ICQ)>0 || $TYPE=="MAIL" && strlen($y_EMAIL)>0)):
					if ($TYPE=="ICQ") echo $y_PERSONAL_ICQ;
					else echo $y_EMAIL;
				else:
					?><input type="text" name="EMAIL" value="<?echo htmlspecialchars($EMAIL); ?>" size="25"><?
				endif;
				?></font>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="forumhead">
				<font class="forumheadtext">&nbsp;<b><?echo GetMessage("MESSAGE_CONTENT");?></b></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody" valign="top">
				<font class="forumheadtext">&nbsp;Тема</font>
			</td>
			<td class="forumbody">
				<input type="text" name="SUBJECT" value="<?echo htmlspecialchars($SUBJECT); ?>" size="50" maxlength="50">
			</td>
		</tr>
		<tr>
			<td class="forumbody" valign="top">
				<font class="forumheadtext">&nbsp;Сообщение</font>
			</td>
			<td class="forumbody">
				<textarea cols="45" rows="12" wrap="soft" name="MESSAGE"><?echo htmlspecialchars($MESSAGE); ?></textarea>
			</td>
		</tr>
		<tr>
			<td  class="forumbody" align="center" colspan="2">
				<input type="submit" value="Отправить <?echo $strTextType; ?>">
			</td>
		</tr>
	</table>

	</td></tr>
	</table>
	</form>
	<?
endif;

//*******************************************************
else:
	?>
	<font class="text"><b>Модуль форума не установлен</b></font>
	<?
endif;
?>