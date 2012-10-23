<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*******************************************************************/
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
//$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/forum.interface/templates/popup/script.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/forum.interface/templates/.default/script.js"></script>', true);
IncludeAJAX();
/*******************************************************************/
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] != "N" ? "Y" : "Y");
$arParams["FILES_COUNT"] = intVal(intVal($arParams["FILES_COUNT"]) > 0 ? $arParams["FILES_COUNT"] : 5);
$arParams["IMAGE_SIZE"] = (intVal($arParams["IMAGE_SIZE"]) > 0 ? $arParams["IMAGE_SIZE"] : 100);
$arParams["form_index"] = $_REQUEST["INDEX"];
if (!empty($arParams["form_index"]))
	$arParams["form_index"] = preg_replace("/[^a-z0-9]/is", "_", $arParams["form_index"]);
$tabIndex = 10;
/*******************************************************************/
if (LANGUAGE_ID == 'ru')
{
	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
	@include_once($path);
}
/*******************************************************************/
?>
<a name="postform"></a>
<div class="forum-title">
<?
if ($arResult["MESSAGE_TYPE"]=="NEW"):
	?><?=GetMessage("FPF_CREATE_IN_FORUM")?>: <a href="<?=$arResult["list"]?>" class="forum"><?=$arResult["FORUM"]["NAME"]?></a><?
elseif ($arResult["MESSAGE_TYPE"]=="REPLY"):
	?><?=GetMessage("FPF_REPLY_FORM")?><?
else:
	?><?=GetMessage("FPF_EDIT_FORM")?> <?=GetMessage("FPF_IN_TOPIC")?>: 
		<a href="<?=$arResult["read"]?>" class="forum-topic"><?=$arResult["~TOPIC"]["TITLE"]?></a>, <?=GetMessage("FPF_IN_FORUM")?>: 
		<a href="<?=$arResult["list"]?>" class="forum"><?=$arResult["FORUM"]["NAME"]?></a><?
endif;?>
</div>
<div class="forum-br"></div>
<?
if ($arParams["AJAX_CALL"] == "Y"):
	$GLOBALS["bShowImageScriptPopup"] = true;
	ob_end_clean();
	ob_start();
	if (!empty($arParams["ERROR_MESSAGE"]))
		ShowError($arParams["ERROR_MESSAGE"]);
endif;
?>
<form name="REPLIER<?=$arParams["form_index"]?>" id="REPLIER<?=$arParams["form_index"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
	?> method="post" enctype="multipart/form-data" onsubmit="return ValidateForm(this, '<?=$arParams["AJAX_TYPE"]?>');"<?
	?> onkeydown="if(null != init_form){init_form(this)}" onmouseover="if(init_form){init_form(this)}" class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="<?=$arParams["PAGE_NAME"];?>" />
	<input type="hidden" name="FID" value="<?=$arParams["FID"]?>" />
	<input type="hidden" name="TID" value="<?=$arParams["TID"]?>" />
	<input type="hidden" name="MID" value="<?=$arResult["MID"];?>" />
	<input type="hidden" name="MESSAGE_TYPE" value="<?=$arParams["MESSAGE_TYPE"];?>" />
	<input type="hidden" name="AUTHOR_ID" value="<?=$arResult["TOPIC"]["AUTHOR_ID"];?>" />
	<input type="hidden" name="forum_post_action" value="save" />
	<input type="hidden" name="MESSAGE_MODE" value="NORMAL" />
	<?=bitrix_sessid_post()?>
<table class="forum-main forum-post-form" cellpadding="0" cellspacing="0" border="0">
<thead>
<?
if ($arParams["AJAX_CALL"] == "N"):
/* GUEST PANEL */
if ($arResult["SHOW_PANEL_GUEST"] == "Y"):?>
	<tr><th><?=GetMessage("FPF_UNREG_USER_INFO")?></th></tr>
	<tr><td><span class="title title-name"><font class="starrequired">*</font><?=GetMessage("FPF_TYPE_NAME")?></span> 
		<span class="value value-name">
			<input name="AUTHOR_NAME" type="text" value="<?=$arResult["MESSAGE"]["AUTHOR_NAME"];?>" tabindex="<?=$tabIndex++;?>" /></span></tr><?
		
	if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y"):?>
	<tr><td><span class="title title-email"><?=GetMessage("FPF_TYPE_EMAIL")?></span>
		<span class="value value-email"><input type="text" name="AUTHOR_EMAIL" value="<?=$arResult["MESSAGE"]["AUTHOR_EMAIL"];?>" tabindex="<?=$tabIndex++;?>" /></span></td></tr><?
	endif;
