<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
IncludeAJAX();
?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
?><?=ShowNote($arResult["OK_MESSAGE"])?><?
if (LANGUAGE_ID == "ru")
{
	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
	@include_once($path);
}

$tabIndex = 1;

?><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="right">
	<div class="out"><div class="in" style="width:<?=$arResult["count"]?>%">&nbsp;</div></div>
	<div class="out1"><div class="in1"><?=GetMessage("PM_POST_FULLY")." ".$arResult["count"]?>%</div></div>
</td></tr></table>
<div class="forum-br"></div><?

if ($arResult["mode"] != "new"):
?><div class="forum-title"><?=$arResult["FolderName"]?></div>
<div class="forum-br"></div>
<?
endif;

?><form action="<?=POST_FORM_ACTION_URI?>" method="post" id="REPLIER" name="REPLIER" <?
	?>class="forum-form" onsubmit="return ValidateForm(this);" <?
	?>onkeydown="if(null != init_form){init_form(this)}" onmouseover="if(init_form){init_form(this)}">
	<input type="hidden" name="PAGE_NAME" value="pm_edit">
	<?=$arResult["sessid"]?>
	<input type="hidden" name="action" id="action" value="<?=$arResult["action"]?>">
	<input type="hidden" name="FID" value="<?=$arResult["FID"]?>">
	<input type="hidden" name="MID" value="<?=$arResult["MID"]?>">
	<input type="hidden" name="mode" value="<?=$arResult["mode"]?>">
<table class="forum-main forum-post-form forum-pm-post-form">
<thead>
	<tr><td>
		<span class="title title-to"><font class="starrequired">*</font><?=GetMessage("PM_HEAD_TO")?></span>
		<span class="value value-to">
		<table cellpadding="0" cellspacing="0" width="100%" border="0" class="clear">
			<tr><td width="10%">
			<input type="text" class="text to" name="input_USER_ID" id="input_USER_ID" tabindex="<?=$tabIndex++;?>" <?
				?>value="<?=htmlspecialchars($arResult["POST_VALUES"]["SHOW_NAME"]["text"])?>" onfocus="fSearchUser()" />
			<input type="hidden" name="USER_ID" id="USER_ID" value="<?=$arResult["POST_VALUES"]["USER_ID"]?>" readonly="readonly" />
			</td>
<?
	if ($arResult["mode"] != "edit"):
?>
			<td td width="1%">
		<div class="icon-profile" id="FindUserForum" OnClick="window.open('<?=$arResult["pm_search"]?>', '', 'scrollbars=yes,resizable=yes,width=370,height=510,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));" title="<?=GetMessage("PM_SEARCH_USER")?>"></div>
			</td>
<?
	endif;
?>
			<td>
		<span id="div_USER_ID" name="div_USER_ID"><?
				if (!empty($arResult["POST_VALUES"]["SHOW_NAME"]))
				{
					?>[<a href="<?=$arResult["POST_VALUES"]["SHOW_NAME"]["link"]?>"><?=htmlspecialchars($arResult["POST_VALUES"]["SHOW_NAME"]["text"])?></a>]<?
				}
				?></span>
		<IFRAME style="width:0px; height:0px; border: 0px" src="javascript:void(0)" name="frame_USER_ID" id="frame_USER_ID"></IFRAME>
			</td></tr>
		</table>
	</td></tr>
	<tr><td>
		<span class="title title-from"><?=GetMessage("PM_HEAD_FROM")?></span>
		<span class="value value-from"><input type="text" class="text from" value="<?=$arResult["CurrUser"]["SHOW_NAME"]?>" disabled="disabled" /></span>
	</td></tr>
	<tr><td>
		<span class="title title-subj"><font class="starrequired">*</font><?=GetMessage("PM_HEAD_SUBJ")?></span>
		<span class="value value-subj"><input type="text" class="text subject" name="POST_SUBJ" value="<?=$arResult["POST_VALUES"]["POST_SUBJ"];?>" tabindex="<?=$tabIndex++;?>"></span>
	</td></tr>
	<tr><th><?=GetMessage("PM_HEAD_MESS")?></th></tr>
