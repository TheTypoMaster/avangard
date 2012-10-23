<?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
//*******************************************************

$FID = IntVal($_REQUEST["FID"]);
$TID = IntVal($_REQUEST["TID"]);
$newFID = IntVal($_REQUEST["newFID"]);

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
$arTopic = CForumTopic::GetByID($TID);

if (!$arTopic)
{
	LocalRedirect("list.php?FID=".$FID);
	die();
}

ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "MOVE");
$FID = IntVal($arTopic["FORUM_ID"]);
$arForum = CForumNew::GetByID($FID);
if (!$arForum)
{
	LocalRedirect("index.php");
	die();
}

if (ForumCurrUserPermissions($FID)<"Q")
	$APPLICATION->AuthForm("У Вас не достаточно прав для перемещения этой темы");

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Initializing Variables: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
$strErrorMessage = "";
$strOKMessage = "";
$bVarsFromForm = false;
if ($_SERVER["REQUEST_METHOD"]=="POST" && $newFID>0 && $_POST["action"]=="move")
{
	if (ForumCurrUserPermissions($newFID)<"Q")
		$strErrorMessage .= "У Вас не достаточно прав на форум назначения. \n";

	if (strlen($strErrorMessage)<=0)
	{
		$res = CForumTopic::MoveTopic2Forum($TID, $newFID);
		if (!$res)
			$strErrorMessage .= "Ошибка перемещения темы. \n";
	}

	if (strlen($strErrorMessage)>0)
	{
		$bVarsFromForm = true;
	}
	else
	{
		if (!$GLOBALS["SHOW_FORUM_DEBUG_INFO"])
			LocalRedirect("read.php?FID=".$newFID."&TID=".$TID);
	}
}
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Actions: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";

$APPLICATION->AddChainItem($arForum["NAME"], "read.php?FID=".$FID."&TID=".$TID);
$APPLICATION->SetTitle("Перемещение темы в другой форум");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");


$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php");
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<table width="99%" border="0" cellspacing="1" cellpadding="0" align="center" class="forumborder">
<tr><td>
	<table width="100%" border="0" cellspacing="1" cellpadding="4">
		<form method="POST">
		<tr>
			<td class="forumhead" colspan="2" align="center">
				<font class="forumheadtext"><b>Перенос темы в другой форум</b></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody" align="right" width="40%">
				<font class="forumheadtext">Перенести тему в форум</font>
			</td>
			<td class="forumbody" align="left" width="60%">
				<font class="forumbodytext">
					<select name="newFID">
						<?
						$arFilter = array();
						if (!$USER->IsAdmin())
						{
							$arFilter["PERMS"] = array($USER->GetGroups(), 'M');
							$arFilter["ACTIVE"] = "Y";
						}
						$db_Forum = CForumNew::GetListEx(array("NAME"=>"ASC"), $arFilter);
						while ($ar_Forum = $db_Forum->Fetch()):
							if (IntVal($ar_Forum["ID"])!=$FID)
							{
								?><option value="<?echo $ar_Forum["ID"]; ?>" <?if ($newFID==IntVal($ar_Forum["ID"])) echo "selected";?>><?echo htmlspecialcharsEx($ar_Forum["NAME"]); ?></option><?
							}
						endwhile;
						?>
					</select>
					<input type="hidden" name="action" value="move">
					<input type="hidden" name="TID" value="<?echo $TID; ?>">
					<input type="hidden" name="FID" value="<?echo $FID; ?>">
				</font>
			</td>
		</tr>
		<tr>
			<td class="forumhead" colspan="2" align="center">
				<font class="forumheadtext"><input type="submit" value="Перенести"></font>
			</td>
		</tr>
		</form>
	</table>
</td></tr>
</table>
<?
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
else:
	?>
	<font class="text"><b>Для просмотра этой страницы вы должны быть авторизованы</b></font>
	<?
endif;
?>