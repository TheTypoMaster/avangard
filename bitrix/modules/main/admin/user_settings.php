<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
define("HELP_FILE", "settings/user_settings.php");

$editable = ($USER->CanDoOperation('edit_own_profile') || $USER->CanDoOperation('edit_other_settings'));
if (!$USER->CanDoOperation('view_other_settings') && !$editable)
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("user_sett_tab"), "ICON"=>"", "TITLE"=>GetMessage("user_sett_tab_title")),
	array("DIV" => "edit2", "TAB" => GetMessage("user_sett_del"), "ICON"=>"", "TITLE"=>GetMessage("user_sett_del_title")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$bFormValues = false;
$sSuccessMsg = "";

if($_REQUEST["action"] <> "" && $editable && check_bitrix_sessid())
{
	if($_REQUEST["action"] == "clear")
	{
		CUserOptions::DeleteUsersOptions($USER->GetID());
		$sSuccessMsg .= GetMessage("user_sett_mess_del")."<br>";
	}
	if($_REQUEST["action"] == "clear_links")
	{
		CUserOptions::DeleteOption("start_menu", "recent");
		$sSuccessMsg .= GetMessage("user_sett_mess_links")."<br>";
	}
	if($_REQUEST["action"] == "clear_all" && $USER->CanDoOperation('edit_other_settings'))
	{
		CUserOptions::DeleteCommonOptions();
		$sSuccessMsg .= GetMessage("user_sett_mess_del_common")."<br>";
	}
	if($_REQUEST["action"] == "clear_all_user" && $USER->CanDoOperation('edit_other_settings'))
	{
		CUserOptions::DeleteUsersOptions();
		$sSuccessMsg .= GetMessage("user_sett_mess_del_user")."<br>";
	}
	if($sSuccessMsg <> "")
	{
		$_SESSION["ADMIN"]["USER_SETTINGS_MSG"] = $sSuccessMsg;
		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam());
	}
}

if($_SERVER["REQUEST_METHOD"]=="POST" && $_REQUEST["Update"]=="Y" && $editable && check_bitrix_sessid())
{
	$aMsg = array();
	if($_REQUEST["theme_id"] == "")
		$aMsg[] = array("id"=>"theme_id", "text"=>GetMessage("user_sett_err"));

	if(empty($aMsg))
	{
		$aFields = array(
			"theme_id" => $_REQUEST["theme_id"],
			"context_menu" => ($_REQUEST["context_menu"] == "Y"? "Y":"N"),
			"context_ctrl" => ($_REQUEST["context_ctrl"] == "Y"? "Y":"N"),
			"autosave" => ($_REQUEST["autosave"] == "Y"? "Y":"N"),
			"start_menu_links" => intval($_REQUEST["start_menu_links"]),
			"start_menu_preload" => ($_REQUEST["start_menu_preload"] == "Y"? "Y":"N"),
			"start_menu_title" => ($_REQUEST["start_menu_title"] == "Y"? "Y":"N"),
			"panel_dynamic_mode" => ($_REQUEST["panel_dynamic_mode"] == "Y"? "Y":"N"),
			"page_edit_control_enable" => ($_REQUEST["page_edit_control_enable"] == "Y"? "Y":"N"),
			"messages" => array(
				"support"=>($_REQUEST["messages_support"] == "Y"? "Y":"N"),
				"security"=>($_REQUEST["messages_security"] == "Y"? "Y":"N"),
				"perfmon"=>($_REQUEST["messages_perfmon"] == "Y"? "Y":"N"),
			),
			"sound" => ($_REQUEST["sound"] == "Y"? "Y":"N"),
			"sound_login" => $_REQUEST["sound_login"],
			"panel_color" => $_REQUEST["panel_color"]
		);

		//common default
		if($USER->CanDoOperation('edit_other_settings') && $_REQUEST["default"] == "Y")
		{
			CUserOptions::SetOption("global", "settings", $aFields, true);
			$sSuccessMsg .= GetMessage("user_sett_mess_save")."<br>";
		}

		//personal
		CUserOptions::SetOption("global", "settings", $aFields);
		$sSuccessMsg .= GetMessage("user_sett_mess_save1")."<br>";

		$_SESSION["ADMIN"]["USER_SETTINGS_MSG"] = $sSuccessMsg;
		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID);
	}
	else
	{
		$bFormValues = true;
		$APPLICATION->ThrowException(new CAdminException($aMsg));
	}
}

$APPLICATION->SetTitle(GetMessage("user_sett_title"));
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