</thead>
<tbody>
	<tr><td>
		<table class="clear" cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<td width="5%">
				<table class="forum-smile" cellpadding="0" cellspacing="0" border="0">
					<tr><th colspan="3"><?=GetMessage("PM_SMILES")?></th></tr>
					<?=$arResult["ForumPrintSmilesList"]?>
				</table>
			</td>
			<td class="value">
<table class="forum-toolbars" cellpadding="0" cellspacing="0" border="0" width="100%"><tr class="top"><td>
	<div class="form_button button_font">
		<select name='FONT' class='button_font' id='form_font' title="<?=GetMessage("PM_FONT_TITLE")?>">
			<option value='0'><?=GetMessage("PM_FONT")?></option>
			<option value='Arial' style='font-family:Arial'>Arial</option>
			<option value='Times' style='font-family:Times'>Times</option>
			<option value='Courier' style='font-family:Courier'>Courier</option>
			<option value='Impact' style='font-family:Impact'>Impact</option>
			<option value='Geneva' style='font-family:Geneva'>Geneva</option>
			<option value='Optima' style='font-family:Optima'>Optima</option>
			<option value='Verdana' style='font-family:Verdana'>Verdana</option>
		</select>
	</div>
	<div class="form_button button_color" id="form_palette" title="<?=GetMessage("PM_COLOR")?>"></div>
	<div class="form_button button_bold" id="form_b" title="<?=GetMessage("PM_BOLD")?>"></div>
	<div class="form_button button_italic" id="form_i" title="<?=GetMessage("PM_ITAL")?>"></div>
	<div class="form_button button_underline" id="form_u" title="<?=GetMessage("PM_UNDER")?>"></div>
	<div class="form_button button_url" id="form_url" title="<?=GetMessage("PM_HYPERLINK_TITLE")?>"></div>
	<div class="form_button button_img" id="form_img" title="<?=GetMessage("PM_IMAGE_TITLE")?>"></div>
	<div class="form_button button_quote" id="form_quote" title="<?=GetMessage("PM_QUOTE_TITLE")?>"></div>
	<div class="form_button button_code" id="form_code" title="<?=GetMessage("PM_CODE_TITLE")?>"></div>
	<div class="form_button button_list" id="form_list" title="<?=GetMessage("PM_LIST_TITLE")?>"></div>
<?
	if (LANGUAGE_ID=="ru"):
?>
	<div class="form_button button_translit" id="form_translit" title="<?=GetMessage("PM_TRANSLIT_TITLE")?>"></div>
<?
	endif;
?>
	<div class="button_closeall" title="<?=GetMessage("PM_CLOSE_OPENED_TAGS")?>" id="form_closeall" style="display:none;">
		<a href="javascript:void(0)"><?=GetMessage("PM_CLOSE_ALL_TAGS")?></a></div>
</td></tr>
<tr class="post_message"><td>
<textarea name="POST_MESSAGE" class="post_message" rows="15" tabindex="<?=$tabIndex++;?>"><?=$arResult["POST_VALUES"]["POST_MESSAGE"]?></textarea>
</td></tr>
</table>

		<input type="checkbox" name="USE_SMILES" id="USE_SMILES" value="Y" <?=(($arResult["POST_VALUES"]["USE_SMILES"] != "N") ? "checked" : "");?> <?
			?>tabindex="<?=$tabIndex++;?>"><label for="USE_SMILES"><?=GetMessage("PM_WANT_ALLOW_SMILES")?></label><br /><?
			
		if ($arParams["version"] == 2 && $arResult["action"] == "send"):
		?><input type="checkbox" name="COPY_TO_OUTBOX" id="COPY_TO_OUTBOX" value="Y" tabindex="<?=$tabIndex++;?>" <?
			?><?=(($arResult["POST_VALUES"]["COPY_TO_OUTBOX"] != "N") ? "checked" : "")?> /> 
		<label for="COPY_TO_OUTBOX"><?=GetMessage("PM_COPY_TO_OUTBOX")?></label><br />
		<input type="checkbox" name="REQUEST_IS_READ" id="REQUEST_IS_READ" value="Y" tabindex="<?=$tabIndex++;?>" <?
			?><?=(($arResult["POST_VALUES"]["REQUEST_IS_READ"] == "Y") ? "checked" : "")?> /> 
		<label for="REQUEST_IS_READ"><?=GetMessage("PM_REQUEST_IS_READ")?></label><br /><?
		endif;
