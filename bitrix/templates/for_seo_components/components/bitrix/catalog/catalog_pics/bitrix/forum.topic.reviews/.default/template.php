<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arResult["SHOW_POSTS"] == "Y"):?>
<?if (strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?><br/><br/></p>
<?endif?>
	<table class="forum-reviews-messages">
	<?foreach ($arResult["MESSAGES"] as $res):?>
		<tr><th align="left">
			<table class="forum-reviews-clear"><tr>
				<td width="100%"><a name="message<?=$res["ID"]?>"></a><i><b><?=$res["AUTHOR_NAME"]?></b>, <?=$res["POST_DATE"]?></i></td>
			<?if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):?>
				<td><a href="#review_anchor" onMouseDown="quoteMessageEx('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>', '<?=$res["FOR_JS"]["POST_MESSAGE_TEXT"]?>')" title="<?=GetMessage("FTR_QUOTE_HINT")?>" class="button-small"><?=GetMessage("FTR_QUOTE")?></a></td>
				
			<?endif;?>
				<td><a href="#review_anchor" onMouseDown="reply2author('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>,')" title="<?=GetMessage("FTR_INSERT_NAME")?>"  class="button-small"><?=GetMessage("FTR_NAME")?></a></td>
			</tr></table>
		</th></tr>
		<tr><td><p><?=$res["POST_MESSAGE_TEXT"]?></p>
		<?if (strLen($res["ATTACH_IMG"]) > 0):?>
			<br/><br/><?=$res["ATTACH_IMG"]?>
		<?endif;?>
		</td></tr>
		<tr><td class="clear"><br/></td></tr>
	<?endforeach;?>
	</table>
