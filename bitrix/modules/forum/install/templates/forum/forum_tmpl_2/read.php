<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

// Let's init $FID (forum id), $TID (topic id) and $MID (message id)
// with actual and coordinated values
$FID = IntVal($_REQUEST["FID"]);
$TID = IntVal($_REQUEST["TID"]);
$MID = IntVal($_REQUEST["MID"]);

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
if ($MID>0)
{
	$arMessage = CForumMessage::GetByID($MID);
	if ($arMessage)
	{
		$TID = IntVal($arMessage["TOPIC_ID"]);
		$FID = IntVal($arMessage["FORUM_ID"]);
	}
	else
	{
		$MID = 0;
	}
}

$arTopic = CForumTopic::GetByID($TID);

ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "READ");
if (!$arTopic)
{
	LocalRedirect("list.php?FID=".$FID);
	die();
}

$FID = IntVal($arTopic["FORUM_ID"]);
$arForum = CForumNew::GetByID($FID);
if (!$arForum)
{
	LocalRedirect("index.php");
	die();
}
// Now $FID and $TID (and $MID if needed) have actual and coordinated values

// Let's check if current user can can view this topic
if (!CForumTopic::CanUserViewTopic($TID, $USER->GetUserGroupArray()))
	$APPLICATION->AuthForm("Для просмотра темы введите ваши логин и пароль");

// Let's init read labels
CForumNew::InitReadLabels($FID, $USER->GetUserGroupArray());
CForumTopic::SetReadLabels($TID, $USER->GetUserGroupArray());

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Initializing Variables: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";

// ACTIONS: reply, open/close topic, moderate, etc.
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
$strErrorMessage = "";
$strOKMessage = "";
$bVarsFromForm = false;
if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["MESSAGE_TYPE"]=="REPLY")
{
	$arFieldsG = array(
		"POST_MESSAGE" => $_POST["POST_MESSAGE"],
		"AUTHOR_NAME" => $_POST["AUTHOR_NAME"],
		"AUTHOR_EMAIL" => $_POST["AUTHOR_EMAIL"],
		"USE_SMILES" => $_POST["USE_SMILES"],
		"ATTACH_IMG" => $_FILES["ATTACH_IMG"]
		);
	$MID = ForumAddMessage("REPLY", $FID, $TID, 0, $arFieldsG, $strErrorMessage, $strOKMessage);
	$MID = IntVal($MID);
	if ($MID>0)
	{
//		LocalRedirect("read.php?FID=".$FID."&TID=".$TID."&MID=".$MID."#message".$MID);
	}
	else
		$bVarsFromForm = true;
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && CModule::IncludeModule("support") && $_GET["ACTION"]=="FORUM_MESSAGE2SUPPORT")
{
	$SuID = ForumMoveMessage2Support($MID, $strErrorMessage, $strOKMessage);
	if (IntVal($SuID)>0)
	{
		LocalRedirect("/bitrix/admin/ticket_list.php?lang=".LANGUAGE_ID."&strNote=".urlencode("Сообщение форума было успешно перенесено в техподдержку в качестве обращения."));
	}
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && ($_GET["ACTION"]=="FORUM_SUBSCRIBE" || $_GET["ACTION"]=="TOPIC_SUBSCRIBE"))
{
	if (ForumSubscribeNewMessages($FID, (($_GET["ACTION"]=="FORUM_SUBSCRIBE")?0:$TID), $strErrorMessage, $strOKMessage))
		LocalRedirect("subscr_list.php?FID=".$FID."&TID=".$TID);
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="HIDE")
{
	ForumModerateMessage($MID, "HIDE", $strErrorMessage, $strOKMessage);
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="SHOW")
{
	ForumModerateMessage($MID, "SHOW", $strErrorMessage, $strOKMessage);
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="SET_ORDINARY")
{
	if (ForumTopOrdinaryTopic($TID, "ORDINARY", $strErrorMessage, $strOKMessage))
		$arTopic["SORT"] = "150";
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="SET_TOP")
{
	if (ForumTopOrdinaryTopic($TID, "TOP", $strErrorMessage, $strOKMessage))
		$arTopic["SORT"] = "100";
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="DEL_TOPIC" && $TID>0)
{
	if (ForumDeleteTopic($TID, $strErrorMessage, $strOKMessage))
		LocalRedirect("list.php?FID=".$FID);
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="STATE_Y")
{
	if (ForumOpenCloseTopic($TID, "OPEN", $strErrorMessage, $strOKMessage))
		$arTopic["STATE"] = "Y";
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="STATE_N")
{
	if (ForumOpenCloseTopic($TID, "CLOSE", $strErrorMessage, $strOKMessage))
		$arTopic["STATE"] = "N";
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="DEL")
{
	if (ForumDeleteMessage($MID, $strErrorMessage, $strOKMessage))
	{
		$arTopic = CForumTopic::GetByID($TID);
		if (!$arTopic)
		{
			LocalRedirect("list.php?FID=".$FID);
		}
	}
}
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Actions: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";
// End of ACTIONS

