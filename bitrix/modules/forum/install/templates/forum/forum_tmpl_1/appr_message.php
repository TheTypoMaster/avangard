<?
//*****************************************************************************************************************
//	
//*****************************************************************************************************************
//*************************!***************************************************************************************
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
//*************************!Обработка входных параметров***********************************************************
	define("FORUM_MODULE_PAGE", "MOVE_MESSAGE");
	
	$arError = array();
	$arOK = array();
	$bAction = false;
	$message = array();
	$arForum = array();
	$arTopic = array();
	$arFilter = array("APPROVED" => "N");
	$TID = IntVal($_REQUEST["TID"]);
	$FID = IntVal($_REQUEST["FID"]);
	$action = trim($_REQUEST["ACTION"]);
	
//*************************!Проверка прав пользователя. Доступ к этой странице имеют только модераторы!************
	if (ForumCurrUserPermissions($FID)<"Q")
		$APPLICATION->AuthForm(GetMessage("FORUM_NO_PERMS"));
//*************************!***************************************************************************************
	$arForum = CForumNew::GetByID($FID);
	if (!$arForum)
	{
		LocalRedirect("index.php");
		die();
	}
	if ($TID > 0)
	{
		$arTopic = CForumTopic::GetByID($TID);
		if (!$arTopic)
			$TID = 0;
	}
	
	if ($TID > 0)	
		$arFilter["TOPIC_ID"] = $TID;
	else 
		$arFilter["FORUM_ID"] = $FID;
		
	if (($_SERVER["REQUEST_METHOD"]=="POST") && (strLen($action)>0))
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
						$bAction = true;
				break;
				case "SHOW":
				case "HIDE":
					if (ForumModerateMessageArray($message, $action, $strErrorMessage, $strOKMessage))
						$bAction = true;
				break;
			}
			if ($action)
			{
				$res = CForumMessage::GetList(array("ID"=>"ASC"), $arFilter);
				if ($res <= 0)
					LocalRedirect("list.php?FID=".$FID);
			}

		}
	}
//*************************!***************************************************************************************
	$APPLICATION->AddChainItem(htmlspecialchars($arForum["NAME"]), "read.php?FID=".$FID);
	$APPLICATION->SetTitle(str_replace("#TITLE#", htmlspecialchars($arForum["NAME"]), GetMessage("FAM_FORUM_PREF")));
	$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
	$arMenuParams = compact("FID", "TID");
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", $arMenuParams);
	echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));
	echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));
	$db_Message = CForumMessage::GetList(array("ID"=>"ASC"), $arFilter);
	$db_Message->NavStart($GLOBALS["FORUM_MESSAGES_PER_PAGE"], false);
	$db_Message->NavPrint(GetMessage("FORUM_POST"));
	?><script language="Javascript"><?
	if ($strJSPath = $APPLICATION->GetTemplatePath("forum/forum_tmpl_1/forum_js.php"))
		include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
	?></script><br><br>
	<form name="FORUM_MESSAGES" id="FORUM_MESSAGES">
		<?=bitrix_sessid_post()?>
	<br>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" class="forumtitle">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr valign="top">
						<td width="0%"><input type="checkbox" name="message_all" value="Y" id="message_all" onclick="SelectAllCheckBox('FORUM_MESSAGES', 'message_id[]', 'message_all');" checked></td>
						<td class="forumtitletext" width="0%">&nbsp;</td>
						<td class="forumtitletext" width="99%"><font class="forumtitletext"><b><?=GetMessage("FORUM_SELECT_ALL")?></b></font></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<font style="font-size:4px;">&nbsp;<br></font>
		
		<table width="100%" border="0" cellspacing="0" cellpadding="5"><?
		if ($db_Message && ($arMessage = $db_Message->Fetch()))
		{
			do
			{
				PrintMessage($arForum, $arTopic, $arMessage, $arCurrUser, array("mode" => "light", "checked" => true, "onclick" => "SelectCheckBox('message_all');"));
			}while ($arMessage = $db_Message->Fetch());
		}
		else 
			LocalRedirect("list.php?FID=".$FID);
		?></table>
	</form><?
	$db_Message->NavPrint(GetMessage("FORUM_POST"));
	?><br><br><?
	$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php", $arMenuParams);
	//*******************************************************
	else:
		?><font class="text"><b><?=GetMessage("FORUM_NO_MODULE")?></b></font><?
	endif;
