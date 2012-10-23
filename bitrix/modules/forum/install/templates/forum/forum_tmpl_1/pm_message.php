<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//******************************Functions/******************************
if(!function_exists("GetUserName"))
{
	function GetUserName($USER_ID)
	{
		$ar_res = false;
		if (IntVal($USER_ID)>0)
		{
			$db_res = CUser::GetByID(IntVal($USER_ID));
			$ar_res = $db_res->Fetch();
		}

		if (!$ar_res)
		{
			$db_res = CUser::GetByLogin($USER_ID);
			$ar_res = $db_res->Fetch();
		}

		$USER_ID = IntVal($ar_res["ID"]);
		$f_LOGIN = htmlspecialcharsex($ar_res["LOGIN"]);

		$forum_user = CForumUser::GetByUSER_ID($USER_ID);
		if (($forum_user["SHOW_NAME"]=="Y") && (strlen(trim($ar_res["NAME"]))>0 || strlen(trim($ar_res["LAST_NAME"]))>0))
		{
			return trim(htmlspecialcharsex($ar_res["NAME"])." ". htmlspecialcharsex($ar_res["LAST_NAME"]));
		}
		else
			return $f_LOGIN;
	}
}
//******************************Functions\******************************
//**********************************************************************
	
	ForumSetLastVisit();
	if ($USER->IsAuthorized()):
//***************************input params/*****************************
		extract($_REQUEST, EXTR_SKIP);
		$UID = $USER->GetID();
		$FID = intVal($FID);
		if (!$FID)
			$FID = 1;
		$MID = intVal($MID);
		$USER_ID = intVal($USER_ID);
		if($_REQUEST["DELETE"])
			$action = "delete";
		if (empty($mode))
			$mode = "list";
//***************************/input params*****************************
//***************************initialization/*****************************
		$strError = "";
		$strNote = "";
		switch ($result) {
			case "sent":
				$strNote = GetMessage("PM_SUCC_SENT"); 
				$FID=1; 
				$mode="list";
				break;
			case "delete":
				$strNote = GetMessage("PM_SUCC_DELETE"); 
				break;
			case "save":
				$strNote = GetMessage("PM_SUCC_SAVE");
				break;
			case "copy":
			case "move":
				$strNote = GetMessage("PM_SUCC_REPLACE"); 
				break;
			default:
				$strNote = ""; 
				break;
		}
		$APPLICATION->ResetException();
		$APPLICATION->ThrowException(" ");
