<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
	$APPLICATION->IncludeComponent("bitrix:forum.interface", "filter", 
		array(
			"HEADER" => array(
				"TITLE" => GetMessage("LU_TITLE_USER")),
			"FIELDS" => array(
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
					"VALUE_TO" => $_REQUEST["date_last_visit2"]))),
				
			$component,
			array(
				"HIDE_ICONS" => "Y")
		);?><?

	?><?ShowError($arResult["ERROR_MESSAGE"])?>
	<div class="forum-br"></div><?
// *****************************************************************************************

	if ($arResult["SHOW_RESULT"] == "Y")
	{
	?><table class="forum-main">
		<tr>
			<th><?=GetMessage("FLU_HEAD_NAME")?>&nbsp;<br/><br/><?=$arResult["SortingEx"]["SHOW_ABC"]?></th>
	<?if ($arParams["SHOW_USER_STATUS"] == "Y"):?>
			<th><?=GetMessage("FLU_HEAD_RANK")?></th>
	<?endif;?>
			<th><?=GetMessage("FLU_HEAD_POST")?>&nbsp;<br/><?=$arResult["SortingEx"]["NUM_POSTS"]?></th>
			<?if ($arResult["SHOW_VOTES"] == "Y"):?>
			<th><?=GetMessage("FLU_HEAD_POINTS")?>&nbsp;<br/><?=$arResult["SortingEx"]["POINTS"]?></th>
			<?endif;?>
			<th><?=GetMessage("FLU_HEAD_DATE_REGISTER")?>&nbsp;<br/><?=$arResult["SortingEx"]["DATE_REGISTER"]?></th>
			<th><?=GetMessage("FLU_HEAD_LAST_VISIT")?>&nbsp;<br/><?=$arResult["SortingEx"]["LAST_VISIT"]?></th>
			<th><?=GetMessage("FLU_HEAD_AVATAR")?></th>
			<?if ($arResult["SHOW_PM"] == "Y" || $arResult["SHOW_MAIL"] == "Y" || $arResult["SHOW_ICQ"] == "Y"):
				$colspan = (($arResult["SHOW_PM"] == "Y") ? 1 : 0);
				(($arResult["SHOW_MAIL"] == "Y") ? $colspan++ : false);
				(($arResult["SHOW_ICQ"] == "Y") ? $colspan++ : false);?>
			<th colspan="<?=$colspan?>"><?=GetMessage("FLU_HEAD_CONTACTS")?></th>
			<?endif;?>
		</tr><?
	foreach ($arResult["USERS"] as $res)
	{
		?><tr>
			<td><a href="<?=$res["profile_view"]?>" title="<?=GetMessage("FLU_PROFILE")?>&nbsp;<?=$res["SHOW_ABC"]?>"><?=$res["SHOW_ABC"]?></a></td>
	<?if ($arParams["SHOW_USER_STATUS"] == "Y"):?>
			<td><?=$res["UserStatus"]?></td>
	<?endif;?>
			<td align="center">
				<a href="<?=$res["user_post"]?>" title=""><?=intVal($res["NUM_POSTS"])?></a>
			</td>
			<?if ($arResult["SHOW_VOTES"] == "Y"):?>
			<td align="center"><?=intVal($res["POINTS"])?></td>
			<?endif;?>
			<td align="center"><?=$res["DATE_REG"]?></td>
			<td align="center"><?=$res["LAST_VISIT"]?></td>
			<td align="center"><?=$res["AVATAR"]?></td>
			
			<?if ($arResult["SHOW_PM"] == "Y"):?>
			<td align="center"><a href="<?=$res["pm_edit"]?>" title="<?=GetMessage("FLU_PMESS_ALT")?>"><?=GetMessage("FLU_PMESS")?></a></td>
			<?endif;?>
			<?if ($arResult["SHOW_MAIL"] == "Y"):?>
			<td align="center" nowrap="nowrap"><a href="<?=$res["mail"]?>" title="<?=GetMessage("FLU_EMAIL_ALT")?>"><?=GetMessage("FLU_EMAIL")?></a></td>
			<?endif;?>
			<?if ($arResult["SHOW_ICQ"] == "Y"):?>
				<td align="center"><a href="<?=$res["icq"]?>" title="<?=GetMessage("FLU_ICQ_ALT")?>"><?=GetMessage("FLU_ICQ")?></a></td>
			<?endif;?>
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