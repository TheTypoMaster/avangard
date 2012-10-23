<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
	if ($USER->IsAuthorized()):
//***********************************input/***********************************
		extract($_REQUEST, EXTR_SKIP);
		$id = intVal($id);
		$title = "";
		$strError = "";
		switch ($res) {
			case "create":
				$strNote = GetMessage("PM_SUCC_CREATE");
				break;
			case "delete":
				$strNote = GetMessage("PM_SUCC_DELETE");
				break;
			case "remove":
				$strNote = GetMessage("PM_SUCC_REMOVE");
				break;
			case "saved":
				$strNote = GetMessage("PM_SUCC_SAVED");
				break;
			default:
				$strNote = "";
				break;
		}
//***********************************/input***********************************
		
		$APPLICATION->ResetException();
		$APPLICATION->ThrowException(" ");
//***********************************action/**********************************
		switch ($action) {
			case "update":
					$res = CForumPMFolder::GetList(array(), array("ID"=>$id));
					if (!$resFolder = $res->GetNext())
						$APPLICATION->ThrowException(GetMessage("PM_NOT_FOLDER"));
					else 
						if (CForumPMFolder::CheckPermissions($id))
						{
							if (CForumPMFolder::Update($id, array("TITLE"=>$FOLDER_TITLE)))
								LocalRedirect("pm_folder.php?res=saved");
						}
						else 
							$APPLICATION->ThrowException(GetMessage("PM_NOT_RIGHT"));
				break;
			case "save":
					if (($_SERVER['REQUEST_METHOD'] == "POST")&&(strLen($FOLDER_TITLE)>0))
					{
						if (CForumPMFolder::Add($FOLDER_TITLE))
							LocalRedirect("pm_folder.php?res=create");
					}
					else 
						$APPLICATION->ThrowException(GetMessage("PM_NOT_FOLDER_TITLE"));
				break;
			case "delete":
			case "remove":
					$remMes = true;
					if (CForumPMFolder::CheckPermissions($id))
					{
						$arFilter = $id == 2 ? array("FOLDER_ID"=>2, "USER_ID"=>$USER->GetId(), "OWNER_ID"=>$USER->GetId()) : array("FOLDER_ID"=>$id, "USER_ID"=>$USER->GetId());
						$arMessage = CForumPrivateMessage::GetList(array(), $arFilter);
//						print_r($arMessage); 
//						die();
						while ($MID = $arMessage->GetNext())
						{
//							print_r($MID); 
							if(!CForumPrivateMessage::Delete($MID["ID"]))
								$remMes = false;
						}
						if (($action == "delete")&&$remMes)
						{
							if(CForumPMFolder::Delete($id))
								LocalRedirect("pm_folder.php?res=delete");
							else 
								$APPLICATION->ThrowException(GetMessage("PM_NOT_DELETE"));
						}
						elseif (($action == "remove")&&$remMes)
							LocalRedirect("pm_folder.php?res=remove");
					}
					else 
						$APPLICATION->ThrowException(GetMessage("PM_NOT_RIGHT"));
				break;
			default:
				break;
		}
//**********************************/action***********************************
//**********************************output/***********************************
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
		// Вывод ошибок и неошибок
		$err = $APPLICATION->GetException();
	 	$strError = $err->GetString();
		ShowError($strError);
		ShowNote($strNote);		
		
