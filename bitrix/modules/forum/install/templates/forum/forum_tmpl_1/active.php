<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/***********************************************
	Компонент "Последние темы форумов"
	Параметры:
	TOP - максимальное количество сообщний
	PAGE_ELEMENTS - количество тем на одну страницу
***********************************************/
global $by, $order, $FilterArr, $strError, $find_date1_DAYS_TO_BACK;
$arrFORUMS = array();
extract($GLOBALS);
if (is_array($_REQUEST)) extract($_REQUEST, EXTR_SKIP);
function CheckLastTopicsFilter()
{
	global $DB, $strError, $FilterArr, $MESS;
	foreach ($FilterArr as $s) global $$s;
	$str = "";
	if (strlen($find_date1)>0 && !$DB->IsDate($find_date1)) $str .= GetMessage("FL_INCORRECT_LAST_MESSAGE_DATE")."<br>";
	elseif (strlen($find_date2)>0 && !$DB->IsDate($find_date2)) $str .= GetMessage("FL_INCORRECT_LAST_MESSAGE_DATE")."<br>";
	$strError .= $str;
	if (strlen($str)>0) return false; else return true;
}
if(CModule::IncludeModule("forum")):
	IncludeTemplateLangFile(__FILE__);
	ForumSetLastVisit(0);

	$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
	$APPLICATION->SetTitle(GetMessage("FL_PAGE_TITLE"));
	// получим список доступных форумов
	$arFilter = array();
	if (!$USER->IsAdmin())
	{
		$arFilter["LID"] = SITE_ID;
		$arFilter["PERMS"] = array($USER->GetGroups(), 'A');
		$arFilter["ACTIVE"] = "Y";
	}
	$ref = $ref_id = array();
	$rsForum = CForumNew::GetListEx(array("FORUM_GROUP_SORT"=>"ASC", "FORUM_GROUP_ID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
	while ($arForum = $rsForum->Fetch())
	{
		$ref_id[] = $arForum["ID"];
		$ref[] = $arForum["NAME"];
		$arrFORUMS[$arForum["ID"]] = array("ID" => $arForum["ID"], "NAME" => $arForum["NAME"]);	
		$LastVisit = intVal($_SESSION["FORUM"]["LAST_VISIT_FORUM_0"]);
		if ($LastVisit < intVal($_SESSION["FORUM"]["LAST_VISIT_FORUM_".intVal($arForum["ID"])]))
			$LastVisit = intVal($_SESSION["FORUM"]["LAST_VISIT_FORUM_".intVal($arForum["ID"])]);
		$arrFORUMS[$arForum["ID"]]["LAST_VISIT"] = $LastVisit;
	}
	
	
	$arrFORUMS_DROPDOWN = array("reference" => $ref, "reference_id" => $ref_id);
	$arrFORUMS_ID = array_keys($arrFORUMS);

	// получим список тем
	$arrFilter = array();
	if ($set_default=="Y")
	{
		$find_date1_DAYS_TO_BACK=1;
		$set_filter = "Y";
	}
	
	$FilterArr = Array(
		"find_date1",
		"find_date2",
		"find_forum"
		);
	if (strlen($set_filter)>0) InitFilterEx($FilterArr,"LAST_TOPICS_LIST","set",false); 
	else InitFilterEx($FilterArr,"LAST_TOPICS_LIST","get",false);
	if (strlen($del_filter)>0) DelFilterEx($FilterArr,"LAST_TOPICS_LIST",false);
	extract($GLOBALS);
	if (CheckLastTopicsFilter())
	{
		if (intval($find_forum)>0) $arFilter["FORUM_ID"] = intval($find_forum);
		if (intval($find_date1)>0) $arFilter[">=LAST_POST_DATE"] = $find_date1;
		if (intval($find_date2)>0) $arFilter["<=LAST_POST_DATE"] = $find_date2;
	}
	$by = (strlen($by)<=0) ? "LAST_POST_DATE" : $by;
	$order = ($order!="asc") ? "desc" : "asc";
	if ($PermissionUser<"Q")
		$arFilter["APPROVED"] = "Y";
	if ($USER->IsAuthorized())
	{
		$arFilter["USER_ID"] = $USER->GetID();
		$arFilter[">RENEW_TOPIC"] = ConvertTimeStamp($_SESSION["FORUM"]["LAST_VISIT_FORUM_0"], "FULL");
	}
	else 
	{
		$arFilter[">LAST_POST_DATE"] = ConvertTimeStamp((time()-24*60*60*60), "FULL");
	}
	
	
	$rsTopics = CForumTopic::GetListEx(array($by => $order, "POSTS" => "DESC"), $arFilter, false, $TOP);
	while ($arTopic = $rsTopics->Fetch())
	{
		$arTopic["LAST_POST_DATE_FORMATED"] = $arTopic["LAST_POST_DATE"];
		$arTopic["LAST_POST_DATE"] = intVal(MakeTimeStamp($arTopic["LAST_POST_DATE"]));
		if (!$USER->IsAuthorized() && is_array($_SESSION["FORUM"]["GUEST_TID"]))
		{
			if (intVal($_SESSION["FORUM"]["GUEST_TID"][$arTopic["ID"]]) > intVal($arrFORUMS[$arTopic["FORUM_ID"]]["LAST_VISIT"]))
			{
				$arrFORUMS[$arTopic["FORUM_ID"]]["LAST_VISIT"] = intVal($_SESSION["FORUM"]["GUEST_TID"][$arTopic["ID"]]);
			}
		}
		
		if (in_array($arTopic["FORUM_ID"], $arrFORUMS_ID) && 
			(($arTopic["LAST_POST_DATE"] > 0) && ($arTopic["LAST_POST_DATE"] > $arrFORUMS[$arTopic["FORUM_ID"]]["LAST_VISIT"])))
		{
			$arrTOPICS[] = array(
				"FORUM_ID"			=> $arTopic["FORUM_ID"],
				"ID"				=> $arTopic["ID"],
				"SORT"				=> $arTopic["SORT"],
				"STATE"				=> $arTopic["STATE"],
				"APPROVED"			=> $arTopic["APPROVED"],
				"IMAGE"				=> $arTopic["IMAGE"],
				"IMAGE_DESCR"		=> $arTopic["IMAGE_DESCR"],
				"TITLE"				=> $arTopic["TITLE"],
				"DESCRIPTION"		=> $arTopic["DESCRIPTION"],
				"USER_START_NAME"	=> $arTopic["USER_START_NAME"],
				"POSTS"				=> $arTopic["POSTS"],
				"VIEWS"				=> $arTopic["VIEWS"],
				"LAST_POST_DATE"	=> $arTopic["LAST_POST_DATE_FORMATED"],
				"LAST_MESSAGE_ID"	=> $arTopic["LAST_MESSAGE_ID"],
				"LAST_POSTER_NAME"	=> $arTopic["LAST_POSTER_NAME"],
				);
		}
	}
	$rsTopics = new CDBResult;
	$rsTopics->InitFromArray($arrTOPICS);
	$rsTopics->NavStart($PAGE_ELEMENTS);
	?>	
	<?$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");?>
	<?=ShowError($strError)?>
	<?=ShowNote($strNote)?>	
	<form name="form1" action="<?=$APPLICATION->GetCurPage()?>" method="GET">
	<table cellspacing=0 cellpadding=1 class="forumborder"">
		<tr>
			<td><table border="0" cellpadding="3" cellspacing="1" width="100%">
			
					<tr class="forumhead">
						<td><font class="forumheadtext"><?=GetMessage("FL_FILTER")?></font></td>
					</tr>
					<tr class="forumbody">
						<td>
						<table border="0" cellpadding="3" cellspacing="0" width="100%">
							<tr>
								<td align="right" valign="center"><font class="forumbodytext"><?=GetMessage("FL_FILTER_FORUM")?></font></td>
								<td valign="center"><?=SelectBoxFromArray("find_forum", $arrFORUMS_DROPDOWN, htmlspecialchars($find_forum), " ", "class=\"inputselect\"");?></td>
							</tr>
							<tr class="forumbody">
								<td align="right" valign="center"><font class="forumbodytext"><?=GetMessage("FL_FILTER_LAST_MESSAGE_DATE")?></font></td>
								<td valign="center"><?echo CalendarPeriod("find_date1", $find_date1, "find_date2", $find_date2, "form1", "Y", "class=\"inputselect\"", "class=\"inputtext\"")?></td>
							</tr>
							<tr class="forumbody">
								<td colspan="2" align="right"><input class="inputbutton" type="submit" name="set_filter" value="<?=GetMessage("FL_SET_FILTER")?>"><input type="hidden" name="set_filter" value="Y">&nbsp;&nbsp;<input class="inputbutton" type="submit" name="del_filter" value="<?=GetMessage("FL_DEL_FILTER")?>"></font></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>	
	</form>

	<?if(intval($rsTopics->SelectedRowsCount())>0):?>
	<p><?echo $rsTopics->NavPrint(GetMessage("FL_TOPICS"))?></p>
	<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder">
		<form action="" method="get">
			<tr>
				<td>
					<table width="100%" border="0" cellspacing="1" cellpadding="4">
						<tr class="forumhead">
							<td width="0%" align="center" nowrap class="forumheadtext">&nbsp;</td>
							<td width="50%" nowrap class="forumheadtext" align="center"><?=GetMessage("FL_TOPIC_NAME")?><br><?echo SortingEx("TITLE")?></td>
							<td width="20%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_FORUM_PREF")?><br><?echo SortingEx("FORDUM_ID")?></td>
							<td width="15%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_AUTHOR")?><br><?echo SortingEx("USER_START_NAME")?></td>
							<td width="3%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_POSTS")?><br><?echo SortingEx("POSTS")?></td>
							<td width="3%" align="center" nowrap class="forumheadtext"><?=GetMessage("FL_TOPIC_VIEWS")?><br><?echo SortingEx("VIEWS")?></td>
							<td width="9%" nowrap align="center" class="forumheadtext"><?=GetMessage("FL_TOPIC_LAST_MESS")?><br><?echo SortingEx("LAST_POST_DATE")?></td>
						</tr>
						<?
						while ($arTopic = $rsTopics->Fetch()):
						?>
						<tr class="forumbody">
							<td valign="top" align="center" class="forumbodytext"><?
								$image_prefix = ($arTopic["STATE"]!="Y") ? "closed_" : "";
								if ($arTopic["APPROVED"]!="Y" && ForumCurrUserPermissions($arTopic["FORUM_ID"])>="Q"):
									?><font color="#FF0000"><b>NA</b></font><?
								elseif (intVal($arTopic["MESSAGE_ID"]) > 0):
									?><a href="read.php?FID=<?=$arTopic["FORUM_ID"];?>&TID=<?=$arTopic["ID"]?>&MID=<?=intVal($arTopic["MESSAGE_ID"])?>#message<?=intVal($arTopic["MESSAGE_ID"])?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_<?echo $image_prefix; ?>norm.gif" width="18" height="12" alt="<?=GetMessage("FL_HAVE_NEW_MESS")?>" border="0"></a><?
								else:
									?><a href="read.php?FID=<?=$arTopic["FORUM_ID"];?>&TID=<?=$arTopic["ID"]?>"><img src="/bitrix/templates/.default/forum/forum_tmpl_1/images/f_<?echo $image_prefix; ?>norm.gif" width="18" height="12" alt="<?=GetMessage("FL_HAVE_NEW_MESS")?>" border="0"></a><?					
								endif;
								?></td>
							<td valign="top" class="forumbodytext"><?
								if (strlen($arTopic["IMAGE"])>0):
									?><img src="/bitrix/images/forum/icon/<?=$arTopic["IMAGE"]?>" alt="<?=$arTopic["IMAGE_DESCR"]?>" border="0" width="15" height="15"> <?
								endif;
								?><a href="read.php?FID=<?=$arTopic["FORUM_ID"]?>&TID=<?=$arTopic["ID"]?>" title="<?=GetMessage("FL_TOPIC_START")?><?=$arTopic["START_DATE"]?>"><?=htmlspecialcharsEx($arTopic["TITLE"])?></a><?
								$mess_count = $arTopic["POSTS"]+1;
								if (ForumCurrUserPermissions($FID)>="Q"):
									$mess_count = CForumMessage::GetList(array(), array("TOPIC_ID"=>$arTopic["ID"]), true);
								endif;
								echo "<br>".ForumShowTopicPages($mess_count, "read.php?FID=".$arTopic["FORUM_ID"]."&TID=".$arTopic["ID"], "PAGEN_1");
								?><br><?=htmlspecialcharsEx($arTopic["DESCRIPTION"])?></td>
							<td valign="top" class="forumbodytext"><a href="list.php?FID=<?=$arTopic["FORUM_ID"]?>"><?=htmlspecialcharsEx($arrFORUMS[$arTopic["FORUM_ID"]]["NAME"])?></a></td>
							<td valign="top" class="forumbodytext"><?=htmlspecialcharsEx($arTopic["USER_START_NAME"])?></td>
							<td valign="top" align="center" class="forumbodytext"><?=$arTopic["POSTS"]?></td>
							<td valign="top" align="center" class="forumbodytext"><?=$arTopic["VIEWS"]?></td>
							<td valign="top" class="forumbodytext"><?=$arTopic["LAST_POST_DATE"]?><br><b><a href="read.php?FID=<?=$arTopic["FORUM_ID"];?>&TID=<?=$arTopic["ID"]?>&MID=<?=$arTopic["LAST_MESSAGE_ID"]?>#message<?=$arTopic["LAST_MESSAGE_ID"]?>"><?=htmlspecialcharsEx($arTopic["LAST_POSTER_NAME"])?></a></b></td>

						</tr>
						<?endwhile;?>
					</table>
				</td>
			</tr>
		</form>
	</table>
	<p><?echo $rsTopics->NavPrint(GetMessage("FL_TOPICS"))?></p><?
	endif;
endif;
?>