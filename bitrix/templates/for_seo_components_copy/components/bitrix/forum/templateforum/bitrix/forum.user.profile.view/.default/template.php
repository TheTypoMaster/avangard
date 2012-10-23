<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
	?><?=ShowNote($arResult["OK_MESSAGE"])?><?
	
	if ($arResult["SHOW_USER_INFO"] == "Y")
	{
		$noData = '<div class="no-data">'.GetMessage("F_NO_DATA").'</div>';
		include_once("interface.php");
		?>	<h2><?=$arResult["SHOW_NAME"]?>
			<?if ($arResult["SHOW_EDIT_PROFILE"] == "Y"):?>
				&nbsp;&nbsp;<small>[<a href="<?=$arResult["profile"]?>" title="<?=$arResult["SHOW_EDIT_PROFILE_TITLE"]?>"><?=GetMessage("F_EDIT_PROFILE")?></a>]</small>
			<?endif;?></h2><?
		
		
		
	$aTabs = array(
		array("DIV" => "forum_1", "TAB" => GetMessage("F_FORUM"), "TITLE" => GetMessage("F_FORUM")),
		array("DIV" => "forum_2", "TAB" => GetMessage("F_PRIVATE_DATA"), "TITLE" => GetMessage("F_PRIVATE_DATA")),
		array("DIV" => "forum_3", "TAB" => GetMessage("F_WORK_DATA"), "TITLE" => GetMessage("F_WORK_DATA")),
	);
	// ********************* User properties ***************************************************
	if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):
		$aTabs[] = array("DIV" => "forum_4", "TAB" => strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB"), "TITLE" => strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB"));
	endif;
	// ******************** /User properties ***************************************************
	$showedContactInfo = false;
	$tabControl = new CForumTabControl("forum_user", $aTabs);
		?><table class="fuser">
		<tr valign="top"><td width="0%" class="fuser-static">
			<table class="fuser-static" border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><th><div><?=GetMessage("F_PHOTO")?></div></th></tr>
			<tr><td class="user-photo"><div class="photo">
				<?if (!empty($arResult["f_PERSONAL_PHOTO"])):?>
					<?=$arResult["f_PERSONAL_PHOTO"]?>
				<?else:?>
					<div class="no-photo"></div>
				<?endif;?>
				</div></td></tr>
			<tr><th><div><?=GetMessage("F_CONTACTS")?></div></th></tr>
			<tr><td class="user-contacts"><?
			
			if ($arResult["SHOW_MAIL"] == "Y"):?>
				<div class="user-contact message-send">
					<div class="email"></div><a href="<?=$arResult["message_mail"]?>" <?
						?>title="<?=GetMessage("F_SEND_EMAIL_ALT")?>"><?=GetMessage("F_SEND")?> <?
						?><?=GetMessage("F_SEND_EMAIL")?></a></div><?
				$showedContactInfo = true;
			endif;
			
			if ($arResult["IsAuthorized"] == "Y"):?>
				<div class="user-contact message-send">
					<div class="pm"></div><a href="<?=$arResult["pm_edit"]?>" title="<?=GetMessage("F_SEND_PM_ALT")?>"><?
					?><?=GetMessage("F_SEND_PM")?></a></div><?
				$showedContactInfo = true;
			endif;
			
			if ($showedContactInfo):
				?><div class="forum-hr"></div><?
			endif;
			
			if ((strlen($arResult["f_PERSONAL_ICQ"])>0) && ($arResult["SHOW_ICQ"] == "Y")):?>
				<div class="user-contact"><div class="icq"></div><?=$arResult["f_PERSONAL_ICQ"]?></div><?
				$showedContactInfo = true;
			endif;
			
			if (!empty($arResult["f_PERSONAL_WWW"])):?>
				<div class="user-contact"><div class="www"></div><?=$arResult["f_PERSONAL_WWW"]?></div><?
				$showedContactInfo = true;
			endif;
			
			if (!$showedContactInfo):?>
				<?=$noData?>
			<?endif;?>
			
			</td></tr>
		<?// **************************  VOTINGS  *************************?>
		<?if ($arResult["SHOW_VOTES"] == "Y"):?>
			<tr><th><?=GetMessage("F_VOTE")?></th></tr>
			<tr><td>
					<div class="f-vote"><?=$arResult["titleVote"]?></div>
					<?if ($arResult["bCanVote"] || $arResult["bCanUnVote"]):?>
						<form method="get" action="<?=$APPLICATION->GetCurPageParam()?>" class="forum-form">
						<?if (($arResult["IsAdmin"] == "Y") && $arResult["bCanVote"]):?>
							<input type="text" name="VOTES" value="<?=intVal($arResult["VOTES"])?>" id="votes" />
						<?endif;?>
						<input type="hidden" name="UID" value="<?=$arResult["UID"]?>"/>
						<input type="hidden" name="FID" value="<?=$arResult["FID"]?>"/>
						<input type="hidden" name="TID" value="<?=$arResult["TID"]?>"/>
						<input type="hidden" name="MID" value="<?=$arResult["MID"]?>"/>
						<input type="hidden" name="VOTE_USER" value="Y"/>
						<input type="hidden" name="PAGE_NAME" value="profile_view"/>
						<?if ($arResult["bCanVote"]):?><input type="submit" name="VOTE_BUTTON" value="<?=GetMessage("F_DO_VOTE")?>" title="<?=GetMessage("F_DO_VOTE_ALT")?>" id="vote" /><?endif;?>
						<?if ($arResult["bCanUnVote"]):?><input type="submit" name="CANCEL_VOTE" value="<?=GetMessage("F_UNDO_VOTE")?>" title="<?=GetMessage("F_UNDO_VOTE_ALT")?>" id="unvote" /><?endif;?>
						</form>
					<?endif;?>
			</td></tr>
		<?endif;?>
		<?// ********************  END OF VOTINGS  ************************?>
			
			</table>
			</td>
			<td class="fuser-separator"></td>
			<td class="fuser-dinamic">
			<?=$tabControl->Begin();?>
			<?=$tabControl->BeginNextTab();?>
			<tr><td class="field-name"><?=GetMessage("F_ZVA")?>:</td><td class="field-value">
			<?if ($arResult["SHOW_RANK"] == "Y"):?>
				<?=$arResult["arRank"]["NAME"]?>
				<?if ($arResult["SHOW_POINTS"] == "Y"):?>
					<br /><?=GetMessage("F_NUM_POINTS").$arResult["fu_POINTS"]?>
					<br /><?=GetMessage("F_NUM_VOTES").$arResult["USER_POINTS"]?>
				<?endif;?>
			<?else:?>
				<?=$arResult["USER_RANK"]?>
			<?endif;?>
			</td></tr>
			<tr><td class="field-name"><?=GetMessage("F_NUM_MESSAGES")?>:</td><td class="field-value"><?=(empty($arResult["fu_NUM_POSTS"]) ? $noData : $arResult["fu_NUM_POSTS"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_DATE_REGISTER")?>:</td><td class="field-value"><?=(empty($arResult["fu_DATE_REG_FORMATED"]) ? $noData : $arResult["fu_DATE_REG_FORMATED"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_DATE_VISIT")?>:</td><td class="field-value"><?=(empty($arResult["fu_LAST_VISIT_FORMATED"]) ? $noData : $arResult["fu_LAST_VISIT_FORMATED"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_LAST_MESSAGE")?>:</td><td class="field-value">
			<?if ($arResult["arTopic"] != "N"):?>
			<?=$arResult["arTopic"]["LAST_POST_DATE"]?><br />
			<?=GetMessage("F_IN_TOPIC")?>: <a href="<?=$arResult["arTopic"]["read"]?>"><b><?=$arResult["arTopic"]["TITLE"]?></b>
				<?if (strlen($arResult["arTopic"]["DESCRIPTION"])>0):?>
					, <?=$arResult["arTopic"]["DESCRIPTION"]?>
				<?endif;?></a>
			<?else:?>
				<?=$noData?>
			<?endif;?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_VIEW_MESSAGE")?>:</td><td class="field-value">
				<a href="<?=$arResult["user_post_lta"]?>" title="<?=GetMessage("F_ALL_TOPICS_AUTHOR")?>"><?=GetMessage("F_ALL_TOPICS_AUTHOR")?></a><br/>
				<a href="<?=$arResult["user_post_lt"]?>" title="<?=GetMessage("F_ALL_TOPICS")?>"><?=GetMessage("F_ALL_TOPICS")?></a><br/>
				<a href="<?=$arResult["user_post_all"]?>" title="<?=GetMessage("F_ALL_MESSAGES")?>"><?=GetMessage("F_ALL_MESSAGES")?></a>				</td></tr>
			<tr><td class="field-name"><?=GetMessage("F_AVATAR")?>:</td><td class="field-value"><?=(empty($arResult["fu_AVATAR"]) ? $noData : $arResult["fu_AVATAR"])?></td></tr>
			<?=$tabControl->BeginNextTab();?>
			<tr><td class="field-name"><?=GetMessage("F_BIRTHDATE")?>:</td><td class="field-value"><?=(empty($arResult["f_PERSONAL_BIRTHDAY_FORMATED"]) ? $noData : $arResult["f_PERSONAL_BIRTHDAY_FORMATED"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_WWW_PAGE")?>:</td><td class="field-value"><?=(empty($arResult["f_PERSONAL_WWW"]) ? $noData : $arResult["f_PERSONAL_WWW"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_SEX")?>:</td><td class="field-value"><?=(empty($arResult["f_PERSONAL_GENDER"]) ? $noData : $arResult["f_PERSONAL_GENDER"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_PROFESSION")?>:</td><td class="field-value"><?=(empty($arResult["f_PERSONAL_PROFESSION"]) ? $noData : $arResult["f_PERSONAL_PROFESSION"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_LOCATION_PERS")?>:</td><td class="field-value"><?=(empty($arResult["f_PERSONAL_LOCATION"]) ? $noData : $arResult["f_PERSONAL_LOCATION"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_INTERESTS")?>:</td><td class="field-value"><?=(empty($arResult["fu_INTERESTS"]) ? $noData : $arResult["fu_INTERESTS"])?></td></tr>
			<?=$tabControl->BeginNextTab();?>
			<tr><td class="field-name"><?=GetMessage("F_COMPANY")?>:</td><td class="field-value"><?=(empty($arResult["f_WORK_COMPANY"]) ? $noData : $arResult["f_WORK_COMPANY"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_WWW_PAGE")?>:</td><td class="field-value"><?=(empty($arResult["f_WORK_WWW"]) ? $noData : $arResult["f_WORK_WWW"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_SEX_DEPARTMENT")?>:</td><td class="field-value"><?=(empty($arResult["f_WORK_DEPARTMENT"]) ? $noData : $arResult["f_WORK_DEPARTMENT"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_POST")?>:</td><td class="field-value"><?=(empty($arResult["f_WORK_POSITION"]) ? $noData : $arResult["f_WORK_POSITION"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_LOCATION")?>:</td><td class="field-value"><?=(empty($arResult["f_WORK_LOCATION"]) ? $noData : $arResult["f_WORK_LOCATION"])?></td></tr>
			<tr><td class="field-name"><?=GetMessage("F_ACTIVITY")?>:</td><td class="field-value"><?=(empty($arResult["f_WORK_PROFILE"]) ? $noData : $arResult["f_WORK_PROFILE"])?></td></tr>
	<?// ********************* User properties ***************************************************?>
	<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
			<?=$tabControl->BeginNextTab();?>
		<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
		<tr><td class="field-name">
			<tr><td class="field-name"><?=$arUserField["EDIT_FORM_LABEL"]?>:</td><td class="field-value">
				<?$APPLICATION->IncludeComponent(
					"bitrix:system.field.view", 
					$arUserField["USER_TYPE"]["USER_TYPE_ID"], 
					array("arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?>
					</td></tr>
		<?endforeach;?>
	<?endif;?>
	<?// ******************** /User properties ***************************************************?>
			<?$tabControl->End();?>
			</td></tr>
		</table><?
}
?>