//**********************************mode/*************************************
		switch ($mode) {
		 	case "edit":
		 	case "new":
		 	$title = GetMessage("PM_TITLE_CREATE");
		 	if (($mode == "edit")&&empty($FOLDER_TITLE))
		 	{
		 		$res = CForumPMFolder::GetList(array(), array("ID"=>$id));
		 		$res = $res->Fetch();
		 		$FOLDER_TITLE = $res["TITLE"];
			 	$title = str_replace("##", $FOLDER_TITLE, GetMessage("PM_TITLE_EDIT"));
		 	}
			?><table border="0" cellpadding="0" cellspacing="0" class="forumborder">
			<form action="<?=$APPLICATION->GetCurPage();?>?mode=<?=htmlspecialchars($mode)?><?=$mode == "new" ? "" : "&id=".$id?>" method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="<?echo $mode=="new" ? "save" : "update";?>">
			<tr><td>
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr><td colspan="2" class="forumhead"><font class="forumheadtext">&nbsp;<b><?=GetMessage("PM_HEAD_FOLDER")?></b></font></td></tr>
					<tr><td class="forumbody"><font class="forumbodytext"><input type="text" name="FOLDER_TITLE" size="40" maxlength="64" value="<?=htmlspecialcharsEx($FOLDER_TITLE)?>" class="inputtext"></td></tr>
					<tr><td class="forumbody" style="border-left:none;" colspan="2" align="center"><input type="submit" name="SAVE" value="<?=$mode == "new" ? GetMessage("PM_ACT_ADD") : GetMessage("PM_ACT_SAVE")	?>" tabindex="4" class="inputbutton"></td></tr>
				</table>
			</td></tr>
			</form>		
			</table><br><?
		 		break;
		 	default:
			 	$title = GetMessage("PM_TITLE_LIST");
	 			echo "<a href='".$APPLICATION->GetCurPage()."?mode=new' class='forumbodytext'>".GetMessage("PM_HEAD_NEW_FOLDER")."</a><br><br>";
				InitSorting();
				global $by, $order;
				?><table border="0" cellspacing="1" cellpadding="0" class="forumborder"  width="100%">
				  <tr>
					<td>
					  <table width="100%" border="0" cellspacing="1" cellpadding="4">
						<tr class="forumhead"><td align="center" nowrap class="forumheadtext" width="80%"><?=GetMessage("PM_HEAD_TITLE")?><br><?echo SortingEx("title")?></td>
						<td align="center" nowrap class="forumheadtext" width="20%"><?=GetMessage("PM_HEAD_MESSAGE")?><br><?echo SortingEx("count")?></td>
						<td align="center" nowrap class="forumheadtext" width="0%" colspan="3"><?=GetMessage("PM_HEAD_ACTION")?><br><br></td>
						</tr><?
				$resReq = CForumPMFolder::GetList(array($by=>$order), array("USER_ID"=>$USER->GetId()));
	 			if ($res = $resReq->GetNext())
	 			{
					for ($ii = 1; $ii <= FORUM_SystemFolder; $ii++)
					{
						$arFilter = $ii == 2 ? array("FOLDER_ID"=>$ii, "USER_ID"=>$USER->GetId(), "OWNER_ID"=>$USER->GetId()) : array("FOLDER_ID"=>$ii, "USER_ID"=>$USER->GetId());
						$res1 = CForumPrivateMessage::GetList(array(), $arFilter, true);
						$res1 = $res1->GetNext();
						?><tr class="forumbody">
							<td align="left" class="forumbodytext">
								<a href="<?=$APPLICATION->GetCurDir()."pm_message.php?mode=list&FID=".$ii?>"><?=GetMessage("PM_FOLDER_ID_".$ii)?></td>
							<td align="center" class="forumbodytext"><?=intVal($res1["CNT"])?></td>
							<td align="center" class="forumbodytext" nowrap>&nbsp;</td>
							<td align="center" class="forumbodytext" nowrap><a href="<?=$APPLICATION->GetCurPage()."?action=remove&id=".$ii?>" class="forumtoolbutton"><?=GetMessage("PM_ACT_REMOVE")?></a></td>
							<td align="center" class="forumbodytext" nowrap>&nbsp;</td>
						</tr><?	
					}
						

	 				do
	 				{
						?><tr class="forumbody">
							<td align="left" class="forumbodytext">
								<a href="<?=$APPLICATION->GetCurDir()."pm_message.php?mode=list&FID=".intVal($res["ID"])?>"><?=$res["TITLE"]?></td>
							<td align="center" class="forumbodytext"><?=intVal($res["CNT"])?></td>
							<td align="center" class="forumbodytext" nowrap><a href="<?=$APPLICATION->GetCurPage()."?mode=edit&id=".intVal($res["ID"])?>" class="forumtoolbutton"><?=GetMessage("PM_ACT_EDIT")?></a></td>
							<td align="center" class="forumbodytext" nowrap><a href="<?=$APPLICATION->GetCurPage()."?action=remove&id=".intVal($res["ID"])?>" class="forumtoolbutton"><?=GetMessage("PM_ACT_REMOVE")?></a></td>
							<td align="center" class="forumbodytext" nowrap><a href="<?=$APPLICATION->GetCurPage()."?action=delete&id=".intVal($res["ID"])?>" class="forumtoolbutton"><?=GetMessage("PM_ACT_DELETE")?></a></td>
						</tr>	
						<?	
	 				}
	 				while ($res = $resReq->GetNext());
	 				
	 			}
	 			else 
	 			{
					for ($ii = 1; $ii <= FORUM_SystemFolder; $ii++)
					{
						$arFilter = $ii == 2 ? array("FOLDER_ID"=>$ii, "USER_ID"=>$USER->GetId(), "OWNER_ID"=>$USER->GetId()) : array("FOLDER_ID"=>$ii, "USER_ID"=>$USER->GetId());
						$res1 = CForumPrivateMessage::GetList(array(), $arFilter, true);
						$res1 = $res1->GetNext();
						?><tr class="forumbody">
							<td align="left" class="forumbodytext"><a href="<?=$APPLICATION->GetCurDir()."pm_message.php?mode=list&FID=".$ii?>"><?=GetMessage("PM_FOLDER_ID_".$ii)?></td>
							<td align="center" class="forumbodytext"><?=intVal($res1["CNT"])?></td>
							<td align="center" class="forumbodytext" nowrap><a href="<?=$APPLICATION->GetCurPage()."?action=remove&id=".$ii?>" class="forumtoolbutton"><?=GetMessage("PM_ACT_REMOVE")?></a></td>
						</tr><?	
					}
	 			}
				?></table>
			</td></tr>
			</table><br><?
		 		break;
		 } 
//*********************************/mode**************************************
		
		
		$APPLICATION->SetTitle(htmlspecialchars($title));
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
//*********************************/output************************************
		
	else:
			LocalRedirect("index.php");
			die();
	endif;
else:
	?><table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr class="forumbody"><td align="left" class="forumbodytext"><?echo GetMessage("PM_NO_MODULE")?></td></tr></table><?
endif;
?>