endif;

/* NEW TOPIC */
if ($arResult["SHOW_PANEL_NEW_TOPIC"] == "Y"):?>
	<tr><th><?=GetMessage("FPF_TOPIC_PARAMS")?></th></tr>
	<tr><td>
		<span class="title title-title"><font class="starrequired">*</font><?=GetMessage("FPF_TOPIC_NAME")?></span>
		<span class="value value-title"><input name="TITLE" type="text" value="<?=$arResult["TOPIC"]["TITLE"];?>" tabindex="<?=$tabIndex++;?>" /></span></td></tr>
	<tr><td>
		<span class="title title-description"><?=GetMessage("FPF_TOPIC_DESCR")?></span>
		<span class="value value-description"><input name="DESCRIPTION" type="text" value="<?=$arResult["TOPIC"]["DESCRIPTION"];?>" tabindex="<?=$tabIndex++;?>" /></span></td></tr><?
	
	if ($arParams["SHOW_TAGS"] == "Y"):
?>
	<tr title="<?=GetMessage("FPF_TOPIC_TAGS_DESCRIPTION")?>"><td>
		<span class="title title-tags"><?=GetMessage("FPF_TOPIC_TAGS")?></span>
		<span class="value value-tags"><?
		if ($arResult["SHOW_SEARCH"] == "Y"):
		$APPLICATION->IncludeComponent(
			"bitrix:search.tags.input", 
			"", 
			array(
				"VALUE" => $arResult["TOPIC"]["~TAGS"], 
				"NAME" => "TAGS",
				"TEXT" => 'tabindex="'.$tabIndex++.'"'),
			$component,
			array("HIDE_ICONS" => "Y"));
		else:
			?><input name="TAGS" type="text" value="<?=$arResult["TOPIC"]["TAGS"]?>" tabindex="<?=$tabIndex++;?>" /><?
		endif;
?>
	</span></td></tr>
<?
	endif;
	
?>
	<tr title="<?=GetMessage("FPF_TOPIC_ICON_DESCRIPTION")?>"><td>
		<span class="title title-icons"><?=GetMessage("FPF_TOPIC_ICON")?></span>
		<span class="value value-icons"><?=$arResult["ForumPrintIconsList"];?></span></td></tr>
<?
endif;

?>
	<tr><th><?=GetMessage("FPF_MESSAGE_TEXT")?></th></tr>
</thead>
<?

endif;

?>
<tbody>
	<tr><td>
		<table class="clear" cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
<?

	if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):
?>
		<td width="5%">
		<table class="forum-smile" cellpadding="0" cellspacing="0" border="0">
			<tr><th colspan="<?=intVal($arParams["SMILE_TABLE_COLS"])?>"><?=GetMessage("FPF_SMILES")?></th></tr>
			<?=$arResult["ForumPrintSmilesList"]?>
		</table>
		</td><td width="95%">
<?
	else:
?>
		<td width="100%">
<?
	endif;

if ($arResult["FORUM"]["ALLOW_FONT"] == "Y" || $arResult["FORUM"]["ALLOW_BUI"] == "Y" || $arResult["FORUM"]["ALLOW_ANCHOR"] == "Y" || 
	$arResult["FORUM"]["ALLOW_IMG"] == "Y" || $arResult["FORUM"]["ALLOW_QUOTE"] == "Y" || $arResult["FORUM"]["ALLOW_CODE"] == "Y"):
?>
	<table class="forum-toolbars" cellpadding="0" cellspacing="0" border="0" width="100%"><tr class="top"><td>
