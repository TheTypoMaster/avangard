<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// *****************************************************************************************
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] != "N" ? "Y" : "Y");
$arParams["form_index"] = $_REQUEST["INDEX"];
if (!empty($arParams["form_index"]))
	$arParams["form_index"] = preg_replace("/[^a-z0-9]/is", "_", $arParams["form_index"]);
// *****************************************************************************************
$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
@include_once($path);
?><a name="postform"></a>
<div class="forum-title"><?
if ($arResult["MESSAGE_TYPE"]=="NEW"):
	?><?=GetMessage("FPF_CREATE_IN_FORUM")?>: <a href="<?=$arResult["list"]?>"><?=$arResult["FORUM"]["NAME"]?></a><?
elseif ($arResult["MESSAGE_TYPE"]=="REPLY"):
	?><b><?=GetMessage("FPF_REPLY_FORM")?></b><?
else:
	?><?=GetMessage("FPF_EDIT_FORM")?> <?=GetMessage("FPF_IN_TOPIC")?>: <a href="<?=$arResult["read"]?>"><?=$arResult["str_TITLE"]?></a>, <?=GetMessage("FPF_IN_FORUM")?>: <a href="<?=$arResult["list"]?>"><?=$arResult["FORUM"]["NAME"]?></a><?
endif;?></div>
<div class="forum-br"></div>
<?

if ($arParams["AJAX_CALL"] == "Y"):
	ob_end_clean();
	ob_start();
	if (!empty($arParams["ERROR_MESSAGE"]))
		ShowError($arParams["ERROR_MESSAGE"]);
endif;
$tabIndex = 1;
?>
<form name="REPLIER<?=$arParams["form_index"]?>" id="REPLIER<?=$arParams["form_index"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
	?> method="post" enctype="multipart/form-data" onsubmit="return ValidateForm(this, '<?=$arParams["AJAX_TYPE"]?>');"<?
	?> onmouseover="if(init_form){init_form(this)}" class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="<?=$arParams["PAGE_NAME"];?>" />
	<input type="hidden" name="FID" value="<?=$arParams["FID"]?>" />
	<input type="hidden" name="TID" value="<?=$arParams["TID"]?>" />
	<input type="hidden" name="MID" value="<?=$arResult["MID"];?>" />
	<input type="hidden" name="MESSAGE_TYPE" value="<?=$arParams["MESSAGE_TYPE"];?>" />
	<input type="hidden" name="AUTHOR_ID" value="<?=$arResult["str_AUTHOR_ID"];?>" />
	<input type="hidden" name="forum_post_action" value="save" />
	<input type="hidden" name="MESSAGE_MODE" value="NORMAL" />
	<?=$arResult["sessid"]?>

<table class="forum-post-form"><?
if ($arParams["AJAX_CALL"] == "N"):
/* GUEST PANEL */
if ($arResult["SHOW_PANEL_GUEST"] == "Y"):?>
	<tr><th><?=GetMessage("FPF_UNREG_USER_INFO")?></th></tr>
	<tr><td><span class="title title-name"><font class="starrequired">*</font><?=GetMessage("FPF_TYPE_NAME")?></span> 
		<span class="value value-name">
			<input name="AUTHOR_NAME" type="text" value="<?=$arResult["str_AUTHOR_NAME"];?>" tabindex="<?=$tabIndex++;?>" /></span></tr><?
		
	if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y"):?>
	<tr><td><span class="title title-email"><?=GetMessage("FPF_TYPE_EMAIL")?></span>
		<span class="value value-email"><input type="text" name="AUTHOR_EMAIL" value="<?=$arResult["str_AUTHOR_EMAIL"];?>" tabindex="<?=$tabIndex++;?>" /></span></td></tr><?
	endif;
endif;

