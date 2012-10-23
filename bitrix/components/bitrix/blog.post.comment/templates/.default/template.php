<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!$this->__component->__parent || empty($this->__component->__parent->__name) || $this->__component->__parent->__name != "bitrix:blog"):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/themes/blue/style.css');
endif;
?>
<div class="blog-comments">
<?
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script.php");

if(strlen($arResult["MESSAGE"])>0)
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<?=$arResult["MESSAGE"]?>
		</div>
	</div>
	<?
}
if(strlen($arResult["ERROR_MESSAGE"])>0)
{
	?>
	<div class="blog-errors blog-note-box blog-note-error">
		<div class="blog-error-text">
			<?=$arResult["ERROR_MESSAGE"]?>
		</div>
	</div>
	<?
}
if(strlen($arResult["FATAL_MESSAGE"])>0)
{
	?>
	<div class="blog-errors blog-note-box blog-note-error">
		<div class="blog-error-text">
			<?=$arResult["FATAL_MESSAGE"]?>
		</div>
	</div>
	<?
}
else
{
	?>
	<div id="form_comment_" style="display:none;">
		<div id="form_c_del">
		<div class="blog-comment-form">
		
		<form method="POST" name="form_comment" id="form_comment" action="<?=POST_FORM_ACTION_URI?>">
		<input type="hidden" name="parentId" id="parentId" value="">
		<input type="hidden" name="edit_id" id="edit_id" value="">
		<input type="hidden" name="act" id="act" value="add">
		<input type="hidden" name="post" value="Y">
		<?=bitrix_sessid_post()?>
		
		<div class="blog-comment-fields">
			<?
			if(empty($arResult["User"]))
			{
				?>
				<div class="blog-comment-field blog-comment-field-user">
					<div class="blog-comment-field blog-comment-field-author"><div class="blog-comment-field-text"><label for="user_name"><?=GetMessage("B_B_MS_NAME")?></label><span class="blog-required-field">*</span></div><span><input maxlength="255" size="30" tabindex="3" type="text" name="user_name" id="user_name" value="<?=htmlspecialcharsEx($_SESSION["blog_user_name"])?>"></span></div>
					<div class="blog-comment-field-user-sep">&nbsp;</div>
					<div class="blog-comment-field blog-comment-field-email"><div class="blog-comment-field-text"><label for="">E-mail</label><span class="blog-required-field">*</span></div><span><input maxlength="255" size="30" tabindex="4" type="text" name="user_email" id="user_email" value="<?=htmlspecialcharsEx($_SESSION["blog_user_email"])?>"></span></div>
					<div class="blog-clear-float"></div>
				</div>
				<?
			}
			?>
			<?if($arParams["NOT_USE_COMMENT_TITLE"] != "Y")
			{
				?>
				<div class="blog-comment-field blog-comment-field-title">
					<div class="blog-comment-field">
					<div class="blog-comment-field-text"><label for="user_name"><?=GetMessage("BPC_SUBJECT")?></label></div>
					<span><input size="70" type="text" name="subject" id="subject" value=""></span>
					<div class="blog-clear-float"></div>
					</div>
				</div>
				<?
			}
			?>
			<div class="blog-comment-field blog-comment-field-bbcode">

				<div class="blog-bbcode-line">
					<a id=bold class="blog-bbcode-bold" href='javascript:simpletag("B")' title="<?=GetMessage("BPC_BOLD")?>"></a>
					<a id=italic class="blog-bbcode-italic" href='javascript:simpletag("I")' title="<?=GetMessage("BPC_ITALIC")?>"></a>
					<a id=under class="blog-bbcode-underline" href='javascript:simpletag("U")' title="<?=GetMessage("BPC_UNDER")?>"></a>
					<a id=strike class="blog-bbcode-strike" href='javascript:simpletag("S")' title="<?=GetMessage("BPC_STRIKE")?>"></a>
					<a id=url class="blog-bbcode-url" href='javascript:tag_url()' title="<?=GetMessage("BPC_HYPERLINK")?>"></a>
					<a id=image class="blog-bbcode-img" href='javascript:tag_image()' title="<?=GetMessage("BLOG_P_INSERT_IMAGE_LINK")?>"></a>
										
					<a id=quote class="blog-bbcode-quote" href='javascript:quoteMessage()' title="<?=GetMessage("BPC_QUOTE")?>"></a>
					<a id=code class="blog-bbcode-code" href='javascript:simpletag("CODE")' title="<?=GetMessage("BPC_CODE")?>"></a>
					<a id=list class="blog-bbcode-list" href='javascript:tag_list()' title="<?=GetMessage("BPC_LIST")?>"></a>
					<a id=FontColor	class="blog-bbcode-color" href='javascript:ColorPicker()' title="<?=GetMessage("BPC_IMAGE")?>"></a>

					<select class="blog-bbcode-font" name="ffont" id="select_font" onchange="alterfont(this.options[this.selectedIndex].value, 'FONT')">
						<option value='0'><?=GetMessage("BPC_FONT")?></option>
						<option value='Arial' style='font-family:Arial'>Arial</option>
						<option value='Times' style='font-family:Times'>Times</option>
						<option value='Courier' style='font-family:Courier'>Courier</option>
						<option value='Impact' style='font-family:Impact'>Impact</option>
						<option value='Geneva' style='font-family:Geneva'>Geneva</option>
						<option value='Optima' style='font-family:Optima'>Optima</option>
						<option value='Verdana' style='font-family:Verdana'>Verdana</option>
					</select>
					<div class="blog-clear-float"></div>
				</div>
				<?
				if(!empty($arResult["Smiles"]))
				{
					?>
					<div class="blog-smiles-line">
					<?
					$arSmiles = $arResult["Smiles"][0];
					$i = 0;
					foreach($arResult["Smiles"] as $arSmiles)
					{
						?>
						<img src="/bitrix/images/blog/smile/<?=$arSmiles["IMAGE"]?>" width="<?=$arSmiles["IMAGE_WIDTH"]?>" height="<?=$arSmiles["IMAGE_HEIGHT"]?>"  title="<?=GetMessage("BPC_SMILE")?>" OnClick="emoticon('<?=$arSmiles["TYPE"]?>')" style="cursor:pointer"<?if($arResult["use_captcha"]!==true) echo ' onload="imageLoaded()"'?>>
							
						<?
						$i++;
						if($i >= $arParams["SMILES_COUNT"])
							break;
					}
					?>
					</div>
					<?

					if(count($arResult["Smiles"]) > $arParams["SMILES_COUNT"])
					{
						?>
						<div class="blog-more-smiles"><a title="<?=GetMessage("BPC_SMILE")?>" href="javascript:Smiles()"><?=GetMessage("BPC_SMILE")?></a></div>
						<?
					}
				}
				?>
				<div class="blog-bbcode-closeall"><a id=close_all style=visibility:hidden href='javascript:closeall()' title='<?=GetMessage("BPC_CLOSE_OPENED_TAGS")?>'><?=GetMessage("BPC_CLOSE_ALL_TAGS")?></a></div>
				<div class="blog-clear-float"></div>
			</div>
			<div class="blog-comment-field blog-comment-field-text blog-comment-field-content">
				<textarea cols="55" rows="10" tabindex="6" id="comment" onKeyPress="check_ctrl_enter(arguments[0])" name="comment"></textarea>
			</div>
			<?
			if($arResult["use_captcha"]===true)
			{
				?>
				<div class="blog-comment-field blog-comment-field-captcha">
					<div class="blog-comment-field-captcha-label">
						<label for=""><?=GetMessage("B_B_MS_CAPTCHA_SYM")?></label><span class="blog-required-field">*</span><br>
						<input type="hidden" name="captcha_code" id="captcha_code" value="<?=$arResult["CaptchaCode"]?>">
						<input type="text" size="30" name="captcha_word" id="captcha_word" value=""  tabindex="7">
						</div>
					<div class="blog-comment-field-captcha-image"><div id="div_captcha"></div></div>
				</div>
				<?
			}
			?>

			<div class="blog-comment-buttons">
				<input tabindex="10" value="<?=GetMessage("B_B_MS_SEND")?>" type="submit" name="post">
				<input tabindex="11" name="preview" value="<?=GetMessage("B_B_MS_PREVIEW")?>" type="submit">
			</div>
			
		</div>
		</form>
		</div>
	</div>
	</div>
	
	<?
	if($arResult["use_captcha"]===true)
	{
		?>
		<div id="captcha_del">
		<script>
			<!--
			var cc;
			if(document.cookie.indexOf('<?echo session_name()?>'+'=') == -1)
				cc = Math.random();
			else
				cc ='<?=$arResult["CaptchaCode"]?>';

			document.write('<img src="/bitrix/tools/captcha.php?captcha_code='+cc+'" width="180" height="40" id="captcha" style="display:none;" onload="imageLoaded()">');
			document.getElementById('captcha_code').value = cc;
			//-->
		</script>
		</div>
		<?
	}
	?>
	<?
	function ShowComment($comment, $tabCount=0, $tabSize=2.5, $canModerate=false, $User=Array(), $use_captcha=false, $bCanUserComment=false, $errorComment=false, $arParams = array())
	{
		if($comment["SHOW_AS_HIDDEN"] == "Y" || $comment["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH || $comment["SHOW_SCREENNED"] == "Y" || $comment["ID"] == "preview")
		{
			$tabCount = IntVal($tabCount);
			if($tabCount <= 5)
				$paddingSize = 2.5 * $tabCount;
			elseif($tabCount > 5 && $tabCount <= 10)
				$paddingSize = 2.5 * 5 + ($tabCount - 5) * 1.5;
			elseif($tabCount > 10)
				$paddingSize = 2.5 * 5 + 1.5 * 5 + ($tabCount-10) * 1;
			?>
			<a name="<?=$comment["ID"]?>"></a>
			<div class="blog-comment" style="padding-left:<?=$paddingSize?>em;">
			<?
			if($comment["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH || $comment["SHOW_SCREENNED"] == "Y" || $comment["ID"] == "preview")
			{
				$aditStyle = "";
				if($comment["AuthorIsAdmin"] == "Y")
					$aditStyle = " blog-comment-admin";
				if(IntVal($comment["AUTHOR_ID"]) > 0)
					$aditStyle .= " blog-comment-user-".IntVal($comment["AUTHOR_ID"]);
				if($comment["AuthorIsPostAuthor"] == "Y")
					$aditStyle .= " blog-comment-author";
				if($comment["PUBLISH_STATUS"] != BLOG_PUBLISH_STATUS_PUBLISH && $comment["ID"] != "preview")
					$aditStyle .= " blog-comment-hidden";
				?>
				<div class="blog-comment-cont<?=$aditStyle?>">
				<div class="blog-comment-cont-white">
				<div class="blog-comment-info">
					<?if ($arParams["SHOW_RATING"] == "Y"):?>
					<div class="blog-post-rating">
					<?
					$GLOBALS["APPLICATION"]->IncludeComponent(
						"bitrix:rating.vote", "",
						Array(
							"ENTITY_TYPE_ID" => "BLOG_COMMENT",
							"ENTITY_ID" => $comment["ID"],
							"OWNER_ID" => $comment["arUser"]["ID"],
							"USER_HAS_VOTED" => $arParams["RATING"][$comment["ID"]]["USER_HAS_VOTED"],
							"TOTAL_VOTES" => $arParams["RATING"][$comment["ID"]]["TOTAL_VOTES"],
							"TOTAL_POSITIVE_VOTES" => $arParams["RATING"][$comment["ID"]]["TOTAL_POSITIVE_VOTES"],
							"TOTAL_NEGATIVE_VOTES" => $arParams["RATING"][$comment["ID"]]["TOTAL_NEGATIVE_VOTES"],
							"TOTAL_VALUE" => $arParams["RATING"][$comment["ID"]]["TOTAL_VALUE"]
						),
						null,
						array("HIDE_ICONS" => "Y")
					);?>
					</div>
					<?endif;?>
					<?
					if (COption::GetOptionString("blog", "allow_alias", "Y") == "Y" && (strlen($comment["urlToBlog"]) > 0 || strlen($comment["urlToAuthor"]) > 0) && array_key_exists("ALIAS", $comment["BlogUser"]) && strlen($comment["BlogUser"]["ALIAS"]) > 0)
						$arTmpUser = array(
							"NAME" => "",
							"LAST_NAME" => "",
							"SECOND_NAME" => "",
							"LOGIN" => "",
							"NAME_LIST_FORMATTED" => $comment["BlogUser"]["~ALIAS"],
						);
					elseif (strlen($comment["urlToBlog"]) > 0 || strlen($comment["urlToAuthor"]) > 0)
						$arTmpUser = array(
							"NAME" => $comment["arUser"]["~NAME"],
							"LAST_NAME" => $comment["arUser"]["~LAST_NAME"],
							"SECOND_NAME" => $comment["arUser"]["~SECOND_NAME"],
							"LOGIN" => $comment["arUser"]["~LOGIN"],
							"NAME_LIST_FORMATTED" => "",
						);

					if(strlen($comment["urlToBlog"])>0)
					{
						?>
						<div class="blog-author">
						<?
						if($arParams["SEO_USER"] == "Y"):?>
							<noindex>
							<a class="blog-author-icon" href="<?=$comment["urlToAuthor"]?>" rel="nofollow"></a>
							</noindex>
						<?else:?>
							<a class="blog-author-icon" href="<?=$comment["urlToAuthor"]?>"></a>
						<?endif;?>
						<?
						$GLOBALS["APPLICATION"]->IncludeComponent("bitrix:main.user.link",
							'',
							array(
								"ID" => $comment["arUser"]["ID"],
								"HTML_ID" => "blog_post_comment_".$comment["arUser"]["ID"],
								"NAME" => $arTmpUser["NAME"],
								"LAST_NAME" => $arTmpUser["LAST_NAME"],
								"SECOND_NAME" => $arTmpUser["SECOND_NAME"],
								"LOGIN" => $arTmpUser["LOGIN"],
								"NAME_LIST_FORMATTED" => $arTmpUser["NAME_LIST_FORMATTED"],
								"USE_THUMBNAIL_LIST" => "N",
								"PROFILE_URL" => $comment["urlToAuthor"],
								"PROFILE_URL_LIST" => $comment["urlToBlog"],
								"PATH_TO_SONET_MESSAGES_CHAT" => $arParams["~PATH_TO_MESSAGES_CHAT"],
								"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
								"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
								"SHOW_YEAR" => $arParams["SHOW_YEAR"],
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"],
								"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
								"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
								"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
								"PATH_TO_SONET_USER_PROFILE" => ($arParams["USE_SOCNET"] == "Y" ? $comment["urlToAuthor"] : $arParams["~PATH_TO_SONET_USER_PROFILE"]),
								"INLINE" => "Y",
								"SEO_USER" => $arParams["SEO_USER"],
							),
							false,
							array("HIDE_ICONS" => "Y")
						);
						?>
						</div>
						<?
					}
					elseif(strlen($comment["urlToAuthor"])>0)
					{
						?><div class="blog-author"><?
						if($arParams["SEO_USER"] == "Y"):?>
							<noindex>
							<a class="blog-author-icon" href="<?=$comment["urlToAuthor"]?>" rel="nofollow"></a>
							</noindex>
						<?else:?>
							<a class="blog-author-icon" href="<?=$comment["urlToAuthor"]?>"></a>
						<?endif;?>
						<?if($arParams["SEO_USER"] == "Y"):?>
							<noindex>
						<?endif;?>
						<?
						$GLOBALS["APPLICATION"]->IncludeComponent("bitrix:main.user.link",
							'',
							array(
								"ID" => $comment["arUser"]["ID"],
								"HTML_ID" => "blog_post_comment_".$comment["arUser"]["ID"],
								"NAME" => $arTmpUser["NAME"],
								"LAST_NAME" => $arTmpUser["LAST_NAME"],
								"SECOND_NAME" => $arTmpUser["SECOND_NAME"],
								"LOGIN" => $arTmpUser["LOGIN"],
								"NAME_LIST_FORMATTED" => $arTmpUser["NAME_LIST_FORMATTED"],
								"USE_THUMBNAIL_LIST" => "N",
								"PROFILE_URL" => $comment["urlToAuthor"],
								"PATH_TO_SONET_MESSAGES_CHAT" => $arParams["~PATH_TO_MESSAGES_CHAT"],
								"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
								"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
								"SHOW_YEAR" => $arParams["SHOW_YEAR"],
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"],
								"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
								"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
								"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
								"PATH_TO_SONET_USER_PROFILE" => ($arParams["USE_SOCNET"] == "Y" ? $comment["urlToAuthor"] : $arParams["~PATH_TO_SONET_USER_PROFILE"]),
								"INLINE" => "Y",
								"SEO_USER" => $arParams["SEO_USER"],
							),
							false,
							array("HIDE_ICONS" => "Y")
						);
						?>
						<?if($arParams["SEO_USER"] == "Y"):?>
							</noindex>
						<?endif;?>
						</div>
						<?
					}
					else
					{
						?>
						<div class="blog-author"><div class="blog-author-icon"></div><?=$comment["AuthorName"]?></div>
						<?
					}
					
					if(strlen($comment["urlToDelete"])>0)
					{
						?>
						<div class="blog-comment-author-info">
						<?
						if(strlen($comment["AuthorEmail"])>0)
						{
							?>
							(<a href="mailto:<?=$comment["AuthorEmail"]?>"><?=$comment["AuthorEmail"]?></a>)
							<?
						}
						?>
						</div>
						<?
					}

					?>
					<div class="blog-comment-date"><?=$comment["DateFormated"]?></div>
				</div>
				<div class="blog-comment-content">
					<div class="blog-comment-avatar"><?=$comment["AVATAR_img"]?></div>
					<?if(strlen($comment["TitleFormated"])>0)
					{
						?>
						<b><?=$comment["TitleFormated"]?></b><br />
						<?
					}
					?>
					<?=$comment["TextFormated"]?>
					
					<div class="blog-comment-meta">
					<?
					if($bCanUserComment===true)
					{
						?>
						<span class="blog-comment-answer"><a href="javascript:void(0)" onclick="return showComment('<?=$comment["ID"]?>', '<?=$comment["CommentTitle"]?>', '', '', '', '')"><?=GetMessage("B_B_MS_REPLY")?></a></span>
						<span class="blog-vert-separator">|</span>
						<?
					}

					if(IntVal($comment["PARENT_ID"])>0)
					{
						?>
						<span class="blog-comment-parent"><a href="#<?=$comment["PARENT_ID"]?>"><?=GetMessage("B_B_MS_PARENT")?></a></span>
						<span class="blog-vert-separator">|</span>
						<?
					}
					?>
					<span class="blog-comment-link"><a href="#<?=$comment["ID"]?>"><?=GetMessage("B_B_MS_LINK")?></a></span>
					<?
					if($comment["CAN_EDIT"] == "Y")
					{
						$Text = CUtil::JSEscape($comment["~POST_TEXT"]);
						$Title = CUtil::JSEscape($comment["TITLE"]);
						?>
						<script>
						<!--
						var cmt<?=$comment["ID"]?> = '<?=$Text?>';
						//-->
						</script>
						<span class="blog-vert-separator">|</span>
						<span class="blog-comment-edit"><a href="javascript:void(0)" onclick="return editComment('<?=$comment["ID"]?>', '<?=$Title?>', cmt<?=$comment["ID"]?>)"><?=GetMessage("BPC_MES_EDIT")?></a></span>
						<?
					}
					if(strlen($comment["urlToShow"])>0)
					{
						?>
						<span class="blog-vert-separator">|</span>
						<span class="blog-comment-show"><a href="<?=$comment["urlToShow"]."&".bitrix_sessid_get()?>"><?=GetMessage("BPC_MES_SHOW")?></a></span>
						<?
					}				
					if(strlen($comment["urlToHide"])>0)
					{
						?>
						<span class="blog-vert-separator">|</span>
						<span class="blog-comment-show"><a href="<?=$comment["urlToHide"]."&".bitrix_sessid_get()?>"><?=GetMessage("BPC_MES_HIDE")?></a></span>
						<?
					}
					if(strlen($comment["urlToDelete"])>0)
					{
						?>
						<span class="blog-vert-separator">|</span>
						<span class="blog-comment-delete"><a href="javascript:if(confirm('<?=GetMessage("BPC_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$comment["urlToDelete"]."&".bitrix_sessid_get()?>'"><?=GetMessage("BPC_MES_DELETE")?></a></span>
						<?
					}
					?>
					</div>
					
				</div>
				</div>
				</div>
					<div class="blog-clear-float"></div>

				<?
				if(strlen($errorComment) <= 0 && strlen($_POST["preview"]) > 0 && (IntVal($_POST["parentId"]) > 0 || IntVal($_POST["id"]) > 0)
					&& ( (IntVal($_POST["parentId"])==$comment["ID"] && IntVal($_POST["id"]) <= 0) 
						|| (IntVal($_POST["id"]) > 0 && IntVal($_POST["id"]) == $comment["ID"] && $comment["CAN_EDIT"] == "Y")))
				{							
					?><div style="border:1px solid red"><?
						$commentPreview = Array(
								"ID" => "preview",
								"TitleFormated" => htmlspecialcharsEx($_POST["subject"]),
								"TextFormated" => $_POST["commentFormated"],
								"AuthorName" => $User["NAME"],
								"DATE_CREATE" => GetMessage("B_B_MS_PREVIEW_TITLE"),
							);
						ShowComment($commentPreview, (IntVal($_POST["id"]) == $comment["ID"] && $comment["CAN_EDIT"] == "Y") ? $level : ($level+1), 2.5, false, Array(), false, false, false, $arParams);
					?></div><?
				}
				
				if(strlen($errorComment)>0 && $bCanUserComment===true
					&& (IntVal($_POST["parentId"])==$comment["ID"] || IntVal($_POST["id"]) == $comment["ID"]))
				{							
					?>
					<div class="blog-errors blog-note-box blog-note-error">
						<div class="blog-error-text">
							<?=$errorComment?>
						</div>
					</div>
					<?
				}
				?>
				<div id="form_comment_<?=$comment['ID']?>"></div>
				
				<?
				if((strlen($errorComment) > 0 || strlen($_POST["preview"]) > 0) 
					&& (IntVal($_POST["parentId"])==$comment["ID"] || IntVal($_POST["id"]) == $comment["ID"]) 
					&& $bCanUserComment===true)
				{
					$form1 = CUtil::JSEscape($_POST["comment"]);
					
					$subj = CUtil::JSEscape($_POST["subject"]);
					$user_name = CUtil::JSEscape($_POST["user_name"]);
					$user_email = CUtil::JSEscape($_POST["user_email"]);
					?>
					<script>
					<!--
					var cmt = '<?=$form1?>';
					<?
					if(IntVal($_POST["id"]) == $comment["ID"])
					{
						?>
						editComment('<?=$comment["ID"]?>', '<?=$subj?>', cmt);
						<?
					}
					else
					{
						?>
						showComment('<?=$comment["ID"]?>', '<?=$subj?>', 'Y', cmt, '<?=$user_name?>', '<?=$user_email?>');
						<?
					}
					?>
					//-->
					</script>
					<?
				}
			}
			elseif($comment["SHOW_AS_HIDDEN"] == "Y")
				echo "<b>".GetMessage("BPC_HIDDEN_COMMENT")."</b>";
			?>
			</div>
			<?
		}
	}

	function RecursiveComments($sArray, $key, $level=0, $first=false, $canModerate=false, $User, $use_captcha, $bCanUserComment, $errorComment, $arSumComments, $arParams)
	{
		if(!empty($sArray[$key]))
		{
			foreach($sArray[$key] as $comment)
			{
				if(!empty($arSumComments[$comment["ID"]]))
				{
					$comment["CAN_EDIT"] = $arSumComments[$comment["ID"]]["CAN_EDIT"];
					$comment["SHOW_AS_HIDDEN"] = $arSumComments[$comment["ID"]]["SHOW_AS_HIDDEN"];
					$comment["SHOW_SCREENNED"] = $arSumComments[$comment["ID"]]["SHOW_SCREENNED"];
				}
				ShowComment($comment, $level, 2.5, $canModerate, $User, $use_captcha, $bCanUserComment, $errorComment, $arParams);
				if(!empty($sArray[$comment["ID"]]))
				{
					foreach($sArray[$comment["ID"]] as $key1)
					{
						if(!empty($arSumComments[$key1["ID"]]))
						{
							$key1["CAN_EDIT"] = $arSumComments[$key1["ID"]]["CAN_EDIT"];
							$key1["SHOW_AS_HIDDEN"] = $arSumComments[$key1["ID"]]["SHOW_AS_HIDDEN"];
							$key1["SHOW_SCREENNED"] = $arSumComments[$key1["ID"]]["SHOW_SCREENNED"];
						}
						ShowComment($key1, ($level+1), 2.5, $canModerate, $User, $use_captcha, $bCanUserComment, $errorComment, $arParams);

						if(!empty($sArray[$key1["ID"]]))
						{
							RecursiveComments($sArray, $key1["ID"], ($level+2), false, $canModerate, $User, $use_captcha, $bCanUserComment, $errorComment, $arSumComments, $arParams);
						}
					}
				}
				if($first)
					$level=0;
			}
		}
	}
	?>
	<?
	if($arResult["CanUserComment"])
	{
		$postTitle = "";
		if($arParams["NOT_USE_COMMENT_TITLE"] != "Y")
			$postTitle = "RE: ".CUtil::JSEscape($arResult["Post"]["TITLE"]);
		
		?>
		<div class="blog-add-comment"><a name="comments"></a><a href="javascript:void(0)" onclick="return showComment('0', '<?=$postTitle?>')"><b><?=GetMessage("B_B_MS_ADD_COMMENT")?></b></a><br /></div>
		<a name="0"></a>
		<?
		if(strlen($arResult["COMMENT_ERROR"]) <= 0 && strlen($_POST["parentId"]) < 2 
			&& IntVal($_POST["parentId"])==0 && strlen($_POST["preview"]) > 0 && IntVal($_POST["id"]) <= 0)
		{		
			?><div style="border:1px solid red"><?
				$commentPreview = Array(
						"ID" => "preview",
						"TitleFormated" => htmlspecialcharsEx($_POST["subject"]),
						"TextFormated" => $_POST["commentFormated"],
						"AuthorName" => $arResult["User"]["NAME"],
						"DATE_CREATE" => GetMessage("B_B_MS_PREVIEW_TITLE"),
					);
				ShowComment($commentPreview, 0, 2.5, false, $arResult["User"], $arResult["use_captcha"], $arResult["CanUserComment"], false, $arParams);
			?></div><?
		}

		if(strlen($arResult["COMMENT_ERROR"]) > 0 && strlen($_POST["parentId"]) < 2
			&& IntVal($_POST["parentId"])==0 && IntVal($_POST["id"]) <= 0)
		{
			?>
			<div class="blog-errors blog-note-box blog-note-error">
				<div class="blog-error-text"><?=$arResult["COMMENT_ERROR"]?></div>
			</div>
			<?
		}
		?>
		<div id=form_comment_0></div>
		<?
		if((strlen($arResult["COMMENT_ERROR"])>0 || strlen($_POST["preview"]) > 0) 
			&& IntVal($_POST["parentId"]) == 0 && strlen($_POST["parentId"]) < 2 && IntVal($_POST["id"]) <= 0)
		{
			$form1 = CUtil::JSEscape($_POST["comment"]);
			
			$subj = CUtil::JSEscape($_POST["subject"]);
			$user_name = CUtil::JSEscape($_POST["user_name"]);
			$user_email = CUtil::JSEscape($_POST["user_email"]);

			?>
			<script>
			<!--
			var cmt = '<?=$form1?>';
			showComment('0', '<?=$subj?>', 'Y', cmt, '<?=$user_name?>', '<?=$user_email?>');
			//-->
			</script>
			<?
		}
		
		if($arResult["NEED_NAV"] == "Y")
		{
			?>
			<div class="blog-comment-nav">
				<?=GetMessage("BPC_PAGE")?>&nbsp;<?
				foreach($arResult["PAGES"] as $v)
				{
					echo $v;
				}
				
				
			?>
			</div>
			<?
		}
	}

	$arParams["RATING"] = $arResult["RATING"];
	RecursiveComments($arResult["CommentsResult"], $arResult["firstLevel"], 0, true, $arResult["canModerate"], $arResult["User"], $arResult["use_captcha"], $arResult["CanUserComment"], $arResult["COMMENT_ERROR"], $arResult["Comments"], $arParams);

	if($arResult["NEED_NAV"] == "Y")
	{
		?>
		<div class="blog-comment-nav">
			<?=GetMessage("BPC_PAGE")?>&nbsp;<?
			foreach($arResult["PAGES"] as $v)
			{
				echo $v;
			}
			
			
		?>
		</div>
		<?
	}

	if($arResult["CanUserComment"] && count($arResult["Comments"])>2)
	{
		?>
		<div class="blog-add-comment"><a href="#comments" onclick="return showComment('00', '<?=$postTitle?>')"><b><?=GetMessage("B_B_MS_ADD_COMMENT")?></b></a><br /></div><a name="00"></a>
		<?
		if(strlen($arResult["COMMENT_ERROR"]) <= 0 && $_POST["parentId"] == "00" && strlen($_POST["parentId"]) > 1 && strlen($_POST["preview"]) > 0)
		{							
			?><div style="border:1px solid red"><?
				$commentPreview = Array(
						"ID" => "preview",
						"TitleFormated" => htmlspecialcharsEx($_POST["subject"]),
						"TextFormated" => $_POST["commentFormated"],
						"AuthorName" => $arResult["User"]["NAME"],
						"DATE_CREATE" => GetMessage("B_B_MS_PREVIEW_TITLE"),
					);
				ShowComment($commentPreview, 0, 2.5, false, $arResult["User"], $arResult["use_captcha"], $arResult["CanUserComment"], $arResult["COMMENT_ERROR"], $arParams);
			?></div><?
		}
		
		if(strlen($arResult["COMMENT_ERROR"])>0 && $_POST["parentId"] == "00" && strlen($_POST["parentId"]) > 1)
		{
			?>
			<div class="blog-errors blog-note-box blog-note-error">
				<div class="blog-error-text">
					<?=$arResult["COMMENT_ERROR"]?>
				</div>
			</div>
			<?
		}
		?>

		<div id=form_comment_00></div><br />
		<?
		if((strlen($arResult["COMMENT_ERROR"])>0 || strlen($_POST["preview"]) > 0) 
			&& $_POST["parentId"] == "00" && strlen($_POST["parentId"]) > 1)
		{
			$form1 = CUtil::JSEscape($_POST["comment"]);
			
			$subj = CUtil::JSEscape($_POST["subject"]);
			$user_name = CUtil::JSEscape($_POST["user_name"]);
			$user_email = CUtil::JSEscape($_POST["user_email"]);
			?>
			<script>
			<!--
			var cmt = '<?=$form1?>';
			showComment('00', '<?=$subj?>', 'Y', cmt, '<?=$user_name?>', '<?=$user_email?>');
			//-->
			</script>
			<?
		}
	}
}
?>
</div>