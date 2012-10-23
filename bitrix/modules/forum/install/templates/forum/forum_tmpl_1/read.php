<?
//*****************************************************************************************************************
//	Read topic. Public part.
//*****************************************************************************************************************
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*************************!Let's init $FID (forum id), $TID (topic id) and $MID (message id)**********************
//*************************!with actual and coordinated values*****************************************************
//*************************!***************************************************************************************
	$FID = IntVal($_REQUEST["FID"]);
	$TID = IntVal($_REQUEST["TID"]);
	$MID = IntVal($_REQUEST["MID"]);
	if (strToLower($_REQUEST["MID"]) == "unread_mid")
	{
		$MID = ForumGetFirstUnreadMessage($FID, $TID);
	}
	$MID = IntVal($MID);
	
	
	define("FORUM_MODULE_PAGE", "READ");
	
	if ($MID>0)
	{
		$arMessage = CForumMessage::GetByID($MID);
		if ($arMessage)
		{
			$TID = IntVal($arMessage["TOPIC_ID"]);
			$FID = IntVal($arMessage["FORUM_ID"]);
		}
		else
			$MID = 0;
	}
	
	$arTopic = CForumTopic::GetByIDEx($TID);
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
//*************************!Переменные*****************************************************************************
	$View = false;
	$strErrorMessage = "";
	$strOKMessage = "";
	$bVarsFromForm = false;
	
	$PermissionUser = ForumCurrUserPermissions($arForum["ID"]);
	if ($PermissionUser < "E")
		$APPLICATION->AuthForm(GetMessage("FR_FPERMS"));
		
//*************************!Subscribe******************************************************************************
	if ($_REQUEST["TOPIC_SUBSCRIBE"]=="Y"||$_REQUEST["FORUM_SUBSCRIBE"]=="Y")
	{
		if ($_REQUEST["TOPIC_SUBSCRIBE"]=="Y")
			ForumSubscribeNewMessagesEx($FID, $TID, "N", $strErrorMessage, $strOKMessage);
		if ($_REQUEST["FORUM_SUBSCRIBE"]=="Y")
			ForumSubscribeNewMessagesEx($FID, 0, "N", $strErrorMessage, $strOKMessage);
	}
//*************************!Subscribe******************************************************************************
	if (!CForumTopic::CanUserViewTopic($TID, $USER->GetUserGroupArray()))
		LocalRedirect("list.php?FID=".$FID."&TID=Y");
	
	ForumSetLastVisit($FID, $TID);
	ForumSetReadTopic($FID, $TID);