/* NEW TOPIC */
if ($arResult["SHOW_PANEL_NEW_TOPIC"] == "Y"):?>
	<tr><th><?=GetMessage("FPF_TOPIC_PARAMS")?></th></tr>
	<tr><td>
		<span class="title title-title"><font class="starrequired">*</font><?=GetMessage("FPF_TOPIC_NAME")?></span>
		<span class="value value-title"><input name="TITLE" type="text" value="<?=$arResult["str_TITLE"];?>" tabindex="<?=$tabIndex++;?>" /></span></td></tr>
	<tr><td>
		<span class="title title-description"><?=GetMessage("FPF_TOPIC_DESCR")?></span>
		<span class="value value-description"><input name="DESCRIPTION" type="text" value="<?=$arResult["str_DESCRIPTION"];?>" tabindex="<?=$tabIndex++;?>" /></span></td></tr><?
	
	if ($arParams["SHOW_TAGS"] == "Y"):?>
	<tr title="<?=GetMessage("FPF_TOPIC_TAGS_DESCRIPTION")?>"><td>
		<span class="title title-tags"><?=GetMessage("FPF_TOPIC_TAGS")?></span>
		<span class="value value-tags"><?
		if ($arResult["SHOW_SEARCH"] == "Y"):
		$APPLICATION->IncludeComponent(
			"bitrix:search.tags.input", 
			"", 
			array(
				"VALUE" => $arResult["str_TAGS"], 
				"NAME" => "TAGS",
				"TEXT" => 'tabindex="'.$tabIndex++.'"'),
			$component,
			array("HIDE_ICONS" => "Y"));
		else:
			?><input name="TAGS" type="text" value="<?=$arResult["str_TAGS"]?>" tabindex="<?=$tabIndex++;?>" /><?
		endif;
	?></span></td></tr><?
	endif;
	
	?><tr title="<?=GetMessage("FPF_TOPIC_ICON_DESCRIPTION")?>"><td>
		<span class="title title-icons"><?=GetMessage("FPF_TOPIC_ICON")?></span>
		<span class="value value-icons"><?=$arResult["ForumPrintIconsList"];?></span></td></tr><?
endif;

	?><tr><th><?=GetMessage("FPF_MESSAGE_TEXT")?></th></tr><?
endif;
	?><tr><td><table class="clear"><tr><?	
if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):?>
	<td class="forum-smile"><table class="forum-smile">
		<tr><th colspan="<?=intVal($arParams["SMILE_TABLE_COLS"])?>"><?=GetMessage("FPF_SMILES")?></th></tr>
		<?=$arResult["ForumPrintSmilesList"]?>
	</table></td><?
endif;
?><td class="forum-postform"><?
if ($arResult["FORUM"]["ALLOW_BIU"] == "Y"):?>
	<input type='button' name='B' class='bold' accesskey='b' value='<?=GetMessage("FPF_B")?>' title="<?=GetMessage("FPF_BOLD")?>" />
	<input type='button' name='I' class='italic' accesskey='i' value='<?=GetMessage("FPF_I")?>' title="<?=GetMessage("FPF_ITAL")?>" />
	<input type='button' name='U' class='underline' accesskey='u' value='<?=GetMessage("FPF_U")?>' title="<?=GetMessage("FPF_UNDER")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_FONT"] == "Y"):?>
	<select name='FONT' class='font'>
		<option value='0'><?=GetMessage("FPF_FONT")?></option>
		<option value='Arial' style='font-family:Arial'>Arial</option>
		<option value='Times' style='font-family:Times'>Times</option>
		<option value='Courier' style='font-family:Courier'>Courier</option>
		<option value='Impact' style='font-family:Impact'>Impact</option>
		<option value='Geneva' style='font-family:Geneva'>Geneva</option>
		<option value='Optima' style='font-family:Optima'>Optima</option>
	</select>
	<select name='COLOR' class="color">
		<option value='0'><?=GetMessage("FPF_COLOR")?></option>
		<option value='blue' style='color:blue'><?=GetMessage("FPF_BLUE")?></option>
		<option value='red' style='color:red'><?=GetMessage("FPF_RED")?></option>
		<option value='gray' style='color:gray'><?=GetMessage("FPF_GRAY")?></option>
		<option value='green' style='color:green'><?=GetMessage("FPF_GREEN")?></option>
	</select><?
