<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
	?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
	?><?=ShowNote($arResult["OK_MESSAGE"])?>
	<div class="forum-title"><?=GetMessage("FP_CHANGE_PROFILE")?></div>
	<div class="forum-br"></div>
	<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data" class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="profile" />
	<input type="hidden" name="Update" value="Y"/>
	<input type="hidden" name="UID" value="<?=$arParams["UID"]?>"/>
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ACTION" value="EDIT"/>	
	<table class="forum-main">
  		<tr>
			<td width="40%" align="right"><?=GetMessage("FP_NAME")?></td>
			<td width="60%"><input type="text" name="NAME" size="45" maxlength="50" value="<?=$arResult["str_NAME"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_LAST_NAME")?></td>
			<td class="field-value"><input type="text" name="LAST_NAME" size="45" maxlength="50" value="<?=$arResult["str_LAST_NAME"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><font class="starrequired">*</font>E-Mail:</td>
			<td class="field-value"><input type="text" name="EMAIL" size="45" maxlength="50" value="<?=$arResult["str_EMAIL"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><font class="starrequired">*</font><?=GetMessage("FP_LOGIN")?></td>
			<td class="field-value"><input type="text" name="LOGIN" size="45" maxlength="50" value="<?=$arResult["str_LOGIN"]?>"/><input type="hidden" name="OLD_LOGIN" value="<?=$arResult["str_LOGIN"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_NEW_PASSWORD")?></td>
			<td class="field-value"><input type="password" name="NEW_PASSWORD" size="45" maxlength="50" value="<?=$arResult["NEW_PASSWORD"]?>" autocomplete="off" /></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_PASSWORD_CONFIRM")?></td>
			<td class="field-value"><input type="password" name="NEW_PASSWORD_CONFIRM" size="45" maxlength="50" value="<?=$arResult["NEW_PASSWORD_CONFIRM"]?>" autocomplete="off" /></td>
		</tr>
		<tr>
			<th colspan="2"><b><?=GetMessage("FP_PRIVATE_INFO")?></b></th>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_PROFESSION")?></td>
			<td class="field-value"><input type="text" name="PERSONAL_PROFESSION" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_PROFESSION"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_WWW_PAGE")?></td>
			<td class="field-value"><input type="text" name="PERSONAL_WWW" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_WWW"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name">ICQ:</td>
			<td class="field-value"><input type="text" name="PERSONAL_ICQ" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_ICQ"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_SEX")?></td>
			<td class="field-value">
				<select name="PERSONAL_GENDER" id="PERSONAL_GENDER">
				<option value=""><?=GetMessage("FP_SEX_NONE")?></option>
				<?if (is_array($arResult["arr_PERSONAL_GENDER"]["data"]) && !empty($arResult["arr_PERSONAL_GENDER"]["data"])):?>
					<?foreach ($arResult["arr_PERSONAL_GENDER"]["data"] as $value => $option):?>
				<option value="<?=$value?>" <?=(($arResult["arr_PERSONAL_GENDER"]["active"] == $value) ? "selected" : "")?>><?=$option?></option>
					<?endforeach?>
				<?endif;?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_BIRTHDATE")?><?=CLang::GetDateFormat("SHORT")?></td>
			<td class="field-value"><?
				$APPLICATION->IncludeComponent(
					"bitrix:main.calendar", 
					"", 
					array(
						"SHOW_INPUT" => "Y", 
						"FORM_NAME" => "form1",
						"INPUT_NAME" => "PERSONAL_BIRTHDAY",
						"INPUT_VALUE" => $arResult["~str_PERSONAL_BIRTHDAY"]),
					$component,
					array("HIDE_ICONS" => "Y"));
		?></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_PHOTO")?></td>
			<td class="field-value"><input name="PERSONAL_PHOTO" size="30" type="file" />
				<?if ($arResult["SHOW_DELETE_PERSONAL_PHOTO"] == "Y"):?>
				<br/><input type="checkbox" name="PERSONAL_PHOTO_del" value="Y" id="PERSONAL_PHOTO_del" /> 
					<label for="PERSONAL_PHOTO_del"><?=GetMessage("FILE_DELETE")?></label>
				<br/>
					<?=$arResult["str_PERSONAL_PHOTO_IMG"]?>
				<?endif;?>
			</td>
		</tr>
		<tr><th colspan="2"><b><?=GetMessage("FP_LOCATION")?></b></th></tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_COUNTRY")?></td>
			<td class="field-value">
				<select name="PERSONAL_COUNTRY" id="PERSONAL_COUNTRY">
				<option value=""><?=GetMessage("FP_COUNTRY_NONE")?></option>
				<?if (is_array($arResult["arr_PERSONAL_COUNTRY"]["data"]) && !empty($arResult["arr_PERSONAL_COUNTRY"]["data"])):?>
					<?foreach ($arResult["arr_PERSONAL_COUNTRY"]["data"] as $value => $option):?>
				<option value="<?=$value?>" <?=(($arResult["arr_PERSONAL_COUNTRY"]["active"] == $value) ? "selected" : "")?>><?=$option?></option>
					<?endforeach?>
				<?endif;?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_REGION")?></td>
			<td class="field-value"><input type="text" name="PERSONAL_STATE" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_STATE"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_CITY")?></td>
			<td class="field-value"><input type="text" name="PERSONAL_CITY" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_CITY"]?>"/></td>
		</tr>
		<tr>
			<th colspan="2"><b><?=GetMessage("FP_WORK_INFO")?></b></th>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_COMPANY_NAME")?></td>
			<td class="field-value"><input type="text" name="WORK_COMPANY" size="45" maxlength="255" value="<?=$arResult["str_WORK_COMPANY"]?>"/></td>
		</tr>		
		<tr>
			<td class="field-name"><?=GetMessage("FP_WWW_PAGE")?></td>
			<td class="field-value"><input type="text" name="WORK_WWW" size="45" maxlength="255" value="<?=$arResult["str_WORK_WWW"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_COMPANY_DEPARTMENT")?></td>
			<td class="field-value"><input type="text" name="WORK_DEPARTMENT" size="45" maxlength="255" value="<?=$arResult["str_WORK_DEPARTMENT"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_COMPANY_ROLE")?></td>
			<td class="field-value"><input type="text" name="WORK_POSITION" size="45" maxlength="255" value="<?=$arResult["str_WORK_POSITION"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_COMPANY_ACT")?></td>
			<td class="field-value"><textarea name="WORK_PROFILE" cols="35" rows="5"><?=$arResult["str_WORK_PROFILE"]?></textarea></td>
		</tr>
		<tr>
			<th colspan="2"><b><?=GetMessage("FP_COMPANY_LOCATION")?></b></th>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_COUNTRY")?></td>
			<td class="field-value">
				<select name="WORK_COUNTRY" id="WORK_COUNTRY">
				<option value=""><?=GetMessage("FP_COUNTRY_NONE")?></option>
				<?if (is_array($arResult["arr_WORK_COUNTRY"]["data"]) && !empty($arResult["arr_WORK_COUNTRY"]["data"])):?>
					<?foreach ($arResult["arr_WORK_COUNTRY"]["data"] as $value => $option):?>
				<option value="<?=$value?>" <?=(($arResult["arr_WORK_COUNTRY"]["active"] == $value) ? "selected" : "")?>><?=$option?></option>
					<?endforeach?>
				<?endif;?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_REGION")?></td>
			<td class="field-value"><input type="text" name="WORK_STATE" size="45" maxlength="255" value="<?=$arResult["str_WORK_STATE"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_CITY")?></td>
			<td class="field-value"><input type="text" name="WORK_CITY" size="45" maxlength="255" value="<?=$arResult["str_WORK_CITY"]?>"/></td>
		</tr>
	
		<tr>
			<th colspan="2"><b><?=GetMessage("FP_FORUM_PROFILE")?></b></th>
		</tr>
		<?if ($arResult["IsAdmin"] == "Y"):?>
		<tr>
			<td class="field-name"><?=GetMessage("FP_ALLOW_POST")?></td>
			<td class="field-value"><input type="checkbox" name="FORUM_ALLOW_POST" value="Y" <?
				if ($arResult["str_FORUM_ALLOW_POST"] == "Y")
				{
					?> checked <?
				}
			?>/></td>
		</tr>
		<?endif;?>
		<tr>
			<td class="field-name"><?=GetMessage("FP_SHOW_NAME")?></td>
			<td class="field-value"><input type="checkbox" name="FORUM_SHOW_NAME" value="Y" <?
				if ($arResult["str_FORUM_SHOW_NAME"] == "Y")
				{
					?> checked <?
				} 
			?>/></td>
		</tr>
		<tr>
			<td class="field-name">
				<?=GetMessage("FP_NOT_SHOW_IN_LIST")?> "<?=GetMessage("FP_NOW_ONLINE")?>":
			</td>
			<td class="field-value">
				<input type="checkbox" name="FORUM_HIDE_FROM_ONLINE" value="Y" <?
				if ($arResult["str_FORUM_HIDE_FROM_ONLINE"] == "Y")
				{
					?> checked <?
				}
			?>/></td>
		</tr>
		<tr>
			<td class="field-name">
				<?=GetMessage("FP_SUBSC_GET_MY_MESSAGE")?>:
			</td>
			<td class="field-value">
				<input type="checkbox" name="FORUM_SUBSC_GET_MY_MESSAGE" value="Y" <?
				if ($arResult["str_FORUM_SUBSC_GET_MY_MESSAGE"] == "Y")
				{
					?> checked <?
				} 
				?>/>
			</td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_DESCR")?></td>
			<td class="field-value"><input type="text" name="FORUM_DESCRIPTION" size="45" maxlength="255" value="<?=$arResult["str_FORUM_DESCRIPTION"]?>"/></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_INTERESTS")?></td>
			<td class="field-value"><textarea name="FORUM_INTERESTS" rows="3" cols="35"><?=$arResult["str_FORUM_INTERESTS"];?></textarea></td>
		</tr>
		<tr>
			<td class="field-name"><?=GetMessage("FP_SIGNATURE")?></td>
			<td class="field-value"><textarea name="FORUM_SIGNATURE" rows="3" cols="35"><?=$arResult["str_FORUM_SIGNATURE"]?></textarea></td>
		</tr>
		<tr>
			<td align="right" style="border-right:none;"><?=GetMessage("FP_AVATAR")?></td>
			<td class="field-value">
				<?=str_replace(array("#SIZE#", "#SIZE_BITE#"), array($arResult["AVATAR_H"]."x".$arResult["AVATAR_V"], $arResult["AVATAR_SIZE"]), GetMessage("FP_SIZE_AVATAR"))?><br/>
				<input name="FORUM_AVATAR" size="30" type="file" />
				<?if ($arResult["SHOW_DELETE_FORUM_AVATAR"] == "Y"):?>
				<br/><input type="checkbox" name="FORUM_AVATAR_del" value="Y" id="FORUM_AVATAR_del" /> 
					<label for="FORUM_AVATAR_del"><?=GetMessage("FILE_DELETE")?></label>
				<br/>
					<?=$arResult["str_FORUM_AVATAR_IMG"]?>
				<?endif;?>
			</td>
		</tr>
	<?// ********************* User properties ***************************************************?>
	<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
		<tr>
			<th colspan="2"><b><?=GetMessage("USER_TYPE_EDIT_TAB")?></b></th>
		</tr>
		<?$first = true;?>
		<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
		<tr><td class="field-name">
			<?if ($arUserField["MANDATORY"]=="Y"):?>
				<span class="required">*</span>
			<?endif;?>
			<?=$arUserField["EDIT_FORM_LABEL"]?>:</td><td class="field-value">
				<?$APPLICATION->IncludeComponent(
					"bitrix:system.field.edit", 
					$arUserField["USER_TYPE"]["USER_TYPE_ID"], 
					array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?>
		<?endforeach;?>
	<?endif;?>
	<?// ******************** /User properties ***************************************************?>
		<tr><th colspan="2"><input type="submit" name="save" value="<?=GetMessage("FP_SAVE")?>" id="save"/>&nbsp;<input type="submit" value="<?=GetMessage("FP_CANCEL")?>" name="cancel" id="cancel" /></th></tr>
	</table>
	</form>
	<font class="starrequired">*</font> <?=GetMessage("FP_REQUIED_FILEDS")?>