//*************************!Action*********************************************************************************
	if (($_POST["MESSAGE_MODE"] != "VIEW") && 
		((strLen(trim($_REQUEST["ACTION"])) > 0) || ($_REQUEST["VOTE4USER"]=="Y") || ($_POST["MESSAGE_TYPE"]=="REPLY")) && 
		check_bitrix_sessid())
	{
		if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["MESSAGE_TYPE"]=="REPLY" && 
			check_bitrix_sessid())
		{
			$arFieldsG = array(
					"POST_MESSAGE" => $_POST["POST_MESSAGE"],
					"AUTHOR_NAME" => trim($_POST["AUTHOR_NAME"]),
					"AUTHOR_EMAIL" => $_POST["AUTHOR_EMAIL"],
					"USE_SMILES" => $_POST["USE_SMILES"],
					"ATTACH_IMG" => $_FILES["ATTACH_IMG"]
				);
			$MID = ForumAddMessage("REPLY", $FID, $TID, 0, $arFieldsG, $strErrorMessage, $strOKMessage, false, $_POST["captcha_word"], 0, $_POST["captcha_code"]);
			$MID = IntVal($MID);
			if ($MID <= 0)
			{
				$bVarsFromForm = true;
			}
			else 
			{
				LocalRedirect($APPLICATION->GetCurPage()."?FID=".$FID."&TID=".$TID."&MID=".$MID."#message".$MID);
			}
		}
		elseif ($_SERVER["REQUEST_METHOD"]=="GET" && 
			($_GET["ACTION"]=="FORUM_SUBSCRIBE" || $_GET["ACTION"]=="TOPIC_SUBSCRIBE" || $_GET["ACTION"]=="FORUM_SUBSCRIBE_TOPICS") && 
			check_bitrix_sessid())
		{
			if (ForumSubscribeNewMessagesEx($FID, (($_GET["ACTION"]=="FORUM_SUBSCRIBE")?0:$TID), (($_GET["ACTION"]=="FORUM_SUBSCRIBE_TOPICS")?"Y":"N"), $strErrorMessage, $strOKMessage))
			{
				LocalRedirect("subscr_list.php?FID=".$FID."&TID=".$TID);
			}
		}
		elseif ($_SERVER["REQUEST_METHOD"]=="GET" && $_GET["VOTE4USER"]=="Y" && check_bitrix_sessid())
		{
			$UID = IntVal($_GET["UID"]);
			if ($UID <= 0)
			{
				$strErrorMessage .= GetMessage("FR_NO_VPERS").".\n";
			}
		
			if (strlen($strErrorMessage)<=0)
			{
				ForumVote4User($UID, $_GET["VOTES"], (($_GET["VOTES_TYPE"]=="U") ? True : False), $strErrorMessage, $strOKMessage);
			}
		
			LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
		}
		elseif ($_SERVER["REQUEST_METHOD"]=="GET" && check_bitrix_sessid())
		{
			$action = strToUpper($_GET["ACTION"]);
			switch ($action)
			{
				case "FORUM_MESSAGE2SUPPORT":
					if (CModule::IncludeModule("support"))
					{
						$SuID = ForumMoveMessage2Support($MID, $strErrorMessage, $strOKMessage);
						if (IntVal($SuID)>0)
							LocalRedirect("/bitrix/admin/ticket_edit.php?ID=".IntVal($SuID)."&lang=".LANGUAGE_ID);
					}
					
				break;
				case "FORUM_SUBSCRIBE":
				case "TOPIC_SUBSCRIBE":
				case "FORUM_SUBSCRIBE_TOPICS":
					if (ForumSubscribeNewMessagesEx($FID, (($action=="FORUM_SUBSCRIBE")?0:$TID), (($action=="FORUM_SUBSCRIBE_TOPICS")?"Y":"N"), $strErrorMessage, $strOKMessage))
					{
						LocalRedirect("subscr_list.php?FID=".$FID."&TID=".$TID);
					}
				break;
				case "MOVE_TOPIC":
					LocalRedirect("move.php?FID=".$FID."&TID=".$TID);
				break;
				case "HIDE":
				case "SHOW":
					ForumModerateMessage($MID, $action, $strErrorMessage, $strOKMessage);
					LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
				break;
				case "SET_ORDINARY":
				case "SET_TOP":
					if ($action == "SET_ORDINARY")
					{
						$action = "ORDINARY";
						$sort = "150";
					}
					else 
					{
						$action = "TOP";
						$sort = "100";
					}
					if (ForumTopOrdinaryTopic($TID, $action, $strErrorMessage, $strOKMessage))
					{
						$arTopic["SORT"] = $sort;
					}
					LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
				break;
				case "DEL_TOPIC":
					if ($TID>0)
					{
						if (ForumDeleteTopic($TID, $strErrorMessage, $strOKMessage))
						{
							LocalRedirect("list.php?FID=".$FID);
						}
					}
				break;
				case "STATE_Y":
				case "STATE_N":
					if ($action == "STATE_Y")
					{
						$action = "OPEN";
						$state = "Y";
					}
					else 
					{
						$action = "CLOSE";
						$state = "N";
					}
					if (ForumOpenCloseTopic($TID, $action, $strErrorMessage, $strOKMessage))
						$arTopic["STATE"] = $state;
					LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
				break;
				case "DEL":
					if (ForumDeleteMessage($MID, $strErrorMessage, $strOKMessage))
					{
						$arTopic = CForumTopic::GetByID($TID);
						if (!$arTopic)
						{
							LocalRedirect("list.php?FID=".$FID);
						}
					}
					LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
				break;
			}
			global $HTTP_GET_VARS;
			unset($_GET["MID"]);
			unset($HTTP_GET_VARS["MID"]);
			unset($_GET["ACTION"]);
			unset($HTTP_GET_VARS["ACTION"]);
		}
		elseif ($_SERVER["REQUEST_METHOD"]=="POST" && check_bitrix_sessid())
		{
			$message = explode(",", $_POST["MID_ARRAY"]);
			$message = ForumMessageExistInArray($message);
			
			if (!$message)
				$strErrorMessage .= GetMessage("FMM_NO_MESSAGE").".\n";
			else 
			{
				$action = strToUpper($_POST["ACTION"]);
				switch ($action)
				{
					case "DEL":
						if (ForumDeleteMessageArray($message, $strErrorMessage, $strOKMessage))
						{
							LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
						}
					break;
					case "MOVE":
						LocalRedirect("move_message.php?FID=".$FID."&TID=".$TID."&MID_ARRAY=".$_POST["MID_ARRAY"]);
					break;
					case "SHOW":
					case "HIDE":
						if (ForumModerateMessageArray($message, $action, $strErrorMessage, $strOKMessage))
						{
							LocalRedirect("read.php?FID=".$FID."&TID=".$TID);
						}
					break;
				}
				global $HTTP_POST_VARS;
				unset($_POST["ACTION"]);
				unset($_POST["MID_ARRAY"]);
				unset($HTTP_POST_VARS["ACTION"]);
				unset($HTTP_POST_VARS["MID_ARRAY"]);
			}
		}
	}
	elseif (($_POST["MESSAGE_MODE"] != "VIEW") && 
		((strLen(trim($_REQUEST["ACTION"])) > 0) || ($_REQUEST["VOTE4USER"]=="Y") || ($_POST["MESSAGE_TYPE"]=="REPLY")) && 
		!check_bitrix_sessid())
	{
		$bVarsFromForm = true;
		$strErrorMessage = GetMessage("F_ERR_SESS_FINISH");
	}
	elseif ($_POST["MESSAGE_MODE"] == "VIEW")
	{
		$View = true;
		$bVarsFromForm = true;
	}