endif;

if ($arResult["SHOW_CLOSE_ALL"] == "Y"):?>	
		<input type="button" name="CLOSE" class="close" title="<?=GetMessage("FPF_CLOSE_OPENED_TAGS")?>" value="<?=GetMessage("FPF_CLOSE_ALL_TAGS")?>"><br /><?
endif;

if ($arResult["FORUM"]["ALLOW_ANCHOR"] == "Y"):?>
		<input type='button' name='URL' class="url" accesskey='h' value='<?=GetMessage("FPF_HYPERLINK")?>' title="<?=GetMessage("FPF_HYPERLINK_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_IMG"] == "Y"):?>
		<input type='button' name='IMG' class='img' accesskey='g' value='<?=GetMessage("FPF_IMAGE")?>' title="<?=GetMessage("FPF_IMAGE_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):?>
		<input type='button' name='QUOTE' class='quote' accesskey='q' value='<?=GetMessage("FPF_QUOTE")?>' title="<?=GetMessage("FPF_QUOTE_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_CODE"] == "Y"):?>
		<input type='button' name='CODE' class='code' accesskey='p' value='<?=GetMessage("FPF_CODE")?>' title="<?=GetMessage("FPF_CODE_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_LIST"] == "Y"):?>
		<input type='button' name="LIST" class="list" accesskey='l' value='<?=GetMessage("FPF_LIST");?>' title="<?=GetMessage("FPF_LIST_TITLE")?>" /><?
endif;

if ($arResult["LANGUAGE_ID"]=="ru"):?>
		<input type='button' name="TRANSLIT" class="translit" accesskey='t' value='<?=GetMessage("FPF_TRANSLIT")?>' title="<?=GetMessage("FPF_TRANSLIT_TITLE")?>" /><?
endif;

?>
	<br /><?=GetMessage("FPF_OPENED_TAGS")?>
		<input type="text" name="tagcount" class="tagcount" value="0" />&nbsp;
		<input type="text" name="helpbox" class="helpbox" value="" /><br />
	<textarea name="POST_MESSAGE" class="post_message" cols="55" rows="15" tabindex="<?=$tabIndex++;?>"><?=$arResult["str_POST_MESSAGE"];?></textarea><?

if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y" && $arParams["MESSAGE_TYPE"] == "REPLY"):?>
		<div class="group quote"><?=GetMessage("FPF_TO_QUOTE_NOTE")?></div><?
endif;

if ($arResult["FORUM"]["ALLOW_SMILES"]=="Y"):?>
		<div class="group smiles"><input type="checkbox" name="USE_SMILES" id="USE_SMILES<?=$arParams["form_index"]?>" <?
			?>value="Y" <?=($arResult["str_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
			?>tabindex="<?=$tabIndex++;?>" /><label for="USE_SMILES<?=$arParams["form_index"]?>"><?=GetMessage("FPF_WANT_ALLOW_SMILES")?></label></div><?
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

if ($arResult["SHOW_PANEL_ATTACH_IMG"] == "Y"):?>
		<div class="group attach">
			<?=($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y") ? GetMessage("FPF_LOAD_IMAGE") : GetMessage("FPF_LOAD_FILE") ?><br />
			<input name="ATTACH_IMG" type="file"/><br /><?
	if (($arResult["MESSAGE_TYPE"] == "EDIT") && ($arResult["str_ATTACH_IMG_FILE"] !== false)):?>
			<input type="checkbox" name="ATTACH_IMG_del" id="ATTACH_IMG_del<?=$arParams["form_index"]?>" value="Y"/>
			<label for="ATTACH_IMG_del<?=$arParams["form_index"]?>"><?
				?><?=GetMessage("FPF_DELETE_FILE")?></label> <br /><?
		if (strlen($arResult["str_ATTACH_IMG"])>0):?>
					<?=$arResult["str_ATTACH_IMG"];?><br /><?
		endif;
	endif;
		?></div><?
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