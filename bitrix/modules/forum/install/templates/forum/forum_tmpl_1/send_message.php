<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

	$UID = IntVal($_REQUEST["UID"]);
	$bUserFound = False;
	
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
		if ($xu_SHOW_NAME == "Y")
			$ShowName = $x_NAME." ".$x_LAST_NAME;
		if (strlen($ShowName) <= 0)
			$ShowName = $x_LOGIN;
	}
	
	
	if (($_REQUEST["TYPE"]!="ICQ") || (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") != "Y"))
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
			if ($yu_SHOW_NAME == "Y")
				$ShowMyName = htmlspecialchars($USER->GetFullName());
			if (strlen($ShowMyName) <= 0)
				$ShowMyName = htmlspecialchars($USER->GetLogin());
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
			$strErrorMessage .= GetMessage("FSM_NO_NAME").". \n";
	
		if (strlen($EMAIL)<=0)
			$strErrorMessage .= GetMessage("FSM_NO_EMAIL1")." ".(($TYPE=="ICQ") ? GetMessage("FSM_NO_EMAIL2") : GetMessage("FSM_NO_EMAIL3")).". \n";
		elseif ($TYPE!="ICQ" && !check_email($EMAIL))
			$strErrorMessage .= GetMessage("FSM_BAD_EMAIL").". \n";
	
		if (strlen($_POST["SUBJECT"])<=0)
			$strErrorMessage .= GetMessage("FSM_NO_SUBJECT").". \n";
		if (strlen($_POST["MESSAGE"])<=0)
			$strErrorMessage .= GetMessage("FSM_NO_MESSAGE").". \n";
		if ($TYPE=="ICQ" && strlen($x_PERSONAL_ICQ)<=0)
			$strErrorMessage .= GetMessage("FSM_NO_ICQ_NUM").". \n";
		if ($TYPE=="MAIL" && strlen($x_EMAIL)<=0)
			$strErrorMessage .= GetMessage("FSM_NO_EMAIL_D").". \n";
	
		if (strlen($strErrorMessage)<=0)
		{
			if ($TYPE=="ICQ" && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y"))
			{
				$body   = "From ".$NAME." (UIN ".$EMAIL.")\n";
				if ($USER->IsAuthorized())
				
					$body  .= GetMessage("FSM_MESS_AUTH")."\n";
				else
					$body  .= GetMessage("FSM_MESS_NOAUTH")."\n";
				$body  .= "<br>-----<br>\n";
				$body  .= $_POST["SUBJECT"]."\n";
				$body  .= "<br>-----<br>\n";
				$body  .= $_POST["MESSAGE"]."\n";
				$from   = $NAME;
				$headers  = "Content-Type: text/plain; charset=windows-1254\n";
				$headers .= "From: $from\nX-Mailer: System33r";
//				@mail($x_PERSONAL_ICQ."@pager.mirabilis.com", $_POST["SUBJECT"], $body, $headers);
				$strOKMessage = GetMessage("FSM_MESS_SEND").". \n";
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
				if ($USER->IsAuthorized())
					$arFields["AUTH"] = GetMessage("FSM_MESS_AUTH");
				else
					$arFields["AUTH"] = GetMessage("FSM_MESS_NOAUTH");
				$event->Send("NEW_FORUM_PRIV", SITE_ID, $arFields);
				$strOKMessage = GetMessage("FSM_MESS_SEND").". \n";
			}
		}
	}
	
	$APPLICATION->AddChainItem($ShowName, "view_profile.php?UID=".$UID, false);
	$APPLICATION->SetTitle($strTextType);
	$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
	
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
	
	if (!$bUserFound)
		$strErrorMessage .= str_replace("#UID#", $UID, GetMessage("FSM_NO_DUSER")).". \n";
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
	
		<table border="0" width="100%" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
	
		<table border="0" cellpadding="3" cellspacing="1" width="100%">
			<tr>
				<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?echo $strTextType; ?> <?echo GetMessage("FSM_TO")?></b></font></td>
			</tr>
			<tr>
				<td class="forumbody"><font class="forumfieldtext"><?echo GetMessage("FSM_NAME")?></font></td>
				<td class="forumbody"><font class="forumbodytext"><a href="view_profile.php?UID=<?=$UID?>"><?echo $ShowName; ?></a></font></td>
			</tr>
			<tr>
				<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?echo $strTextType; ?> <?echo GetMessage("FSM_FROM")?></b></font></td>
			</tr>
			<tr>
				<td class="forumbody"><font class="forumfieldtext"><?echo GetMessage("FSM_NAME")?></font></td>
				<td class="forumbody"><font class="forumbodytext"><?
				if ($USER->IsAuthorized()):?>
						<?echo $ShowMyName; ?>
				<?else:
					?><input type="text" name="NAME" value="<?= htmlspecialchars($_REQUEST["NAME"]) ?>" size="35" class="inputtext"><?
				endif;?></font></td>
			</tr>
			<tr>
				<td class="forumbody"><font class="forumfieldtext"><nobr>&nbsp;<?
					if ($TYPE=="ICQ") echo GetMessage("FSM_ICQ");
					else echo GetMessage("FSM_EMAIL");
				?></nobr></font></td>
				<td class="forumbody"><font class="forumbodytext"><?
					if ($USER->IsAuthorized() && ($TYPE=="ICQ" && strlen($y_PERSONAL_ICQ)>0 || $TYPE=="MAIL" && strlen($y_EMAIL)>0)):
						if ($TYPE=="ICQ") echo $y_PERSONAL_ICQ;
						else echo $y_EMAIL;
					else:
						?><input type="text" name="EMAIL" value="<?echo htmlspecialchars($_REQUEST["EMAIL"]); ?>" size="35" class="inputtext"><?
					endif;
				?></font>			
			</tr>
			<tr>
				<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?echo GetMessage("FSM_MESSAGE")?></b></font></td>
			</tr>
			<tr>
				<td class="forumbody" valign="top"><font class="forumfieldtext"><?echo GetMessage("FSM_TOPIC")?></font></td>
				<td class="forumbody"><input type="text" name="SUBJECT" value="<?= htmlspecialchars($_REQUEST["SUBJECT"]); ?>" size="47" maxlength="50" class="inputtext"></td>
			</tr>
			<tr>
				<td class="forumbody" valign="top"><font class="forumfieldtext"><?echo GetMessage("FSM_TEXT")?></font></td>
				<td class="forumbody"><textarea cols="47" rows="12" wrap="soft" name="MESSAGE" class="inputtextarea"><?= htmlspecialchars($_REQUEST["MESSAGE"]); ?></textarea></td>
			</tr>
			<tr>
				<td  class="forumbody" align="center" colspan="2"><input type="submit" value="<?echo GetMessage("FSM_SEND")?> <?echo $strTextType; ?>" class="inputbutton"></td>
			</tr>
		</table>
	
		</td></tr>
		</table>
		</form>
		<?
	endif;
	?>
	
	<br><br><br>
	<?
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
	
	//*******************************************************
else:
	?>
	<font class="text"><b><?echo GetMessage("FSM_NO_MODULE")?></b></font>
	<?
endif;
?>