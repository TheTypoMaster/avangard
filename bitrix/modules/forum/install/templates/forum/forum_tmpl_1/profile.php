<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
// *****************************************************************************************
		$strErrorMessage = "";
		$strOKMessage = "";
		$bVarsFromForm = false;
		$arUser = array();
		define("FORUM_MODULE_PAGE", "PROFILE");
		ForumSetLastVisit();
		
		$ID = $USER->GetID();
		if ($USER->IsAdmin() && (intVal($_REQUEST["ID"]) > 0))
			$ID = $_REQUEST["ID"];
		$ID = intVal($ID);
// ************User info********************************************************************
		$db_user = CUser::GetByID($ID);
		if ($db_user && ($ar_user = $db_user->Fetch()))
		{
			while (list($key, $val) = each($ar_user))
				${"str_".$key} = htmlspecialchars($val);
		
			$ar_forum_user = CForumUser::GetByUSER_ID($ID);
			if ($ar_forum_user)
			{
				while (list($key, $val) = each($ar_forum_user))
					${"str_FORUM_".$key} = htmlspecialchars($val);
			}
		}
		else
		{
			$ID = 0;
			$strErrorMessage .= GetMessage("FP_ERR_INTERN").". \n";
		}

// ***********ACTION************************************************************************
		if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["ACTION"]=="EDIT" && ($ID > 0) && check_bitrix_sessid())
		{
			$arPERSONAL_PHOTO = $_FILES["PERSONAL_PHOTO"];
			$arPERSONAL_PHOTO["old_file"] = $ar_user["PERSONAL_PHOTO"];
			$arPERSONAL_PHOTO["del"] = $_POST["PERSONAL_PHOTO_del"];

			$arFields = Array(
				"NAME"					=> $_POST["NAME"],
				"LAST_NAME"				=> $_POST["LAST_NAME"],
				"EMAIL"					=> $_POST["EMAIL"],
				"LOGIN"					=> $_POST["LOGIN"],
				"PERSONAL_PROFESSION"=> $_POST["PERSONAL_PROFESSION"],
				"PERSONAL_WWW"			=> $_POST["PERSONAL_WWW"],
				"PERSONAL_ICQ"			=> $_POST["PERSONAL_ICQ"],
				"PERSONAL_GENDER"		=> $_POST["PERSONAL_GENDER"],
				"PERSONAL_BIRTHDAY"	=> $_POST["PERSONAL_BIRTHDAY"],
				"PERSONAL_PHOTO"		=> $arPERSONAL_PHOTO,
				"PERSONAL_CITY"		=> $_POST["PERSONAL_CITY"],
				"PERSONAL_STATE"		=> $_POST["PERSONAL_STATE"],
				"PERSONAL_COUNTRY"	=> $_POST["PERSONAL_COUNTRY"],
				"WORK_COMPANY"			=> $_POST["WORK_COMPANY"],
				"WORK_DEPARTMENT"		=> $_POST["WORK_DEPARTMENT"],
				"WORK_POSITION"		=> $_POST["WORK_POSITION"],
				"WORK_WWW"				=> $_POST["WORK_WWW"],
				"WORK_CITY"				=> $_POST["WORK_CITY"],
				"WORK_STATE"			=> $_POST["WORK_STATE"],
				"WORK_COUNTRY"			=> $_POST["WORK_COUNTRY"],
				"WORK_PROFILE"			=> $_POST["WORK_PROFILE"]
			);
			if (strlen($_POST["NEW_PASSWORD"])>0)
			{
				$arFields["PASSWORD"] = $_POST["NEW_PASSWORD"];
				$arFields["CONFIRM_PASSWORD"] = $_POST["NEW_PASSWORD_CONFIRM"];
			}
			$res = $USER->Update($ID, $arFields);
			if (!$res)
				$strErrorMessage .= $USER->LAST_ERROR.". \n";
		
			if (strlen($strErrorMessage)<=0)
			{
				$arFields = array(
					"SHOW_NAME" => ($_POST["FORUM_SHOW_NAME"]=="Y") ? "Y" : "N",
					"HIDE_FROM_ONLINE" => ($_POST["FORUM_HIDE_FROM_ONLINE"]=="Y") ? "Y" : "N",
					"SUBSC_GROUP_MESSAGE" => ($_POST["FORUM_SUBSC_GROUP_MESSAGE"]=="Y") ? "Y" : "N",
					"SUBSC_GET_MY_MESSAGE" => ($_POST["FORUM_SUBSC_GET_MY_MESSAGE"]=="Y") ? "Y" : "N",
					"DESCRIPTION" => $_POST["FORUM_DESCRIPTION"],
					"INTERESTS" => $_POST["FORUM_INTERESTS"],
					"SIGNATURE" => $_POST["FORUM_SIGNATURE"],
					"AVATAR" => $_FILES["FORUM_AVATAR"]
					);
				$arFields["AVATAR"]["del"] = $_POST["FORUM_AVATAR_del"];
		
				if ($USER->IsAdmin())
				{
					$arFields["ALLOW_POST"] = ($_POST["FORUM_ALLOW_POST"]!="Y") ? "N" : "Y";
				}
		
				$ar_res = CForumUser::GetByUSER_ID($ID);
				if ($ar_res)
				{
					$arFields["AVATAR"]["old_file"] = $ar_res["AVATAR"];
					$FID = CForumUser::Update($ar_res["ID"], $arFields);
				}
				else
				{
					$arFields["USER_ID"] = $ID;
					$FID = CForumUser::Add($arFields);
				}
				if (intVal($FID)<=0)
				{
					$db_err = $APPLICATION->GetException();
					if ($db_err && ($err = $db_err->GetString()))
					{
						$strErrorMessage .= $err;
					}
				}
			}
		
			if (strlen($strErrorMessage)>0)
			{
				$bVarsFromForm = true;
			}
			else
			{
				if ($USER->GetID() == $ID)
					$USER->Authorize($ID);
				if ($_POST["OLD_LOGIN"]!=$_POST["LOGIN"] || strlen($_POST["NEW_PASSWORD"])>0)
				{
					$USER->SendUserInfo($USER->GetParam("USER_ID"), LANG, GetMessage("FP_CHG_REG_INFO"));
				}
				LocalRedirect("view_profile.php?UID=".$ID);
			}
		}
		
		$APPLICATION->SetTitle(GetMessage("FP_PTITLE"));
		$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
		
		
		if ($bVarsFromForm)
		{
			$arUserFields = &$DB->GetTableFieldsList("b_user");
			for ($i = 0; $i < count($arUserFields); $i++)
				if (array_key_exists($arUserFields[$i], $_REQUEST))
					${"str_".$arUserFields[$i]} = htmlspecialchars($_REQUEST[$arUserFields[$i]]);
		
			$arUserFields = &$DB->GetTableFieldsList("b_forum_user");
			for ($i = 0; $i < count($arUserFields); $i++)
				if (array_key_exists("FORUM_".$arUserFields[$i], $_REQUEST))
					${"str_FORUM_".$arUserFields[$i]} = htmlspecialchars($_REQUEST["FORUM_".$arUserFields[$i]]);
		}
		?>
		
		<?
		echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));
		echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));
		?>
		
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function SectionClick(id)
		{
			var div = document.getElementById('user_div_'+id);
			document.cookie = "user_div_"+id+"="+(div.style.display != 'none'? 'N':'Y')+"; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=<?echo BX_ROOT?>/admin/;";
			div.style.display = (div.style.display != 'none'? 'none':'block');
		}
		//-->
		</SCRIPT>
		
		<form method="POST" name="form1" action="profile.php" enctype="multipart/form-data">
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" class="forumtitle">
					<?echo GetMessage("FP_CHANGE_PROFILE")?>
				</td>
			</tr>
		</table>
		
		<font style="font-size:4px;">&nbsp;<br></font>
		
		<input type="hidden" name="Update" value="Y">
		<input type="hidden" name="ID" value="<?= $ID?>">
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
			<tr valign="top" class="forumbody">
				<td width="40%" class="forumbrd" style="border-right:none;border-bottom:none;" align="right"><font class="forumfieldtext"><?echo GetMessage("FP_NAME")?></font></td>
				<td width="60%" class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="NAME" size="45" maxlength="50" value="<?= $str_NAME?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_LAST_NAME")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="LAST_NAME" size="45" maxlength="50" value="<?= $str_LAST_NAME?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><font class="starrequired">*</font>E-Mail:</font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="EMAIL" size="45" maxlength="50" value="<? echo $str_EMAIL?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><font class="starrequired">*</font><?echo GetMessage("FP_LOGIN")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="LOGIN" size="45" maxlength="50" value="<? echo $str_LOGIN?>"><input type="hidden" name="OLD_LOGIN" value="<? echo $str_LOGIN?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_NEW_PASSWORD")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input class="inputtext" type="password" name="NEW_PASSWORD" size="45" maxlength="50" value="<? echo $NEW_PASSWORD ?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_PASSWORD_CONFIRM")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input class="inputtext" type="password" name="NEW_PASSWORD_CONFIRM" size="45" maxlength="50" value="<? echo $NEW_PASSWORD_CONFIRM ?>"></td>
			</tr>
		
			<tr valign="top" class="forumhead">
				<td class="forumbrd" colspan="2" style="border-bottom:none;"><font class="forumheadtext"><b><?echo GetMessage("FP_PRIVATE_INFO")?></b></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_PROFESSION")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="PERSONAL_PROFESSION" size="45" maxlength="255" value="<?=$str_PERSONAL_PROFESSION?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_WWW_PAGE")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="PERSONAL_WWW" size="45" maxlength="255" value="<?=$str_PERSONAL_WWW?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext">ICQ:</font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="PERSONAL_ICQ" size="45" maxlength="255" value="<?=$str_PERSONAL_ICQ?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_SEX")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><?
					$arr = array(
						"reference" => array(GetMessage("FP_SEX_MALE"), GetMessage("FP_SEX_FEMALE")),
						"reference_id" => array("M","F"));
					echo SelectBoxFromArray("PERSONAL_GENDER", $arr, $str_PERSONAL_GENDER, GetMessage("FP_SEX_NONE"), "class='inputselect'");
					?></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_BIRTHDATE")?><?= CLang::GetDateFormat("SHORT")?>):</font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><font class="forumheadtext"><?echo CalendarDate("PERSONAL_BIRTHDAY", $str_PERSONAL_BIRTHDAY, "form1", "15")?></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_PHOTO")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><font class="forumbodytext"><?
				echo CFile::InputFile("PERSONAL_PHOTO", 30, $str_PERSONAL_PHOTO, false, 0, "IMAGE", "class=\"inputfile\"", 0, "class=inputtext", "", False);
				if (strlen($str_PERSONAL_PHOTO)>0):
					?><br><?
					echo CFile::ShowImage($str_PERSONAL_PHOTO, 150, 150, "border=0", "", true);
				endif;
				?></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td class="forumbrd" style="border-bottom:none;" colspan="2" align="center"><font class="forumheadtext"><b><?echo GetMessage("FP_LOCATION")?></b></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_COUNTRY")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><?echo SelectBoxFromArray("PERSONAL_COUNTRY", GetCountryArray(), $str_PERSONAL_COUNTRY, GetMessage("FP_COUNTRY_NONE"), "class='inputselect'");?></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_REGION")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="PERSONAL_STATE" size="45" maxlength="255" value="<?=$str_PERSONAL_STATE?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_CITY")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="PERSONAL_CITY" size="45" maxlength="255" value="<?=$str_PERSONAL_CITY?>"></td>
			</tr>
			<tr valign="top" class="forumhead">
				<td colspan="2" class="forumbrd" style="border-bottom:none;"><font class="forumheadtext"><b><?echo GetMessage("FP_WORK_INFO")?></b></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_COMPANY_NAME")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="WORK_COMPANY" size="45" maxlength="255" value="<?=$str_WORK_COMPANY?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_WWW_PAGE")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="WORK_WWW" size="45" maxlength="255" value="<?=$str_WORK_WWW?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_COMPANY_DEPARTMENT")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="WORK_DEPARTMENT" size="45" maxlength="255" value="<?=$str_WORK_DEPARTMENT?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_COMPANY_ROLE")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="WORK_POSITION" size="45" maxlength="255" value="<?=$str_WORK_POSITION?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_COMPANY_ACT")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><textarea name="WORK_PROFILE" class="inputtextarea" cols="35" rows="5"><?echo $str_WORK_PROFILE?></textarea></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td colspan="2" align="center" class="forumbrd" style="border-bottom:none;"><font class="forumheadtext"><b><?echo GetMessage("FP_COMPANY_LOCATION")?></b></font></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_COUNTRY")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><?echo SelectBoxFromArray("WORK_COUNTRY", GetCountryArray(), $str_WORK_COUNTRY, GetMessage("FP_COUNTRY_NONE"), "class='inputselect'");?></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_REGION")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="WORK_STATE" size="45" maxlength="255" value="<?=$str_WORK_STATE?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_CITY")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input type="text" class="inputtext" name="WORK_CITY" size="45" maxlength="255" value="<?=$str_WORK_CITY?>"></td>
			</tr>
		
			<tr valign="top" class="forumhead">
				<td class="forumbrd" colspan="2" style="border-bottom:none;"><font class="forumheadtext"><b><?echo GetMessage("FP_FORUM_PROFILE")?></b></font></td>
			</tr>
			<?if ($USER->IsAdmin()):?>
				<tr valign="top" class="forumbody">
					<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_ALLOW_POST")?></font></td>
					<td class="forumbrd forumbrd1" style="border-bottom:none;"><input class="inputcheckbox" type="checkbox" name="FORUM_ALLOW_POST" value="Y" <?if ($str_FORUM_ALLOW_POST=="Y") echo "checked";?>></td>
				</tr>
			<?endif;?>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_SHOW_NAME")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input class="inputcheckbox" type="checkbox" name="FORUM_SHOW_NAME" value="Y" <?if ($str_FORUM_SHOW_NAME=="Y") echo "checked";?>></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;">
					<font class="forumfieldtext"><?echo GetMessage("FP_NOT_SHOW_IN_LIST")?> "<?echo GetMessage("FP_NOW_ONLINE")?>":</font>
				</td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;">
					<input type="checkbox" class="inputcheckbox" name="FORUM_HIDE_FROM_ONLINE" value="Y" <?if ($str_FORUM_HIDE_FROM_ONLINE=="Y") echo "checked";?>>
				</td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;">
					<font class="forumfieldtext"><?echo GetMessage("FP_SUBSC_GET_MY_MESSAGE")?> :</font>
				</td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;">
					<input type="checkbox" class="inputcheckbox" name="FORUM_SUBSC_GET_MY_MESSAGE" value="Y" <?if ($str_FORUM_SUBSC_GET_MY_MESSAGE=="Y") echo "checked";?>>
				</td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_DESCR")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><input class="inputtext" type="text" name="FORUM_DESCRIPTION" size="45" maxlength="255" value="<?=$str_FORUM_DESCRIPTION?>"></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_INTERESTS")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><textarea class="inputtextarea" name="FORUM_INTERESTS" rows="3" cols="35"><?echo $str_FORUM_INTERESTS; ?></textarea></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-bottom:none;border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_SIGNATURE")?></font></td>
				<td class="forumbrd forumbrd1" style="border-bottom:none;"><textarea class="inputtextarea" name="FORUM_SIGNATURE" rows="3" cols="35"><?echo $str_FORUM_SIGNATURE; ?></textarea></td>
			</tr>
			<tr valign="top" class="forumbody">
				<td align="right" class="forumbrd" style="border-right:none;"><font class="forumfieldtext"><?echo GetMessage("FP_AVATAR")?></font></td>
				<td class="forumbrd forumbrd1"><font class="forumbodytext"><?
					echo CFile::InputFile("FORUM_AVATAR", 30, $str_FORUM_AVATAR, false, 0, "IMAGE", "class=\"inputfile\"", 0, "class=inputtext", "", False);
					if (strlen($str_FORUM_AVATAR)>0):
						?><br><?
						echo CFile::ShowImage($str_FORUM_AVATAR, 150, 150, "border=0", "", true);
					endif;
					?></font></td>
			</tr>
		</table>
		<p align="left">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ACTION" value="EDIT">
		<input class="inputbutton" type="submit" name="save" value="<?echo GetMessage("FP_SAVE")?>">&nbsp;
		<input class="inputbutton" type="reset" value="<?echo GetMessage("FP_CANCEL")?>">
		</p>
		</form>
		
		<font class="forumbodytext">
		<font class="starrequired">*</font> <?echo GetMessage("FP_REQUIED_FILEDS")?>
		</font>
		
		<br><br><br>
		<?
		$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");
		
		//*******************************************************
	else:
		?>
		<font class="text"><b><?echo GetMessage("FP_NO_MODULE")?></b></font>
		<?
	endif;
else:
	?>
	<font class="text"><b><?echo GetMessage("FP_NO_AUTHORIZE")?></b></font>
	<?
endif;
?>