$APPLICATION->AddChainItem($arForum["NAME"], "list.php?FID=".$FID);
$APPLICATION->SetTitle("Форум &laquo;".$arForum["NAME"]."&raquo;");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");


$arMenuParams = compact("arTopic", "FID", "TID");
$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php", $arMenuParams);
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<?
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();

$parser = new textParser(LANGUAGE_ID);

$bCanUserDeleteMessages = CForumTopic::CanUserDeleteTopicMessage($TID, $USER->GetUserGroupArray(), $USER->GetID());

$arAllow = array(
	"HTML" => $arForum["ALLOW_HTML"],
	"ANCHOR" => $arForum["ALLOW_ANCHOR"],
	"BIU" => $arForum["ALLOW_BIU"],
	"IMG" => $arForum["ALLOW_IMG"],
	"LIST" => $arForum["ALLOW_LIST"],
	"QUOTE" => $arForum["ALLOW_QUOTE"],
	"CODE" => $arForum["ALLOW_CODE"],
	"FONT" => $arForum["ALLOW_FONT"],
	"SMILES" => $arForum["ALLOW_SMILES"],
	"UPLOAD" => $arForum["ALLOW_UPLOAD"],
	"NL2BR" => $arForum["ALLOW_NL2BR"]
	);

$iLAST_TOPIC_MESSAGE = 0;
$db_res = CForumMessage::GetList(array("ID"=>"DESC"), array("TOPIC_ID"=>$TID), false, 1);
if ($ar_res = $db_res->Fetch()) $iLAST_TOPIC_MESSAGE = IntVal($ar_res["ID"]);

$arFilter = array("TOPIC_ID" => $TID);
if (ForumCurrUserPermissions($FID)<"Q") $arFilter["APPROVED"] = "Y";
$db_Message = CForumMessage::GetListEx(array("ID"=>"ASC"), $arFilter);

if ($MID>0)
	$db_Message->NavStart($GLOBALS["FORUM_MESSAGES_PER_PAGE"], true, CForumMessage::GetMessagePage($MID, $GLOBALS["FORUM_MESSAGES_PER_PAGE"], $USER->GetUserGroupArray()));
else
	$db_Message->NavStart($GLOBALS["FORUM_MESSAGES_PER_PAGE"]);
?>

<table width="100%" border="0">
	<tr>
		<td align="left">
			<?
			//Otherwise we can not move through the pages...
			unset($_GET["MID"]);
			unset($HTTP_GET_VARS["MID"]);
			unset($_GET["ACTION"]);
			unset($HTTP_GET_VARS["ACTION"]);
			?>
			<?echo $db_Message->NavPrint("Сообщения")?>
		</td>
		<td align="right">
			<?
			if (CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())):
				?>
				<a href="new_topic.php?FID=<?echo $FID;?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/t_new.gif" width="93" height="19" alt="Добавить новую тему" border="0"></a>
				<?
			endif;
			?>
		</td>
	</tr>
</table>

<table width="99%" border="0" cellspacing="1" cellpadding="0" align="center" class="forumborder">
<tr><td>

<table width="100%" border="0" cellspacing="0" cellpadding="3" class="forumborder">
  <tr>
	<td> </td>
	<td width="100%" class="forumtitletext">
		<b>Тема &laquo;<?echo htmlspecialcharsEx($arTopic["TITLE"]);?>&raquo;</b><?
		if (strlen($arTopic["DESCRIPTION"])>0)
		{
			echo ", ".htmlspecialcharsEx($arTopic["DESCRIPTION"]);
		}
		?>
	</td>
  </tr>
</table>

</td></tr>
<tr><td>