//*************************!Action*********************************************************************************

//*************************!Making page****************************************************************************
	$APPLICATION->AddChainItem(htmlSpecialCharsEx($arForum["NAME"]), "list.php?FID=".$FID);
	$APPLICATION->SetTitle(GetMessage("FR_FTITLE")." &laquo;".htmlSpecialCharsEx($arForum["NAME"])."&raquo;");
	$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
	$arMenuParams = compact("arTopic", "FID", "TID");
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", $arMenuParams);
	
	echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));
	echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));
	
	$tmp = 0;
	$db_res = CForumMessage::GetList(array("ID"=>"DESC"), array("TOPIC_ID"=>$TID), false, 1);
	if ($ar_res = $db_res->Fetch()) 
		$tmp = IntVal($ar_res["ID"]);
	$arTopic["iLAST_TOPIC_MESSAGE"] = $tmp;
	
	$arFilter = array("TOPIC_ID" => $TID);
	if ($PermissionUser < "Q")
		$arFilter["APPROVED"] = "Y";
	if ($USER->IsAuthorized())
	{
		$arFilter["POINTS_TO_AUTHOR_ID"] = $USER->GetID();
	}
	
	$db_Message = CForumMessage::GetListEx(array("ID"=>"ASC"), $arFilter);
	global $HTTP_GET_VARS;
	unset($_GET["MID"]);
	unset($HTTP_GET_VARS["MID"]);
	unset($_GET["ACTION"]);
	unset($HTTP_GET_VARS["ACTION"]);
	
	if ($MID>0)
		$db_Message->NavStart($GLOBALS["FORUM_MESSAGES_PER_PAGE"], false, CForumMessage::GetMessagePage($MID, $GLOBALS["FORUM_MESSAGES_PER_PAGE"], $USER->GetUserGroupArray()));
	else
		$db_Message->NavStart($GLOBALS["FORUM_MESSAGES_PER_PAGE"], false);
		
	$UserInfo = array();
	$arCurrUser = array();
	$arCurrUser["Rank"] = CForumUser::GetUserRank(IntVal($USER->GetParam("USER_ID")));
	$arCurrUser["bCanUserDeleteMessages"] = CForumTopic::CanUserDeleteTopicMessage($TID, $USER->GetUserGroupArray(), $USER->GetID());
	$arCurrUser["Perms"] = ForumCurrUserPermissions($FID);
	$parser = new textParser(LANGUAGE_ID);
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
	
	?>
	<table width="100%" border="0">
		<tr>
			<td align="left"><?=$db_Message->NavPrint(GetMessage("FR_MESS"))?></td>
			<?if (CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())):?>
				<form action='new_topic.php' method='GET'><td align='right'>
				<input type='hidden' name='FID' value='<?=$FID?>'>
				<input type='submit' value='<?=GetMessage('FR_CREATE_NEW_TOPIC')?>' title='<?=GetMessage('FR_CREATE_NEW_TOPIC1')?>' class='forumnewtopic_button'>
				</td></form>
			<?endif;?>
		</tr>
	</table>
	<br>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" class="forumtitle">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr valign="top">
						<?if ($arCurrUser["Perms"]>="Q"):?>
							<td width="0%"><input type="checkbox" name="message_all" value="Y" id="message_all" onclick="SelectAllCheckBox('FORUM_MESSAGES', 'message_id[]', 'message_all');"></td>
							<td class="forumtitletext" width="0%">&nbsp;</td>
						<?endif;?>
							<td class="forumtitletext" width="99%"><font class="forumtitletext"><?=GetMessage("FR_TOPIC")?> &laquo;<b><?=htmlspecialcharsEx($arTopic["TITLE"]);?></b>
						<?if (strlen($arTopic["DESCRIPTION"])>0):
							?>, <?=htmlspecialcharsEx($arTopic["DESCRIPTION"])?>
						<?endif;?>&raquo;
							<?=GetMessage("FR_ON_FORUM")?> <a href="list.php?FID=<?=$arForum["ID"] ?>"><b><?=htmlSpecialCharsEx($arForum["NAME"])?></b></a></font></td>
						<td nowrap width="1%" align="right" valign="middle" class="forumtitletext"><?=GetMessage("FR_ON_VIEWS")?> <?=$arTopic["VIEWS"]?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<font style="font-size:4px;">&nbsp;<br></font>
	<form name="FORUM_MESSAGES" id="FORUM_MESSAGES">
	<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<?
	
		while ($arMessage = $db_Message->Fetch()):
	$arUser = array();