<?
if ($arResult["FORUM"]["ALLOW_FONT"] == "Y"):
?>
	<div class="form_button button_font">
		<select name='FONT' class='button_font' id='form_font' title="<?=GetMessage("FPF_FONT_TITLE")?>">
			<option value='none'><?=GetMessage("FPF_FONT")?></option>
			<option value='Arial' style='font-family:Arial'>Arial</option>
			<option value='Times' style='font-family:Times'>Times</option>
			<option value='Courier' style='font-family:Courier'>Courier</option>
			<option value='Impact' style='font-family:Impact'>Impact</option>
			<option value='Geneva' style='font-family:Geneva'>Geneva</option>
			<option value='Optima' style='font-family:Optima'>Optima</option>
			<option value='Verdana' style='font-family:Verdana'>Verdana</option>
		</select>
	</div>
	<div class="form_button button_color" id="form_palette" title="<?=GetMessage("FPF_COLOR_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_BIU"] == "Y"):
?>
	<div class="form_button button_bold" id="form_b" title="<?=GetMessage("FPF_BOLD")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
	<div class="form_button button_italic" id="form_i" title="<?=GetMessage("FPF_ITAL")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
	<div class="form_button button_underline" id="form_u" title="<?=GetMessage("FPF_UNDER")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
	<div class="form_button button_strike" id="form_s" title="<?=GetMessage("FPF_STRIKE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;
if ($arResult["FORUM"]["ALLOW_ANCHOR"] == "Y"):
?>
	<div class="form_button button_url" id="form_url" title="<?=GetMessage("FPF_HYPERLINK_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;
if ($arResult["FORUM"]["ALLOW_IMG"] == "Y"):
?>
	<div class="form_button button_img" id="form_img" title="<?=GetMessage("FPF_IMAGE_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_VIDEO"] == "Y"):
?>
	<div class="form_button button_video" id="form_video" title="<?=GetMessage("FPF_VIDEO_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):
?>
	<div class="form_button button_quote" id="form_quote" title="<?=GetMessage("FPF_QUOTE_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;
if ($arResult["FORUM"]["ALLOW_CODE"] == "Y"):
?>
	<div class="form_button button_code" id="form_code" title="<?=GetMessage("FPF_CODE_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_LIST"] == "Y"):
?>
	<div class="form_button button_list" id="form_list" title="<?=GetMessage("FPF_LIST_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;

if ($arResult["SHOW_PANEL_TRANSLIT"] == "Y"):
?>
	<div class="form_button button_translit" id="form_translit" title="<?=GetMessage("FPF_TRANSLIT_TITLE")?>">
		<img src="/bitrix/components/bitrix/forum/templates/old/images/postform/empty_for_ie.gif" /></div>
<?
endif;
?>
	<div class="button_closeall" title="<?=GetMessage("FPF_CLOSE_OPENED_TAGS")?>" id="form_closeall" style="display:none;">
		<a href="javascript:void(0)"><?=GetMessage("FPF_CLOSE_ALL_TAGS")?></a></div>
</td></tr>
<tr class="post_message"><td>
<textarea name="POST_MESSAGE" class="post_message" rows="15" tabindex="<?=$tabIndex++;?>"><?=$arResult["MESSAGE"]["POST_MESSAGE"];?></textarea></td></tr>
</table>
<?
else:
?>
	<textarea name="POST_MESSAGE" class="post_message" rows="15" tabindex="<?=$tabIndex++;?>"><?=$arResult["MESSAGE"]["POST_MESSAGE"];?></textarea>
<?
endif;

