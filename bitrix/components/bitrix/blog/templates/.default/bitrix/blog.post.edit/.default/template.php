<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!$this->__component->__parent || empty($this->__component->__parent->__name) || $this->__component->__parent->__name != "bitrix:blog"):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/themes/blue/style.css');
endif;
?>
<div class="blog-post-edit">
<?
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
elseif(strlen($arResult["UTIL_MESSAGE"])>0)
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<?=$arResult["UTIL_MESSAGE"]?>
		</div>
	</div>
	<?
}
else
{	
	if($arResult["imageUpload"] == "Y")
	{
		?>
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head>
				<title><?=GetMessage("BLOG_P_IMAGE_UPLOAD")?></title>
			</head>
			<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
			<?=bitrix_sessid_post()?>
			<style type="text/css">
				td.tableborder, table.tableborder {background-color:#8FB0D2;}
				table.tablehead, td.tablehead {background-color:#F1F5FA;}
				table.tablebody, td.tablebody {background-color:#FFFFFF;}
				.tableheadtext, .tablebodylink {font-family: Verdana,Arial,Hevetica,sans-serif; font-size:12px;}
				.tableheadtext {color:#456A74}
				H1, H2, H3, H4 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#3A84C4; font-size:13px; font-weight:bold; line-height: 16px; margin-bottom: 1px;}
				errortext, .oktext, .notetext {font-family:Verdana,Arial,Hevetica,sans-serif; font-size:13px; font-weight:bold;}
				.errortext {color:red;}
			</style>
			<h1><?=GetMessage("BLOG_P_IMAGE_UPLOAD")?></h1>
			<br />
			<table border="0" cellspacing="1" cellpadding="3" class="tableborder">
			<tr>
				<td class="tablehead" valign="top" align="right" nowrap>
				<span class="tableheadtext"><b><?=GetMessage("BLOG_IMAGE")?></b></span></td>
				<td class="tablebody"><?=CFile::InputFile("FILE_ID", 20, 0)?></td>
			</tr>
			</table>
		<br />
		<input type="submit" value="<?=GetMessage("BLOG_P_DO_UPLOAD")?>" name="do_upload">
		<input type="button" value="<?=GetMessage("BLOG_P_CANCEL")?>" onclick="self.close()">
		</form>
		</html>
		<?
		if(strlen($_POST["do_upload"])>0)
		{
			?>
			<script>
				<?
				if(!empty($arResult["Image"]))
				{?>
				my_html = '<div class="blog-post-image-item"><div class="blog-post-image-item-border"><?=$arResult["ImageModified"]?></div>' +
					'<div class="blog-post-image-item-input"><input name=IMAGE_ID_title[<?=$arResult["Image"]["ID"]?>] value="<?=Cutil::JSEscape($arResult["Image"]["TITLE'"])?>"></div>' +
					'<div><input type=checkbox name=IMAGE_ID_del[<?=$arResult["Image"]["ID"]?>] id=img_del_<?=$arResult["Image"]["ID"]?>> <label for=img_del_<?=$arResult["Image"]["ID"]?>><?=GetMessage("BLOG_DELETE")?></label></div></div>';
					
				imgTable = opener.document.getElementById('blog-post-image');

				imgTable.innerHTML += my_html;
				
				<?
				if($_GET["htmlEditor"] == "Y")
				{
					?>
					var editorId = 'POST_MESSAGE_HTML';		
					if(editorId)
					{
						var pMainObj = window.opener.GLOBAL_pMainObj[editorId];
						if(pMainObj)
						{
							imageSrc = window.opener.document.getElementById(<?=$arResult["Image"]["ID"]?>).src;
							_str = '<img __bxtagname="blogImage" __bxcontainer="<?=$arResult["Image"]["ID"]?>" src="'+imageSrc+'">';
											
							pMainObj.insertHTML(_str);
							var i = window.opener.arImages.length++;
							window.opener.arImages[i] = '<?=$arResult["Image"]["ID"]?>';
						}
					}
					<?
				}
				else
				{
					?>
					opener.doInsert('[IMG ID=<?=$arResult["Image"]["ID"]?>]','',false);
					<?
				}
				}
				?>
				self.close();
			</script>
			<?
		}

		die();
	}
	elseif($_REQUEST["load_editor"] == "Y")
	{
		$APPLICATION->RestartBuffer();

		if(CModule::IncludeModule("fileman"))
		{
			?>
			<script language="JavaScript">
			<!--
			var arImages = Array();
			var arVideo = Array();
			var arVideoP = Array();
			var arVideoW = Array();
			var arVideoH = Array();
			<?
			$i = 0;
			foreach($arResult["Images"] as $aImg)
			{
				?>arImages['<?=$i?>'] = '<?=$aImg["ID"]?>';<?
				$i++;
			}

			$i = 0;
			preg_match_all("#\[video(.+?)\](.+?)\[/video[\s]*\]#ie", $arResult["PostToShow"]["~DETAIL_TEXT"], $matches);
			if(!empty($matches))
			{
				foreach($matches[0] as $key => $value)
				{
					if(strlen($value) > 0)
					{
						preg_match("#width=([0-9]+)#ie", $matches[1][$key], $width);
						preg_match("#height=([0-9]+)#ie", $matches[1][$key], $height);
						
						$matches[0][$key] = preg_replace("/([\.,\?]|&#33;)$/".BX_UTF_PCRE_MODIFIER, "", $matches[0][$key]);
						$matches[2][$key] = preg_replace("/([\.,\?]|&#33;)$/".BX_UTF_PCRE_MODIFIER, "", $matches[2][$key]);
						?>
						arVideo['<?=$i?>'] = '<?=CUtil::JSEscape($matches[0][$key])?>';
						arVideoP['<?=$i?>'] = '<?=CUtil::JSEscape($matches[2][$key])?>';
						arVideoW['<?=$i?>'] = '<?=IntVal($width[1])?>';
						arVideoH['<?=$i?>'] = '<?=IntVal($height[1])?>';
						<?
						$i++;
					}
				}
			}
			?>		
		
			function BXDialogImageUpload()
			{
				BXDialogImageUpload.prototype._Create = function ()
				{
					jsUtils.OpenWindow('<?=$APPLICATION->GetCurPageParam("image_upload=Y")?>&htmlEditor=Y', 400, 150);
				}
			}
			//-->
			</script>
			
			<?
			function CustomizeEditorForBlog()
			{
				?>
				<script>
				<!--
				function _blogImageLinkParser(_str)
				{
					for(var i=0, cnt = arImages.length; i<cnt; i++)
					{
						j = _str.indexOf("[IMG ID="+arImages[i]+"]");
						while(j > -1)
						{
							imageSrc = document.getElementById(arImages[i]).src;
							_str = _str.replace("[IMG ID="+arImages[i]+"]", '<img __bxtagname="blogImage" __bxcontainer="'+arImages[i]+'" src="'+imageSrc+'">');
							j = _str.indexOf("[IMG ID="+arImages[i]+"]");
						} 
					}
					
					for(var i=0, cnt = arVideo.length; i<cnt; i++)
					{
						j = _str.indexOf(arVideo[i]);
						while(j > -1)
						{
							_str = _str.replace(arVideo[i], '<img __bxtagname="blogVideo" src="/bitrix/images/1.gif" style="border: 1px solid rgb(182, 182, 184); background-color: rgb(226, 223, 218); background-image: url('+document.getElementById('videoImg').src+'); background-position: center center; background-repeat: no-repeat; width: '+arVideoW[i]+'px; height: '+arVideoH[i]+'px;" __bxcontainer="'+arVideoP[i]+'" width="'+arVideoW[i]+'" height="'+arVideoH[i]+'" />');
							j = _str.indexOf(arVideo[i]);
						} 
					}

					return _str;
				}
				oBXEditorUtils.addContentParser(_blogImageLinkParser);

				function _blogImageLinkUnParser(_node)
				{
					if (_node.arAttributes["__bxtagname"] == "blogImage")
						return '[IMG ID='+_node.arAttributes["__bxcontainer"]+']';
						
					if (_node.arAttributes["__bxtagname"] == "blogVideo")
					{
						return '[video width='+_node.arAttributes["width"]+' height='+_node.arAttributes["height"]+']'+_node.arAttributes["__bxcontainer"]+'[/video]';
					}
					
					return false;
				}
				oBXEditorUtils.addUnParser(_blogImageLinkUnParser);
				
				arButtons['ImageLink']	=	[
						'BXButton',
						{
							src : '/bitrix/components/bitrix/blog/templates/.default/images/bbcode/font_image_upload.gif',
							id : 'ImageLink',
							name : '<?=GetMessage("BLOG_P_IMAGE_LINK")?>',
							title : '<?=GetMessage("BLOG_P_IMAGE_LINK")?>',
							handler : function ()
							{
								this.pMainObj.CreateCustomElement("tag_image");
							}
						}
					];
				
				arButtons['image'][1].handler = function ()
					{
						this.bNotFocus = true;
						this.pMainObj.CreateCustomElement("BXDialogImageUpload");
					};
				arButtons['BlogInputVideo']	=	
					[
						'BXButton',
						{
							src : '/bitrix/components/bitrix/blog/templates/.default/images/bbcode/font_video.gif',
							id : 'BlogInputVideo',
							name : '<?=GetMessage("FPF_VIDEO")?>',
							title : '<?=GetMessage("FPF_VIDEO")?>',
							handler : function ()
							{
								ShowVideoInput();
								
							}
						}
					];

				arButtons['BlogCUT']	=	
					[
						'BXButton',
						{
							src : '/bitrix/components/bitrix/blog/templates/.default/images/bbcode/cut.gif',
							id : 'BlogCUT',
							name : '<?=GetMessage("FPF_CUT")?>',
							title : '<?=GetMessage("FPF_CUT")?>',
							handler : function ()
							{
								this.pMainObj.insertHTML('[CUT]');
								
							}
						}
					];
					

				for(var i=0, cnt = arGlobalToolbar.length; i<cnt; i++)
				{
					if(arGlobalToolbar[i][1])
					{
						if(arGlobalToolbar[i][1].id == "image")
							imageID = i;						
						else if(arGlobalToolbar[i][1].id == "InsertHorizontalRule")
							cutID = i;
					}
				}

				if(imageID > 0)
				{
					tmpArray = arGlobalToolbar.slice(0, imageID).concat([arButtons['ImageLink']]);
					arGlobalToolbar = tmpArray.concat(arGlobalToolbar.slice(imageID));		
					imageID++;
					imageID++;
					
					tmpArray = arGlobalToolbar.slice(0, imageID).concat([arButtons['BlogInputVideo']]);
					arGlobalToolbar = tmpArray.concat(arGlobalToolbar.slice(imageID));						
				}
				if(cutID > 0)
				{
					tmpArray = arGlobalToolbar.slice(0, cutID).concat([arButtons['BlogCUT']]);
					arGlobalToolbar = tmpArray.concat(arGlobalToolbar.slice(cutID));					
				}										
				
				//-->
				</script>

				<?
			}
			
			AddEventHandler("fileman", "OnIncludeHTMLEditorScript", "CustomizeEditorForBlog");
			?>
			<script>
			jsUtils.addCustomEvent('EditorLoadFinish_POST_MESSAGE_HTML', BXBlogSetEditorContent);
			</script>

			<?
			CFileman::ShowHTMLEditControl("POST_MESSAGE_HTML", $arResult["PostToShow"]["~DETAIL_TEXT"], Array(
					"site" => SITE_ID,
					"templateID" => "",
					"bUseOnlyDefinedStyles" => "N",
					"bWithoutPHP" => true,
					"arToolbars" => Array("manage", "standart", "style", "formating", "source", "table"),
					"arTaskbars" => Array("BXPropertiesTaskbar"),
					"sBackUrl" => "",
					"fullscreen" => false,
					"path" => "",
					"limit_php_access" => true,
					'height' => '490',
					'width' => '100%',
					'light_mode' => true,
				));
		}
		else
		{
			ShowError(GetMessage("FILEMAN_MODULE_NOT_INSTALL"));
		}
		die();
	}
	else
	{
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script.php");
		
		if($arResult["preview"] == "Y" && !empty($arResult["PostToShow"])>0)
		{
			echo "<p><b>".GetMessage("BLOG_PREVIEW_TITLE")."</b></p>";
			$className = "blog-post";
			$className .= " blog-post-first";
			$className .= " blog-post-alt";
			$className .= " blog-post-year-".$arResult["postPreview"]["DATE_PUBLISH_Y"];
			$className .= " blog-post-month-".IntVal($arResult["postPreview"]["DATE_PUBLISH_M"]);
			$className .= " blog-post-day-".IntVal($arResult["postPreview"]["DATE_PUBLISH_D"]);
			?>
			<div class="<?=$className?>">
				<h2 class="blog-post-title"><span><?=$arResult["postPreview"]["TITLE"]?></span></h2>
				<div class="blog-post-info-back blog-post-info-top">
					<div class="blog-post-info">
						<div class="blog-author"><div class="blog-author-icon"></div><?=$arResult["postPreview"]["AuthorName"]?></div>
						<div class="blog-post-date"><span class="blog-post-day"><?=$arResult["postPreview"]["DATE_PUBLISH_DATE"]?></span><span class="blog-post-time"><?=$arResult["postPreview"]["DATE_PUBLISH_TIME"]?></span><span class="blog-post-date-formated"><?=$arResult["postPreview"]["DATE_PUBLISH_FORMATED"]?></span></div>
					</div>
				</div>
				<div class="blog-post-content">
					<div class="blog-post-avatar"><?=$arResult["postPreview"]["BlogUser"]["AVATAR_img"]?></div>
					<?=$arResult["postPreview"]["textFormated"]?>
					<br clear="all" />
				</div>
				<div class="blog-post-meta">
					<div class="blog-post-info-bottom">
						<div class="blog-post-info">
							<div class="blog-author"><div class="blog-author-icon"></div><?=$arResult["postPreview"]["AuthorName"]?></div>
							<div class="blog-post-date"><span class="blog-post-day"><?=$arResult["postPreview"]["DATE_PUBLISH_DATE"]?></span><span class="blog-post-time"><?=$arResult["postPreview"]["DATE_PUBLISH_TIME"]?></span><span class="blog-post-date-formated"><?=$arResult["postPreview"]["DATE_PUBLISH_FORMATED"]?></span></div>
						</div>
					</div>
					<div class="blog-post-meta-util">
						<span class="blog-post-comments-link"><a href=""><span class="blog-post-link-caption"><?=GetMessage("BLOG_COMMENTS")?>:</span><span class="blog-post-link-counter">0</span></a></span>
						<span class="blog-post-views-link"><a href=""><span class="blog-post-link-caption"><?=GetMessage("BLOG_VIEWS")?>:</span><span class="blog-post-link-counter">0</span></a></span>
					</div>

					<?if(!empty($arResult["postPreview"]["Category"]))
					{
						?>
						<div class="blog-post-tag">
							<span><?=GetMessage("BLOG_BLOG_BLOG_CATEGORY")?></span>
							<?
							$i=0;
							foreach($arResult["postPreview"]["Category"] as $v)
							{
								if($i!=0)
									echo ",";
								?> <a href="<?=$v["urlToCategory"]?>"><?=$v["NAME"]?></a><?
								$i++;
							}
							?>
						</div>
						<?
					}
					?>
				</div>
			</div>
			<?
		}
		
		?>
		<form action="<?=POST_FORM_ACTION_URI?>" name="REPLIER" method="post" enctype="multipart/form-data" onmouseover="check_ctrl_enter">
		<?=bitrix_sessid_post();?>
		<div class="blog-edit-form blog-edit-post-form blog-post-edit-form">
		<div class="blog-post-fields blog-edit-fields">
			<div class="blog-post-field blog-post-field-title blog-edit-field blog-edit-field-title">
				<input maxlength="255" size="70" tabindex="1" type="text" name="POST_TITLE" id="POST_TITLE" value="<?=$arResult["PostToShow"]["TITLE"]?>">
			</div>
			<div class="blog-clear-float"></div>

			<div class="blog-post-field blog-post-field-date blog-edit-field blog-edit-field-post-date">
				<span><input type="hidden" id="DATE_PUBLISH_DEF" name="DATE_PUBLISH_DEF" value="<?=$arResult["PostToShow"]["DATE_PUBLISH"];?>">
				<script>
					function changeDate()
					{
						document.getElementById('date-publ').style.display = 'block';
						document.getElementById('date-publ-text').style.display = 'none';
						document.getElementById('DATE_PUBLISH_DEF').value = '';
					}
				</script>
				<div id="date-publ-text">
					<a href="javascript:changeDate()" title="<?=GetMessage("BLOG_DATE")?>"><?=$arResult["PostToShow"]["DATE_PUBLISH"];?></a>
				</div>
				<div id="date-publ" style="display:none;">					
				<?
					$APPLICATION->IncludeComponent(
						'bitrix:main.calendar',
						'',
						array(
							'SHOW_INPUT' => 'Y',
							'FORM_NAME' => 'REPLIER',
							'INPUT_NAME' => 'DATE_PUBLISH',
							'INPUT_VALUE' => $arResult["PostToShow"]["DATE_PUBLISH"],
							'SHOW_TIME' => 'Y'
						),
						null,
						array('HIDE_ICONS' => 'Y')
					);
				?>
				</div></span>
			</div>
			<div class="blog-clear-float"></div>
		</div>
		

		
		<div class="blog-post-message blog-edit-editor-area blog-edit-field-text">
			<div class="blog-comment-field blog-comment-field-bbcode">
				<?if($arResult["allow_html"] == "Y")
				{
					?>
					<input type="radio" id="blg-text-text" name="POST_MESSAGE_TYPE" value="text"<?if($arResult["PostToShow"]["DETAIL_TEXT_TYPE"] != "html") echo " checked";?> onclick="showEditField('text', 'Y')"> <label for="blg-text-text">Text</label> / <input type="radio" id="blg-text-html" name="POST_MESSAGE_TYPE" value="html"<?if($arResult["PostToShow"]["DETAIL_TEXT_TYPE"] == "html") echo " checked";?> onclick="showEditField('html', 'Y')"> <label for="blg-text-html">HTML</label>
					<input type="hidden" name="editor_loaded" id="editor_loaded" value="N">
					<div id="edit-post-html" style="display:none;"></div>
				<?
				}
				?>
				<div id="edit-post-text" style="display:none;">
					<div class="blog-post-bbcode-line">
					<div class="blog-bbcode-line">
						<a id=bold class="blog-bbcode-bold" href='javascript:simpletag("B")' title="<?=GetMessage("FPF_BOLD")?>"></a>
						<a id=italic class="blog-bbcode-italic" href='javascript:simpletag("I")' title="<?=GetMessage("FPF_ITALIC")?>"></a>
						<a id=under class="blog-bbcode-underline" href='javascript:simpletag("U")' title="<?=GetMessage("FPF_UNDER")?>"></a>
						<a id=strike class="blog-bbcode-strike" href='javascript:simpletag("S")' title="<?=GetMessage("FPF_STRIKE")?>"></a>
						<a id=url class="blog-bbcode-url" href='javascript:tag_url()' title="<?=GetMessage("FPF_HYPERLINK")?>"></a>
						<a id=image class="blog-bbcode-img" href='javascript:tag_image()' title="<?=GetMessage("BLOG_P_IMAGE_LINK")?>"></a>
						<a class="blog-bbcode-img-upload" href="javascript:ShowImageUpload()" title="<?=GetMessage("BLOG_P_DO_UPLOAD")?>" id="image-upload"></a>
						<?if($arResult["allowVideo"] == "Y"):?>
						<a class="blog-bbcode-video" href="javascript:ShowVideoInput()" title="<?=GetMessage("FPF_VIDEO")?>" id="videoImg"></a>
						<?endif;?>
						
						<a id=quote class="blog-bbcode-quote" href='javascript:quoteMessage()' title="<?=GetMessage("FPF_QUOTE")?>"></a>
						<a id=code class="blog-bbcode-code" href='javascript:simpletag("CODE")' title="<?=GetMessage("FPF_CODE")?>"></a>
						<a id=code class="blog-bbcode-cut" href="javascript:void(0)" onclick="doInsert('[CUT]', '', false)" title="<?=GetMessage("FPF_CUT")?>"></a>
						<a id=list class="blog-bbcode-list" href='javascript:tag_list()' title="<?=GetMessage("FPF_LIST")?>"></a>
						<a id=FontColor	class="blog-bbcode-color" href='javascript:ColorPicker()' title="<?=GetMessage("FPF_IMAGE")?>"></a>

						<select class="blog-bbcode-font" name="ffont" id="select_font" onchange="alterfont(this.options[this.selectedIndex].value, 'FONT')">
							<option value='0'><?=GetMessage("FPF_FONT")?></option>
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
							<img src="/bitrix/images/blog/smile/<?=$arSmiles["IMAGE"]?>" width="<?=$arSmiles["IMAGE_WIDTH"]?>" height="<?=$arSmiles["IMAGE_HEIGHT"]?>"  title="<?=GetMessage("BPC_SMILE")?>" OnClick="emoticon('<?=$arSmiles["TYPE"]?>')" style="cursor:pointer">
								
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
					<div class="blog-bbcode-closeall"><a id=close_all style=visibility:hidden href='javascript:closeall()' title='<?=GetMessage("FPF_CLOSE_OPENED_TAGS")?>'><?=GetMessage("FPF_CLOSE_ALL_TAGS")?></a></div>
					<div class="blog-clear-float"></div>
					</div>
					<div class="blog-comment-field blog-comment-field-text">
						<textarea cols="55" rows="15" tabindex="2" onKeyPress="check_ctrl_enter(arguments[0])" name="POST_MESSAGE" id="MESSAGE" onKeyPress="check_ctrl_enter(arguments[0])"><?=$arResult["PostToShow"]["DETAIL_TEXT"]?></textarea>
					</div>
				</div>
			</div>
		
		
		
			<?
			if($arResult["allow_html"] == "Y")
			{
				?>
				<script type="text/javascript" src="/bitrix/js/main/ajax.js"></script>
				<script type="text/javascript" src="/bitrix/js/main/admin_tools.js"></script>
				<script type="text/javascript" src="/bitrix/js/main/utils.js"></script>
				<?
				$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/pubstyles.css");
				$APPLICATION->SetAdditionalCSS("/bitrix/admin/htmleditor2/editor.css");
				$APPLICATION->SetTemplateCSS('ajax/ajax.css');
			}

			?>
			<?if($arResult["PostToShow"]["DETAIL_TEXT_TYPE"] == "html" && $arResult["allow_html"] == "Y")
			{
				
				?>
				<script>
				<!--
				setTimeout("showEditField('html', 'N')", 100);
				//-->
				</script>
				<?
			}
			else
			{
				?>
				<script>
				<!--
				showEditField('text', 'N');
				//-->
				</script>
				<?
			}

			?>
			<div class="blog-post-field blog-post-field-images blog-edit-field" id="blog-post-image">
			<?
			if (!empty($arResult["Images"]))
			{
				?>
				<div><?=GetMessage("BLOG_P_IMAGES")?></div>
				<?
				foreach($arResult["Images"] as $aImg)
				{
					?>
						<div class="blog-post-image-item">
							<div class="blog-post-image-item-border"><?=$aImg["FileShow"]?></div>
							
								<div class="blog-post-image-item-input">
									<input name="IMAGE_ID_title[<?=$aImg["ID"]?>]" value="<?=$aImg["TITLE"]?>" title="<?=GetMessage("BLOG_BLOG_IN_IMAGES_TITLE")?>">
								</div>
								<div>
									<input type="checkbox" name="IMAGE_ID_del[<?=$aImg["ID"]?>]" id="img_del_<?=$aImg["ID"]?>"> <label for="img_del_<?=$aImg["ID"]?>"><?=GetMessage("BLOG_DELETE")?></label>
								</div>
							
						</div>
					<?
				}
			}
			?>
			</div>
		</div>
		<div class="blog-clear-float"></div>
		<div class="blog-post-field blog-post-field-category blog-edit-field blog-edit-field-tags">
			<div class="blog-post-field-text">
			<label for="TAGS" class="blog-edit-field-caption"><?=GetMessage("BLOG_CATEGORY")?></label>
			</div>
			<span><?
					if(IsModuleInstalled("search"))
					{
						$arSParams = Array(
							"NAME"	=>	"TAGS",
							"VALUE"	=>	$arResult["PostToShow"]["CategoryText"],
							"arrFILTER"	=>	"blog",
							"PAGE_ELEMENTS"	=>	"10",
							"SORT_BY_CNT"	=>	"Y",
							"TEXT" => 'size="30" tabindex="3"'
							);
						if($arResult["bSoNet"] && $arResult["bGroupMode"])
						{
							$arSParams["arrFILTER"] = "socialnetwork";
							$arSParams["arrFILTER_socialnetwork"] = $arParams["SOCNET_GROUP_ID"];
						}
						$APPLICATION->IncludeComponent("bitrix:search.tags.input", ".default", $arSParams);
					}
					else
					{
						?><input type="text" id="TAGS" tabindex="3" name="TAGS" size="30" value="<?=$arResult["PostToShow"]["CategoryText"]?>">
						<?
					}?>
			</span>
		</div>
		<div class="blog-clear-float"></div>
		<div class="blog-post-field blog-post-field-enable-comments blog-edit-field">
			<span><input name="ENABLE_COMMENTS" id="ENABLE_COMMENTS" type="checkbox" value="N"<?if($arResult["PostToShow"]["ENABLE_COMMENTS"] == "N") echo " checked"?>></span>
			<div class="blog-post-field-text"><label for="ENABLE_COMMENTS"><?=GetMessage("BLOG_ENABLE_COMMENTS")?></label></div>
		</div>
		<div class="blog-clear-float"></div>
		<?if(!$arResult["bSoNet"])
		{
			?>
			<div class="blog-post-field blog-post-field-favorite blog-edit-field">
				<span><input name="FAVORITE_SORT" id="FAVORITE_SORT" type="checkbox" value="100"<?if(IntVal($arResult["PostToShow"]["FAVORITE_SORT"]) > 0) echo " checked"?>></span>
				<div class="blog-post-field-text"><label for="FAVORITE_SORT"><?=GetMessage("BLOG_FAVORITE_SORT")?></label></div>
			</div>
			<div class="blog-clear-float"></div>
			<?
		}
		?>
		<div class="blog-post-params">
			<?if(!$arResult["bSoNet"])
			{
			
				function ShowSelectPerms($type, $id, $def, $arr)
				{

					$res = "<select name='perms_".$type."[".$id."]'>";
					while(list(,$key)=each($arr))
						if ($id > 1 || ($type=='p' && $key <= BLOG_PERMS_READ) || ($type=='c' && $key <= BLOG_PERMS_WRITE))
							$res.= "<option value='$key'".($key==$def?' selected':'').">".$GLOBALS["AR_BLOG_PERMS"][$key]."</option>";
					$res.= "</select>";
					return $res;
				}

				?>
				<div class="blog-post-field blog-post-field-access blog-edit-field">
					<div class="blog-post-field-access-title"><?=GetMessage("BLOG_ACCESS")?></div>
					<input name="blog_perms" value="0" onClick="show_special()" id="blog_perms_0" type="radio"<?=$arResult["PostToShow"]["ExtendedPerms"]=="Y" ? "" : " checked"?>> <label for="blog_perms_0"><?=GetMessage("BLOG_DEFAULT_PERMS")?></label>
					<br />
					<input name="blog_perms" value="1" onClick="show_special()" id="blog_perms_1" type="radio"<?=$arResult["PostToShow"]["ExtendedPerms"]=="Y" ? " checked" : ""?>> <label for="blog_perms_1"><?=GetMessage("BLOG_SPECIAL_PERMS")?></label>

					<div id="special_perms"<?=($arResult["PostToShow"]["ExtendedPerms"]=="Y" ? "" : "style=\"display:none;\"")?>>
					<table class="blog-post-perm-table">
						<tr>
							<th><?=GetMessage("BLOG_GROUPS")?></th>
							<th><?=GetMessage("BLOG_POST_MESSAGE")?></th>
							<th><?=GetMessage("BLOG_COMMENTS")?></th>

						</tr>
						<tr>
							<td><?=GetMessage("BLOG_ALL_USERS")?></td>
							<td><?
								if(!empty($arResult["ar_post_everyone_rights"]))
									echo ShowSelectPerms('p', 1, $arResult["PostToShow"]["arUGperms_p"][1], $arResult["ar_post_everyone_rights"]);
								else
									echo ShowSelectPerms('p', 1, $arResult["PostToShow"]["arUGperms_p"][1], $arResult["BLOG_POST_PERMS"]);
							?></td>
							<td><?
								if(!empty($arResult["ar_comment_everyone_rights"]))
									echo ShowSelectPerms('c', 1, $arResult["PostToShow"]["arUGperms_c"][1], $arResult["ar_comment_everyone_rights"]);
								else
									echo ShowSelectPerms('c', 1, $arResult["PostToShow"]["arUGperms_c"][1], $arResult["BLOG_COMMENT_PERMS"]);
							?></td>
						</tr>
						<tr>
							<td><?=GetMessage("BLOG_REG_USERS")?></td>
							<td><?
								if(!empty($arResult["ar_post_auth_user_rights"]))
									echo ShowSelectPerms('p', 2, $arResult["PostToShow"]["arUGperms_p"][2], $arResult["ar_post_auth_user_rights"]);
								else
									echo ShowSelectPerms('p', 2, $arResult["PostToShow"]["arUGperms_p"][2], $arResult["BLOG_POST_PERMS"]);
							?></td>
							<td><?
								if(!empty($arResult["ar_comment_auth_user_rights"]))
									echo ShowSelectPerms('c', 2, $arResult["PostToShow"]["arUGperms_c"][2], $arResult["ar_comment_auth_user_rights"]);
								else
									echo ShowSelectPerms('c', 2, $arResult["PostToShow"]["arUGperms_c"][2], $arResult["BLOG_COMMENT_PERMS"]);
							?></td>

						</tr>
						
						
						<?
						foreach($arResult["UserGroups"] as $aUGroup)
						{
							?>
							<tr>
								<td><?=$aUGroup["NAME"]?></td>
								<td><?
									if(!empty($arResult["ar_post_group_user_rights"]))
										echo ShowSelectPerms('p', $aUGroup["ID"], $arResult["PostToShow"]["arUGperms_p"][$aUGroup["ID"]], $arResult["ar_post_group_user_rights"]);
									else
										echo ShowSelectPerms('p', $aUGroup["ID"], $arResult["PostToShow"]["arUGperms_p"][$aUGroup["ID"]], $arResult["BLOG_POST_PERMS"]);
								?></td>
								<td><?
									if(!empty($arResult["ar_comment_group_user_rights"]))
										echo ShowSelectPerms('c', $aUGroup["ID"], $arResult["PostToShow"]["arUGperms_c"][$aUGroup["ID"]], $arResult["ar_comment_group_user_rights"]);
									else
										echo ShowSelectPerms('c', $aUGroup["ID"], $arResult["PostToShow"]["arUGperms_c"][$aUGroup["ID"]], $arResult["BLOG_COMMENT_PERMS"]);
								?></td>

							</tr>
							<?
						}
						?>
					</table>
					</div>
				</div>
			<?
			}
			if(!empty($arResult["avBlog"]) && IntVal($arParams["ID"]) > 0)
			{
				?>
				<br />
				<div class="blog-post-params">
					<div class="blog-post-field blog-post-field-access blog-edit-field">
						<div class="blog-post-field-access-title"><?=GetMessage("BPET_MOVE")?></div>
						<select name="move2blog">
							<option value=""><?=GetMessage("BPET_MOVE_NO")?></option>
							<?
							foreach($arResult["avBlogCategory"] as $cat => $blogs)
							{
								if($cat == "socnet_groups")
								{
									?><optgroup label="<?=GetMessage("BPET_MOVE_SOCNET_GROUPS")?>"><?
								}
								elseif($cat == "socnet_users")
								{
									?><optgroup label="<?=GetMessage("BPET_MOVE_SOCNET_USERS")?>"><?
								}
								$bF = true;
								foreach($blogs as $blog)
								{
									if($cat != "socnet_users" && $cat != "socnet_groups" && $bF)
									{
										?><optgroup label="<?=$blog["GROUP_NAME"]?>"><?
										$bF = false;
									}
									?><option value="<?=$blog["ID"]?>"<?if($blog["ID"] == $arResult["PostToShow"]["move2blog"]) echo " selected"?>><?=$blog["NAME"]?></option><?
								}
								?></optgroup><?
							}
							?>
						</select>
						<br />
						<input type="checkbox" id="move2blogcopy" name="move2blogcopy" value="Y"<?if($arResult["PostToShow"]["move2blogcopy"] == "Y") echo " checked=\"checked\""?>><label for="move2blogcopy"><?=GetMessage("BPET_MOVE_COPY")?></label>
					</div>
				</div>
				<?
			}
			?>
			
			<div class="blog-clear-float"></div>
			<?if($arResult["POST_PROPERTIES"]["SHOW"] == "Y"):?>
				<br />
				<div class="blog-post-field blog-post-field-user-prop blog-edit-field">
					<?foreach ($arResult["POST_PROPERTIES"]["DATA"] as $FIELD_NAME => $arPostField):?>
					<div><?=$arPostField["EDIT_FORM_LABEL"]?>:
								<?$APPLICATION->IncludeComponent(
									"bitrix:system.field.edit", 
									$arPostField["USER_TYPE"]["USER_TYPE_ID"], 
									array("arUserField" => $arPostField), null, array("HIDE_ICONS"=>"Y"));?>
					</div>
					<?endforeach;?>
				</div>
				<div class="blog-clear-float"></div>
			<?endif;?>
		</div>
		<div class="blog-post-buttons blog-edit-buttons">
			<input type="hidden" name="save" value="Y">
			<input tabindex="4" type="submit" name="save" value="<?=GetMessage("BLOG_PUBLISH")?>">
			<input type="submit" name="apply" value="<?=GetMessage("BLOG_APPLY")?>">
			<?if($arResult["perms"] >= BLOG_PERMS_WRITE):?>
				<input type="submit" name="draft" value="<?=GetMessage("BLOG_TO_DRAFT")?>">
			<?endif;?>
			<input type="submit" name="preview" value="<?=GetMessage("BLOG_PREVIEW")?>">
		</div>
		</div>
		</form>
		
		<script>
		<!--
		document.REPLIER.POST_TITLE.focus();
		//-->
		</script>
		<?
	}
}
?>
</div>