<?endif?>
<?if (strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>

<?if ($arResult["SHOW_LINK"] == "Y"):?>
	<br /><a href="<?=$arResult["read"]?>"><?=GetMessage("F_C_GOTO_FORUM") ?></a><br /><br />
<?endif?>
<?=ShowError($arResult["ERROR_MESSAGE"])?>
<?=ShowNote($arResult["OK_MESSAGE"])?>
	<?if ($arResult["SHOW_POST_FORM"] == "Y"):?>
		<script type="text/javascript"><?
			include("script.php");
		?></script>
		
		<form action="<?=POST_FORM_ACTION_URI?>#review_anchor" method="post" name="REPLIER" id="REPLIER" enctype="multipart/form-data" onsubmit="return ValidateForm(this);">
		<input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
		<input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
		<input type="hidden" name="SECTION_ID" value="<?=$arParams["SECTION_ID"]?>" />
		<input type="hidden" name="save_product_review" value="Y" />
		<?=$arResult["sessid"]?>
		
		<?if ($arResult["VIEW"] != "Y"):?>
			<a name="review_anchor"></a>
		<?endif;?>
		
		<br/>
		
		<table class="forum-reviews-form data-table">
		
			<?if ($arResult["IS_AUTHORIZED"]):?>
				<tr>
					<th align="right"><?=GetMessage("OPINIONS_NAME")?>:</th>
					<th align="left">&nbsp;<b><?=$arResult["REVIEW_AUTHOR"]?></b></th>
				</tr>
			<?else:?>
				<tr>
					<th align="right"><?=GetMessage("OPINIONS_NAME")?>:</th>
					<th align="left">&nbsp;<input type="text" name="REVIEW_AUTHOR" size="30" maxlength="64" value="<?=$arResult["REVIEW_AUTHOR"]?>" /></th>
				</tr>
			<?if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y"):?>
				<tr>
					<th align="right"><?=GetMessage("OPINIONS_EMAIL")?>:</th>
					<th align="left">&nbsp;<input type="text" name="REVIEW_EMAIL" size="30" maxlength="64" value="<?=$arResult["REVIEW_EMAIL"]?>"/></th>
				</tr>
			<?endif;?>
			<?endif;?>
		
		<tr>
			<th colspan="2" align="left"><?=GetMessage("FTR_MESSAGE_TEXT")?></th>
		</tr>
		
		<tr>
			<?if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):?>
			<td align="center" valign="top">
					<br/><table class="forum-smile">
						<tr><th colspan="3"><?=GetMessage("FTR_SMILES")?></th></tr>
						<?=$arResult["ForumPrintSmilesList"]?>
					</table>
			</td>
			<td>
			<?else:?>
			<td colspan="2">
			<?endif;?>
				<table class="forum-reviews-clear">
					<tr><td>
						<?if ($arResult["FORUM"]["ALLOW_BIU"] == "Y"):?>
							<input type='button' accesskey='b' value='B' onClick='simpletag("B")' name='B' title="<?=GetMessage("FTR_BOLD")?>" onMouseOver="show_hints('bold')"/> 
							<input type='button' accesskey='i' value='I' onClick='simpletag("I")' name='I' title="<?=GetMessage("FTR_ITAL")?>" onMouseOver="show_hints('italic')"/> 
							<input type='button' accesskey='u' value='U' onClick='simpletag("U")' name='U' title="<?=GetMessage("FTR_UNDER")?>" onMouseOver="show_hints('under')"/> 
						<?endif;?>
						
						<?if ($arResult["FORUM"]["ALLOW_FONT"] == "Y"):?>
							<select name='ffont' onchange="alterfont(this.options[this.selectedIndex].value, 'FONT')" onMouseOver="show_hints('font')">
								<option value='0'><?=GetMessage("FTR_FONT")?></option>
								<option value='Arial' style='font-family:Arial'>Arial</option>
								<option value='Times' style='font-family:Times'>Times</option>
								<option value='Courier' style='font-family:Courier'>Courier</option>
								<option value='Impact' style='font-family:Impact'>Impact</option>
								<option value='Geneva' style='font-family:Geneva'>Geneva</option>
								<option value='Optima' style='font-family:Optima'>Optima</option>
							</select> 
							<select name='fcolor' onchange="alterfont(this.options[this.selectedIndex].value, 'COLOR')" onMouseOver="show_hints('color')">
								<option value='0'><?=GetMessage("FTR_COLOR")?></option>
								<option value='blue' style='color:blue'><?=GetMessage("FTR_BLUE")?></option>
								<option value='red' style='color:red'><?=GetMessage("FTR_RED")?></option>
								<option value='gray' style='color:gray'><?=GetMessage("FTR_GRAY")?></option>
								<option value='green' style='color:green'><?=GetMessage("FTR_GREEN")?></option>
							</select> 
						<?endif;?>
						<?if (($arResult["FORUM"]["ALLOW_BIU"] == "Y") || ($arResult["FORUM"]["ALLOW_CODE"] == "Y") || ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y") || $arResult["FORUM"]["ALLOW_FONT"] == "Y"):?>
						<br /><a href="#review_anchor" onclick="closeall();" title="<?=GetMessage("FTR_CLOSE_OPENED_TAGS")?>" onMouseOver="show_hints('close')"><?=GetMessage("FTR_CLOSE_ALL_TAGS")?></a>
						<?endif;?>
						</td>
					</tr>
					<tr>
						<td align="left">
						
						<?if ($arResult["FORUM"]["ALLOW_ANCHOR"] == "Y"):?>
							<input type='button' accesskey='h' value=' http:// ' onClick='tag_url()' name='url' title="<?=GetMessage("FTR_HYPERLINK")?>" onMouseOver="show_hints('url')"/>
						<?endif;?>
						
						<?if ($arResult["FORUM"]["ALLOW_IMG"] == "Y"):?>
							<input type='button' accesskey='g' value=' IMG ' onClick='tag_image()' name='img' title="<?=GetMessage("FTR_IMAGE")?>" onMouseOver="show_hints('img')"/>
						<?endif;?>
						
						<?if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):?>
							<input type='button' accesskey='q' value=' QUOTE ' onClick='simpletag("QUOTE")' name='QUOTE' title="<?=GetMessage("FTR_QUOTE")?>" onMouseOver="show_hints('quote')"/>
						<?endif;?>
						
						<?if ($arResult["FORUM"]["ALLOW_CODE"] == "Y"):?>
							<input type='button' accesskey='p' value=' CODE ' onClick='simpletag("CODE")' name='CODE' title="<?=GetMessage("FTR_CODE")?>" onMouseOver="show_hints('code')"/>
						<?endif;?>
						
						<?if ($arResult["FORUM"]["ALLOW_LIST"] == "Y"):?>
							<input type='button' accesskey='l' value=' LIST ' onClick='tag_list()' name="LIST" title="<?=GetMessage("FTR_LIST")?>" onMouseOver="show_hints('list')"/>
						<?endif;?>
						
						<?if ($arResult["LANGUAGE_ID"]=="ru"):?>
							<input type='button' accesskey='t' value=' Транслит ' onClick='translit()' name="TRANSLIT" title="<?=GetMessage("FTR_TRANSLIT")?>" onMouseOver="show_hints('translit')"/>
						<?endif;?>
						</td>
					</tr>
					
					<tr>
						<td align="left">
							<?if (($arResult["FORUM"]["ALLOW_BIU"] == "Y") || ($arResult["FORUM"]["ALLOW_CODE"] == "Y") || ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y") || $arResult["FORUM"]["ALLOW_FONT"] == "Y"):?>
							<?=GetMessage("FTR_OPENED_TAGS")?>&nbsp;
								<input 
									type='text' name='tagcount' size='3' maxlength='3' 
									style='background-color: transparent; border: 0px solid transparent;' 
									readonly="readonly" value="0"/>
							&nbsp;<br />
								<input 
									type='text' name='helpbox' size='50' maxlength='120' 
									style='font-weight:bold; background-color: transparent; border: 0px solid transparent;font-size:80%; width:80%' 
									readonly="readonly" value=""/>
							<?endif;?>
						</td>
					</tr>
				</table>
	
				<textarea rows="15" name="REVIEW_TEXT" id="REVIEW_TEXT" tabindex="3" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?=$arResult["REVIEW_TEXT"];?></textarea><br />
					<?=GetMessage("FTR_TO_QUOTE_NOTE")?> <b><?=GetMessage("FTR_TO_QUOTE_NOTE1")?></b>.<br/>
					<?if ($arResult["FORUM"]["ALLOW_SMILES"]=="Y"):?>
					<input type="checkbox" name="REVIEW_USE_SMILES" value="Y" <?=($arResult["REVIEW_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?>/>&nbsp;<?=GetMessage("FTR_WANT_ALLOW_SMILES")?><br/><br/>
					<?endif;?>
					
					<?if ($arResult["SHOW_SUBSCRIBE"] == "Y"):?>
					<input type="checkbox" name="TOPIC_SUBSCRIBE" value="Y" <?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?>/>&nbsp;<?=GetMessage("FTR_WANT_SUBSCRIBE_TOPIC")?><br/>
					<input type="checkbox" name="FORUM_SUBSCRIBE" value="Y" <?=($arResult["FORUM_SUBSCRIBE"] == "Y")? "checked disabled " : "";?>/>&nbsp;<?=GetMessage("FTR_WANT_SUBSCRIBE_FORUM")?><br/><br/>
					<?endif;?>
					
					<?if ($arResult["SHOW_PANEL_ATTACH_IMG"] == "Y"):?>
					<?=GetMessage("FTR_LOAD")?> <?= ($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y") ? GetMessage("FTR_IMAGE1") : GetMessage("FTR_FILE1") ?> <?=GetMessage("FTR_FOR_MESSAGE")?><br/>	
					<input name="REVIEW_ATTACH_IMG" size="20" type="file"/><br/><br/>
					<?endif;?>
					<?if (!empty($arResult["CAPTCHA_CODE"])):?>
					<b><?=GetMessage("CAPTCHA_TITLE")?>:</b><br />
					<?=GetMessage("CAPTCHA_PROMT")?>:&nbsp;<input type="text" size="20" name="captcha_word" /><br />
					<img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("CAPTCHA_TITLE")?>" />
					<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/><br/><br/>
					<?endif;?>
				<input type="submit" value="<?=GetMessage("OPINIONS_SEND"); ?>" name="submit"/>
			</td>
		</tr>
	</table>
</form>
<?endif;?>