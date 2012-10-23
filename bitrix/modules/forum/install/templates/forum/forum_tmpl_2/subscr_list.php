<?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
//*******************************************************

ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "SUBSCRIPTION");
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

$ID = IntVal($ID);
if ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["ACTION"]=="DEL" && IntVal($_GET["ID"])>0)
{
	CForumSubscribe::Delete(IntVal($_GET["ID"]));
}

$APPLICATION->AddChainItem("Профиль", "profile.php");
$APPLICATION->SetTitle("Список подписки на сообщения форума");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php");

if (!$bUserFound)
	$strErrorMessage .= "Пользователь с кодом $UID не найден. \n";
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<?
$db_res = CForumSubscribe::GetList(array("FORUM_ID"=>"ASC", "TOPIC_ID"=>"ASC", "START_DATE"=>"ASC"), array("USER_ID"=>$UID));
?>
<form action="<?echo $APPLICATION->GetCurPage();?>" method="post">

<font class="text">
<?
$FID = IntVal($_REQUEST["FID"]);
$TID = IntVal($_REQUEST["TID"]);
if ($TID>0)
{
	?><a href="read.php?FID=<?echo $FID?>&TID=<?echo $TID?>">Вернуться назад</a><?
}
elseif ($FID>0)
{
	?><a href="list.php?FID=<?echo $FID?>">Вернуться назад</a><?
}
?>
</font><br><br>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr>
		<td class="forumhead" align="center">
			<font class="forumheadtext"><b>Название форума</b></font>
		</td>
		<td class="forumhead" align="center">
			<font class="forumheadtext"><b>Название темы</b></font>
		</td>
		<td class="forumhead" align="center">
			<font class="forumheadtext"><b>Дата подписки</b></font>
		</td>
		<td class="forumhead" align="center">
			<font class="forumheadtext"><b>Последнее отправленное сообщение</b></font>
		</td>
		<td class="forumhead" align="center">
			<font class="forumheadtext"><b>Действия</b></font>
		</td>
	</tr>
	<?
	while ($res = $db_res->Fetch()):
		$arForum_tmp = CForumNew::GetByID($res["FORUM_ID"]);
		$arTopic_tmp = CForumTopic::GetByID($res["TOPIC_ID"]);
		?>
		<tr>
			<td class="forumbody">
				<font class="forumbodytext"><a href="list.php?FID=<?echo $res["FORUM_ID"];?>"><?echo $arForum_tmp["NAME"];?></a></font>
			</td>
			<td class="forumbody">
				<font class="forumbodytext">
					<?
					if (IntVal($res["TOPIC_ID"])>0)
					{
						echo "<a href=\"read.php?FID=".$res["FORUM_ID"]."&TID=".$res["TOPIC_ID"]."\">".$arTopic_tmp["TITLE"]."</a>";
					}
					else
					{
						echo "Все темы";
					}
					?>
				</font>
			</td>
			<td class="forumbody">
				<font class="forumbodytext"><?echo $res["START_DATE"];?></font>
			</td>
			<td class="forumbody" align="center">
				<font class="forumbodytext">
				<?if (IntVal($res["LAST_SEND"])>0):?>
					<a href="read.php?MID=<?echo $res["LAST_SEND"];?>#message<?echo $res["LAST_SEND"];?>">Здесь</a>
				<?endif;?></font>
			</td>
			<td class="forumbody">
				<font class="forumbodytext"><a href="subscr_list.php?ID=<?echo $res["ID"];?>&ACTION=DEL">Удалить</a></font>
			</td>
		</tr>
	<?endwhile;?>
</table>
<td><tr></table>

</form>

<?
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