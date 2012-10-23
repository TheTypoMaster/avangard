<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
$arFields = array(
	array(
		"NAME" => "PAGE_NAME",
		"TYPE" => "HIDDEN",
		"VALUE" => "user_list"),
	array(
		"TITLE" => GetMessage("LU_FILTER_USER_NAME"),
		"NAME" => "user_name",
		"TYPE" => "TEXT",
		"VALUE" => $_REQUEST["user_name"]),
	array(
		"TITLE" => GetMessage("LU_FILTER_LAST_VISIT"),
		"NAME" => "date_last_visit1",
		"NAME_TO" => "date_last_visit2",
		"TYPE" => "PERIOD",
		"VALUE" => $_REQUEST["date_last_visit1"],
		"VALUE_TO" => $_REQUEST["date_last_visit2"]));
if ($USER->IsAdmin()):
	
endif;
?><div class="forum-user-list-page"><?
	$APPLICATION->IncludeComponent("bitrix:forum.interface", "filter", 
		array(
			"HEADER" => array(
				"TITLE" => GetMessage("LU_TITLE_USER")),
			"FIELDS" => $arFields),
				
			$component,
			array(
				"HIDE_ICONS" => "Y")
		);?><?
?></div><?
	?><?ShowError($arResult["ERROR_MESSAGE"])?>
	<div class="forum-br"></div><?
// *****************************************************************************************

	if ($arResult["SHOW_RESULT"] == "Y")
	{
	?><table class="forum-main forum-user-list">
		<tr>
			<th><?=GetMessage("FLU_HEAD_NAME")?>&nbsp;<br/><?=$arResult["SortingEx"]["SHOW_ABC"]?></th>
			<th><?=GetMessage("FLU_HEAD_POST")?>&nbsp;<br/><?=$arResult["SortingEx"]["NUM_POSTS"]?></th>
			<?if ($arResult["SHOW_VOTES"] == "Y"):?>
			<th><?=GetMessage("FLU_HEAD_POINTS")?>&nbsp;<br/><?=$arResult["SortingEx"]["POINTS"]?></th>
			<?endif;?>
			<th><?=GetMessage("FLU_HEAD_DATE_REGISTER")?>&nbsp;<br/><?=$arResult["SortingEx"]["DATE_REGISTER"]?></th>
			<th><?=GetMessage("FLU_HEAD_LAST_VISIT")?>&nbsp;<br/><?=$arResult["SortingEx"]["LAST_VISIT"]?></th>
		</tr><?
	foreach ($arResult["USERS"] as $res)
	{
		?><tr>
			<td>
				<div class="forum-user name"><?
		$arUserInfo = array(
			"profile" => array(
				"TITLE" => GetMessage("FLU_PROFILE"),
				"CONTENT" => array(
					"<div class=\"forum-user profile\"></div>",
					GetMessage("FLU_PROFILE")),
				"ONCLICK" => "jsUtils.Redirect([], '".$res["profile_view"]."');"));
				
		if ($arParams["SHOW_MAIL"] == "Y"):
			$arUserInfo["email"] = array(
				"TITLE" => GetMessage("FLU_EMAIL"),
				"CONTENT" => array(
					"<div class=\"forum-user email\"></div>",
					"E-Mail"),
				"ONCLICK" => "jsUtils.Redirect([], '".$res["email"]."');"
				);
		endif;
		
		if ($arResult["SHOW_ICQ"] == "Y"):
			$arUserInfo["icq"] = array(
				"TITLE" => GetMessage("FLU_ICQ"),
				"CONTENT" => array(
					"<div class=\"forum-user icq\"></div>",
					"ICQ"),
				"ONCLICK" => "jsUtils.Redirect([], '".$res["icq"]."');"
				);
		endif;
		
		if ($arResult["SHOW_PM"] == "Y"):
			$arUserInfo["pm"] = array(
				"TITLE" => GetMessage("FLU_PMESS"),
				"CONTENT" => array(
					"<div class=\"forum-user pm\"></div>",
					GetMessage("FLU_PMESS")),
				"ONCLICK" => "jsUtils.Redirect([], '".$res["pm_edit"]."');"
				);
		endif;
		
		$APPLICATION->IncludeComponent(
			"bitrix:forum.interface", "popup", 
			array("DATA" => $arUserInfo), 
			$component, 
			array("HIDE_ICONS" => "Y"));
		?><a href="<?=$res["profile_view"]?>" class="forum-user name" title="<?
				?><?=GetMessage("FLU_PROFILE")?>&nbsp;<?=$res["SHOW_ABC"]?>"><?=$res["SHOW_ABC"]?></a>
		</div><?
				
		if (is_array($res["~AVATAR"]) && (strLen($res["~AVATAR"]["HTML"]) > 0)):
			?><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("FLU_PROFILE")?>" class="forum-user avatar"><?=$res["~AVATAR"]["HTML"]?></a><?
		else:
			?><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("FLU_PROFILE")?>" class="forum-user avatar"><div class="no-avatar"></div></a><?
		endif;
		if ($arParams["SHOW_USER_STATUS"] == "Y"):?>
				<div class="forum-user status"><?=$res["UserStatus"]?></div>
		<?endif;
		
		if (intVal($res["NUM_POSTS"]) > 0):
			?><div class="forum-user posts"><span><?=GetMessage("FLU_NUM_MESS")?></span> <?=$res["NUM_POSTS"];?></div><?
		endif;
		
		if (strlen($res["~DATE_REG"]) > 0):
			?><div class="forum-user datereg"><span><?=GetMessage("FLU_DATE_REGISTER")?></span> <?=$res["DATE_REG"];?></div><?
		endif;
		?>				
			</td>
			<td align="center">
				<a href="<?=$res["user_post"]?>" title=""><?=intVal($res["NUM_POSTS"])?></a>
			</td>
			<?if ($arResult["SHOW_VOTES"] == "Y"):?>
			<td align="center"><?=intVal($res["POINTS"])?></td>
			<?endif;?>
			<td align="center"><?=$res["DATE_REG"]?></td>
			<td align="center"><?=$res["LAST_VISIT"]?></td>
		</tr><?
	}
	?></table><?
	}
	else 
	{
		?><table class="forum-main"><tr><th class="left"><?=GetMessage("FLU_EMPTY")?></td></tr></table><?
	}
	
	if (strLen($arResult["NAV_STRING"]) > 0)
	{
		?><div class="forum-br"></div><?
		?><?=$arResult["NAV_STRING"]?><?
		?><div class="forum-br"></div><?
	}
?>