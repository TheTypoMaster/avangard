<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
				Input params
********************************************************************/
/***************** BASE ********************************************/
$arParams["PATH_TO_ICON"] = (empty($arParams["PATH_TO_ICON"]) ? $templateFolder."/images/icon" : $arParams["PATH_TO_ICON"]);
$arParams["PATH_TO_ICON"] = str_replace("//", "/", $arParams["PATH_TO_ICON"]."/");
/********************************************************************
				/Input params
********************************************************************/

if (!empty($arResult["ERROR_MESSAGE"])):
	ShowError($arResult["ERROR_MESSAGE"]);
endif;
if (!empty($arResult["OK_MESSAGE"])):
	ShowNote($arResult["OK_MESSAGE"]);
endif;
	
$filter_title = ($arParams["mode"] == "lta" ? GetMessage("LU_TITLE_LTA") : ($arParams["mode"] == "lt" ? GetMessage("LU_TITLE_LT") : GetMessage("LU_TITLE_ALL")));

$filter_value_fid = array(
	"0" => GetMessage("F_ALL_FORUMS"));
if (is_array($arResult["GROUPS_FORUMS"])):
	foreach ($arResult["GROUPS_FORUMS"] as $key => $res):
		if ($res["TYPE"] == "GROUP"):
			$filter_value_fid["GROUP_".$res["ID"]] = array(
				"NAME" => ($res["DEPTH"] > 0 ? str_pad("", ($res["DEPTH"] - 1)*4, " ") : "").$res["~NAME"], 
				"CLASS" => "forums level".$res["DEPTH"], 
				"TYPE" => "OPTGROUP");
		else:
			$filter_value_fid[$res["ID"]] = array(
				"NAME" => ($res["DEPTH"] > 0 ? str_pad("", ($res["DEPTH"] + 1)*4, " ") : "").$res["~NAME"], 
				"CLASS" => "forums level".$res["DEPTH"], 
				"TYPE" => "OPTION");
		endif;
	endforeach;
endif;


$APPLICATION->IncludeComponent("bitrix:forum.interface", "filter", 
	array(
		"HEADER" => array(
			"TITLE" => $filter_title),
		"FIELDS" => array(
			array(
				"NAME" => "PAGE_NAME",
				"TYPE" => "HIDDEN",
				"VALUE" => "user_post"),
			array(
				"NAME" => "UID",
				"TYPE" => "HIDDEN",
				"VALUE" => $arParams["UID"]),
			array(
				"NAME" => "mode",
				"TYPE" => "HIDDEN",
				"VALUE" => $arParams["mode"]),
			array(
				"TITLE" => GetMessage("LU_FORUM"),
				"NAME" => "fid",
				"TYPE" => "SELECT",
				"VALUE" => $filter_value_fid,
				"ACTIVE" => $_REQUEST["fid"]),
			array(
				"TITLE" => GetMessage("LU_DATE_CREATE"),
				"NAME" => "date_create",
				"NAME_TO" => "date_create1",
				"TYPE" => "PERIOD",
				"VALUE" => $_REQUEST["date_create"],
				"VALUE_TO" => $_REQUEST["date_create1"]),
			array(
				"TITLE" => GetMessage("LU_TOPIC"),
				"NAME" => "topic",
				"TYPE" => "TEXT",
				"VALUE" => $_REQUEST["topic"]),
			array(
				"TITLE" => GetMessage("LU_MESSAGE"),
				"NAME" => "message",
				"TYPE" => "TEXT",
				"VALUE" => $_REQUEST["message"]))),

		array(
			"HIDE_ICONS" => "Y"));?><?
?>
<br />
<?
if (empty($arResult["FORUMS"])):
?>
	<table class="forum-main"><tr><th align="left"><?=GetMessage("FR_EMPTY")?></th></tr></table>
	<div class="forum-br"></div>
<?
	return false;
endif;