//***************************/initialization*****************************
//***************************/action*****************************
		switch($action)
		{
			case "delete":
				if (!is_array($message))
					$message = array($MID);
				foreach ($message as $MID) 
				{
					if (CForumPrivateMessage::CheckPermissions($MID))
						if(!CForumPrivateMessage::Delete($MID, array("FOLDER_ID"=>4,)))
							$strError .= str_replace("##", $MID, GetMessage("PM_MESSAGE_NOT_DELETE"))."\n";
				}
				if (strlen($strError) == 0)
					LocalRedirect($APPLICATION->GetCurPage()."?result=delete&FID=".$FID."&mode=list");
				break;
			case "send":
					if(($_SERVER['REQUEST_METHOD']=="POST")&&((intval($USER_ID)>0)||(strLen(trim($USER_LOGIN))>0)))
					{
						$USER_INFO = CForumUser::GetByUSER_ID(intval($USER_ID));
						if (empty($USER_INFO))
							$USER_INFO = CForumUser::GetByLogin(trim($USER_LOGIN));
						if(!empty($USER_INFO))
						{
							$arrVars = array(
								"AUTHOR_ID" => $USER->GetID(),
								"POST_SUBJ" => $POST_SUBJ,
								"POST_MESSAGE" => $POST_MESSAGE,
								"USE_SMILES" => $USE_SMILES,
								"USER_ID" => $USER_INFO["USER_ID"],
								);
							if($MID = CForumPrivateMessage::Send($arrVars))
							{
								$recipient = CUser::GetById(intval($USER_INFO["USER_ID"]));
								$recipient = $recipient->GetNext();
								if ($recipient["EMAIL"])
								{
									$author = CUser::GetById($arrVars["AUTHOR_ID"]);
									$author = $author->GetNext();
									$author = array_merge($author, CForumUser::GetByUSER_ID(intval($arrVars["AUTHOR_ID"])));
									$event = new CEvent;
									$arFields = Array(
										"FROM_NAME" => $author["SHOW_NAME"] == "Y" ? $author["NAME"]." ".$author["LAST_NAME"] : $author["LOGIN"],
										"FROM_USER_ID" => $arrVars["AUTHOR_ID"],
										"FROM_EMAIL" => $author["EMAIL"] != "" ? $author["EMAIL"] : "",
										"TO_NAME" => $USER_INFO["SHOW_NAME"] == "Y" ? $recipient["NAME"]." ".$recipient["LAST_NAME"] : $recipient["LOGIN"],
										"TO_USER_ID" => $USER_INFO["USER_ID"],
										"TO_EMAIL" => $recipient["EMAIL"],
										"SUBJECT" => $POST_SUBJ,
										"MESSAGE" => $POST_MESSAGE,
										"MESSAGE_DATE" => date("d.m.Y H:i:s"),
										"MESSAGE_LINK" => "http://#SERVER_NAME#".$APPLICATION->GetCurPage()."?FID=0&MID=".$MID."&mode=read"
									);
									$event->Send("NEW_FORUM_PRIVATE_MESSAGE", SITE_ID, $arFields, "N");
								}
								LocalRedirect($APPLICATION->GetCurPage()."?result=sent");
							}
						}
						else 
							$strError = str_replace("##", htmlspecialchars($USER_LOGIN), GetMessage("PM_USER_NOT_FOUND"));
					}
					else 
						$strError = GetMessage("PM_USER_ABSENT");
				break;
			case "save":
					if($_SERVER['REQUEST_METHOD']=="POST")
					{
						if (CForumPrivateMessage::CheckPermissions($MID))
						{
							$arrVars = array(
								"POST_SUBJ" => $POST_SUBJ,
								"POST_MESSAGE" => $POST_MESSAGE,
								"USE_SMILES" => $USE_SMILES,
								);
								if(CForumPrivateMessage::Update($MID, $arrVars))
									LocalRedirect($APPLICATION->GetCurPage()."?result=save&MID=".$MID."&FID=".$FID."&mode=read");
						}
						else 
							$strError = GetMessage("PM_NOT_RIGHT");
					}
					else 
						$strError = GetMessage("PM_NO_DATA");
				break;
			case "copy":
			case "move":
				if (!is_array($message))
				{
					$message = array();
					if (intVal($MID) > 0)
						$message = array($MID);
				}
				if(($_SERVER['REQUEST_METHOD']=="POST")&&(!empty($message)))
				{
					foreach ($message as $MID) 
					{
						if (CForumPrivateMessage::CheckPermissions($MID))
						{
							if ($FOLDER_ID > 0)
							{
								$arrVars = array(
									"FOLDER_ID" => intVal($FOLDER_ID),
									"USER_ID" => $USER->GetId(),
									"IS_READ" => "Y",
									);
								if ($action == "move")
									CForumPrivateMessage::Update($MID, $arrVars);
								else 
									CForumPrivateMessage::Copy($MID, $arrVars);
							}
							else 
								$strError = GetMessage("PM_FOLDER_NOT_SELECT");
						}
						else 
							$strError = GetMessage("PM_NOT_RIGHT");
					}
				}
				else 
					$strError = GetMessage("PM_NO_DATA");
				if (strlen($strError) == 0)
						LocalRedirect($APPLICATION->GetCurPage()."?result=".$action."&FID=".$FOLDER_ID."&mode=list");

				break;
		}
//***************************/action*****************************
			
		
//***************************output/*****************************
		$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
		$count = CForumPrivateMessage::PMSize($USER->GetId(), COption::GetOptionInt("forum", "MaxPrivateMessages", 100));
		echo "<table width='100%' border='0'><tr><td align='right'>
			<table width=200 cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width='100%'>
					<div class=out>
						<div class=in style='width:".round($count*100)."%;'>&nbsp;</div>
					</div>
					<div class=out1>
						<div class=in1 align=center style='width:100%'>".GetMessage("PM_POST_FULLY")." ".round($count*100)."%</div>
					</div>
					</td>
				</tr>
			</table>
		</td></tr></table>";
			if ($mode == "list")
			{
				if ($FID < 5)
					$title = GetMessage("PM_FOLDER_ID_".$FID);
				else 
					$title = GetMessage("PM_FOLDER_ID_5");
			}
				
			else $title = GetMessage("PM_".$mode);
		?><font style="font-size:4px;">&nbsp;<br></font><?
		// Вывод ошибок и неошибок
		$err = $APPLICATION->GetException();
		$strError .= $err->GetString();
		ShowError($strError);
		ShowNote($strNote);		
		// Вывод страниц
