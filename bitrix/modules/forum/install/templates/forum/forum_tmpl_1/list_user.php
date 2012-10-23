<?
//*****************************************************************************************************************
//	Список пользователей, тем, cообщений пользователя. Публичная часть.
//*****************************************************************************************************************
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
	
	if ($USER->IsAuthorized()):
//***************************!initialization************************************************************************
		ForumSetLastVisit();
		extract($_REQUEST, EXTR_SKIP);
		$mode = strToLower($mode);
		$UID = intval($UID);
		if ($UID <= 0)
			$mode = "";
			
		function PrintMessages($mass, $arUser)
		{
			global $USER;
			$parser = new textParser(LANGUAGE_ID);
			
		?><table width="100%" border="0" cellspacing="0" cellpadding="5"><?
			if (count($mass)<=0):
			?><tr><td class="forumtitle" colspan="2">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr valign="top">
					<td width="99%" class="forumtitletext"><b><?=GetMessage("LU_NONE_MESSAGE")?></b></td>
				</tr></table></td></tr><?
			else:
				foreach ($mass as $arForum)
				{
					$strPerms = ForumCurrUserPermissions($arForum["ID"]);
					if (($strPerms<"E") || ($arForum["ACTIVE"]!="Y"))
						continue;
					?><tr><td colspan="2"><font style="font-size:4px;">&nbsp;<br></font></td></tr><?
					?><tr><td class="forumtitle" colspan="2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr valign="top">
							<td width="99%" class="forumtitletext"><?=GetMessage("FL_FORUM")?> <a href="list.php?FID=<?=$arForum["ID"]?>"><b><?=$arForum["NAME"];?></b></a></td>
							<td nowrap width="1%" align="right" class="forumtitletext"><?=GetMessage("LU_USER_POSTS_ON_FORUM")?>: <?=$arForum["NUM_POSTS_ALL"]?></td>
						</tr></table></td></tr><?
					?><tr><td colspan="2"><font style="font-size:4px;">&nbsp;<br></font></td></tr><?
					
					foreach ($arForum["TOPIC"] as $arTopic)
					{
						if ($strPerms<"Q" && $arTopic["APPROVED"]!="Y") 
							continue;
						?><tr><td class="forumtitle" colspan="2">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" background="red">
								<tr valign="top">
									<?if (strlen($arTopic["IMAGE"])>0):?>
										<td width="0%"><img src="/bitrix/images/forum/icon/<?=$arTopic["IMAGE"];?>" alt="<?=$arTopic["IMAGE_DESCR"]?>" border="0" width="15" height="15" vspace="0"><br></td>
										<td class="forumtitletext" width="0%">&nbsp;</td>
									<?endif;?>		
									<td class="forumtitletext" width="99%"><font class="forumtitletext">
										<?=GetMessage("FR_TOPIC")?> &laquo;<a href="read.php?FID=<?=$arForum["ID"]?>&TID=<?=$arTopic["ID"]?>&UID=<?=$arUser["USER_ID"]?>"><b><?=htmlspecialcharsEx($arTopic["TITLE"]);?></b><?
										if (strlen($arTopic["DESCRIPTION"])>0)
											echo ", ".htmlspecialcharsEx($arTopic["DESCRIPTION"]);
										?></a >&raquo;  
										<?=GetMessage("FR_ON_FORUM")?> <a href="list.php?FID=<?=$arForum["ID"]?>"><b><?=$arForum["NAME"] ?></b></a></font></td>
									<td nowrap width="1%" align="right" valign="middle" class="forumtitletext"><?=GetMessage("FR_ON_VIEWS")?> <?=$arTopic["VIEWS"]?><br>
									<?=GetMessage("LU_USER_POSTS_ON_TOPIC")?>: <?=$arTopic["COUNT_MESSAGE"]?></td>
								</tr>
							</table>
						</td></tr><?
					?><tr><td colspan="2"><font style="font-size:4px;">&nbsp;<br></font></td></tr><?
					
						
						foreach ($arTopic["MESSAGE"] as $arMessage)
						{
						if ($strPerms<"Q" && $arMessage["APPROVED"]!="Y") 
							continue;
							
					?><tr valign="top" class="forumbody">
						<td align="left" rowspan="2" width="140" class="forumbrd" style="border-right:none;">
							<a name="message<?=$arMessage["ID"];?>"></a>
							<font class="forumbodytext"><b><?=htmlspecialcharsEx($arMessage["AUTHOR_NAME"]); ?></b>
							<br><font class=\"forumheadcolor\"><?=htmlSpecialCharsEx($arForum["USER_PERM_STR"])?></font>
							<br>
							<?if (strlen($arUser["AVATAR"])>0):?>
								<a href="view_profile.php?UID=<?=$arUser["USER_ID"] ?>&FID=<?=$arForum["ID"]?>&TID=<?=$arTopic["ID"]?>&MID=<?=$arMessage["ID"]?>" title="<?=GetMessage("FR_AUTHOR_PROFILE")?>"><? echo CFile::ShowImage($arUser["AVATAR"], 90, 90, "border=0 vspace=5", "", true)?></a><br>
							<?endif;?>
							<?if (strlen($arUser["DESCRIPTION"])>0):?>
								<i><?=htmlspecialcharsEx($arUser["DESCRIPTION"]);?></i><br>
							<?endif;?>
							<font style="font-size:8px;">&nbsp;<br></font>
							<?if (IntVal($arUser["NUM_POSTS"])>0):?>
								<font class="forumheadcolor"><?=GetMessage("FR_NUM_MESS")?></font> <?=$arUser["NUM_POSTS"];?><br>
							<?endif;?>
							<?if (strlen($arUser["DATE_REG"])>0):?>
								<font class="forumheadcolor"><?=GetMessage("FR_DATE_REGISTER")?></font> <?=$arUser["DATE_REG"];?><br>
							<?endif;?>
							</font>
						</td>
						<td class="forumbrd forumbrd1" style="border-bottom:none;">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr><td width="100%"><font class="forumbodytext"><font class="forumheadcolor"><?=GetMessage("FR_DATE_CREATE")?></font><nobr><?=$arMessage["POST_DATE"];?></nobr><br></font></td></tr></table>
							<font style="font-size:5px;">&nbsp;<br></font>
							<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
							<font style="font-size:8px;">&nbsp;<br></font>
							<font class="forumbodytext">
							<?
							$arForum["ALLOW"]["SMILES"] = $arForum["ALLOW_SMILES"];
							if ($arMessage["USE_SMILES"]!="Y") 
								$arForum["ALLOW"]["SMILES"] = "N";
							
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
							if (strlen($arUser["SIGNATURE"])>0)
							{
								echo "<br><br><font class=\"forumsigntext\">";
								$arForum["ALLOW"]["SMILES"] = "N";
								echo $parser->convert($arUser["SIGNATURE"], $arForum["ALLOW"]);
								echo "</font>";
							}
							?>
							</font>
						</td>
					</tr>
					<tr>
						<td valign="bottom" class="forumbody forumbrd forumbrd1" style="border-top:none;">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr><td class="forumhr"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td></tr></table>
							<font style="font-size:5px;">&nbsp;<br></font>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr valign="top">
									<td>
										<table border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td nowrap class="forummessbutton"><a href="view_profile.php?UID=<?=$arMessage["AUTHOR_ID"] ?>&FID=<?=$arForum["ID"]?>&TID=<?=$arTopic["ID"]?>&MID=<?=$arMessage["ID"]?>" title="<?=GetMessage("FR_AUTHOR_PROFILE")?>" class="forummessbuttontext"><?=GetMessage("FR_PROFILE")?></a></td>
												<td><div class="forummessbuttonsep"></div></td>
												<?if (strlen($arUser["EMAIL"])>0):?>
													<td nowrap class="forummessbutton"><a href="send_message.php?TYPE=MAIL&UID=<?=$arMessage["AUTHOR_ID"]; ?>" title="<?=GetMessage("FR_EMAIL_AUTHOR")?>" class="forummessbuttontext">E-Mail</a></td>
													<td><div class="forummessbuttonsep"></div></td>
												<?endif;?>
												<?if ((strLen($arUser["PERSONAL_ICQ"])>0) && (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y")):?>
													<td nowrap class="forummessbutton"><a href="send_message.php?TYPE=ICQ&UID=<?=$arMessage["AUTHOR_ID"]; ?>" title="<?=GetMessage("FR_ICQ_AUTHOR")?>" class="forummessbuttontext">ICQ</a></td>
													<td><div class="forummessbuttonsep"></div></td>
												<?endif;?>
												<?if ($USER->IsAuthorized()):?>
													<td nowrap class="forummessbutton"><a href="pm_message.php?mode=new&USER_ID=<?=$arMessage["AUTHOR_ID"]?>" title="<?=GetMessage("FR_PRIVATE_MESSAGE")?>"  class="forummessbuttontext">P-Mess</a></td>
													<td><div class="forummessbuttonsep"></div></td>
												<?endif;?>
												<?
											?></tr>
										</table>
										<?
										if ((ForumCurrUserPermissions($arForum["ID"]) >="Q")
											|| 	($arTopic["LAST_TOPIC_MESSAGE"] == IntVal($arMessage["ID"]) 
													&& $USER->IsAuthorized() 
													&& (IntVal($ar_Message["AUTHOR_ID"]) == IntVal($USER->GetParam("USER_ID"))))
											|| $arTopic["USER_PERM_DELETE"]):?>
											<font style="font-size:4px;">&nbsp;<br></font>
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<?if ($arMessage["APPROVED"]=="Y" && ForumCurrUserPermissions($arForum["ID"]) >= "Q"):?>
														<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$arForum["ID"]?>&TID=<?=$arTopic["ID"]; ?>&MID=<?=$arMessage["ID"] ?>&ACTION=HIDE&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_HIDE_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_HIDE")?></a></td>
														<td><div class="forummessbuttonsep"></div></td>
													<?elseif (ForumCurrUserPermissions($arForum["ID"])>="Q"):?>
														<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$arForum["ID"]; ?>&TID=<?=$arTopic["ID"]; ?>&MID=<?=$arMessage["ID"] ?>&ACTION=SHOW&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_SHOW_MESS")?>" class="forummessbuttontext"><i><b><?=GetMessage("FR_SHOW")?></b></i></a></td>
														<td><div class="forummessbuttonsep"></div></td>
													<?endif;?>
													<?if (ForumCurrUserPermissions($arForum["ID"]) >= "U"
														|| ($arTopic["LAST_TOPIC_MESSAGE"] == IntVal($arMessage["ID"])
															&& $USER->IsAuthorized()
															&& (IntVal($arMessage["AUTHOR_ID"]) == IntVal($USER->GetParam("USER_ID"))))):?>
														<td nowrap class="forummessbutton"><a href="new_topic.php?FID=<?=$arForum["ID"]; ?>&TID=<?=$arTopic["ID"]; ?>&MID=<?=$arMessage["ID"] ?>&MESSAGE_TYPE=EDIT" title="<?=GetMessage("FR_EDIT_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_EDIT")?></a></td>
														<td><div class="forummessbuttonsep"></div></td>
													<?endif;?>
													<?if ($arTopic["USER_PERM_DELETE"]):?>
														<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$arForum["ID"]; ?>&TID=<?=$arTopic["ID"]; ?>&MID=<?=$arMessage["ID"] ?>&ACTION=DEL&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_DELETE_MESS")?>" class="forummessbuttontext"><?=GetMessage("FR_DELETE")?></a></td>
														<td><div class="forummessbuttonsep"></div></td>
														<?if(IntVal($arMessage["AUTHOR_ID"])>0 && CModule::IncludeModule("support")):?>
															<td nowrap class="forummessbutton"><a href="read.php?FID=<?=$arForum["ID"]; ?>&TID=<?=$arTopic["ID"]; ?>&MID=<?=$arMessage["ID"] ?>&ACTION=FORUM_MESSAGE2SUPPORT&<?=bitrix_sessid_get()?>" title="<?=GetMessage("FR_MOVE2SUPPORT")?>" class="forummessbuttontext"><?=GetMessage("FR_2SUPPORT")?></a></td>
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
						</td>
					</tr><?	
					?><tr><td colspan="2"><font style="font-size:4px;">&nbsp;<br></font></td></tr><?
					}
				}
			}
			endif;
			?></table><?
		}
			
		$ForumsPerms = array("Q"=>GetMessage("LU_USER_Q"), "U"=>GetMessage("LU_USER_U"), "Y"=>GetMessage("LU_USER_Y"), "user"=>GetMessage("LU_USER_USER"));
			
		$APPLICATION->ResetException();
		
		$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
		?><font style="font-size:4px;">&nbsp;<br></font><?
	
//***************************!mode*********************************************************************
		switch($mode)
		{
			
			case "lta":
			case "lt":
				$arUser = CForumUser::GetListEx(array(),array("USER_ID"=>$UID));
					$arUser = $arUser->GetNext();
				$Filter = array("AUTHOR_ID"=>$UID);
				if ($mode == "lta")
					$Filter = array_merge($Filter, array("USER_START_ID"=>$UID));

				$arForum_posts = array();
				$ArrForum = array();
				$ArrForumPerm = array();

				$db_topics = CForumUser::UserAddInfo(array("FORUM_ID"=>"ASC", "TOPIC_ID"=>"ASC"), $Filter, "topics");
				$db_topics->NavStart($PAGE_ELEMENTS, false);
				
				if ($db_topics && ($arTopic = $db_topics->GetNext())):
				
					//**********************************************************************************************
					// User right`s
					$arUserGroup = CUser::GetUserGroup($UID);
					if (!in_array(2, $arUserGroup)) 
						$arUserGroup[] = 2;
						
					$arForum = array();
					$main = array();
					//**********************************************************************************************
					do
					{
						if ($arForum["ID"] != $arTopic["FORUM_ID"]):
							$arForum = CForumNew::GetByIDEx($arTopic["FORUM_ID"]);
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
							
							$UserPerm = CForumNew::GetUserPermission($arForum["ID"], $arUserGroup);
							if (array_key_exists($UserPerm, $ForumsPerms))
								$UserPermStr = $ForumsPerms[$UserPerm];
							elseif (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y")
							{
								$arUserRank = CForumUser::GetUserRank($UID, LANGUAGE_ID);
								$UserPermStr = $arUserRank["NAME"];
							}
							$UserPermStr = strlen(trim($UserPermStr)) > 0 ? $UserPermStr : $ForumsPerms["user"];
							// 
							$main[$arForum["ID"]] = $arForum;
							$main[$arForum["ID"]]["ALLOW"] = $arAllow;
							$main[$arForum["ID"]]["USER_PERM"] = $UserPerm;
							$main[$arForum["ID"]]["USER_PERM_STR"] = $UserPermStr;
						endif;
					//**********************************************************************************************
						// Topics
//						$arTopic = array_merge($arTopic, CForumTopic::GetByIDEx($arTopic["TOPIC_ID"]));
						$arTopic["ID"] = $arTopic["TOPIC_ID"];

						$main[$arForum["ID"]]["TOPIC"][$arTopic["ID"]] = $arTopic;
						// Message
						$main[$arForum["ID"]]["TOPIC"][$arTopic["ID"]]["MESSAGE"][] = CForumMessage::GetByID($arTopic["FIRST_POST"]);
						// User	
						$main[$arForum["ID"]]["TOPIC"][$arTopic["ID"]]["USER_PERM_DELETE"] = ForumCurrUserPermissions($arTopic["FORUM_ID"]) < "U" ? false : true;
						$main[$arForum["ID"]]["TOPIC"][$arTopic["ID"]]["LAST_TOPIC_MESSAGE"] = false;
						$main[$arForum["ID"]]["NUM_POSTS_ALL"] += intVal($arTopic["COUNT_MESSAGE"]);
					}while ($arTopic = $db_topics->GetNext());
				endif;
				$db_topics->NavPrint(GetMessage("LU_TITLE_TOPIC"));
				PrintMessages($main, $arUser);
				$db_topics->NavPrint(GetMessage("LU_TITLE_TOPIC"));
				
			break;
			
			
			
			case "all":
				// Информация о пользователе 1 запрос
				$arUser = CForumUser::GetListEx(array(),array("USER_ID"=>$UID));
					$arUser = $arUser->GetNext();
					
				$db_topics = CForumUser::UserAddInfo(array("FORUM_ID"=>"ASC"), array("AUTHOR_ID"=>$UID), "topics");
				
				$arForum_posts = array();
				$arTopic_posts = array();
				$FilterMess = array();
				$arTopics_info = array();
				
				if ($db_topics)
				{
					while ($res = $db_topics->Fetch())
					{
						$arTopic_posts[$res["TOPIC_ID"]] = $res["COUNT_MESSAGE"];
						$arForum_posts[$res["FORUM_ID"]] += intVal($res["COUNT_MESSAGE"]);
						$arTopics_info[$res["TOPIC_ID"]] = $res;
						$arTopics_info[$res["TOPIC_ID"]]["ID"] = $res["TOPIC_ID"];
						
						$strPerms = ForumCurrUserPermissions($res["FORUM_ID"]);
						
						$FilterMess[] = $strPerms < "Q" ? array("TOPIC_ID"=>$res["TOPIC_ID"], "APPROVED"=>"Y") : array("TOPIC_ID"=>$res["TOPIC_ID"]);
						$arTopics_info[$res["TOPIC_ID"]]["USER_PERM_DELETE"] = $strPerms < "U" ? false : true;
					}
				}
				
				//Message
				// Общий список сообщений 1 запрос 
				$main = array();
				$dbMessage = false;
				if (count($FilterMess) >0)
				{
					$dbMessage = CForumMessage::GetList(array("FORUM_ID"=>"ASC", "TOPIC_ID"=>"ASC"), array("AUTHOR_ID"=>$UID, "PERMISSION"=>$FilterMess));
				
					$dbMessage->NavStart($PAGE_ELEMENTS,false);
					if ($dbMessage)
					{
						$arMessage = array();
						$arTopic = array();
						$arForum = array();

						// User right`s
						$arUserGroup = CUser::GetUserGroup($UID);
						if (!in_array(2, $arUserGroup)) 
							$arUserGroup[] = 2;
						
						while ($arMessage = $dbMessage->Fetch())
						{
							if ($arForum["ID"] != $arMessage["FORUM_ID"]):
								// Информация о форуме 1 запрос
								$arForum = CForumNew::GetByIDEx($arMessage["FORUM_ID"]);
								$arForum["NUM_POSTS_ALL"] = $arForum_posts[$arForum["ID"]];
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
								
									
								// Звание пользователя из списка на форуме 1 запрос
								$UserPerm = CForumNew::GetUserPermission($arForum["ID"], $arUserGroup);
								if (array_key_exists($UserPerm, $ForumsPerms))
									$UserPermStr = $ForumsPerms[$UserPerm];
								elseif (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y")
								{
									$arUserRank = CForumUser::GetUserRank($UID, LANGUAGE_ID);
									$UserPermStr = $arUserRank["NAME"];
								} 
								$UserPermStr = strlen(trim($UserPermStr)) > 0 ? $UserPermStr : $ForumsPerms["user"];
								// 
								$main[$arForum["ID"]] = $arForum;
								$main[$arForum["ID"]]["ALLOW"] = $arAllow;
								$main[$arForum["ID"]]["USER_PERM"] = $UserPerm;
								$main[$arForum["ID"]]["USER_PERM_STR"] = $UserPermStr;
							endif;
							
							if ($arTopic["ID"] != $arMessage["TOPIC_ID"]):
								// Информация о темах по одному запросу на каждую тему.
//								$arTopic = CForumTopic::GetByIDEx($arMessage["TOPIC_ID"]);
								$arTopic = $arTopics_info[$arMessage["TOPIC_ID"]];
								$arTopic["COUNT_MESSAGE"] = $arTopic_posts[$arTopic["ID"]];
								//Message
								$arTopic["LAST_TOPIC_MESSAGE"] = false;
								$main[$arForum["ID"]]["TOPIC"][$arTopic["ID"]] = $arTopic;
							endif;
							$main[$arForum["ID"]]["TOPIC"][$arTopic["ID"]]["MESSAGE"][] = $arMessage;
						}
						
					}
				}
			if ($dbMessage)
				$dbMessage->NavPrint(GetMessage("LU_TITLE_POSTS"));
				PrintMessages($main, $arUser);
			if ($dbMessage)
				$dbMessage->NavPrint(GetMessage("LU_TITLE_POSTS"));
			break;
			
			
			
			default: 
				InitSorting();
				global $by, $order;
				if (!$by)
				{
					$by = "NUM_POSTS";
					$order = "DESC";
				}
				$db_res = CForumUser::GetList(array($by=>$order), array("!USER_ID"=>"0", "SHOW_ABC"=>""));
				if($db_res)
				{
					$db_res->NavStart($PAGE_ELEMENTS, false);

				if ($res = $db_res->GetNext())
				{
					$db_Forums = CForumNew::GetList();
					$Forums = array();
					while ($res_forum = $db_Forums->Fetch())
						$Forums[] = $res_forum;
					
					$db_res->NavPrint(GetMessage("LU_TITLE_USER"));
					?><br><br>
					<table border="0" cellspacing="1" cellpadding="0" class="forumborder" width="100%">
					  <tr>
						<td>
						  <table width="100%" border="0" cellspacing="1" cellpadding="4">
							<tr class="forumhead">
								<td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_NAME")?>&nbsp;<br><?=SortingEx("SHOW_ABC")?></td>
								<td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_RANK")?></td><?
								?><td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_POST")?>&nbsp;<br><?=SortingEx("NUM_POSTS")?></td><?
								?><td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_POINTS")?>&nbsp;<br><?=SortingEx("POINTS")?></td><?
								?><td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_DATE_REGISTER")?>&nbsp;<br><?=SortingEx("DATE_REGISTER")?></td>
								<td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_LAST_VISIT")?>&nbsp;<br><?=SortingEx("LAST_VISIT")?></td>
								<td align="center" nowrap class="forumheadtext" colspan=<?=((COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y") ? "3":"2")?>><?=GetMessage("FLU_HEAD_CONTACTS")?></td>
								<td align="center" nowrap class="forumheadtext"><?=GetMessage("FLU_HEAD_AVATAR")?></td>
							</tr><?
						do
						{
							
							$arUserGroup = CUser::GetUserGroup($res["USER_ID"]);
							if (!in_array(2, $arUserGroup)) 
								$arUserGroup[] = 2;
							sort($arUserGroup);
							$UserP = array(); $UserPermStr = "";
							foreach ($Forums as $forum)
							{
								$UserP[] = CForumNew::GetUserPermission($forum["ID"], $arUserGroup);
							}
							rsort($UserP);
							if (array_key_exists($UserP[0], $ForumsPerms))
								$UserPermStr = $ForumsPerms[$UserP[0]];
							elseif (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y")
							{
								$arUserRank = CForumUser::GetUserRank($res["USER_ID"], LANGUAGE_ID);
								$UserPermStr = $arUserRank["NAME"];
							} 
							$UserPermStr = strlen(trim($UserPermStr)) > 0 ? $UserPermStr : $ForumsPerms["user"];
							?><tr class="forumbody">
								<td align="left" class="forumbodytext"><a href="view_profile.php?UID=<?=$res["USER_ID"]?>" title="<?=GetMessage("FMI_FORUM_PROFILE")?>"><?=$res["SHOW_ABC"]?></a></td>
								<td align="left" class="forumbodytext"><?=htmlSpecialCharsEx($UserPermStr)?></td><?
								?><td align="center" class="forumbodytext"><?
									?><a class="forumtoolbutton" href="?UID=<?=$res["USER_ID"]?>&mode=all" title=""><?=intVal($res["NUM_POSTS"])?></a><?
								?></td>
								<td align="center" class="forumbodytext"><?=$res["POINTS"]?></td>
								<td align="center" class="forumbodytext"><?=$res["DATE_REGISTER_SHORT"]?></td>
								<td align="center" class="forumbodytext"><?=$res["LAST_VISIT_SHORT"]?></td>
								<td align="center" class="forumbodytext"><a class="forumtoolbutton" href="pm_message.php?mode=new&USER_ID=<?=$res["USER_ID"]?>" title="<?=GetMessage("FLU_PMESS_ALT")?>"><?=GetMessage("FLU_PMESS")?></a></td>
								<td align="center" class="forumbodytext"><a class="forumtoolbutton" href="send_message.php?TYPE=MAIL&UID=<?=$res["USER_ID"]?>" title="<?=GetMessage("FLU_EMAIL_ALT")?>"><?=GetMessage("FLU_EMAIL")?></a></td>
								<?if (COption::GetOptionString("forum", "SHOW_ICQ_CONTACT", "N") == "Y"):?>
								<td align="center" class="forumbodytext"><?
									if (strlen($res["PERSONAL_ICQ"])>0):
									 ?><a class="forumtoolbutton" href="send_message.php?TYPE=ICQ&UID=<?=$res["USER_ID"]?>" title="<?=GetMessage("FLU_ICQ_ALT")?>"><?=GetMessage("FLU_ICQ")?></a><?
									else:
									 ?><font class="forumtoolbutton" title="<?=GetMessage("FLU_ICQ_NO_ALT")?>"><?=GetMessage("FLU_ICQ")?></a></font><?
									endif;
								?></td>
								<?endif;?>
								<td align="center" class="forumbodytext" nowrap>
									<?
									if (strLen($res["AVATAR"])>0)
										echo CFile::ShowImage($res["AVATAR"], 20, 20, "border=0", "", true);
								?></td></tr><?
						}while($res = $db_res->GetNext());
						?></table>
						</td></tr></table><br><?
						$db_res->NavPrint(GetMessage("LU_TITLE_USER"));
				}
				}
					else 
					{
						?><table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr class="forumbody"><td align="left" class="forumbodytext"><?=GetMessage("PM_EMPTY_FOLDER")?></td></tr></table><?
					}
					break;
			}
		
		?><br><br><br><?
		$Title = GetMessage("FLU_TITLE_".strToUpper($mode));
		if (in_array($mode, array("lt", "lta", "all")))
		{
			if ($arUser["SHOW_NAME"] == "Y" && (strlen(trim($arUser["NAME"]))>0 || strlen(trim($arUser["LAST_NAME"]))>0))
				$Title .= " &laquo;".trim($arUser["NAME"]." ".$arUser["LAST_NAME"])."&raquo;";
			else 
				$Title .= " &laquo;".trim($arUser["NAME"]." ".$arUser["LOGIN"])."&raquo;";
		}	
		$APPLICATION->SetTitle($Title);
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
		
	else: 
		LocalRedirect("index.php");
	endif;
	
else:
	?><table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr class="forumbody"><td align="left" class="forumbodytext"><?GetMessage("PM_NO_MODULE")?></td></tr></table><?
endif;
?>