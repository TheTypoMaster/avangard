<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

// Let's init $FID (forum id) with actual and coordinated value
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();

$FID = IntVal($_REQUEST["FID"]);
$arForum = CForumNew::GetByID($FID);
ForumSetLastVisit();

if (!$arForum)
{
	LocalRedirect("index.php");
	die();
}

define("FORUM_MODULE_PAGE", "LIST");
// Let's check if current user can can view this forum
if (!CForumNew::CanUserViewForum($FID, $USER->GetUserGroupArray()))
	$APPLICATION->AuthForm("Для входа в закрытый форум введите ваши логин и пароль");

// Let's init read labels
CForumNew::InitReadLabels($FID, $USER->GetUserGroupArray());

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Initializing Variables: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";


// ACTIONS: subscribe
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
$strErrorMessage = "";
$strOKMessage = "";
if ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="FORUM_SUBSCRIBE" && IntVal($FID)>0)
{
	if (ForumSubscribeNewMessages($FID, 0, $strErrorMessage, $strOKMessage))
		LocalRedirect("subscr_list.php?FID=".$FID);
}
elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="SET_BE_READ" && IntVal($FID)>0)
{
	ForumSetAllMessagesReaded($FID);
}
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Actions: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";
// End of ACTIONS

$APPLICATION->AddChainItem($arForum["NAME"], "list.php?FID=".$FID);
$APPLICATION->SetTitle("Форум &laquo;".$arForum["NAME"]."&raquo;");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php", array("FID"=>$FID));
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<?
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();

if (strlen($_REQUEST["ORDER_BY"])<=0)
	$ORDER_BY = $arForum["ORDER_BY"];
else
	$ORDER_BY = $_REQUEST["ORDER_BY"];

if (strlen($_REQUEST["ORDER_DIRECTION"])<=0)
	$ORDER_DIRECTION = $arForum["ORDER_DIRECTION"];
else
	$ORDER_DIRECTION = $_REQUEST["ORDER_DIRECTION"];

if ($ORDER_BY=="T")
	$strOrderBy = "TITLE";
elseif ($ORDER_BY=="N")
	$strOrderBy = "POSTS";
elseif ($ORDER_BY=="A")
	$strOrderBy = "USER_START_NAME";
elseif ($ORDER_BY=="V")
	$strOrderBy = "VIEWS";
elseif ($ORDER_BY=="D")
	$strOrderBy = "START_DATE";
else
	$strOrderBy = "LAST_POST_DATE";

if (strtoupper($ORDER_DIRECTION) == "ASC")
	$strOrderDir = "ASC";
else
	$strOrderDir = "DESC";

$arOrder = array("SORT"=>"ASC", $strOrderBy=>$strOrderDir);

$arFilter = array("FORUM_ID"=>$FID);
if (ForumCurrUserPermissions($FID)<"Q")
	$arFilter["APPROVED"] = "Y";
$db_Topic = CForumTopic::GetListEx($arOrder, $arFilter);

$db_Topic->NavStart($GLOBALS["FORUM_TOPICS_PER_PAGE"]);
?>
<table width="100%" border="0">
	<tr>
		<td align="left">
			<?echo $db_Topic->NavPrint("Темы")?>
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