?>
	<div class="group buttons">
		<input type="submit" name="SAVE_BUTTON" id="SAVE_BUTTON" tabindex="<?=$tabIndex++;?>" <?
			?>value="<?=($arResult["action"] == "save" ? GetMessage("PM_ACT_SAVE") : GetMessage("PM_ACT_SEND"))?>" />
	</div>
			</td>
		</tr></table>
	</td></tr>
</tbody>
</table>
</form>
<script language="Javascript">
window.switcher = '<?=CUtil::JSEscape($arResult["POST_VALUES"]["SHOW_NAME"]["text"])?>';
function fSearchUser()
{
	var name = 'USER_ID';
	var template_path = '<?=CUtil::JSEscape($arResult["pm_search_for_js"])?>';
	var handler = document.getElementById('input_'+name);
	var div_ = document.getElementById('div_'+name);
	if (typeof handler != "object" || null == handler || typeof div_ != "object")
		return false;
	
	
	if (window.switcher != handler.value)
	{
		window.switcher = handler.value;
		handler.form.elements[name].value=handler.value;
		if (handler.value != '')
		{
			div_.innerHTML = '<i><?=GetMessage("FORUM_MAIN_WAIT")?></i>';
			document.getElementById('frame_'+name).src=template_path.replace(/\#LOGIN\#/gi, handler.value);
		}
		else
			div_.innerHTML = '';
	}
	setTimeout(fSearchUser, 1000);
	return true;
}
fSearchUser();

var bSendForm = false;
if (typeof oErrors != "object")
	var oErrors = {};
oErrors['no_topic_name'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_TOPIC_NAME"))?>";
oErrors['no_message'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_MESSAGE"))?>";
oErrors['max_len1'] = "<?=CUtil::addslashes(GetMessage("JERROR_MAX_LEN1"))?>";
oErrors['max_len2'] = "<?=CUtil::addslashes(GetMessage("JERROR_MAX_LEN2"))?>";
oErrors['no_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_URL"))?>";
oErrors['no_title'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_TITLE"))?>";
if (typeof oText != "object")
	var oText = {};
oText['author'] = " <?=CUtil::addslashes(GetMessage("JQOUTE_AUTHOR_WRITES"))?>:\n";
oText['translit_en'] = "<?=CUtil::addslashes(GetMessage("FORUM_TRANSLIT_EN"))?>";
oText['enter_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL"))?>";
oText['enter_url_name'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL_NAME"))?>";
oText['enter_image'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_IMAGE"))?>";
oText['list_prompt'] = "<?=CUtil::addslashes(GetMessage("FORUM_LIST_PROMPT"))?>";
if (typeof oHelp != "object")
	var oHelp = {};
oHelp['B'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_BOLD"))?>";
oHelp['I'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_ITALIC"))?>";
oHelp['U'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_UNDER"))?>";
oHelp['FONT'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_FONT"))?>";
oHelp['COLOR'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_COLOR"))?>";
oHelp['CLOSE'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_CLOSE"))?>";
oHelp['URL'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_URL"))?>";
oHelp['IMG'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_IMG"))?>";
oHelp['QUOTE'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_QUOTE"))?>";
oHelp['LIST'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_LIST"))?>";
oHelp['CODE'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_CODE"))?>";
oHelp['CLOSE_CLICK'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_CLICK_CLOSE"))?>";
oHelp['TRANSLIT'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_TRANSLIT"))?>";
</script>