if ($arResult["FORUM"]["ALLOW_SMILES"]=="Y"):?>
		<div class="group smiles"><input type="checkbox" name="USE_SMILES" id="USE_SMILES<?=$arParams["form_index"]?>" <?
			?>value="Y" <?=($arResult["MESSAGE"]["USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
			?>tabindex="<?=$tabIndex++;?>" /><label for="USE_SMILES<?=$arParams["form_index"]?>"><?=GetMessage("FPF_WANT_ALLOW_SMILES")?></label></div><?
endif;


if ($arResult["SHOW_PANEL_ATTACH_IMG"] == "Y"):?>
<div class="group attach">
	<?=($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y") ? GetMessage("FPF_LOAD_IMAGE") : GetMessage("FPF_LOAD_FILE") ?>
<?
$iFileSize = intVal(COption::GetOptionString("forum", "file_max_size", 50000));
$size = array(
	"B" => $iFileSize, 
	"KB" => round($iFileSize/1024, 2), 
	"MB" => round($iFileSize/1048576, 2));
$sFileSize = $size["KB"].GetMessage("F_KB");
if ($size["KB"] < 1)
	$sFileSize = $size["B"].GetMessage("F_B");
elseif ($size["MB"] >= 1 )
	$sFileSize = $size["MB"].GetMessage("F_MB");

?> (<?=str_replace("#SIZE#", $sFileSize, GetMessage("F_FILE_SIZE"))?>):<br /><?
$counter = 0;
if (!empty($arResult["MESSAGE"]["FILES"])):
?><div class="forum-files"><?

foreach ($arResult["MESSAGE"]["FILES"] as $key => $val):
$counter++;
?>
<fieldset style="float:left;">
	<legend>
	<input type="hidden" name="FILES[<?=$key?>]" value="<?=$key?>" />
	<input type="checkbox" name="FILES_TO_UPLOAD[<?=$key?>]" id="FILES_TO_UPLOAD_<?=$key?>" value="<?=$key?>" checked="checked" />
	<label for="FILES_TO_UPLOAD_<?=$key?>"> <?=$val["ORIGINAL_NAME"]?></label>
	</legend>
<?
	?><?$GLOBALS["APPLICATION"]->IncludeComponent(
		"bitrix:forum.interface",
		"show_file",
		Array(
			"FILE" => $val,
			"WIDTH"=> $arParams["IMAGE_SIZE"],
			"HEIGHT"=> $arParams["IMAGE_SIZE"],
			"CONVERT" => "N",
			"FAMILY" => "FORUM",
			"SINGLE" => "Y",
			"RETURN" => "N",
			"SHOW_LINK" => "Y"
		),
		null,
		array("HIDE_ICONS" => "Y"));
?>
</fieldset>
<?
endforeach;

?></div>
<div class="forum-br"></div><?
endif;
for ($ii = $counter; $ii < $arParams["FILES_COUNT"]; $ii++):
?>
<input name="FILE_NEW_<?=$ii?>" type="file" value="" /><br />
<?
endfor;
?></div><?
endif;

if ($arResult["SHOW_SUBSCRIBE"] == "Y"):?>
		<div class="group subscribe">
			<input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
				?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" />
			<label for="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>"><?=GetMessage("FPF_WANT_SUBSCRIBE_TOPIC")?></label><br />
			<input type="checkbox" name="FORUM_SUBSCRIBE" id="FORUM_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
				?><?=($arResult["FORUM_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>"/>
			<label for="FORUM_SUBSCRIBE<?=$arParams["form_index"]?>"><?=GetMessage("FPF_WANT_SUBSCRIBE_FORUM")?></label></div><?
endif;

/* CAPTHCA */
if (strLen($arResult["CAPTCHA_CODE"]) > 0):?>
		<div class="group captcha">
		<b><?=GetMessage("FPF_CAPTCHA_TITLE")?></b><br />
		<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
		<img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("FPF_CAPTCHA_TITLE")?>" /><br />
		<?=GetMessage("FPF_CAPTCHA_PROMT")?> <input type="text" size="10" name="captcha_word" tabindex="<?=$tabIndex++;?>" /></div><?
endif;

/* EDIT PANEL */
if ($arResult["SHOW_PANEL_EDIT"] == "Y"):
	$checked = ($_REQUEST["EDIT_ADD_REASON"]=="Y" || $_REQUEST["forum_post_action"] != "save") ? true : false;
?><div class="group edit"><?
	if ($arResult["SHOW_PANEL_EDIT_ASK"] == "Y"):?>
		<input type="checkbox" name="EDIT_ADD_REASON" id="EDIT_ADD_REASON<?=$arParams["form_index"]?>" value="Y" <?=($checked ? "checked=\"checked\"" : "")?>	<?
			?>onclick="if(this.form.EDITOR_NAME){this.form.EDITOR_NAME.disabled=!this.checked;} if(this.form.EDITOR_EMAIL){this.form.EDITOR_EMAIL.disabled=!this.checked;}if(this.form.EDIT_REASON){this.form.EDIT_REASON.disabled=!this.checked;}" /> <label for="EDIT_ADD_REASON<?=$arParams["form_index"]?>"><?=GetMessage("FPF_EDIT_ADD_REASON")?></label><br /><?
	endif;
	
	if ($arResult["SHOW_EDIT_PANEL_GUEST"] == "Y"):
	?><font class="starrequired">*</font><?=GetMessage("FPF_TYPE_NAME")?>
		<input name="EDITOR_NAME" type="text" value="<?=$arResult["EDITOR_NAME"];?>" /></br><?
		
		if ($arResult["FORUM"]["ASK_GUEST_EMAIL"] == "Y"):
		?><?=GetMessage("FPF_TYPE_EMAIL")?>
			<input type="text" name="EDITOR_EMAIL" value="<?=$arResult["EDITOR_EMAIL"];?>" /></br><?
		endif;
	endif;
	
	?><?=GetMessage("FPF_EDIT_REASON")?><textarea name="EDIT_REASON"><?=$arResult["EDIT_REASON"]?></textarea></div><?
endif;

?>
	<div class="group buttons">
		<input name="send_button" type="submit" value="<?=$arResult["SUBMIT"]?>" tabindex="<?=$tabIndex++;?>" <?
			?>onclick="this.form.MESSAGE_MODE.value = 'NORMAL';" />
		<?if ($arParams["AJAX_CALL"] != "Y"):?>
		<input name="view_button" type="submit" value="<?=GetMessage("FPF_VIEW")?>" tabindex="<?=$tabIndex++;?>" <?
			?>onclick="this.form.MESSAGE_MODE.value = 'VIEW';" />
		<?endif;?>
	</div>
		</td></tr></table></td>
	</tr>
</table>
</form><?
if ($arParams["AJAX_CALL"] == "Y")
{
	if(!function_exists("__ConverData"))
	{
		function __ConverData(&$item, $key)
		{
			if(is_array($item))
				array_walk($item, "__ConverData");
			else
			{
				$item = htmlspecialcharsEx($item);
			}
		}
	}
	
	$post = ob_get_contents();
	ob_end_clean();
	$res = array(
		"id" => $arParams["MID"],
		"post" => $post);
	if ($_REQUEST["CONVERT_DATA"] == "Y")
		array_walk($res, "__ConverData");
		
	$GLOBALS["APPLICATION"]->RestartBuffer();
	?><?=CUtil::PhpToJSObject($res)?><?
	die();
}
?>
<script type="text/javascript">
var bSendForm = false;
if (typeof oErrors != "object")
	var oErrors = {};
oErrors['no_topic_name'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_TOPIC_NAME"))?>";
oErrors['no_message'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_MESSAGE"))?>";
oErrors['max_len'] = "<?=CUtil::addslashes(GetMessage("JERROR_MAX_LEN"))?>";
oErrors['no_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_URL"))?>";
oErrors['no_title'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_TITLE"))?>";
oErrors['no_path'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_PATH_TO_VIDEO"))?>";
if (typeof oText != "object")
	var oText = {};
oText['author'] = " <?=CUtil::addslashes(GetMessage("JQOUTE_AUTHOR_WRITES"))?>:\n";
oText['enter_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL"))?>";
oText['enter_url_name'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL_NAME"))?>";
oText['enter_image'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_IMAGE"))?>";
oText['list_prompt'] = "<?=CUtil::addslashes(GetMessage("FORUM_LIST_PROMPT"))?>";
oText['video'] = "<?=CUtil::addslashes(GetMessage("FORUM_VIDEO"))?>";
oText['path'] = "<?=CUtil::addslashes(GetMessage("FORUM_PATH"))?>:";
oText['width'] = "<?=CUtil::addslashes(GetMessage("FORUM_WIDTH"))?>:";
oText['height'] = "<?=CUtil::addslashes(GetMessage("FORUM_HEIGHT"))?>:";

oText['BUTTON_OK'] = "<?=CUtil::addslashes(GetMessage("FORUM_BUTTON_OK"))?>";
oText['BUTTON_CANCEL'] = "<?=CUtil::addslashes(GetMessage("FORUM_BUTTON_CANCEL"))?>";

if (typeof oHelp != "object")
	var oHelp = {};

function reply2author(name)
{
	<?if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):?>
	document.REPLIER.POST_MESSAGE.value += "[b]"+name+"[/b]"+" \n";
	<?else:?>
	document.REPLIER.POST_MESSAGE.value += name+" \n";
	<?endif;?>
	return false;
}
</script>