if($bFormValues)
{
	$aUserOpt = array(
		"theme_id"=>$_REQUEST["theme_id"],
		"context_menu"=>$_REQUEST["context_menu"],
		"context_ctrl"=>$_REQUEST["context_ctrl"],
		"autosave"=>$_REQUEST["autosave"],
		"start_menu_links"=>$_REQUEST["start_menu_links"],
		"start_menu_preload"=>$_REQUEST["start_menu_preload"],
		"start_menu_title"=>$_REQUEST["start_menu_title"],
		"panel_dynamic_mode"=>$_REQUEST["panel_dynamic_mode"],
		"page_edit_control_enable" => $_REQUEST['page_edit_control_enable'],
		"messages" => array(
			"support"=>$_REQUEST["messages_support"],
			"security"=>$_REQUEST["messages_security"],
			"perfmon"=>$_REQUEST["messages_perfmon"],
		),
		"sound" => $_REQUEST["sound"],
		"sound_login" => $_REQUEST["sound_login"],
		"panel_color" => $_REQUSET["panel_color"]
	);
}
else
{
	$aUserOpt = CUserOptions::GetOption("global", "settings");
	if($aUserOpt["theme_id"] == "") $aUserOpt["theme_id"] = ".default";
	if($aUserOpt["context_menu"] == "") $aUserOpt["context_menu"] = "Y";
	if($aUserOpt["context_ctrl"] == "") $aUserOpt["context_ctrl"] = "N";
	if($aUserOpt["autosave"] == "") $aUserOpt["autosave"] = "Y";
	if($aUserOpt["start_menu_links"] == "") $aUserOpt["start_menu_links"] = "5";
	if($aUserOpt["start_menu_preload"] == "") $aUserOpt["start_menu_preload"] = "N";
	if($aUserOpt["start_menu_title"] == "") $aUserOpt["start_menu_title"] = "Y";
	if($aUserOpt["panel_dynamic_mode"] == "") $aUserOpt["panel_dynamic_mode"] = "N";
	if($aUserOpt["page_edit_control_enable"] == "") $aUserOpt["page_edit_control_enable"] = "Y";
	if($aUserOpt["messages"]["support"] == "") $aUserOpt["messages"]["support"] = "Y";
	if($aUserOpt["messages"]["security"] == "") $aUserOpt["messages"]["security"] = "Y";
	if($aUserOpt["messages"]["perfmon"] == "") $aUserOpt["messages"]["perfmon"] = "Y";
	if($aUserOpt["sound"] == "") $aUserOpt["sound"] = "N";
	if($aUserOpt["sound_login"] == "") $aUserOpt["sound_login"] = "/bitrix/sounds/main/bitrix_tune.mp3";
//	if($aUserOpt["panel_color"] == "") $aUserOpt["panel_color"] = "#214385";
}

$message = null;
if($e = $APPLICATION->GetException())
{
	$message = new CAdminMessage(GetMessage("user_sett_err_title"), $e);
	echo $message->Show();
}

if(!empty($_SESSION["ADMIN"]["USER_SETTINGS_MSG"]))
{
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("user_sett_mess_title"), "TYPE"=>"OK", "DETAILS"=>$_SESSION["ADMIN"]["USER_SETTINGS_MSG"], "HTML"=>true));
	unset($_SESSION["ADMIN"]["USER_SETTINGS_MSG"]);
}
?>
<form method="POST" name="form1" action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("user_sett_personal")?></td>
	</tr>
	<tr valign="top">
		<td width="40%"><span class="required">*</span><?echo GetMessage("user_sett_theme")?></td>
		<td width="60%">
<table cellspacing="0" class="internal">
	<tr class="heading">
		<td style="border-right:none;">&nbsp;</td>
		<td style="border-left:none; text-align:left;"><?echo GetMessage("user_sett_name")?></td>
		<td><?echo GetMessage("user_sett_files")?></td>
		<td>&nbsp;</td>
	</tr>
<?
$aThemes = CAdminTheme::GetList();
$n = 0;
foreach($aThemes as $theme):
?>
	<tr>
		<td style="border-right:none; padding-right:0px;"><input type="radio" name="theme_id" id="theme_<?echo $n?>" value="<?echo htmlspecialchars($theme["ID"])?>"<?if($aUserOpt["theme_id"] == $theme["ID"]) echo " checked"?>></td>
		<td style="border-left:none;"><label for="theme_<?echo $n?>" title="<?echo htmlspecialchars($theme["DESCRIPTION"])?>"><?echo htmlspecialchars($theme["NAME"])?></label></td>
		<td><a href="fileman_admin.php?lang=<?echo LANGUAGE_ID?>&amp;path=<?echo ADMIN_THEMES_PATH."/".$theme["ID"]?>" title="<?echo GetMessage("user_sett_files_title")?>"><?echo htmlspecialchars($theme["ID"])?></a></td>
		<td>