<table width="99%" align="center" border="0" cellspacing="1" cellpadding="0" class="forumborder">
<form action="" method="get">
  <tr>
	<td>
	  <table width="100%" border="0" cellspacing="0" cellpadding="3" class="forumborder">
		<tr>
			<td> </td>
			<td width="100%" class="forumtitletext"><b><?echo $arForum["NAME"];?></b></td>
		</tr>
	  </table>
	</td>
  </tr>
  <tr>
	<td>
	  <table width="100%" border="0" cellspacing="1" cellpadding="4">
		<tr class="forumhead">
			<td align="center" nowrap class="forumheadtext">

			</td>
			<td align="center" nowrap class="forumheadtext">

			</td>
			<td width="45%" nowrap class="forumheadtext" align="center">
				Заголовок темы<br>
				<?echo SortingEx("T", "", "ORDER_BY", "ORDER_DIRECTION")?>
			</td>
			<td width="14%" align="center" nowrap class="forumheadtext">
				Автор темы<br>
				<?echo SortingEx("A", "", "ORDER_BY", "ORDER_DIRECTION")?>
			</td>
			<td width="7%" align="center" nowrap class="forumheadtext">
				Ответов<br>
				<?echo SortingEx("N", "", "ORDER_BY", "ORDER_DIRECTION")?>
			</td>
			<td width="7%" align="center" nowrap class="forumheadtext">
				Прочитано<br>
				<?echo SortingEx("V", "", "ORDER_BY", "ORDER_DIRECTION")?>
			</td>
			<td width="27%" nowrap align="center" class="forumheadtext">
				Последнее сообщение<br>
				<?echo SortingEx("P", "", "ORDER_BY", "ORDER_DIRECTION")?>
			</td>
		</tr>
	<?
	while ($ar_Topic = $db_Topic->Fetch()):
		list($FirstUnreadedTopicID, $FirstUnreadedMessageID) = CForumMessage::GetFirstUnreadEx($ar_Topic["FORUM_ID"], $ar_Topic["ID"], $USER->GetUserGroupArray());
		?>
		<tr class="forumbody">
			<td align="center" class="forumbodytext">
				<?
				$strClosed = "";
				if ($ar_Topic["STATE"]!="Y") $strClosed = "closed_";
				if ($ar_Topic["APPROVED"]!="Y" && ForumCurrUserPermissions($ar_Topic["FORUM_ID"])>="Q")
				{
					?><font color="#FF0000"><b>NA</b></font><?
				}
				elseif ($FirstUnreadedMessageID>0)
				{
					?><a href="read.php?FID=<?echo $ar_Topic["FORUM_ID"];?>&TID=<?echo $ar_Topic["ID"]?>&MID=<?echo $FirstUnreadedMessageID?>#message<?echo $FirstUnreadedMessageID?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/f_<?echo $strClosed; ?>norm.gif" width="18" height="12" alt="Есть новые сообщения!" border="0"></a><?
				}
				else
				{
					?><img src="/bitrix/templates/.default/forum/forum_tmpl_2/images/f_<?echo $strClosed; ?>norm_no.gif" width="18" height="12" alt="Нет новых сообщений" border="0"><?
				}
				?>
			</td>
			<td align="center" class="forumbodytext">
				<?if (strlen($ar_Topic["IMAGE"])>0):?>
					<img src="/bitrix/images/forum/icon/<?echo $ar_Topic["IMAGE"];?>" alt="<?echo $ar_Topic["IMAGE_DESCR"];?>" border="0" width="15" height="15">
				<?endif;?>
			</td>
			<td class="forumbodytext">
				<?if (IntVal($ar_Topic["SORT"])!=150) echo "<b>Закрепленная:</b> ";?>
				<a href="read.php?FID=<?echo $ar_Topic["FORUM_ID"];?>&TID=<?echo $ar_Topic["ID"]?>" title="Тема начата <?echo $ar_Topic["START_DATE"]?>"><?echo htmlspecialcharsEx($ar_Topic["TITLE"])?></a>
				<?
				$numMessages = $ar_Topic["POSTS"]+1;
				if (ForumCurrUserPermissions($FID)>="Q")
				{
					$numMessages = CForumMessage::GetList(array(), array("TOPIC_ID"=>$ar_Topic["ID"]), true);
				}
				echo ForumShowTopicPages($numMessages, "read.php?FID=".$ar_Topic["FORUM_ID"]."&TID=".$ar_Topic["ID"]."", "PAGEN_1");
				?>
				<br>
				<?echo htmlspecialcharsEx($ar_Topic["DESCRIPTION"])?>
			</td>
			<td align="center" class="forumbodytext">
				<?echo htmlspecialcharsEx($ar_Topic["USER_START_NAME"])?>
			</td>
			<td align="center" class="forumbodytext">
				<?echo $ar_Topic["POSTS"]?>
			</td>
			<td align="center" class="forumbodytext">
				<?echo $ar_Topic["VIEWS"]?>
			</td>
			<td class="forumbodytext">
				<?echo $ar_Topic["LAST_POST_DATE"]?><br>
				<a href="read.php?FID=<?echo $ar_Topic["FORUM_ID"];?>&TID=<?echo $ar_Topic["ID"]?>&MID=<?echo $ar_Topic["LAST_MESSAGE_ID"]?>#message<?echo $ar_Topic["LAST_MESSAGE_ID"]?>">Автор:</a>
				<b><a href="read.php?FID=<?echo $ar_Topic["FORUM_ID"];?>&TID=<?echo $ar_Topic["ID"]?>&MID=<?echo $ar_Topic["LAST_MESSAGE_ID"]?>#message<?echo $ar_Topic["LAST_MESSAGE_ID"]?>"><?echo htmlspecialcharsEx($ar_Topic["LAST_POSTER_NAME"])?></a></b>
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

<table width="100%" border="0">
	<tr>
		<td align="left">
			<?echo $db_Topic->NavPrint("Темы")?>
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

<br>
<center><font class="text">
<a href="list.php?FID=<?echo $FID; ?>&ACTION=SET_BE_READ" title="Пометить все темы этого форума как прочитанные">[Пометить как прочитанные]</a>
</font></center>

<?
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Making Page: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";
{
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