//*************************!***************************************************************************************

	if (($arMessage["AUTHOR_ID"]>0) && (!isset($UserInfo[$arMessage["AUTHOR_ID"]])))
	{
		$arUser["Groups"] = CUser::GetUserGroup($arMessage["AUTHOR_ID"]);
		if (!in_array(2, $arUser["Groups"]))
			$arUser["Groups"][] = 2;
		$arUser["Perms"] = CForumNew::GetUserPermission($arMessage["FORUM_ID"], $arUser["Groups"]);
		if (($arUser["Perms"]<="Q") && (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y"))
			$arUser["Rank"] = CForumUser::GetUserRank($arMessage["AUTHOR_ID"], LANGUAGE_ID);
			
		if (intVal($arMessage["POINTS"]) > 0)
			$arUser["Points"] = array("POINTS" => $arMessage["POINTS"], "DATE_UPDATE" => $arMessage["DATE_UPDATE"]);
		else
			$arUser["Points"] = false;
			
		$UserInfo[$arMessage["AUTHOR_ID"]] = $arUser;
	}
	elseif(($arMessage["AUTHOR_ID"]>0) && (isset($UserInfo[$arMessage["AUTHOR_ID"]])))
	{
		$arUser = $UserInfo[$arMessage["AUTHOR_ID"]];
	}
	$arMessage["AUTHOR_ID"]	= intVal($arMessage["AUTHOR_ID"]);
	$arMessage["FOR_JS"]["AUTHOR_NAME"] = Cutil::JSEscape(htmlspecialchars($arMessage["AUTHOR_NAME"]));
	$arMessage["FOR_JS"]["POST_MESSAGE"] = Cutil::JSEscape(htmlspecialchars($arMessage["POST_MESSAGE"]));
		?><tr valign="top" class="forumbody">
			<td align="left" rowspan="2" width="140" class="forumbrd" style="border-right:none;">
				<a name="message<?=$arMessage["ID"];?>"></a>
				<font class="forumbodytext"><b><?=htmlspecialcharsEx($arMessage["AUTHOR_NAME"]); ?></b>
				<?
				if ($arMessage["AUTHOR_ID"]>0)
				{
					if ($arUser["Perms"]=="Q") 
						echo "<br><font class=\"forumheadcolor\">".GetMessage("FR_MODERATOR")."</font>";
					elseif ($arUser["Perms"]=="U") 
						echo "<br><font class=\"forumheadcolor\">".GetMessage("FR_EDITOR")."</font>";
					elseif ($arUser["Perms"]=="Y") 
						echo "<br><font class=\"forumheadcolor\">".GetMessage("FR_ADMIN")."</font>";
					elseif (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y")
						echo "<br><font class=\"forumheadcolor\">".htmlspecialcharsEx($arUser["Rank"]["NAME"])."</font>";
				}
				else
					echo "<br><font class=\"forumheadcolor\"><i>".GetMessage("FR_GUEST")."</i></font>";
				?>
				<br>
				<?if (strlen($arMessage["AVATAR"])>0):?>
					<a href="view_profile.php?UID=<?=$arMessage["AUTHOR_ID"] ?>&FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"]?>" title="<?=GetMessage("FR_AUTHOR_PROFILE")?>"><?=CFile::ShowImage($arMessage["AVATAR"], 90, 90, "border=0 vspace=5", "", true)?></a><br>
				<?endif;?>
				<?if (strlen($arMessage["DESCRIPTION"])>0):?>
					<i><?=htmlspecialcharsEx($arMessage["DESCRIPTION"]);?></i><br>
				<?endif;?>
				<font style="font-size:8px;">&nbsp;<br></font>
				<?if (IntVal($arMessage["NUM_POSTS"])>0):?>
					<font class="forumheadcolor"><?=GetMessage("FR_NUM_MESS")?></font> <?=$arMessage["NUM_POSTS"];?><br>
				<?endif;?>
				<?if (strlen($arMessage["DATE_REG"])>0):?>
					<font class="forumheadcolor"><?=GetMessage("FR_DATE_REGISTER")?></font> <?=htmlspecialcharsEx($arMessage["DATE_REG"]);?><br>
				<?endif;?>
				</font>
			</td>
			<td class="forumbrd forumbrd1" style="border-bottom:none;">
			
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
					<?if ($arCurrUser["Perms"]>="Q"):?>
						<td nowrap><input type="checkbox" name="message_id[]" value="<?=$arMessage["ID"]?>" id="message_id[<?=$arMessage["ID"]?>]" onclick="SelectCheckBox('message_all');">&nbsp;</td>
					<?endif;?>
						<td width="100%">
							<font class="forumbodytext">
							<font class="forumheadcolor"><?=GetMessage("FR_DATE_CREATE")?></font> 
							<nobr><?=htmlspecialcharsEx($arMessage["POST_DATE"]);?></nobr><br>
							</font>
						</td>
						<?if ($arCurrUser["Perms"]>="I" && $arTopic["STATE"]=="Y"):?>
							<td nowrap class="forummessbutton">
								<a href="#postform" onMouseDown="reply2author('<?=$arMessage["FOR_JS"]["AUTHOR_NAME"]?>,')" class="forummessbuttontext" title="<?=GetMessage("FR_INSERT_NAME")?>"><?=GetMessage("FR_NAME")?></a></td>
							<td><div class="forummessbuttonsep"></div></td>
							<td nowrap class="forummessbutton">
								<a href="#postform" onMouseDown="quoteMessageEx('<?=$arMessage["FOR_JS"]["AUTHOR_NAME"]?>', '<?=$arMessage["FOR_JS"]["POST_MESSAGE"]?>')" title="<?=GetMessage("FR_QUOTE_HINT")?>" class="forummessbuttontext"><?=GetMessage("FR_QUOTE")?></a></td>
						<?endif;?>
					</tr>
				</table>
				<font style="font-size:5px;">&nbsp;<br></font>
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
				<font style="font-size:8px;">&nbsp;<br></font>
				<font class="forumbodytext">
				<?
				$arAllow["SMILES"] = $arForum["ALLOW_SMILES"];
				if ($arMessage["USE_SMILES"]!="Y") 
					$arAllow["SMILES"] = "N";
				if ((COption::GetOptionString("forum", "FILTER", "Y")=="Y")||(COption::GetOptionString("forum", "MESSAGE_HTML", "Y")=="Y"))
					$message = $arMessage["POST_MESSAGE_HTML"];
				else 
					$message = $arMessage["POST_MESSAGE"];
					
				if (COption::GetOptionString("forum", "MESSAGE_HTML", "Y") == "N")
					$message = $parser->convert($message, $arAllow);
				echo $message;
				
				if (IntVal($arMessage["ATTACH_IMG"])>0)
				{
					echo "<br><br>";
					if ($arForum["ALLOW_UPLOAD"]=="Y" || $arForum["ALLOW_UPLOAD"]=="F" || $arForum["ALLOW_UPLOAD"]=="A")
					{
						echo CFile::ShowFile($arMessage["ATTACH_IMG"], 0, 300, 300, true, "border=0", false);
					}
				}

				if (strlen($arMessage["SIGNATURE"])>0)
				{
					$arAllow["SMILES"] = "N";
					?><br><br><font class="forumsigntext"><?=$parser->convert($arMessage["SIGNATURE"], $arAllow)?></font><?
				}
				?>
				</font>
			</td>
		</tr>
		<tr>
			<td valign="bottom" class="forumbody forumbrd forumbrd1" style="border-top:none;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
				<font style="font-size:5px;">&nbsp;<br></font>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr valign="top">
						<td>
							<?if (($arMessage["AUTHOR_ID"] > 0)):?>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td nowrap class="forummessbutton"><a href="view_profile.php?UID=<?=$arMessage["AUTHOR_ID"] ?>&FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"]?>" title="<?=GetMessage("FR_AUTHOR_PROFILE")?>" class="forummessbuttontext"><?=GetMessage("FR_PROFILE")?></a></td>
										<td><div class="forummessbuttonsep"></div></td>
										<?if (strlen($arMessage["EMAIL"])>0):?>
											<td nowrap class="forummessbutton"><a href="send_message.php?TYPE=MAIL&UID=<?=$arMessage["AUTHOR_ID"]; ?>" title="<?=GetMessage("FR_EMAIL_AUTHOR")?>" class="forummessbuttontext">E-Mail</a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;?>
										<?if ((strLen($arMessage["PERSONAL_ICQ"])>0) && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y")):?>
											<td nowrap class="forummessbutton"><a href="send_message.php?TYPE=ICQ&UID=<?=$arMessage["AUTHOR_ID"]; ?>" title="<?=GetMessage("FR_ICQ_AUTHOR")?>" class="forummessbuttontext">ICQ</a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;?>
										<?if ($USER->IsAuthorized()):?>
											<td nowrap class="forummessbutton"><a href="pm_message.php?mode=new&USER_ID=<?=$arMessage["AUTHOR_ID"]?>" title="<?=GetMessage("FR_PRIVATE_MESSAGE")?>"  class="forummessbuttontext"><?=GetMessage("FR_PRIVATE_MESSAGE")?></a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;?>
										
										<?
										if (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y" && $USER->IsAuthorized()
											&& ($USER->IsAdmin() || IntVal($USER->GetID())!=$arMessage["AUTHOR_ID"]))
										{
											$strNotesText = "";
											$bCanVote = False;
											$bCanUnVote = False;
											
											if ($arUser["Points"])
											{
												$bCanUnVote = True;
												$strNotesText .= str_replace("#POINTS#", $arUser["Points"]["POINTS"], str_replace("#END#", ForumNumberRusEnding($arUser["Points"]["POINTS"]), GetMessage("FR_YOU_ALREADY_VOTE1"))).". ";

												if (IntVal($arUser["Points"]["POINTS"]) < IntVal($arCurrUser["Rank"]["VOTES"]))
												{
													$bCanVote = True;
													$strNotesText .= str_replace("#POINTS#", (IntVal($arUser["Points"]["VOTES"])-IntVal($arUser["Points"]["POINTS"])), str_replace("#END#", ForumNumberRusEnding((IntVal($arCurrUser["Rank"]["VOTES"])-IntVal($arUser["Points"]["POINTS"]))), GetMessage("FR_YOU_ALREADY_VOTE3")));
												}
												if ($USER->IsAdmin())
													$strNotesText .= GetMessage("FR_VOTE_ADMIN");
											}
											else
											{
												if (IntVal($arCurrUser["Rank"]["VOTES"])>0)
												{
													$bCanVote = True;
													$strNotesText .= GetMessage("FR_NO_VOTE");
													$strNotesText .= str_replace("#POINTS#", (IntVal($arCurrUser["Rank"]["VOTES"])-IntVal($arUser["Points"]["POINTS"])), str_replace("#END#", ForumNumberRusEnding((IntVal($arCurrUser["Rank"]["VOTES"])-IntVal($arUser["Points"]["POINTS"]))), GetMessage("FR_NO_VOTE1"))).". ";
													if ($USER->IsAdmin())
														$strNotesText .= GetMessage("FR_VOTE_ADMIN");
												}
											}
											if ($bCanVote || $bCanUnVote)
											{
												?><td nowrap class="forummessbutton"><a href="read.php?UID=<?=$arMessage["AUTHOR_ID"] ?>&FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"]?>&VOTES=<?=IntVal($arCurrUser["Rank"]["VOTES"])?>&VOTES_TYPE=<?=(($bCanVote) ? "V" : "U");?>&VOTE4USER=Y&<?=bitrix_sessid_get()?>" title="<?=$strNotesText?>" class="forummessbuttontext"><img src="/bitrix/images/forum/icon/<?=(($bCanVote) ? "icon7.gif" : "icon6.gif");?>" width="15" height="15" border="0" alt="<?=(($bCanVote) ? GetMessage("FR_NO_VOTE_DO") : GetMessage("FR_NO_VOTE_UNDO"));?>"> <?=(($bCanVote) ? GetMessage("FR_NO_VOTE_DO") : GetMessage("FR_NO_VOTE_UNDO"));?></a></td>
													<td><div class="forummessbuttonsep"></div></td><?
											}
										}
									?></tr>
								</table>
							<?endif;?>
							<?if ($arCurrUser["Perms"]>="Q" || (($arTopic["iLAST_TOPIC_MESSAGE"] == IntVal($arMessage["ID"])) && $USER->IsAuthorized() && ($arMessage["AUTHOR_ID"] == IntVal($USER->GetParam("USER_ID")))) || $arCurrUser["bCanUserDeleteMessages"]):
								?>
								<?if($arMessage["AUTHOR_ID"]>0):?>
									<font style="font-size:4px;">&nbsp;<br></font>
								<?endif;?>
								
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<?if ($arMessage["APPROVED"]=="Y" && $arCurrUser["Perms"]>="Q"):?>
											<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$FID; ?>&TID=<?=$TID; ?>&MID=<?=$arMessage["ID"] ?>&ACTION=HIDE&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_HIDE_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_HIDE")?></a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?elseif ($arCurrUser["Perms"]>="Q"):?>
											<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$FID; ?>&TID=<?=$TID; ?>&MID=<?=$arMessage["ID"] ?>&ACTION=SHOW&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_SHOW_MESS")?>" class="forummessbuttontext"><i><b><?=GetMessage("FR_SHOW")?></b></i></a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;
										if ($arCurrUser["Perms"] >= "U" || ($arTopic["iLAST_TOPIC_MESSAGE"] == IntVal($arMessage["ID"]) && $USER->IsAuthorized() && $arMessage["AUTHOR_ID"] == IntVal($USER->GetParam("USER_ID")))):?>
											<td nowrap class="forummessbutton"><a href="new_topic.php?FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"] ?>&MESSAGE_TYPE=EDIT&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_EDIT_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_EDIT")?></a></td>
											<td><div class="forummessbuttonsep"></div></td>
											<?
										endif;
										if ($arCurrUser["bCanUserDeleteMessages"]):
											?><td nowrap class="forummessbutton"><a href="read.php?FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"] ?>&ACTION=DEL&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_DELETE_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_DELETE")?></a></td>
											<td><div class="forummessbuttonsep"></div></td>
											<?if($arMessage["AUTHOR_ID"]>0 && CModule::IncludeModule("support")):?>
												<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"] ?>&ACTION=FORUM_MESSAGE2SUPPORT&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_MOVE2SUPPORT")?>" class="forummessbuttontext"><?=GetMessage("FR_2SUPPORT")?></a></td>
												<td><div class="forummessbuttonsep"></div></td>
											<?endif;?>
										<?endif;?>
										</font>
									</tr>
								</table>
							<?endif?>
						</td>
						<td align="right">
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td nowrap class="forummessbutton" style="padding-left:2px; padding-right:2px;"><a href="javascript:scroll(0,0);" title="<?=GetMessage("FR_2TOP")?>" class="forummessbuttontext"><?=GetMessage("FR_TOP")?></a></td>
							  </tr>
							</table>
						</td>
					</tr>
				</table>
				<?if ($arCurrUser["Perms"]>="Q"):?>
					<font class="forumbodytext">
					<font style="font-size:5px;">&nbsp;<br></font>
					<?
					$bIP = False;
					if (ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $arMessage["AUTHOR_IP"]))
						$bIP = True;
					if ($bIP)
						$arMessage["AUTHOR_IP"] = GetWhoisLink($arMessage["AUTHOR_IP"], "");
					else
						$arMessage["AUTHOR_IP"] = htmlspecialchars($arMessage["AUTHOR_IP"]);

					$bIP = False;
					if (ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $arMessage["AUTHOR_REAL_IP"]))
						$bIP = True;
					if ($bIP)
						$arMessage["AUTHOR_REAL_IP"] =  GetWhoisLink($arMessage["AUTHOR_REAL_IP"], "");
					else
						$arMessage["AUTHOR_REAL_IP"] = htmlspecialchars($arMessage["AUTHOR_REAL_IP"]);
					?>
					<font class="forumheadcolor">IP
					<?if($arMessage["AUTHOR_IP"] <> $arMessage["AUTHOR_REAL_IP"]):?> 
					<?=GetMessage("FR_REAL_IP")?><?endif;?>: </font><?=$arMessage["AUTHOR_IP"];?><?if($arMessage["AUTHOR_IP"] <> $arMessage["AUTHOR_REAL_IP"]):?>&nbsp;/ <?=$arMessage["AUTHOR_REAL_IP"];?><?endif?><br>
					<?if (CModule::IncludeModule("statistic") && IntVal($arMessage["GUEST_ID"])>0 && $APPLICATION->GetGroupRight("statistic")!="D"):?>
						<font class="forumheadcolor"><?=GetMessage("FR_USER_ID")?> </font><a href="/bitrix/admin/guest_list.php?lang=<?=LANGUAGE_ID?>&find_id=<?=$arMessage["GUEST_ID"]?>&set_filter=Y"><?=$arMessage["GUEST_ID"];?></a><br>
					<?endif;?>
					<?if ($arMessage["AUTHOR_ID"]>0 && $APPLICATION->GetGroupRight("main")>="R"):?>
						<font class="forumheadcolor"><?=GetMessage("FR_USER_ID_USER")?> </font><a href="/bitrix/admin/user_edit.php?lang=<?=LANG_ADMIN_LID?>&ID=<?=$arMessage["AUTHOR_ID"]?>"><?=$arMessage["AUTHOR_ID"];?></a><br>
					<?endif;?>
					</font>
				<?endif;?>
			</td>
		</tr>
		<tr class="forumpostsep">
			<td colspan="2"><!-- --></td>
		</tr>
		<?
		endwhile;
		
		if ($View):
			$arAllow["SMILES"] = $arForum["ALLOW_SMILES"];
			if ($_POST["USE_SMILES"]!="Y") 
				$arAllow["SMILES"] = "N";
			?><tr class="forumpostsep"><td colspan="2"><a name="postform"></a></td></tr>
			<tr><td valign="top" class="forumbody forumbrd forumbrd1"><b><font class="forumbodytext"><?=GetMessage("FR_VIEW")?></font></b></td>
				<td valign="bottom" class="forumbody forumbrd forumbrd1" style="border-left : none;"><font style="font-size:5px;">&nbsp;<br></font>
					<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
					<font style="font-size:8px;">&nbsp;<br></font><font class="forumbodytext"><?=$parser->convert($_POST["POST_MESSAGE"], $arAllow);?></font>
					<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table><font style="font-size:5px;">&nbsp;<br></font>
				</td></tr><?
		endif;
	?></table></form><br><?
	
	if ($arTopic["STATE"]=="Y")
	{
		$arFormParams1 = array("MESSAGE_TYPE" => "REPLY");
		$arFormParams2 = compact("arForum", "FID", "TID", "bVarsFromForm", "strErrorMessage", "strOKMessage", "View");
		$arFormParams = array_merge($arFormParams1, $arFormParams2);
		if ($bVarsFromForm)
		{
			$arFormParams["AUTHOR_NAME"] = $_POST["AUTHOR_NAME"];
			$arFormParams["AUTHOR_EMAIL"] = $_POST["AUTHOR_EMAIL"];
			$arFormParams["POST_MESSAGE"] = $_POST["POST_MESSAGE"];
			$arFormParams["USE_SMILES"] = $_POST["USE_SMILES"];
		}
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/post_form.php", $arFormParams);
	}
	else
	{
		?><script language="Javascript"><?
			if ($strJSPath = $APPLICATION->GetTemplatePath("forum/forum_tmpl_1/forum_js.php"))
			include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
		?></script><?	
	} 
	?><br>
	<table width="100%" border="0">
		<tr>
			<td align="left">
				<?=$db_Message->NavPrint(GetMessage("FR_MESS"))?>
			</td>
			<?if (CForumTopic::CanUserAddTopic($FID, $USER->GetUserGroupArray(), $USER->GetID())):?>
				<form action='new_topic.php' method='GET'><td align='right'>
				<input type='hidden' name='FID' value='<?=$FID?>'>
				<input type='submit' value='<?=GetMessage('FR_CREATE_NEW_TOPIC')?>' title='<?=GetMessage('FR_CREATE_NEW_TOPIC1')?>' class='forumnewtopic_button'>
				</td></form>
			<?endif;?>
		</tr>
	</table>
	<br>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td><table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class="forumhead"><td valign="top" class="forumtitletext"><?=GetMessage("FR_NOW_ONLINE")?></td></tr>
		<tr class="forumbody"><td valign="top" class="forumbodytext"><?
		$UserOnLine = ShowActiveUser(array("PERIOD" => 600, "FORUM_ID" => $FID, "TOPIC_ID" => $TID));
		echo $UserOnLine["BODY"];?></td></tr>
	</table></td></tr></table><br><br><?
	$arMenuParams = compact("arTopic", "FID", "TID");
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", $arMenuParams);
	if (intVal($MID) > 0)
	{
		?><script type="text/javascript">
		location.hash = 'message<?=$MID?>';
		</script><?
	}
	?></font><br><br><?
//*************************!Making page****************************************************************************
	else:
		?>
		<font class="text"><b><?=GetMessage("FR_NO_MODULE")?></b></font>
		<?
	endif;
	
function ForumNumberRusEnding($num)
{
	if (LANGUAGE_ID=="ru")
	{
		if (strlen($num)>1 && substr($num, strlen($num)-2, 1)=="1")
		{
			return "ов";
		}
		else
		{
			$c = IntVal(substr($num, strlen($num)-1, 1));
			if ($c==0 || ($c>=5 && $c<=9))
				return "ов";
			elseif ($c==1)
				return "";
			else
				return "а";
		}
	}
	else
	{
		if (IntVal($num)>1)
			return "s";
		return "";
	}
}
?>