else:
	$APPLICATION->AuthForm(GetMessage("FORUM_NO_PERMS"));
endif;

function PrintMessage($arForum, $arTopic, $arMessage, $arCurrUser, $addParams = array("mode"=>"full", "checked" =>false))
{
	global $USER, $APPLICATION;
	static $UserInfo = array();
	static $StaticArForum = array();
	static $StaticArTopic = array();
	$arUser = array();
	
//*************************!***************************************************************************************
	$mode = strToLower($addParams["mode"]);
	$mode = $mode == "light" ? "light" : "full";
	
	if (!is_array($arMessage) && intVal($arMessage) > 0)
		$arMessage = CForumMessage::GetByIDEx($arMessage);
	elseif(((is_array($arMessage)) && (count($arMessage) <=0)) || ((!is_array($arMessage)) && (intVal($arMessage) <= 0)))
		return false;
	
//	if (!is_array($arForum) || ((is_array($arForum)) && ((count($arForum) <= 0) || ($arForum["ID"] != $arMessage["FORUM_ID"]))))
	if (!is_array($arForum) || ((is_array($arForum)) && (count($arForum) <= 0)))
	{
		$FID = intVal($arMessage["FORUM_ID"]);
		if (!isset($StaticArForum[$FID]))
			$StaticArForum[$FID] = CForumNew::GetByID($FID);
		$arForum = $StaticArForum[$FID];
	}
	$FID = intVal($arForum["ID"]);
		
	if (!is_array($arTopic) || ((is_array($arTopic)) && (count($arTopic) <= 0)))
	{
		$TID = intVal($arMessage["TOPIC_ID"]);
		if (!isset($StaticArTopic[$TID]))
			$StaticArTopic[$TID] = CForumTopic::GetByID($TID);
		$arTopic = $StaticArTopic[$TID];
	}
	$TID = intVal($arTopic["ID"]);
	
	if ((count($arCurrUser) <= 0))
	{
		$arCurrUser = array();
		$arCurrUser["Rank"] = CForumUser::GetUserRank(IntVal($USER->GetParam("USER_ID")));
		$arCurrUser["bCanUserDeleteMessages"] = CForumTopic::CanUserDeleteTopicMessage($TID, $USER->GetUserGroupArray(), $USER->GetID());
		$arCurrUser["Perms"] = ForumCurrUserPermissions($FID);
	}
	else 
		$arCurrUser["Perms"] = ForumCurrUserPermissions($FID);
	
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
	// Здесь здорово было бы сделать проверку данных (int). Они выводятся без преобразования
	$arMessage["ID"] = intVal($arMessage["ID"]);
	$arMessage["AUTHOR_ID"] = intVal($arMessage["AUTHOR_ID"]);
	$arMessage["FORUM_ID"] = intVal($arMessage["FORUM_ID"]);
	$arMessage["TOPIC_ID"] = intVal($arMessage["TOPIC_ID"]);
	$arMessage["NUM_POSTS"]	= intVal($arMessage["NUM_POSTS"]);
	$arMessage["GUEST_ID"] = intVal($arMessage["GUEST_ID"]);
	$parser = new textParser(LANGUAGE_ID);
//	$arForum
	if ($mode == "full")
	{
		if (($arMessage["AUTHOR_ID"]>0) && (!isset($UserInfo[$arMessage["AUTHOR_ID"]])))
		{
			$arUser["Groups"] = CUser::GetUserGroup($arMessage["AUTHOR_ID"]);
			if (!in_array(2, $arUser["Groups"]))
				$arUser["Groups"][] = 2;
			$arUser["Perms"] = CForumNew::GetUserPermission($arMessage["FORUM_ID"], $arUser["Groups"]);
			if (($arUser["Perms"]<="Q") && (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y"))
				$arUser["Rank"] = CForumUser::GetUserRank($arMessage["AUTHOR_ID"], LANGUAGE_ID);
			if (is_set($arMessage, "POINTS"))
				$arUser["Points"] = array($arMessage["POINTS"], $arMessage["DATE_UPDATE"]);
			else
				$arUser["Points"] = CForumUserPoints::GetByID(IntVal($USER->GetParam("USER_ID")), $arMessage["AUTHOR_ID"]);
				
			$UserInfo[$arMessage["AUTHOR_ID"]] = $arUser;
		}
		elseif(($arMessage["AUTHOR_ID"]>0) && (isset($UserInfo[$arMessage["AUTHOR_ID"]])))
		{
			$arUser = $UserInfo[$arMessage["AUTHOR_ID"]];
		}
	}
	
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
						<td nowrap><input type="checkbox" name="message_id[]" value="<?=$arMessage["ID"]?>" id="message_id[<?=$arMessage["ID"]?>]" <?=($addParams["checked"] == true) ? "checked" : ""?> <?=(strlen(trim($addParams["onclick"])) > 3) ? "onclick =\"".htmlspecialcharsEx($addParams["onclick"])."\"" : ""?>> &nbsp;</td>
					<?endif;?>
						<td width="100%">
							<font class="forumbodytext">
							<font class="forumheadcolor"><?=GetMessage("FR_DATE_CREATE")?></font> 
							<nobr><?=htmlspecialcharsEx($arMessage["POST_DATE"]);?></nobr><br>
							</font>
						</td>
						<?if ($arCurrUser["Perms"]>="I" && $arTopic["STATE"]=="Y" && $mode == "full"):?>
							<td nowrap class="forummessbutton"><a class="forummessbuttontext" title="<?=GetMessage("FR_INSERT_NAME")?>" href="javascript:reply2author('<?=htmlspecialchars(str_replace("'", "\'", str_replace("\\", "\\\\", $arMessage["AUTHOR_NAME"]))) ?>,')"><?=GetMessage("FR_NAME")?></a></td>
							<td><div class="forummessbuttonsep"></div></td>
							<td nowrap class="forummessbutton"><a href="#postform" OnMouseDown="javascript:quoteMessageEx('<?=htmlspecialchars(str_replace("'", "\'", str_replace("\\", "\\\\", $arMessage["AUTHOR_NAME"]))) ?>')" title="<?=GetMessage("FR_QUOTE_HINT")?>" class="forummessbuttontext"><?=GetMessage("FR_QUOTE")?></a></td>
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
				<?if ($mode == "full"):?>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr valign="top">
						<td>
							<?if (($arMessage["AUTHOR_ID"]>0)):?>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td nowrap class="forummessbutton"><a href="view_profile.php?UID=<?=$arMessage["AUTHOR_ID"] ?>&FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"]?>" title="<?=GetMessage("FR_AUTHOR_PROFILE")?>" class="forummessbuttontext"><?=GetMessage("FR_PROFILE")?></a></td>
										<td><div class="forummessbuttonsep"></div></td>
										<?if (strlen($arMessage["EMAIL"])>0):?>
											<td nowrap class="forummessbutton"><a href="send_message.php?TYPE=MAIL&UID=<?=$arMessage["AUTHOR_ID"]; ?>" title="<?=GetMessage("FR_EMAIL_AUTHOR")?>" class="forummessbuttontext">E-Mail</a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;?>
										<?if ((strlen($arMessage["PERSONAL_ICQ"])>0) && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y")):?>
											<td nowrap class="forummessbutton"><a href="send_message.php?TYPE=ICQ&UID=<?=$arMessage["AUTHOR_ID"]; ?>" title="<?=GetMessage("FR_ICQ_AUTHOR")?>" class="forummessbuttontext">ICQ</a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;?>
										<?if ($USER->IsAuthorized()):?>
											<td nowrap class="forummessbutton"><a href="pm_message.php?mode=new&USER_ID=<?=$arMessage["AUTHOR_ID"]?>" title="<?=GetMessage("FR_PRIVATE_MESSAGE")?>"  class="forummessbuttontext"><?=GetMessage("FR_PRIVATE_MESSAGE")?></a></td>
											<td><div class="forummessbuttonsep"></div></td>
										<?endif;?>
										
										<?
										if (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y" && $USER->IsAuthorized()
											&& ($USER->IsAdmin() || IntVal($USER->GetParam("USER_ID"))!=$arMessage["AUTHOR_ID"]))
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
											<td nowrap class="forummessbutton"><a href="new_topic.php?FID=<?=$FID?>&TID=<?=$TID?>&MID=<?=$arMessage["ID"] ?>&MESSAGE_TYPE=EDIT" title="<?=GetMessage("FR_EDIT_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_EDIT")?></a></td>
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
				<?endif; // $mode == "full"?>
			</td>
		</tr>
		<tr class="forumpostsep">
			<td colspan="2"><!-- --></td>
		</tr>
		<?
	
}

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