<table width="100%" border="0" cellspacing="1" cellpadding="4">
  <tr class="forumhead">
	<td width="100%" nowrap colspan="2">

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
				<td><font class="forumheadtext">
					<b>&laquo;&nbsp;<?
					list($iPREV_TOPIC, $iNEXT_TOPIC) = CForumTopic::GetNeighboringTopics($TID, $USER->GetUserGroupArray());
					if (IntVal($iPREV_TOPIC)>0):?><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $iPREV_TOPIC ?>"><?endif;?>Предыдущая тема<?if (IntVal($iPREV_TOPIC)>0):?></a><?endif;?> | <?
					if (IntVal($iNEXT_TOPIC)>0):?><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $iNEXT_TOPIC; ?>"><?endif;?>Следующая тема<?if (IntVal($iPREV_TOPIC)>0):?></a><?endif;?>&nbsp;&raquo;</b></font>
				</td>
				<td align="right"><font class="forumheadtext">
					<?if ($arTopic["STATE"]=="Y"):?>
						<b><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $TID?>&ACTION=TOPIC_SUBSCRIBE">Подписаться</a>
					<?endif;?>
					</b></font>
				</td>
		  </tr>
		</table>

	</td>
  </tr>

<?
while ($ar_Message = $db_Message->Fetch()):
  ?>
  <tr valign="top" class="forumbody">
	<td><font class="forumbodytext">
		<a name="message<?echo $ar_Message["ID"];?>"></a>
		<?if (ForumCurrUserPermissions($FID)>="I" && $arTopic["STATE"]=="Y") echo "<a href=\"javascript:reply2author('".str_replace("'", "\'", htmlspecialchars($ar_Message["AUTHOR_NAME"]))."!')\">";?>
		<?echo htmlspecialcharsEx($ar_Message["AUTHOR_NAME"]);?>
		<?if (ForumCurrUserPermissions($FID)>="I" && $arTopic["STATE"]=="Y") echo "</a>";?>
		<?if (strlen($ar_Message["DESCRIPTION"])>0) echo "<br>".htmlspecialcharsEx($ar_Message["DESCRIPTION"]);?>
		<?
		if (IntVal($ar_Message["AUTHOR_ID"])>0)
		{
			$arMessageUserGroups = CUser::GetUserGroup($ar_Message["AUTHOR_ID"]);
			$arMessageUserGroups[] = 2;
			$strMessageUserPerms = CForumNew::GetUserPermission($FID, $arMessageUserGroups);
			if ($strMessageUserPerms=="Q") echo "<br><b>Модератор</b>";
			elseif ($strMessageUserPerms=="U") echo "<br><b>Редактор</b>";
			elseif ($strMessageUserPerms=="Y") echo "<br><b>Администратор</b>";
			elseif (IntVal($ar_Message["RANK_ID"])>0)
			{
				$arRank = CForumRank::GetLangByID($ar_Message["RANK_ID"], LANGUAGE_ID);
				echo "<br>".$arRank["NAME"];
			}
		}
		else
		{
			echo "<br><i>Гость</i>";
		}
		?>
		<br>
		<?if (strlen($ar_Message["AVATAR"])>0):?>
			<center><br>
			<?echo CFile::ShowImage($ar_Message["AVATAR"], 90, 90, "border=0", "", true)?>
			</center>
		<?else:?>
			<br>
		<?endif;?>
		<br><small>
		<?if (strlen($ar_Message["EMAIL"])>0):?>
			<nobr><a href="send_message.php?TYPE=MAIL&UID=<?echo $ar_Message["AUTHOR_ID"]; ?>" target="_blank" title="Отправить письмо на E-Mail автора сообщения">Написать (E-Mail)</a></nobr>
		<?endif;?>
		<?if ((strLen($ar_Message["PERSONAL_ICQ"])>0) && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y")):?>
			<nobr><a href="send_message.php?TYPE=ICQ&UID=<?echo $ar_Message["AUTHOR_ID"]; ?>" target="_blank" title="Отправить письмо на номер ICQ автора сообщения">Написать (ICQ)</a></nobr>
		<?endif;?>
		<?if (IntVal($ar_Message["AUTHOR_ID"])>0):?>
			<a href="view_profile.php?UID=<?echo $ar_Message["AUTHOR_ID"] ?>" target="_blank" title="Профиль автора сообщения">Профиль</a>
		<?endif;?>
		</small><br>
		<?if (IntVal($ar_Message["NUM_POSTS"])>0):?>
			<small><nobr>всего сообщений: <?echo $ar_Message["NUM_POSTS"];?></nobr><br></small>
		<?endif;?>
		<?if (strlen($ar_Message["DATE_REG"])>0):?>
			<small>дата регистрации: <?echo $ar_Message["DATE_REG"];?><br></small>
		<?endif;?>
		<?if (ForumCurrUserPermissions($FID)>="Q" && CModule::IncludeModule("statistic") && IntVal($ar_Message["GUEST_ID"])>0 && $APPLICATION->GetGroupRight("statistic")!="D"):?>
			<small><nobr>ID посетителя: <a href="/bitrix/admin/guest_list.php?lang=<?=LANGUAGE_ID?>&find_id=<?=$ar_Message["GUEST_ID"]?>&set_filter=Y"><?echo $ar_Message["GUEST_ID"];?></a></nobr><br></small>
		<?endif;?>
		<?if (ForumCurrUserPermissions($FID)>="Q"):?>
			<small><nobr>IP: 
			<?
			$bIP = False;
			if (ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $ar_Message["AUTHOR_IP"])) $bIP = True;
			if ($bIP) echo GetWhoisLink($ar_Message["AUTHOR_IP"]);
			else echo $ar_Message["AUTHOR_IP"];
			?>
			</nobr><br>
			<nobr>IP (реальный): 
			<?
			$bIP = False;
			if (ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $ar_Message["AUTHOR_REAL_IP"])) $bIP = True;
			if ($bIP) echo GetWhoisLink($ar_Message["AUTHOR_REAL_IP"]);
			else echo $ar_Message["AUTHOR_REAL_IP"];
			?>
			</nobr><br></small>
		<?endif;?>
		</font>
	</td>
	<td width="100%">
		<font class="forumbodytext">
		<?
		$arAllow["SMILES"] = $arForum["ALLOW_SMILES"];
		if ($ar_Message["USE_SMILES"]!="Y") $arAllow["SMILES"] = "N";
		echo $parser->convert($ar_Message["POST_MESSAGE"], $arAllow);

		if (IntVal($ar_Message["ATTACH_IMG"])>0)
		{
			echo "<br>";
			if ($arForum["ALLOW_UPLOAD"]=="Y" || $arForum["ALLOW_UPLOAD"]=="F" || $arForum["ALLOW_UPLOAD"]=="A")
			{
				echo CFile::ShowFile($ar_Message["ATTACH_IMG"], 0, 400, 400, false, "border=0", false);
			}
		}

		if (strlen($ar_Message["SIGNATURE"])>0)
		{
			echo "<br><br>";
			$arAllow["SMILES"] = "N";
			echo $parser->convert($ar_Message["SIGNATURE"], $arAllow);
		}
		?>
		</font>
	</td>
  </tr>
  <tr class="forumhead">
	<td><font class="forumbodytext">
		<small><b>Создано</b> <nobr><?echo $ar_Message["POST_DATE"];?></nobr></small></font>
	</td>
	<td nowrap>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td align="left"><font class="forumbodytext">
				<?if ($ar_Message["APPROVED"]=="Y" && ForumCurrUserPermissions($FID)>="Q"):?>
					<a href="read.php?FID=<?echo $FID; ?>&TID=<?echo $TID; ?>&MID=<?echo $ar_Message["ID"] ?>&ACTION=HIDE" title="Скрыть сообщение">Скрыть</a>
				<?elseif (ForumCurrUserPermissions($FID)>="Q"):?>
					<a href="read.php?FID=<?echo $FID; ?>&TID=<?echo $TID; ?>&MID=<?echo $ar_Message["ID"] ?>&ACTION=SHOW" title="Показать сообщение">Показать</a>
				<?endif;?>
				<?if (ForumCurrUserPermissions($FID)>="U"
					|| $iLAST_TOPIC_MESSAGE == IntVal($ar_Message["ID"])
					&& $USER->IsAuthorized()
					&& IntVal($ar_Message["AUTHOR_ID"]) == IntVal($USER->GetParam("USER_ID"))):?>
					&nbsp;|&nbsp;<a href="new_topic.php?FID=<?echo $FID; ?>&TID=<?echo $TID; ?>&MID=<?echo $ar_Message["ID"] ?>&MESSAGE_TYPE=EDIT" title="Редактировать сообщение">Редактировать</a>
				<?endif;?>
				<?if ($bCanUserDeleteMessages):?>
					&nbsp;|&nbsp;<a href="read.php?FID=<?echo $FID; ?>&TID=<?echo $TID; ?>&MID=<?echo $ar_Message["ID"] ?>&ACTION=DEL" title="Удалить сообщение">Удалить</a>
					<?if (IntVal($ar_Message["AUTHOR_ID"])>0):?>
						&nbsp;|&nbsp;<a href="read.php?FID=<?echo $FID; ?>&TID=<?echo $TID; ?>&MID=<?echo $ar_Message["ID"] ?>&ACTION=FORUM_MESSAGE2SUPPORT" title="Перенести в техподдержку">В техподдержку</a>
					<?endif;?>
				<?endif;?>
				</font>
			</td>
			<td align="right"><font class="forumbodytext">
				<?if (ForumCurrUserPermissions($FID)>="I" && $arTopic["STATE"]=="Y"):?>
					<a href="javascript:quoteMessageEx('<?echo htmlspecialcharsEx($ar_Message["AUTHOR_NAME"]) ?>')" title="Для вставки цитаты в форму ответа, выделите ее, и нажмите сюда">Цитировать</a>
					&nbsp;|&nbsp;
				<?endif;?>
				<a href="javascript:scroll(0,0);">Наверх</a></font>
			</td>
		  </tr>
		</table>

	</td>
  </tr>
  <tr class="forumhead" style="height:5px">
	<td colspan="2"><!-- --></td>
  </tr>
  <?
