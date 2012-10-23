<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
IncludeAJAX();
?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
?><?=ShowNote($arResult["OK_MESSAGE"])?><?
$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
@include_once($path);

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

?><form action="<?=POST_FORM_ACTION_URI?>" method="post" id="REPLIER" name="REPLIER" class="forum-form" <?
	?>onmouseover="if(null != init_form){init_form(this)}" onsubmit="return ValidateForm(this);" >
	<input type="hidden" name="PAGE_NAME" value="pm_edit">
	<?=$arResult["sessid"]?>
	<input type="hidden" name="action" id="action" value="<?=$arResult["action"]?>">
	<input type="hidden" name="FID" value="<?=$arResult["FID"]?>">
	<input type="hidden" name="MID" value="<?=$arResult["MID"]?>">
	<input type="hidden" name="mode" value="<?=$arResult["mode"]?>">
<table class="forum-post-form">
	<tr><th colspan="2" class="left">&nbsp;<b><?=GetMessage("PM_HEAD_TO")?></b></td></tr>
	<tr>
		<td width="10%"><b><font class="starrequired">*</font></b><?=GetMessage("PM_HEAD_NAME_LOGIN")?></td>
		<td width="90%" nowrap="nowrap">
			<input type="text" name="input_USER_ID" id="input_USER_ID" tabindex="<?=$tabIndex++;?>" <?
				?>value="<?=$arResult["POST_VALUES"]["SHOW_NAME"]["text"]?>" onfocus="fSearchUser()" />
			<input type="hidden" name="USER_ID" id="USER_ID" value="<?=$arResult["POST_VALUES"]["USER_ID"]?>" readonly="readonly" />
				<?if ($arResult["mode"] != "edit"):?>
				<input type="button" name="FindUserForum" id="FindUserForum" <?
					?>OnClick="window.open('<?=$arResult["pm_search"]?>', '', 'scrollbars=yes,resizable=yes,width=370,height=510,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));" 
					value="<?=GetMessage("PM_SEARCH_USER")?>" />
				<?endif;?>
				<span id="div_USER_ID" name="div_USER_ID" style="width:100%;"><?
				if (!empty($arResult["POST_VALUES"]["SHOW_NAME"]))
				{
					?>[<a href="<?=$arResult["POST_VALUES"]["SHOW_NAME"]["link"]?>"><?=$arResult["POST_VALUES"]["SHOW_NAME"]["text"]?></a>]<?
				}
				?></span>
				<IFRAME style="width:0px; height:0px; border: 0px" src="javascript:void(0)" name="frame_USER_ID" id="frame_USER_ID"></IFRAME>
		</td>
	</tr>
	<tr><th colspan="2" class="left">&nbsp;<b><?=GetMessage("PM_HEAD_FROM")?></b></td></tr>
	<tr><td><?=GetMessage("PM_HEAD_NAME_LOGIN")?></td>
		<td><?=$arResult["CurrUser"]["SHOW_NAME"]?></td>
	</tr>
	<tr><th colspan="2" class="left">&nbsp;<b><?=GetMessage("PM_HEAD_MESS")?></b></th></tr>
	<tr><td><b><font class="starrequired">*</font></b><?=GetMessage("PM_HEAD_SUBJ")?></td>
		<td><input type="text" name="POST_SUBJ" value="<?=$arResult["POST_VALUES"]["POST_SUBJ"];?>" tabindex="<?=$tabIndex++;?>"></td></tr>
	<tr><td>
			<table class="forum-smile"><tr><th colspan="3"><?=GetMessage("PM_SMILES")?></th></tr>
			<?=$arResult["ForumPrintSmilesList"]?></table></td>
		<td>
			<input type='button' name='B' class='bold' accesskey='b' value='<?=GetMessage("PM_B")?>' title="<?=GetMessage("PM_BOLD")?>" />
			<input type='button' name='I' class='italic' accesskey='i' value='<?=GetMessage("PM_I")?>' title="<?=GetMessage("PM_ITAL")?>" />
			<input type='button' name='U' class='underline' accesskey='u' value='<?=GetMessage("PM_U")?>' title="<?=GetMessage("PM_UNDER")?>" />
			
			<select name='FONT' class='font'>
				<option value='0'><?=GetMessage("PM_FONT")?></option>
				<option value='Arial' style='font-family:Arial'>Arial</option>
				<option value='Times' style='font-family:Times'>Times</option>
				<option value='Courier' style='font-family:Courier'>Courier</option>
				<option value='Impact' style='font-family:Impact'>Impact</option>
				<option value='Geneva' style='font-family:Geneva'>Geneva</option>
				<option value='Optima' style='font-family:Optima'>Optima</option>
			</select>
			<select name='COLOR' class="color">
				<option value='0'><?=GetMessage("PM_COLOR")?></option>
				<option value='blue' style='color:blue'><?=GetMessage("PM_BLUE")?></option>
				<option value='red' style='color:red'><?=GetMessage("PM_RED")?></option>
				<option value='gray' style='color:gray'><?=GetMessage("PM_GRAY")?></option>
				<option value='green' style='color:green'><?=GetMessage("PM_GREEN")?></option>
			</select>
			<input type="button" name="CLOSE" class="close" title="<?=GetMessage("PM_CLOSE_OPENED_TAGS")?>" value="<?=GetMessage("PM_CLOSE_ALL_TAGS")?>"><br />
	
			<input type='button' name='URL' class="url" accesskey='h' value='<?=GetMessage("PM_HYPERLINK")?>' title="<?=GetMessage("PM_HYPERLINK_TITLE")?>" />
			<input type='button' name='IMG' class='img' accesskey='g' value='<?=GetMessage("PM_IMAGE")?>' title="<?=GetMessage("PM_IMAGE_TITLE")?>" />
			<input type='button' name='QUOTE' class='quote' accesskey='q' value='<?=GetMessage("PM_QUOTE")?>' title="<?=GetMessage("PM_QUOTE_TITLE")?>" />
			<input type='button' name='CODE' class='code' accesskey='p' value='<?=GetMessage("PM_CODE")?>' title="<?=GetMessage("PM_CODE_TITLE")?>" />
			<input type='button' name="LIST" class="list" accesskey='l' value='<?=GetMessage("PM_LIST");?>' title="<?=GetMessage("PM_LIST_TITLE")?>" />
		<?if (LANGUAGE_ID=="ru"):?>
			<input type='button' name="TRANSLIT" class="translit" accesskey='t' value='<?=GetMessage("PM_TRANSLIT")?>' title="<?=GetMessage("PM_TRANSLIT_TITLE")?>" />
		<?endif;?>	<br /><?=GetMessage("PM_OPENED_TAGS")?>
		<input type="text" name="tagcount" class="tagcount" value="0" />&nbsp;
		<input type="text" name="helpbox" class="helpbox" value="" /><br />
		<textarea name="POST_MESSAGE" class="post_message" cols="55" rows="15" tabindex="<?=$tabIndex++;?>"><?
			?><?=$arResult["POST_VALUES"]["POST_MESSAGE"]?></textarea><br />

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
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			<input type="submit" name="SAVE_BUTTON" id="SAVE_BUTTON" value="<?
				if ($arResult["action"] == "save"):
					?><?=GetMessage("PM_ACT_SAVE")?><?
				else:
					?><?=GetMessage("PM_ACT_SEND")?><?
				endif;
			?>" tabindex="<?=$tabIndex++;?>">
			</td>
		</tr>
	</table>
</form>
<script language="Javascript">
window.switcher = '<?=$arResult["POST_VALUES"]["SHOW_NAME"]["text"]?>';
function fSearchUser()
{
	var name = 'USER_ID';
	var template_path = '<?=$arResult["pm_search_for_js"]?>';
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
</script>