<?
if($theme["PREVIEW"] <> "")
{
	$previewName = ADMIN_THEMES_PATH."/".$theme["ID"]."/".$theme["PREVIEW"];
	if(file_exists($_SERVER["DOCUMENT_ROOT"].$previewName))
	{
		$aSize = CFile::GetImageSize($_SERVER["DOCUMENT_ROOT"].$previewName);
		if($aSize !== false)
		{
			CFile::OutputJSImgShw();
			echo '<a title="'.GetMessage("user_sett_screen").'" href="'.htmlspecialchars($previewName).'" onclick="ImgShw(\''.htmlspecialchars(CUtil::JSEscape($previewName)).'\', '.$aSize[0].', '.$aSize[1].', \'\'); return false;"><img src="/bitrix/images/main/preview.gif" width="16" height="16" alt="'.GetMessage("user_sett_screen").'" border="0"></a>';
		}
	}
}
?>
		</td>
	</tr>
<?
	$n++;
endforeach;
?>
</table>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("user_sett_context")?></td>
		<td><input type="checkbox" name="context_menu" value="Y"<?if($aUserOpt["context_menu"] == "Y") echo " checked"?> onclick="this.form.context_ctrl[0].disabled = this.form.context_ctrl[1].disabled = !this.checked"></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_context_ctrl")?></td>
		<td>
			<input type="radio" name="context_ctrl" id="context_ctrl_N" value="N"<?if($aUserOpt["context_ctrl"] <> "Y") echo " checked"?><?if($aUserOpt["context_menu"] <> "Y") echo " disabled"?>><label for="context_ctrl_N"><?echo GetMessage("user_sett_context_ctrl_val1")?></label><br>
			<input type="radio" name="context_ctrl" id="context_ctrl_Y" value="Y"<?if($aUserOpt["context_ctrl"] == "Y") echo " checked"?><?if($aUserOpt["context_menu"] <> "Y") echo " disabled"?>><label for="context_ctrl_Y"><?echo GetMessage("user_sett_context_ctrl_val2")?></label><br>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("user_sett_autosave")?></td>
		<td><input type="checkbox" name="autosave" value="Y"<?if($aUserOpt["autosave"] == "Y") echo " checked=\"checked\""?> /></td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("user_sett_panel")?></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("MAIN_OPTION_DYN_EDIT")?></td>
		<td><input type="checkbox" name="panel_dynamic_mode" value="Y"<?if($aUserOpt["panel_dynamic_mode"] == "Y") echo " checked"?>></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("MAIN_OPTION_PAGE_EDIT_ENABLE")?></td>
		<td><input type="checkbox" name="page_edit_control_enable" value="Y"<?if($aUserOpt["page_edit_control_enable"] != "N") echo " checked"?>></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("MAIN_OPTION_PANEL_COLOR")?></td>
		<td><input type="text" name="panel_color" id="panel_color" onchange="_PanelSetColor(this.value)" size="7" value="<?=htmlspecialchars($aUserOpt['panel_color'])?>" align="left">
			<style>
			table.tcell td {height: 30px; width: 30px;}
			</style>
			<script>
			function _ClickSetColor(c)
			{
				_PanelSetColor(c);
				BX('panel_color').value = c;
				BX.fireEvent(BX('panel_color'), 'change');
			}
			function _PanelSetColor(c)
			{
				try{
					BX('bx-panel-admin-tab-background').style.cssText = 'background-color:' + c + ' !important;';
					BX('bx-panel-admin-toolbar').style.cssText = 'background-color:' + c + ' !important;';
				}catch(e)
				{}
			}
			</script>
			<?
			$arColors = array("#214385", "#000000", "#404040", "#6e1111", "#245a0a", "#5b4d0b", "#641861", "#185166", "#8a3e00", "#0d1545");
			?>

			<table class="tcell">
				<?for($i=0; $i<2; $i++):?>
					<tr>
					<?for($j=0; $j<5; $j++):?>
						<td style="background-color: <?=$arColors[$i*5+$j]?>; cursor: pointer;" onclick="_ClickSetColor('<?=$arColors[$i*5+$j]?>')">&nbsp;</td>
					<?endfor?>
					</tr>
				<?endfor?>
			</table>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("user_sett_start")?></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_start_preload")?></td>
		<td><input type="checkbox" name="start_menu_preload" value="Y"<?if($aUserOpt["start_menu_preload"] == "Y") echo " checked"?>></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_titles")?></td>
		<td><input type="checkbox" name="start_menu_title" value="Y"<?if($aUserOpt["start_menu_title"] == "Y") echo " checked"?>></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_start_links")?></td>
		<td>
			<input type="text" name="start_menu_links" value="<?echo htmlspecialchars($aUserOpt["start_menu_links"])?>" size="10"><br>
			<a href="javascript:if(confirm('<?echo CUtil::addslashes(GetMessage("user_sett_del_links_conf"))?>'))window.location='user_settings.php?action=clear_links&lang=<?echo LANG?>&<?echo bitrix_sessid_get()?>';"><?echo GetMessage("user_sett_del_links")?></a>
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><?echo GetMessage("user_sett_sounds")?></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_sounds_play")?></td>
		<td><input type="checkbox" name="sound" value="Y"<?if($aUserOpt["sound"] == "Y") echo " checked"?>></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_sounds_login")?></td>
		<td>
