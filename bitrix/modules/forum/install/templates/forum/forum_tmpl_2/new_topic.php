<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

$FID = IntVal($_REQUEST["FID"]);
$MID = IntVal($_REQUEST["MID"]);

if ($_REQUEST["MESSAGE_TYPE"]!="EDIT")
	$MESSAGE_TYPE = "NEW";
else
	$MESSAGE_TYPE = "EDIT";

if ($MESSAGE_TYPE=="EDIT" && $MID<=0)
{
	LocalRedirect("index.php");
	die();
}

define("FORUM_MODULE_PAGE", "NEW_TOPIC");

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
if ($MESSAGE_TYPE=="EDIT")
{
	$arMessage = CForumMessage::GetByID($MID);
	if (!$arMessage)
	{
		LocalRedirect("index.php");
		die();
	}
	$FID = IntVal($arMessage["FORUM_ID"]);
	$TID = IntVal($arMessage["TOPIC_ID"]);
}

$arForum = CForumNew::GetByID($FID);

if (!$arForum)
{
	LocalRedirect("index.php");
	die();
}

ForumSetLastVisit();
if ($MESSAGE_TYPE=="NEW" && !CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID()))
	$APPLICATION->AuthForm("У Вас недостаточно прав для создания новой темы в этом форуме");

if ($MESSAGE_TYPE=="EDIT" && !CForumMessage::CanUserUpdateMessage($MID, $USER->GetUserGroupArray(), IntVal($USER->GetID())))
	$APPLICATION->AuthForm("У Вас недостаточно прав для редактирования этого сообщения");

if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Initializing Variables: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";


if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $prexectime = getmicrotime();
$strErrorMessage = "";
$strOKMessage = "";
$bVarsFromForm = false;
if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["forum_post_action"])>0)
{
	$arATTACH_IMG = $_FILES["ATTACH_IMG"];
	if ($MESSAGE_TYPE=="EDIT")
		$arATTACH_IMG["del"] = $_POST["ATTACH_IMG_del"];

	$arFieldsG = array(
		"POST_MESSAGE" => $_POST["POST_MESSAGE"],
		"AUTHOR_NAME" => $_POST["AUTHOR_NAME"],
		"AUTHOR_EMAIL" => $_POST["AUTHOR_EMAIL"],
		"USE_SMILES" => $_POST["USE_SMILES"],
		"TITLE" => $_POST["TITLE"],
		"DESCRIPTION" => $_POST["DESCRIPTION"],
		"ICON_ID" => $_POST["ICON_ID"],
		"ATTACH_IMG" => $arATTACH_IMG
		);
	$MID1 = ForumAddMessage($MESSAGE_TYPE, $FID, ($MESSAGE_TYPE=="NEW") ? 0 : IntVal($TID), ($MESSAGE_TYPE=="NEW") ? 0 : IntVal($MID), $arFieldsG, $strErrorMessage, $strOKMessage);
	$MID1 = IntVal($MID1);
	if ($MID1>0)
	{
		$MID = $MID1;
		if (!$GLOBALS["SHOW_FORUM_DEBUG_INFO"])
			LocalRedirect("read.php?FID=".$FID."&TID=".$TID."&MID=".$MID."#message".$MID);
	}
	else
		$bVarsFromForm = true;
}
if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) $arForumDebugInfo[] = "<br><font color=\"#FF0000\">Actions: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";

$APPLICATION->AddChainItem($arForum["NAME"], "list.php?FID=".$FID);
$APPLICATION->SetTitle((($MESSAGE_TYPE=="NEW")?"Новая тема":"Изменение сообщения"));
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");


$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php", array("FID"=>$FID));
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<table width="99%" border="0" cellspacing="1" cellpadding="0" align="center" class="forumborder">
<tr><td>

<table width="100%" border="0" cellspacing="1" cellpadding="4">
<?
$arFormParams = compact("arForum", "FID", "TID", "MID", "bVarsFromForm", "MESSAGE_TYPE", "strErrorMessage", "strOKMessage");

if ($bVarsFromForm)
{
	$arFormParams["AUTHOR_NAME"] = $_POST["AUTHOR_NAME"];
	$arFormParams["AUTHOR_EMAIL"] = $_POST["AUTHOR_EMAIL"];
	$arFormParams["POST_MESSAGE"] = $_POST["POST_MESSAGE"];
	$arFormParams["USE_SMILES"] = $_POST["USE_SMILES"];

	$arFormParams["TITLE"] = $_POST["TITLE"];
	$arFormParams["DESCRIPTION"] = $_POST["DESCRIPTION"];
	$arFormParams["ICON_ID"] = $_POST["ICON_ID"];
}

$APPLICATION->IncludeFile("forum/forum_tmpl_2/post_form.php", $arFormParams);
?>
</table>

</td></tr>
</table>

<?
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
	<font class="text"><b>Модуль форума не установлен</b></font>
	<?
endif;
?>