foreach ($arResult["FORUMS"] as $arForum):
	foreach ($arForum["TOPICS"] as $arTopic)
	{
		?><div class="forum-title">
				<div class="forum-title-views"><?=GetMessage("LU_USER_POSTS_ON_TOPIC")?>: <span><?=$arTopic["COUNT_MESSAGE"]?></span></div><?
				if (strlen($arTopic["IMAGE"])>0):
				?><img src="<?=$arParams["PATH_TO_ICON"].$arTopic["IMAGE"];?>" alt="<?=$arTopic["IMAGE_DESCR"]?>" /><?
				endif;
				?><span><?=GetMessage("FR_TOPIC")?></span> &laquo;<a href="<?=$arTopic["read"]?>"><?=$arTopic["TITLE"]?></a><?
				if (strLen($arTopic["DESCRIPTION"])>0):
				?>, <?=$arTopic["DESCRIPTION"]?><?
				endif;
				?>&raquo;  <span><?=GetMessage("FR_ON_FORUM")?> <a href="<?=$arForum["list"]?>"><?=$arForum["NAME"] ?></a></span>
		</div>
		<div class="forum-br"></div><?
		
		foreach ($arTopic["MESSAGES"] as $res)
		{
		?><table class="forum-message" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td class="forum-message-user-info" rowspan="2">
					<b><?=$res["AUTHOR_NAME"]?></b><br /><?
					if (strLen(trim($arForum["USER_PERM_STR"])) > 0):
						?><div class="forum-message-status"><?=$arForum["USER_PERM_STR"]?></div><?
					endif;
					if (strlen($arResult["USER"]["AVATAR"])>0):
						?><a href="<?=$arResult["USER"]["profile_view"]?>" title="<?=GetMessage("FR_AUTHOR_PROFILE")?>"><?=$arResult["USER"]["AVATAR"]?></a><br /><?
					endif;
					?><div class="forum-message-description"><?=$arResult["USER"]["DESCRIPTION"]?></div><?
					if (intVal($arResult["USER"]["NUM_POSTS"]) > 0):
						?><div class="forum-message-posts"><?=GetMessage("FR_NUM_MESS")?></div><?=$arResult["USER"]["NUM_POSTS"];?><br /><?
					endif;
					if (strlen($arResult["USER"]["~DATE_REG"]) > 0):
						?><div class="forum-message-datereg"><?=GetMessage("FR_DATE_REGISTER")?></div> <?=$arResult["USER"]["DATE_REG"];?><br /><?
					endif;
				?></td>
				<td class="border-bottom">
					<table class="clear"><tr>
						<td width="100%"><div class="forum-message-datecreate"><?=GetMessage("FR_DATE_CREATE")?> </div><?=$res["POST_DATE"]?><br /></td>
						<td><a title="" href="<?=$res["read"]?>" class="forum-button-small"><?=GetMessage("LU_GO_TO_MESSAGE")?></a></td></tr></table>
					<div class="forum-hr"></div>
					<?=$res["POST_MESSAGE_TEXT"]?><?
				foreach ($res["FILES"] as $arFile): 
				?><div class="forum-message-img"><?
					?><?$GLOBALS["APPLICATION"]->IncludeComponent(
						"bitrix:forum.interface", "show_file",
						Array(
							"FILE" => $arFile,
							"WIDTH" => $arResult["PARSER"]->image_params["width"],
							"HEIGHT" => $arResult["PARSER"]->image_params["height"],
							"CONVERT" => "N",
							"FAMILY" => "FORUM",
							"SINGLE" => "Y",
							"RETURN" => "N",
							"SHOW_LINK" => "Y"),
						null,
						array("HIDE_ICONS" => "Y"));
				?></div><?
				endforeach;
				
					if (strLen($res["SIGNATURE"]) > 0):
						?><div class="forum-user signature forum-message-signature"><div class="forum-hr"></div><?=$res["SIGNATURE"]?></div><?
					endif;
				?></td>
			</tr>
			<tr><td class="border-top"><div class="empty"></div></td></tr>
		</table>
		<div class="forum-br"></div><?
	}
}
endforeach;

if (!empty($arResult["NAV_STRING"])):
?><div class="forum-nav"><?=$arResult["NAV_STRING"]?></div><?
endif;

?>