<?
CAdminFileDialog::ShowScript(
	Array
	(
		"event" => "OpenFileBrowserWindFile",
		"arResultDest" => Array("FORM_NAME" => "form1", "FORM_ELEMENT_NAME" => "sound_login"),
		"arPath" => Array('PATH' => '/bitrix/sounds/main/'),
		"select" => 'F',// F - file only, D - folder only
		"operation" => 'O',// O - open, S - save
		"showUploadTab" => true,
		"fileFilter" => 'wma,mp3,aac',
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);
?>
			<input type="text" name="sound_login" value="<?echo htmlspecialchars($aUserOpt["sound_login"])?>" size="40">
			<input type="button" value="..." title="<?echo GetMessage("user_sett_sounds_button_title")?>" onclick="OpenFileBrowserWindFile()">
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><?echo GetMessage("user_sett_mess_head")?></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("user_sett_mess")?></td>
		<td>
			<input type="checkbox" name="messages_support" value="Y" id="messages_support"<?if($aUserOpt['messages']['support'] == 'Y') echo " checked"?>><label for="messages_support"><?echo GetMessage("user_sett_mess_support")?></label>
<?if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/install/index.php")):?>
			<br><input type="checkbox" name="messages_security" value="Y" id="messages_security"<?if($aUserOpt['messages']['security'] == 'Y') echo " checked"?>><label for="messages_security"><?echo GetMessage("user_sett_mess_security")?></label>
<?endif;?>
<?if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/install/index.php")):?>
			<br><input type="checkbox" name="messages_perfmon" value="Y" id="messages_perfmon"<?if($aUserOpt['messages']['perfmon'] == 'Y') echo " checked"?>><label for="messages_perfmon"><?echo GetMessage("user_sett_mess_perfmon")?></label>
<?endif;?>
		</td>
	</tr>
<?if($USER->CanDoOperation('edit_other_settings')):?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("user_sett_common")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("user_sett_common_set")?></td>
		<td><input type="checkbox" name="default" value="Y"></td>
	</tr>
<?endif;?>
<?
$tabControl->BeginNextTab();
?>
	<tr colspan="2">
		<td><a href="javascript:if(confirm('<?echo CUtil::addslashes(GetMessage("user_sett_del_pers_conf"))?>'))window.location='user_settings.php?action=clear&lang=<?echo LANG?>&<?echo bitrix_sessid_get()?>&tabControl_active_tab=edit2';"><?echo GetMessage("user_sett_del_pers1")?></a></td>
	</tr>
<?if($USER->CanDoOperation('edit_other_settings')):?>
	<tr colspan="2">
		<td><a href="javascript:if(confirm('<?echo CUtil::addslashes(GetMessage("user_sett_del_common_conf"))?>'))window.location='user_settings.php?action=clear_all&lang=<?echo LANG?>&<?echo bitrix_sessid_get()?>&tabControl_active_tab=edit2';"><?echo GetMessage("user_sett_del_common1")?></a></td>
	</tr>
	<tr colspan="2">
		<td><a href="javascript:if(confirm('<?echo CUtil::addslashes(GetMessage("user_sett_del_user_conf"))?>'))window.location='user_settings.php?action=clear_all_user&lang=<?echo LANG?>&<?echo bitrix_sessid_get()?>&tabControl_active_tab=edit2';"><?echo GetMessage("user_sett_del_user1")?></a></td>
	</tr>
<?endif;?>

<?
$tabControl->Buttons();
?>
<input<?if(!$editable) echo " disabled"?> type="submit" name="apply" value="<?echo GetMessage("admin_lib_edit_apply")?>" title="<?echo GetMessage("admin_lib_edit_apply_title")?>">
<?
$tabControl->End();
$tabControl->ShowWarnings("form1", $message);
?>
</form>

<?echo BeginNote();?>
<span class="required">*</span> <?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>
<?
require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/epilog_admin.php");
?>
