<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
	//*******************************************************
	$FID = IntVal($_REQUEST["FID"]);
	$MID = IntVal($_REQUEST["MID"]);
	$MESSAGE_TYPE = $_REQUEST["MESSAGE_TYPE"];
	if ($MESSAGE_TYPE!="EDIT")
		$MESSAGE_TYPE = "NEW";
	if ($MESSAGE_TYPE=="EDIT" && $MID<=0)
	{
		LocalRedirect("index.php");
		die();
	}
	define("FORUM_MODULE_PAGE", "NEW_TOPIC");
	
	if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) 
		$prexectime = getmicrotime();
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
	ForumSetLastVisit();
	if (!$arForum)
	{
		LocalRedirect("index.php");
		die();
	}
	
	if ($MESSAGE_TYPE=="NEW" && !CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID()))
		$APPLICATION->AuthForm(GetMessage("FNT_NO_NPERMS"));
	
	if ($MESSAGE_TYPE=="EDIT" && !CForumMessage::CanUserUpdateMessage($MID, $USER->GetUserGroupArray(), IntVal($USER->GetID())))
		$APPLICATION->AuthForm(GetMessage("FNT_NO_EPERMS"));
	
	if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) 
		$arForumDebugInfo[] = "<br><font color=\"#FF0000\">Initializing Variables: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";
	
	if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) 
		$prexectime = getmicrotime();
	$strErrorMessage = "";
	$strOKMessage = "";
	$bVarsFromForm = false;
	$View = false;
	if ($_POST["MESSAGE_MODE"] == "VIEW")
	{
		$View = true;
		$bVarsFromForm = true;
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
			"NL2BR" => $arForum["ALLOW_NL2BR"],
			"SMILES" => ($_POST["USE_SMILES"]!="Y") ? "N" : $arForum["ALLOW_SMILES"]
			);
	}
	elseif ($_SERVER["REQUEST_METHOD"]=="POST" && (strlen($_POST["forum_post_action"])>0) && check_bitrix_sessid())
	{
		$arATTACH_IMG = $_FILES["ATTACH_IMG"];
		if ($MESSAGE_TYPE=="EDIT")
			$arATTACH_IMG["del"] = $_POST["ATTACH_IMG_del"];
	
		$arFieldsG = array(
			"POST_MESSAGE" => $_POST["POST_MESSAGE"],
			"AUTHOR_NAME" => $_POST["AUTHOR_NAME"],
			"AUTHOR_EMAIL" => $_POST["AUTHOR_EMAIL"],
			"USE_SMILES" =>  ($_POST["USE_SMILES"]!="Y") ? "N" : $arForum["ALLOW_SMILES"],
			"TITLE" => $_POST["TITLE"],
			"DESCRIPTION" => $_POST["DESCRIPTION"],
			"ICON_ID" => $_POST["ICON_ID"],
			"ATTACH_IMG" => $arATTACH_IMG
			);

		$MID1 = ForumAddMessage($MESSAGE_TYPE, $FID, ($MESSAGE_TYPE=="NEW") ? 0 : IntVal($TID), ($MESSAGE_TYPE=="NEW") ? 0 : IntVal($MID), $arFieldsG, $strErrorMessage, $strOKMessage, false, $_POST["captcha_word"], 0, $_POST["captcha_code"]);
		$MID1 = IntVal($MID1);
		if ($MID1>0)
		{
			$MID = $MID1;
			$str = "";
			if ($_REQUEST["TOPIC_SUBSCRIBE"]=="Y"||$_REQUEST["FORUM_SUBSCRIBE"]=="Y")
			{
				if ($_REQUEST["TOPIC_SUBSCRIBE"]=="Y")
					$str .= "TOPIC_SUBSCRIBE=Y&";
				if ($_REQUEST["FORUM_SUBSCRIBE"]=="Y")
					$str .= "FORUM_SUBSCRIBE=Y&";
			}
			if (!$GLOBALS["SHOW_FORUM_DEBUG_INFO"])
				LocalRedirect("read.php?".$str."FID=".$FID."&TID=".$TID."&MID=".$MID."#message".$MID);
		}
		else
			$bVarsFromForm = true;
	}
	elseif ($_SERVER["REQUEST_METHOD"]=="POST" && (strlen($_POST["forum_post_action"]) > 0) && !check_bitrix_sessid())
	{
		$strErrorMessage .= GetMessage("F_ERR_SESS_FINISH").".\n";
	}
	
	if ($GLOBALS["SHOW_FORUM_DEBUG_INFO"]) 
		$arForumDebugInfo[] = "<br><font color=\"#FF0000\">Actions: ".Round(getmicrotime()-$prexectime, 3)." sec</font>";
	
	$APPLICATION->AddChainItem($arForum["NAME"], "list.php?FID=".$FID);
	$APPLICATION->SetTitle((($MESSAGE_TYPE=="NEW")?GetMessage("FNT_NTITLE"):GetMessage("FNT_ETITLE")));
	$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", array("FID"=>$FID));
	?>
	<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
	<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>
	
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
	if ($View)
	{
		$parser = new textParser(LANGUAGE_ID);
		?><font style="font-size:4px;">&nbsp;<br></font>
		<table width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr class="forumpostsep"><td colspan="2"><a name="postform"></a></td></tr>
			<tr>
				<td valign="top" class="forumbody forumbrd forumbrd1" width="10%"><b><font class="forumtitletext"><?=GetMessage("FNT_VIEW")?>:</font></b></td>
				<td valign="bottom" class="forumbody forumbrd forumbrd1" style="border-left : none;">
					<font style="font-size:5px;">&nbsp;<br></font>
					<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
					<font style="font-size:8px;">&nbsp;<br></font>
					<font class="forumbodytext"><?
					echo $parser->convert($_POST["POST_MESSAGE"], $arAllow);
					?></font>
					<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
					<font style="font-size:5px;">&nbsp;<br></font>
				</td>
			</tr>
		</table>
		<font style="font-size:4px;">&nbsp;<br></font>		
		<?
	}
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/post_form.php", $arFormParams);
	?><br><br><?
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", array("FID"=>$FID));
	
	{
	if ($SHOW_FORUM_DEBUG_INFO)
	{
		for ($i = 0; $i < count($arForumDebugInfo); $i++) 
			echo $arForumDebugInfo[$i];
	}
	}
	
	//*******************************************************
else:
	?><font class="text"><b><?echo GetMessage("FNT_NO_MODULE")?></b></font><?
endif;
?>