//***************************mode/*****************************
		if($mode == "read")
		{
			$dbrMessage = CForumPrivateMessage::GetById($MID);
			if(($arMessage = $dbrMessage->GetNext()) && CForumPrivateMessage::CheckPermissions($MID))
			{
				if(($arMessage["IS_READ"]!="Y")&&($FID != 2))
					CForumPrivateMessage::MakeRead($MID);
				$parser = new textParser(LANGUAGE_ID);
				
				$StatusUser = "AUTHOR";
				$InputOutput = "AUTHOR_ID";
				if (intVal($FID) <= 1)
				{
					$StatusUser = "SENDER";
					$InputOutput = "AUTHOR_ID";
				}
				elseif ((intVal($FID) > 1) && (intVal($FID) <=3))
				{
					$StatusUser = "RECIPIENT";
					$InputOutput = "RECIPIENT_ID";
				}
									
				$title = GetMessage("PM_FOLDER_ID_".$FID).GetMessage("PM_SEPARATOR").str_replace("##", $arMessage["POST_SUBJ"], GetMessage("PM_read"));
				
				?><table border="0" width="100%" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
					<table border="0" cellpadding="3" cellspacing="1" width="100%">
						<tr>
							<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?=GetMessage("PM_HEAD_".$StatusUser)?></b></font></td>
						</tr>
						<tr>
							<td class="forumbody" width="5%"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_NAME_LOGIN")?></font></td>
							<td class="forumbody" width="95%"><font class="forumbodytext"><?=GetUserName($arMessage[$InputOutput])?></font>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?=GetMessage("PM_HEAD_MESS")?></b></font></td>
						</tr>
						<tr>
							<td class="forumbody" valign="top"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_SUBJ")?></font></td>
							<td class="forumbody"><font class="forumbodytext"><?=$arMessage["POST_SUBJ"];?></font></td>
						</tr>
						<tr valign="top">
							<td class="forumbody" valign="top"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_MESS_TEXT")?></font><br><br>
							</td>
							<td class="forumbody">
							<font class="forumfieldtext"><?
							echo $parser->convert(
								$arMessage["POST_MESSAGE"], 
								array(
									"HTML" => "N",
									"ANCHOR" => "Y",
									"BIU" => "Y",
									"IMG" => "Y",
									"LIST" => "Y",
									"QUOTE" => "Y",
									"CODE" => "Y",
									"FONT" => "Y",
									"SMILES" => $arMessage["USE_SMILES"],
									"UPLOAD" => "N",
									"NL2BR" => "N"
								));?></font></textarea>
							</td>
						</tr>
						<tr>
							<td class="forumbody" style="border-left:none;" colspan="2" align="center">
								<table border="0" cellspacing="0" cellpadding="0">						
									<form action="<?=$APPLICATION->GetCurPage()."?MID=".$MID?>" method="POST">
									<input type="hidden" name="FID" value="<?=$FID?>">
									<tr>
										<td nowrap class="forummessbutton"><a  class="forummessbuttontext" href="<?=$APPLICATION->GetCurPage()?>?mode=reply&MID=<?=$MID?>"?><?=GetMessage("PM_ACT_REPLY")?></a></td>
										<td><div class="forummessbuttonsep"></div></td>
										<td nowrap class="forummessbutton"><a  class="forummessbuttontext" href="<?=$APPLICATION->GetCurPage()?>?mode=edit&MID=<?=$MID?>&FID=<?=$FID?>"?><?=GetMessage("PM_ACT_EDIT")?></a></td>
										<td><div class="forummessbuttonsep"></div></td>
										<td nowrap class="forummessbutton"><a  class="forummessbuttontext" href="<?=$APPLICATION->GetCurPage()?>?action=delete&FID=<?=$FID?>&MID=<?=$MID?>"?><?=GetMessage("PM_ACT_DELETE")?></a></td>
										<td><div class="forummessbuttonsep"></div></td>
										<td nowrap><select name="action"><option value="copy"><?=GetMessage("PM_ACT_COPY")?></option><option value="move"><?=GetMessage("PM_ACT_MOVE")?></option></select></td>
										<td nowrap class="forummessbutton"><font class="forummessbuttontext">&nbsp;<?=GetMessage("PM_ACT_IN")?>&nbsp;</font></td>
										<td nowrap><select name="FOLDER_ID"><option value="-1"><?=GetMessage("PM_ACT_FOLDER_SELECT")?></option><?
											for ($ii = 1; $ii <= FORUM_SystemFolder; $ii++)
											{
												?><option value="<?=$ii?>"><?=getMessage("PM_FOLDER_ID_".$ii)?></option><?
											}
											$resFolder = CForumPMFolder::GetList(array(), array("USER_ID" => $USER->GetId()));
											if ($resF = $resFolder->GetNext()):
												do
												{
													?><option value="<?=intVal($resF["ID"])?>"><?=$resF["TITLE"]?></option><?
												}
												while ($resF = $resFolder->GetNext());
											endif;
												?></select></td>
										<td width="0"><input type="submit" class="forummessbuttontext" value="<?=GetMessage("PM_OK")?>"></td>
										<td><div class="forummessbuttonsep"></div></td>
									</tr></form>
								</table>	
							</td>
						</tr>
						</form>
					</table>
				</td></tr></table>
				<?
			}
			else 
			{
				?><?=ShowError(GetMessage("PM_NOT_RIGHT_NOT_MESSAGE"))?><?
			}
		}
		elseif($mode=="edit" || $mode=="new" || $mode=="reply")
		{
			$title = GetMessage("PM_new");
			if ($mode == "edit" || $mode=="reply")
			{
				$dbrMessage = CForumPrivateMessage::GetById($MID);
				if(($arMessage = $dbrMessage->Fetch()) && CForumPrivateMessage::CheckPermissions($MID))
				{
					if (($action != "save")&&(strlen(trim($strError))<=0))
					{
						foreach ($arMessage as $key=>$value)
							${$key} = $value;
					}
					if ($mode == "reply")
					{
						$POST_SUBJ = GetMessage("PM_REPLY").$POST_SUBJ;
						$POST_MESSAGE = "[QUOTE]".$POST_MESSAGE."[/QUOTE]";
						$USER_ID = $AUTHOR_ID;
						$USER_LOGIN = htmlspecialcharsEx(GetUserName($USER_ID));
						$mode = "new";
					}
					$title = GetMessage("PM_FOLDER_ID_".$FID).GetMessage("PM_SEPARATOR").str_replace("##", htmlspecialcharsEx($POST_SUBJ), GetMessage("PM_edit"));
				}
			}
			?><script language="Javascript"><?
				if ($strJSPath = $APPLICATION->GetTemplatePath("forum/forum_tmpl_1/forum_js.php"))
				include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
			?></script><?	
				$search_form = '
					<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
					<html><head><meta  http-equiv="Content-Type" content="text/html; charset='.SITE_CHARSET.'"><title>'.GetMessage("PM_SEARCH_USER").'</title></head>
					<form action="'.$APPLICATION->GetCurDir().'pm_message.php" method=GET enctype="multipart/form-data">
					<input type=hidden name="mode" value="new">
					'.bitrix_sessid_post().'
					<style type=text/css>
						td.tableborder, table.tableborder {background-color:#8FB0D2;}
						table.tablehead, td.tablehead {background-color:#F1F5FA;}
						table.tablebody, td.tablebody {background-color:#FFFFFF;}
						.tableheadtext, .tablebodylink {font-family: Verdana,Arial,Hevetica,sans-serif; font-size:12px;}
						.tableheadtext {color:#456A74}
						H1, H2, H3, H4 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#3A84C4; font-size:13px; font-weight:bold; line-height: 16px; margin-bottom: 1px;}
						input.inputradio, input.inputfile, input.inputbutton, input.inputbodybutton {font-family:Verdana,Arial,Helvetica; font-size:11px;}
						.errortext, .oktext, .notetext {font-family:Verdana,Arial,Hevetica,sans-serif; font-size:13px; font-weight:bold;}
						.errortext {color:red;}
					</style>
					<h1>'.GetMessage("PM_SEARCH_USER").'</h1>
					<table border=0 cellspacing=1 cellpadding=3 class=tableborder>
					<tr><td class=tablebody valign=top align=center colspan=3>
						<font class=tableheadtext>'.GetMessage("PM_SEARCH_PATTERN").'</font></td></tr>
					<tr>
						<td class=tablehead valign=top align=right nowrap>
						<font class=tableheadtext>
						<b>'.GetMessage("PM_SEARCH_INSERT").'</b></td>
						<td class=tablebody colspan=2><input type=text name="search_template" value="'.htmlspecialcharsEx($_REQUEST["search_template"]).'" style="width:180px;"></td>
					</tr>
					</table>
					<br>
					<table border=0 width=100%>
					<tr><td align="right"><input type=hidden value="Y" name="do_search"><input type=submit value="'.GetMessage("PM_SEARCH").'" name=do_search class=inputbutton></td><td align="left"><input type=button value="'.GetMessage("PM_CANCEL").'" onclick=self.close() class=inputbutton></td></tr>
					</table><br>
					<!-- !-->
				</form>
				</html>';
				if ($_REQUEST["do_search"]) // если задан поиск
				{
					$APPLICATION->RestartBuffer();
					header("Pragma: no-cache");
					$_REQUEST["search_template"] = trim($_REQUEST["search_template"]);
					if (strlen($_REQUEST["search_template"])>0)
					{
						$reqSearch = CForumUser::SearchUser($_REQUEST["search_template"]);
						$reqSearch->NavStart(15);
						ob_start();
						if ($res = $reqSearch->GetNext())
						{
							?><table border=0 cellspacing=1 cellpadding=3 class=tableborder width='100%'>
								<tr><td class=tablehead><font class=tableheadtext><?=$reqSearch->NavPrint(GetMessage("PM_SEARCH_RESULT"))?></font></td></tr>
								<tr><td class=tablebody><?
							do 
							{
								echo "<a class='tableheadtext' href=\"".$APPLICATION->GetCurPage()."?mode=".$mode."&do_search=Y&search_template=".$_REQUEST["search_template"]."&search_insert=Y&SEARCH_USER=".intVal($res["ID"])."&".bitrix_sessid_get()."\">".$res["SHOW_ABC"]."</a><br>";
								if (($SEARCH_USER > 0) &&($SEARCH_USER == $res["ID"]))
									$NAME_USER = $res["SHOW_ABC"];
							}
							while ($res = $reqSearch->GetNext());
							?>
								<tr><td class=tablehead><font class=tableheadtext><?=$reqSearch->NavPrint(GetMessage("PM_SEARCH_RESULT"))?></font></td></tr>
							</table><?
						}
						else 
							echo "<font class=tableheadtext>".GetMessage("PM_SEARCH_NOTHING")."</font>";
						$reqStr = ob_get_clean();
					}
					else 
						$reqStr = "<font class=tableheadtext>".GetMessage("PM_NO_DATA")."</font>";
					$search_form = str_replace(
						"<!-- !-->", $reqStr, $search_form);
					print $search_form;
					if ($_REQUEST["search_insert"])
					{
						?><script language="Javascript">
							var form = opener.document.getElementById('REPLIER');
							form.USER_ID.value = '<?=intVal($SEARCH_USER)?>';
							form.USER_LOGIN.value = '<?=$NAME_USER?>';
							if (form.USER_ID.value != '<?=intVal($arMessage["USER_ID"])?>')
							{
								form.action.value = 'send';
								form.SAVE_BUTTON.value = '<?=GetMessage("PM_ACT_SEND")?>';
							}
							else
							{
								form.action.value = 'save';
								form.SAVE_BUTTON.value = '<?=GetMessage("PM_ACT_SAVE")?>';
							}
							self.close();
						</script><?
					}
					die();
				}
				?><script language="Javascript">
				function ShowSearchWindow()
				{
					win = window.open(null,null, 'height=500,width=400,scrollbars=1');
				<?
					$L = explode("\n",$search_form);
					foreach($L as $line)
					{
						$line = str_replace('"','\"',$line);
						$line = str_replace("\n",'\n',$line);
						$line = str_replace("\r",'\n',$line);
						print "win.document.write(\"".$line."\");\n";
					}
				?>
					win.document.close();
				}
				function ClearUserId()
				{
					var form = document.getElementById('REPLIER');
					form.USER_ID.value='';
					return;
				}
				</script>
				<?	
			?>
			<form action="<?=$APPLICATION->GetCurPage();?>?mode=<?=htmlspecialchars($mode)?><?=$mode == "new" ? "" : "&MID=".$MID?>" method="post" id="REPLIER" name="REPLIER" enctype="multipart/form-data">
			<input type="hidden" name="action" id="action" value="<?echo $mode=="new" ? "send" : "save";?>">
			<input type="hidden" name="FID" value="<?=$FID?>">
			<table border="0" width="100%" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?=GetMessage("PM_HEAD_TO")?></b></font></td>
					</tr>
					<tr>
						<td class="forumbody" width="30%"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_NAME_LOGIN")?></font></td>
						<td class="forumbody" width="70%"><font class="forumbodytext">
						<input type="hidden" name='USER_ID' id='USER_ID' value='<?=$USER_ID?>'>
						<input type="text" name="USER_LOGIN" id='USER_LOGIN' size="40" maxlength="64" onchange="ClearUserId();" value="<?=empty($USER_LOGIN) ? GetUserName($USER_ID) : htmlspecialcharsex($USER_LOGIN)?>" class="inputtext" <? echo $mode=="edit" ? "disabled" : ""?> tabindex="1">&nbsp;&nbsp;
						<input type="button" class="inputbutton" name="search_user" value="<?=GetMessage("PM_SEARCH_USER")?>" onClick="ShowSearchWindow(); return false;"><?
						?>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?=GetMessage("PM_HEAD_FROM")?></b></font></td>
					</tr>
					<tr>
						<td class="forumbody"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_NAME_LOGIN")?></font></td>
						<td class="forumbody"><font class="forumbodytext"><?=GetUserName($UID)?></font></td>
					</tr>
					<tr>
						<td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?=GetMessage("PM_HEAD_MESS")?></b></font></td>
					</tr>
					<tr>
						<td class="forumbody" valign="top"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_SUBJ")?></font></td>
						<td class="forumbody"><input type="text" name="POST_SUBJ" value="<?=htmlspecialcharsEx($POST_SUBJ);?>" size="47" maxlength="50" class="inputtext" tabindex="2"></td>
					</tr>
					<tr>
						<td class="forumbody" valign="top"><font class="forumfieldtext">&nbsp;<?=GetMessage("PM_HEAD_MESS")?></font><br><br>
							<table align="center" cellspacing="1" cellpadding="5" border="0" style="border-width:1px; border-color:#999999; border-style:solid;">
								<tr>
									<td colspan="3" align="center" style="border-bottom:1px; border-bottom-color:#999999; border-bottom-style:solid;">
										<font class="forumheadtext"><?=GetMessage("PM_SMILES")?></font>
									</td>
								</tr>
								<?
									echo ForumPrintSmilesList(3, LANGUAGE_ID);
								?>
							</table>
						</td>
						<td class="forumbody">
							<table cellpadding='2' cellspacing='2' width='100%' align='center'>
								<tr>
									<td nowrap width='10%'><font class="text">
										<input type='button' accesskey='b' value=' B ' onClick='simpletag("B")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle; font-weight:bold" name='B' title="<?echo GetMessage("PM_BOLD")?>" onMouseOver="show_hints('bold')">&nbsp;
										<input type='button' accesskey='i' value=' I ' onClick='simpletag("I")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle; font-style:italic" name='I' title="<?echo GetMessage("PM_ITAL")?>" onMouseOver="show_hints('italic')">&nbsp;
										<input type='button' accesskey='u' value=' U ' onClick='simpletag("U")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle; text-decoration:underline" name='U' title="<?echo GetMessage("PM_UNDER")?>" onMouseOver="show_hints('under')">&nbsp;
										<select name='ffont' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" onchange="alterfont(this.options[this.selectedIndex].value, 'FONT')" onMouseOver="show_hints('font')">
											<option value='0'><?echo GetMessage("PM_FONT")?></option>
											<option value='Arial' style='font-family:Arial'>Arial</option>
											<option value='Times' style='font-family:Times'>Times</option>
											<option value='Courier' style='font-family:Courier'>Courier</option>
											<option value='Impact' style='font-family:Impact'>Impact</option>
											<option value='Geneva' style='font-family:Geneva'>Geneva</option>
											<option value='Optima' style='font-family:Optima'>Optima</option>
										</select>&nbsp;
										<select name='fcolor' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" onchange="alterfont(this.options[this.selectedIndex].value, 'COLOR')" onMouseOver="show_hints('color')">
											<option value='0'><?echo GetMessage("PM_COLOR")?></option>
											<option value='blue' style='color:blue'><?echo GetMessage("PM_BLUE")?></option>
											<option value='red' style='color:red'><?echo GetMessage("PM_RED")?></option>
											<option value='gray' style='color:gray'><?echo GetMessage("PM_GRAY")?></option>
											<option value='green' style='color:green'><?echo GetMessage("PM_GREEN")?></option>
										</select>&nbsp; <a href='javascript:closeall();' title="<?echo GetMessage("PM_CLOSE_OPENED_TAGS")?>" onMouseOver="show_hints('close')"><?echo GetMessage("PM_CLOSE_ALL_TAGS")?></a></font></td>
								</tr>
								<tr>
									<td align='left'>
										<input type='button' accesskey='h' value=' http:// ' onClick='tag_url()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='url' title="<?echo GetMessage("PM_HYPERLINK")?>" onMouseOver="show_hints('url')">
										<input type='button' accesskey='q' value=' QUOTE ' onClick='simpletag("QUOTE")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='QUOTE' title="<?echo GetMessage("PM_QUOTE")?>" onMouseOver="show_hints('quote')">
										<input type='button' accesskey='p' value=' CODE ' onClick='simpletag("CODE")' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name='CODE' title="<?echo GetMessage("PM_CODE")?>" onMouseOver="show_hints('code')">
										<input type='button' accesskey='l' value=' LIST ' onClick='tag_list()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name="LIST" title="<?echo GetMessage("PM_LIST")?>" onMouseOver="show_hints('list')">
										<?if (LANGUAGE_ID=="ru"):?>
											<input type='button' accesskey='t' value=' Транслит ' onClick='translit()' style="font-size: 8.5pt; font-family: verdana, helvetica, sans-serif; vertical-align: middle" name="TRANSLIT" title="<?echo GetMessage("PM_TRANSLIT")?>" onMouseOver="show_hints('translit')">
										<?endif;?>
									</td>
								</tr>
								<tr>
									<td align='left' valign='middle'>
										<font class="text">
										<?echo GetMessage("PM_OPENED_TAGS")?><input type='text' name='tagcount' size='3' maxlength='3' style='font-size:10px;font-family:verdana,arial;border: 0 solid;font-weight:bold;' readonly class='forumbody' value="0">
										&nbsp;<input type='text' name='helpbox' size='50' maxlength='120' style='width:80%;font-size:10px;font-family:verdana,arial;border: 0 solid;' readonly class='forumbody' value="">
										</font>
									</td>
								</tr>
							</table>
						<textarea cols="47" rows="12" wrap="soft" name="POST_MESSAGE" tabindex="3" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" class="inputtextarea"><?echo htmlspecialcharsEx($POST_MESSAGE);?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<td width="100%" class="forumbody" style="border-left:none;border-bottom:none;" colspan="2">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<input type="checkbox" name="USE_SMILES" value="Y" <?$USE_SMILES = $mode == "new" ? "Y" : $USE_SMILES; 
											echo ($USE_SMILES=="Y") ? "checked" : "";?> class="inputcheckbox">
										</td>
										<td width="100%">
											<font class="forumbodytext"><?echo GetMessage("PM_WANT_ALLOW_SMILES")?></font>
										</td>
									</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="forumbody" style="border-left:none;" colspan="2" align="center">
						<input type="submit" name="SAVE_BUTTON" id="SAVE_BUTTON" value="<?
							
							if ($mode=="new")
								echo GetMessage("PM_ACT_SEND");
							else
								echo GetMessage("PM_ACT_SAVE");
							?>" class="inputbutton" tabindex="4">
						</td>
					</tr>
				</table>
			</td></tr></table>
		</form>		
		<script language="Javascript">
			document.getElementById('SAVE_BUTTON').focus();
		</script>
		
		<?
	}
	else
	{
		?><script language="Javascript"><?
			if ($strJSPath = $APPLICATION->GetTemplatePath("forum/forum_tmpl_1/forum_js.php"))
			include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
		?></script><?
			
		$StatusUser = "AUTHOR";
		$InputOutput = "AUTHOR_ID";
		$SortingField = "AUTHOR_NAME";
		if (intVal($FID) <= 1)
		{
			$StatusUser = "SENDER";
			$InputOutput = "AUTHOR_ID";
			$SortingField = "AUTHOR_NAME";
		}
		elseif ((intVal($FID) > 1) && (intVal($FID) <=3))
		{
			$StatusUser = "RECIPIENT";
			$InputOutput = "RECIPIENT_ID";
			$SortingField = "RECIPIENT_NAME";
		}
		
		InitSorting();
		global $by, $order;
		$arFilter = array("USER_ID"=>$UID, "FOLDER_ID"=>$FID);
		if ($FID == 2) //If this is outbox folder
		{
			$arFilter = array("OWNER_ID" => $UID);
		}
		
		
		if ($FID > 4)
		{
			$db_res = CForumPMFolder::GetList(array(), array("ID"=>$FID));
			if ($db_res && ($res = $db_res->GetNext()))
				$title = $res["TITLE"];
		}
			
		$dbrMessages = CForumPrivateMessage::GetListEx(array($by=>$order), $arFilter);
		$dbrMessages->NavStart(20);
		if($arMsg = $dbrMessages->GetNext())
		{
			$dbrMessages->NavPrint(GetMessage("PM_TITLE"));
			?>
			<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder">
			<form action="<?=$APPLICATION->GetCurPage()?>?" name='REPLIER' id='REPLIER' method='POST'>
			<input type="hidden" name="FID" value="<?=$FID?>"> 
			  <tr>
				<td>
				  <table width="100%" border="0" cellspacing="1" cellpadding="4">
					<tr class="forumhead">
						<td align="center" nowrap class="forumheadtext" width="0%"><br><input type="checkbox" id="CheckBoxSelectAll" onclick="SelectAllCheckBox('REPLIER', 'message[]', 'CheckBoxSelectAll');"></td>
						<td align="center" nowrap class="forumheadtext" width="0%">&nbsp;</td>
						<td align="center" nowrap class="forumheadtext" width="80%"><?=GetMessage("PM_HEAD_SUBJ")?><br><?=SortingEx("post_subj")?></td>
						<td align="center" nowrap class="forumheadtext" width="20%"><?=GetMessage("PM_HEAD_".$StatusUser)?><br><?=SortingEx(strToLower($SortingField))?></td>
						<td align="center" nowrap class="forumheadtext" width="0%"><?=GetMessage("PM_HEAD_DATE")?><br><?=SortingEx("post_date")?></td>
					</tr><?
				do
				{
					?>
					<tr class="forumbody">
						<td align="center" class="forumbodytext">
							<input type=CheckBox name='message[]' value='<?=$arMsg['ID']?>' onclick="SelectCheckBox('CheckBoxSelectAll');">
						</td>
						<td align="center" class="forumbodytext">
						<?if($arMsg["IS_READ"] == "Y"):?>
							<a href="<?=$APPLICATION->GetCurPage()?>?FID=<?=$FID?>&MID=<?=$arMsg['ID']?>&mode=read"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_norm_no.gif" width="18" height="12" alt="<?=GetMessage("PM_HAVE_MESS")?>" border="0"></a>
						<?else:?>
							<a href="<?=$APPLICATION->GetCurPage()?>?FID=<?=$FID?>&MID=<?=$arMsg['ID']?>&mode=read"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_norm.gif" width="18" height="12" alt="<?echo GetMessage("PM_HAVE_NEW_MESS")?>" border="0"></a>
						<?endif?>
						</td>
						<td align="left" class="forumbodytext"><a href="<?=$APPLICATION->GetCurPage()?>?FID=<?=$FID?>&MID=<?=$arMsg['ID']?>&mode=read"><?=wordwrap($arMsg["POST_SUBJ"], 100, " ", 1)?></a></td>
						<td align="center" class="forumbodytext"><a href="<?=$APPLICATION->GetCurPage()?>?FID=<?=$FID?>&MID=<?=$arMsg['ID']?>&mode=new&USER_ID=<?=$arMsg[$InputOutput]?>"><?=GetUserName($arMsg[$InputOutput])?></a></td>
						<td align="center" class="forumbodytext" nowrap><?=$arMsg["POST_DATE"]?></td>
					</tr><?
				}while($arMsg = $dbrMessages->GetNext());
					?><tr><td colspan=5 align="center" class="forumhead">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr><td nowrap class="forumtoolbutton"><?=GetMessage("PM_ACT_SELECTED")?></td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td><input type=submit name="DELETE" value="<?=GetMessage("PM_ACT_DELETE")?>" class="forummessbuttontext"></td>
								<td nowrap><select name="action" class="forummessbuttontext"><option value="copy"><?=GetMessage("PM_ACT_COPY")?></option><option value="move"><?=GetMessage("PM_ACT_MOVE")?></option></select></td>
								<td nowrap><font class="forummessbuttontext">&nbsp;<?=GetMessage("PM_ACT_IN")?>&nbsp;</font></td>
								<td nowrap><select name="FOLDER_ID"  class="forummessbuttontext"><option value="-1"><?=GetMessage("PM_ACT_FOLDER_SELECT")?></option><?
									for ($ii = 1; $ii <= FORUM_SystemFolder; $ii++)
									{
										?><option value="<?=$ii?>"><?=getMessage("PM_FOLDER_ID_".$ii)?></option><?
									}
									$resFolder = CForumPMFolder::GetList(array(), array("USER_ID" => $USER->GetId()));
									if ($resF = $resFolder->GetNext()):
										do
										{
											?><option value="<?=intVal($resF["ID"])?>"><?=$resF["TITLE"]?></option><?
										}
										while ($resF = $resFolder->GetNext());
									endif;
										?></select></td>
								<td width="0"><BUTTON class="forummessbuttontext" name="SELECTED" onclick="submit();"><?=GetMessage("PM_OK")?></BUTTON></td>
								<td><div class="forummessbuttonsep"></div></td>
							</tr>
						</table>
					</td></tr>
					</table>
					</td></tr>
				</form>
				</table><br><?
				$dbrMessages->NavPrint(GetMessage("PM_TITLE"));

			}
			else 
			{
				?><table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr class="forumbody"><td align="left" class="forumbodytext"><?=GetMessage("PM_EMPTY_FOLDER")?></td></tr></table><?
			}
		}
//***************************/mode*****************************
		
		?><br><br><br><?
		$APPLICATION->SetTitle($title);
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
//***************************/output*****************************
	else: 
		LocalRedirect("index.php");
	endif;
	
else:
	?>
	<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr class="forumbody"><td align="left" class="forumbodytext"><?GetMessage("PM_NO_MODULE")?></td></tr></table>
	<?
endif;
?>