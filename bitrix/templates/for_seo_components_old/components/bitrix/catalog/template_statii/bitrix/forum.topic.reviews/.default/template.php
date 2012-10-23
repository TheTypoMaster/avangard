<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
// ************************* Input params***************************************************************
$arParams["SHOW_LINK_TO_FORUM"] = ($arParams["SHOW_LINK_TO_FORUM"] == "N" ? "N" : "Y");
// *************************/Input params***************************************************************
if (!empty($arResult["MESSAGES"])):
	if (strlen($arResult["NAV_STRING"]) > 0):
		?><div class="forum-nav top"><?=$arResult["NAV_STRING"]?></div><?
	endif

?><table class="forum-reviews-messages" cellpadding="0" cellspacing="0" width="100%" border="0"><?
	foreach ($arResult["MESSAGES"] as $res):
		?><tr><th align="left">
			<table class="forum-reviews-clear"><tr>
				<td width="100%"><a name="message<?=$res["ID"]?>"></a><i><b><?=$res["AUTHOR_NAME"]?></b>, <?=$res["POST_DATE"]?></i></td>
			<?
	if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):?>
				<td><a href="#review_anchor" onMouseDown="quoteMessageEx('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>', 'message_text_<?=$res["ID"]?>')" title="<?=GetMessage("FTR_QUOTE_HINT")?>" class="button-small"><?=GetMessage("FTR_QUOTE")?></a></td><?
	endif;
				?><td><a href="#review_anchor" onMouseDown="reply2author('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>,')" title="<?=GetMessage("FTR_NAME")?>"  class="button-small"><?=GetMessage("FTR_NAME")?></a></td>
			</tr></table>
		</th></tr>
		<tr><td>
			<div class="forum-text" id="message_text_<?=$res["ID"]?>"><?=$res["POST_MESSAGE_TEXT"]?></div>
		<?if (strLen($res["ATTACH_IMG"]) > 0):?>
			<div class="forum-attach"><?=$res["ATTACH_IMG"]?></div>
		<?endif;?>
		</td></tr>
		<tr><td class="clear"><div class="empty"></div></td></tr>
	<?
	endforeach;
?></table><?

	if (strlen($arResult["NAV_STRING"]) > 0):
		?><div class="forum-nav bottom"><?=$arResult["NAV_STRING"]?></div><?
	endif;

	if (!empty($arResult["read"]) && $arParams["SHOW_LINK_TO_FORUM"] != "N"):
		?><a href="<?=$arResult["read"]?>" class="forum-link"><?=GetMessage("F_C_GOTO_FORUM") ?></a><?
	endif;
endif;
	
?><?=ShowError($arResult["ERROR_MESSAGE"])?>
<?=ShowNote($arResult["OK_MESSAGE"])?><?
if ($arResult["SHOW_POST_FORM"] == "Y"):
$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
include($path);
?>
<a name="review_anchor"></a>
<form action="<?=POST_FORM_ACTION_URI?>#review_anchor" method="post" <?
	?>name="REPLIER" id="REPLIER" enctype="multipart/form-data" onsubmit="return ValidateForm(this);" <?
	?>onmouseover="if(init_form){init_form(this)}" >
	<input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
	<input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
	<input type="hidden" name="SECTION_ID" value="<?=$arParams["SECTION_ID"]?>" />
	<input type="hidden" name="save_product_review" value="Y" />
	<?=$arResult["sessid"]?>
	<table class="forum-reviews-form data-table"><?
		if ($arResult["IS_AUTHORIZED"]):
		
		?><tr>
			<th class="title"><?=GetMessage("OPINIONS_NAME")?>:</th>
			<th class="value"><b><?=$arResult["REVIEW_AUTHOR"]?></b></th>
		</tr><?
		
		else:
		
		?><tr>
			<th class="title"><?=GetMessage("OPINIONS_NAME")?>:</th>
			<th class="value"><input type="text" name="REVIEW_AUTHOR" value="<?=$arResult["REVIEW_AUTHOR"]?>" /></th>
		</tr><?
		
			if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y"):
		?><tr>
			<th class="title"><?=GetMessage("OPINIONS_EMAIL")?>:</th>
			<th class="value"><input type="text" name="REVIEW_EMAIL" value="<?=$arResult["REVIEW_EMAIL"]?>"/></th>
		</tr><?
			endif;
		endif;
		
		?><tr><th colspan="2" class="title"><?=GetMessage("FTR_MESSAGE_TEXT")?></th></tr>
		<tr><?
		if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):
			?><td class="title forum-smile">
				<table class="forum-smile">
					<tr><th colspan="3"><?=GetMessage("FTR_SMILES")?></th></tr>
					<?=$arResult["ForumPrintSmilesList"]?>
				</table>
			</td>
			<td><?
		else:
			?><td colspan="2"><?
		endif;
		