endwhile;
?>

<?
if ($arTopic["STATE"]=="Y")
{
	$arFormParams1 = array("MESSAGE_TYPE" => "REPLY");
	$arFormParams2 = compact("arForum", "FID", "TID", "bVarsFromForm", "strErrorMessage", "strOKMessage");
	$arFormParams = array_merge($arFormParams1, $arFormParams2);

	if ($bVarsFromForm)
	{
		$arFormParams["AUTHOR_NAME"] = $_POST["AUTHOR_NAME"];
		$arFormParams["AUTHOR_EMAIL"] = $_POST["AUTHOR_EMAIL"];
		$arFormParams["POST_MESSAGE"] = $_POST["POST_MESSAGE"];
		$arFormParams["USE_SMILES"] = $_POST["USE_SMILES"];
	}

	$APPLICATION->IncludeFile("forum/forum_tmpl_2/post_form.php", $arFormParams);
}
?>

</table>

</td></tr>
</table>

<table width="100%" border="0">
	<tr>
		<td align="left">
			<?echo $db_Message->NavPrint("Сообщения")?>
		</td>
		<td align="center" width="0%">
		  <?if (ForumCurrUserPermissions($FID)>="Q"):?>
				<font class="forumheadtext"><a href="move.php?FID=<?echo $FID;?>&TID=<?echo $TID;?>">Перенести тему</a></font>
				&nbsp;|&nbsp;
				<font class="forumheadtext"><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $TID;?>&ACTION=<?echo (IntVal($arTopic["SORT"])!=150)?"SET_ORDINARY":"SET_TOP";?>"><?echo (IntVal($arTopic["SORT"])!=150)?"Снять прикрепление":"Прикрепить вверху";?></a></font>
				&nbsp;|&nbsp;
				<font class="forumheadtext"><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $TID;?>&ACTION=<?echo ($arTopic["STATE"]!="Y")?"STATE_Y":"STATE_N";?>"><?echo ($arTopic["STATE"]!="Y")?"Открыть тему":"Закрыть тему";?></a></font>
				<?if (CForumTopic::CanUserDeleteTopic($TID, $USER->GetUserGroupArray(), $USER->GetID())):?>
					&nbsp;|&nbsp;
					<font class="forumheadtext"><a href="read.php?FID=<?echo $FID;?>&TID=<?echo $TID;?>&ACTION=DEL_TOPIC">Удалить тему</a></font>
			  <?endif;?>
		  <?endif;?>
		</td>
		<td align="right">
			<?
			if (CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())):
				?>
				<a href="new_topic.php?FID=<?echo $FID;?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/t_new.gif" width="93" height="19" alt="Добавить новую тему" border="0"></a>
				<?
			endif;
			?>
		</td>
	</tr>
</table>
<?
{
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Making Page: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"])
{
	for ($i = 0; $i < count($arForumDebugInfo); $i++)
		echo $arForumDebugInfo[$i];
}
}

//*******************************************************
else:
	?>
	<font class="text"><b>Модуль форума не установлен</b></font>
	<?
endif;
?>