if ($arResult["FORUM"]["ALLOW_BIU"] == "Y"):?>
	<input type='button' name='B' class='bold' accesskey='b' value='<?=GetMessage("FTR_B")?>' title="<?=GetMessage("FTR_BOLD")?>" />
	<input type='button' name='I' class='italic' accesskey='i' value='<?=GetMessage("FTR_I")?>' title="<?=GetMessage("FTR_ITAL")?>" />
	<input type='button' name='U' class='underline' accesskey='u' value='<?=GetMessage("FTR_U")?>' title="<?=GetMessage("FTR_UNDER")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_FONT"] == "Y"):?>
	<select name='FONT' class='font'>
		<option value='0'><?=GetMessage("FTR_FONT")?></option>
		<option value='Arial' style='font-family:Arial'>Arial</option>
		<option value='Times' style='font-family:Times'>Times</option>
		<option value='Courier' style='font-family:Courier'>Courier</option>
		<option value='Impact' style='font-family:Impact'>Impact</option>
		<option value='Geneva' style='font-family:Geneva'>Geneva</option>
		<option value='Optima' style='font-family:Optima'>Optima</option>
	</select>
	<select name='COLOR' class="color">
		<option value='0'><?=GetMessage("FTR_COLOR")?></option>
		<option value='blue' style='color:blue'><?=GetMessage("FTR_BLUE")?></option>
		<option value='red' style='color:red'><?=GetMessage("FTR_RED")?></option>
		<option value='gray' style='color:gray'><?=GetMessage("FTR_GRAY")?></option>
		<option value='green' style='color:green'><?=GetMessage("FTR_GREEN")?></option>
	</select><?
endif;

if ($arResult["SHOW_CLOSE_ALL"] == "Y"):?>	
		<input type="button" name="CLOSE" class="close" title="<?=GetMessage("FTR_CLOSE_OPENED_TAGS")?>" value="<?=GetMessage("FTR_CLOSE_ALL_TAGS")?>"><br /><?
endif;

if ($arResult["FORUM"]["ALLOW_ANCHOR"] == "Y"):?>
		<input type='button' name='URL' class="url" accesskey='h' value='<?=GetMessage("FTR_HYPERLINK")?>' title="<?=GetMessage("FTR_HYPERLINK_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_IMG"] == "Y"):?>
		<input type='button' name='IMG' class='img' accesskey='g' value='<?=GetMessage("FTR_IMAGE")?>' title="<?=GetMessage("FTR_IMAGE_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):?>
		<input type='button' name='QUOTE' class='quote' accesskey='q' value='<?=GetMessage("FTR_QUOTE")?>' title="<?=GetMessage("FTR_QUOTE_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_CODE"] == "Y"):?>
		<input type='button' name='CODE' class='code' accesskey='p' value='<?=GetMessage("FTR_CODE")?>' title="<?=GetMessage("FTR_CODE_TITLE")?>" /><?
endif;

if ($arResult["FORUM"]["ALLOW_LIST"] == "Y"):?>
		<input type='button' name="LIST" class="list" accesskey='l' value='<?=GetMessage("FTR_LIST");?>' title="<?=GetMessage("FTR_LIST_TITLE")?>" /><?
endif;
if ($arResult["LANGUAGE_ID"]=="ru"):?>
		<input type='button' name="TRANSLIT" class="translit" accesskey='t' value='<?=GetMessage("FTR_TRANSLIT")?>' title="<?=GetMessage("FTR_TRANSLIT_TITLE")?>" /><?
endif;

?><br /><?=GetMessage("FTR_OPENED_TAGS")?>
<input type="text" name="tagcount" class="tagcount" value="0" />&nbsp;
<input type="text" name="helpbox" class="helpbox" value="" /><br />
<textarea name="REVIEW_TEXT" id="REVIEW_TEXT" tabindex="<?=$tabIndex++;?>"><?=$arResult["REVIEW_TEXT"];?></textarea><br /><?
	
if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y" && $arParams["MESSAGE_TYPE"] == "REPLY"):?>
		<div class="group quote"><?=GetMessage("FTR_TO_QUOTE_NOTE")?></div><?
endif;

if ($arResult["FORUM"]["ALLOW_SMILES"]=="Y"):?>
		<div class="group smiles"><input type="checkbox" name="REVIEW_USE_SMILES" id="REVIEW_USE_SMILES" <?
			?>value="Y" <?=($arResult["REVIEW_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
			?>tabindex="<?=$tabIndex++;?>" /><label for="REVIEW_USE_SMILES"><?=GetMessage("FTR_WANT_ALLOW_SMILES")?></label></div><?
endif;

if ($arResult["SHOW_SUBSCRIBE"] == "Y"):?>
		<div class="group subscribe">
			<input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE" value="Y" <?
				?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" />
			<label for="TOPIC_SUBSCRIBE"><?=GetMessage("FTR_WANT_SUBSCRIBE_TOPIC")?></label><br />
			<input type="checkbox" name="FORUM_SUBSCRIBE" id="FORUM_SUBSCRIBE" value="Y" <?
				?><?=($arResult["FORUM_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>"/>
			<label for="FORUM_SUBSCRIBE"><?=GetMessage("FTR_WANT_SUBSCRIBE_FORUM")?></label></div><?
endif;

if ($arResult["SHOW_PANEL_ATTACH_IMG"] == "Y"):?>
		<div class="group attach"><?=($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y") ? GetMessage("FTR_LOAD_IMAGE") : GetMessage("FTR_LOAD_FILE") 
			?><br /><input name="REVIEW_ATTACH_IMG" type="file"/></div><?
endif;

/* CAPTHCA */
if (!empty($arResult["CAPTCHA_CODE"])):
	?><div class="group captcha"><?
	?><b><?=GetMessage("CAPTCHA_TITLE")?>:</b><br />
	<?=GetMessage("CAPTCHA_PROMT")?>:<input type="text" name="captcha_word" /><br />
	<img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("CAPTCHA_TITLE")?>" />
	<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/></div><?
endif;

	?><input type="submit" value="<?=GetMessage("OPINIONS_SEND"); ?>" name="submit"/>
		</td>
	</tr>
</table>
</form><?
endif;?>