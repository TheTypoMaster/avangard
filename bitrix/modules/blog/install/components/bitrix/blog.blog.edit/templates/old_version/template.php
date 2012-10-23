<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if($arResult["NEED_AUTH"] == "Y")
{
	$APPLICATION->AuthForm("");
}
elseif(!empty($arResult["FATAL_ERROR"])>0)
{
	foreach($arResult["FATAL_ERROR"] as $v)
	{
		?>
		<span class='errortext'><?=$v?></span><br /><br />
		<?
	}
}
else
{
	if(!empty($arResult["ERROR_MESSAGE"])>0)
	{
		foreach($arResult["ERROR_MESSAGE"] as $v)
		{
			?>
			<span class='errortext'><?=$v?></span><br /><br />
			<?
		}
	}
	?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>" ENCTYPE="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="BLOG_URL" value="<?=$arResult["BLOG"]["URL"]?>">
	<table class="blog-blog-edit">
		<tr>
			<th><span class="blog-req">*</span> <b><?=GetMessage('BLOG_TITLE')?></b></th>
			<td><input type="text" name="NAME" maxlength="100" size="40" value="<?= $arResult["BLOG"]["NAME"]?>" style="width:98%"></td>
			<td class="blog-small"><?=GetMessage('BLOG_TITLE_DESCR')?></td>
		</tr>
		<tr>
			<th><b><?=GetMessage('BLOG_DESCR')?></b></th>
			<td>
				<textarea name="DESCRIPTION" rows="5" cols="40" style="width:98%"><?=$arResult["BLOG"]["DESCRIPTION"]?></textarea>
			</td>
			<td class="blog-small"><?=GetMessage('BLOG_DESCR_TITLE')?></td>
		</tr>
		<tr>
			<th><span class="blog-req">*</span> <b><?=GetMessage('BLOG_URL')?></b></th>
			<td>
				<?
				if ($arResult["BlockURL"] == "Y")
					echo $arResult["BLOG"]["URL"];
				else
				{
					?><input type="text" name="URL" maxlength="100" size="40" value="<?=$arResult["BLOG"]["URL"]?>" style="width:98%"><?
				}
				?>
			</td>
			<td class="blog-small"><?=GetMessage("BLOG_URL_TITLE")?></td>
		</tr>
		<tr>
			<th><span class="blog-req">*</span> <b><?=GetMessage('BLOG_GRP')?></b></th>
			<td><select name="GROUP_ID">
					<?
					foreach($arResult["GROUP"] as $v)
					{
						?><option value="<?=$v["ID"]?>"<?if ($v["SELECTED"]=="Y") echo " selected";?>><?=$v["NAME"]?></option><?
					}
					?>
				</select>
			</td>
			<td class="blog-small"><?=GetMessage('BLOG_GRP_TITLE')?></td>
		</tr>
		<?
		if($arResult["useCaptcha"] == "U")
		{
			?>
			<tr>
				<th><b><?=GetMessage('BLOG_AUTO_MSG')?></b></th>
				<td>
					<input id="IMG_VERIF" type="checkbox" name="ENABLE_IMG_VERIF" value="Y"<?if ($arResult["BLOG"]["ENABLE_IMG_VERIF"] != "N") echo " checked";?>>
					<label for="IMG_VERIF"><?=GetMessage('BLOG_AUTO_MSG_TITLE')?></label>
				</td>
				<td class="blog-small"><?=GetMessage('BLOG_CAPTHA')?></td>
			</tr>
			<?
		}
		?>
		<tr>
			<th><b><?=GetMessage('BLOG_EMAIL_NOTIFY')?></b></th>
			<td>
				<input id="EMAIL_NOTIFY" type="checkbox" name="EMAIL_NOTIFY" value="Y"<?if ($arResult["BLOG"]["EMAIL_NOTIFY"] != "N") echo " checked";?>>
				<label for="EMAIL_NOTIFY"><?=GetMessage('BLOG_EMAIL_NOTIFY_TITLE')?></label>
			</td>
			<td class="blog-small"><?=GetMessage('BLOG_EMAIL_NOTIFY_HELP')?></td>
		</tr>
		<?
		if(!empty($arResult["USER_GROUP"]))
		{
		?>
			<tr>
				<th><b><?=GetMessage('BLOG_OPENED_GRPS')?></b></th>
				<td>
					<?
					foreach($arResult["USER_GROUP"] as $v)
					{
						?>
						<input id="group_<?=$v["ID"]?>" type="checkbox" name="group[<?=$v['ID']?>]"<?if($v["CHECKED"] == "Y") echo " checked";?>>
						<label for="group_<?=$v["ID"]?>"><?=$v["NAME"]?></label>
						<br />
						<?
					}
					?>
				</td>
				<td class="blog-small"><?=GetMessage('BLOG_OPENED_TITLE')?></td>
			</tr>
		<?	
		}

		function ShowSelectPerms($type,$id,$def,$arr)
		{
			if(empty($def))
			{
				if($type == "p")
					$def = BLOG_PERMS_READ;
				elseif($type == "c")
					$def = BLOG_PERMS_WRITE;
			}
			
			$res = "<select name='perms_{$type}[{$id}]'>";
			while(list(,$key)=each($arr))
				if ($id > 1 || ($type=='p' && $key <= BLOG_PERMS_READ) || ($type=='c' && $key <= BLOG_PERMS_WRITE))
					$res.= "<option value='$key'".(($key==$def)?' selected':'').">".$GLOBALS["AR_BLOG_PERMS"][$key]."</option>";
			$res.= "</select>";
			return $res;
		}
		?>
		<tr>
			<th><b><?=GetMessage('BLOG_DEF_PERMS')?></b></th>
			<td>
				<table class="blog-blog-edit-table">
					<tr>
						<td nowrap><b><?=GetMessage('BLOG_GROUPS')?></b></td>
						<td><b><?=GetMessage('BLOG_MESSAGES')?></b></td>
						<td><b><?=GetMessage('BLOG_COMMENTS')?></b></td>
					</tr>
					<tr>
						<td nowrap><?=GetMessage('BLOG_ALL_USERS')?></td>
						<td><?=ShowSelectPerms('p',1,$arResult["BLOG"]["perms_p"][1],$arResult["BLOG_POST_PERMS"])?></td>
						<td><?=ShowSelectPerms('c',1,$arResult["BLOG"]["perms_c"][1],$arResult["BLOG_COMMENT_PERMS"])?></td>
					</tr>
					<tr>
						<td nowrap><?=GetMessage('BLOG_REGISTERED')?></td>
						<td><?=ShowSelectPerms('p',2,$arResult["BLOG"]["perms_p"][2],$arResult["BLOG_POST_PERMS"])?></td>
						<td><?=ShowSelectPerms('c',2,$arResult["BLOG"]["perms_c"][2],$arResult["BLOG_COMMENT_PERMS"])?></td>
					</tr>
					
					<?
				if(!empty($arResult["USER_GROUP"]))
				{
					foreach($arResult["USER_GROUP"] as $aUGroup)
					{
						?>
						<tr>
							<td nowrap><?=$aUGroup['NAME']?></td>
							<td><?=ShowSelectPerms('p',$aUGroup['ID'],$arResult["BLOG"]["perms_p"][$aUGroup['ID']],$arResult["BLOG_POST_PERMS"])?></td>
							<td><?=ShowSelectPerms('c',$aUGroup['ID'],$arResult["BLOG"]["perms_c"][$aUGroup['ID']],$arResult["BLOG_COMMENT_PERMS"])?></td>
						</tr>
						<?
					}
				}
					?>
				</table>
			</td>
			<td class="blog-small"><?=GetMessage('BLOG_PERMS_TITLE')?></td>
		</tr>
		<?if($arResult["BLOG_PROPERTIES"]["SHOW"] == "Y"):?>
			<?foreach ($arResult["BLOG_PROPERTIES"]["DATA"] as $FIELD_NAME => $arBlogField):?>
			<tr>
				<th><b><?=$arBlogField["EDIT_FORM_LABEL"]?>:</b></th>
				<td>
						<?$APPLICATION->IncludeComponent(
							"bitrix:system.field.edit", 
							$arBlogField["USER_TYPE"]["USER_TYPE_ID"], 
							array("arUserField" => $arBlogField), null, array("HIDE_ICONS"=>"Y"));?>
				</td>
				<td>&nbsp;</td>
			</tr>			
			<?endforeach;?>
		<?endif;?>
		</table>
		<br />

		<input type="submit" name="save" value="<?= (IntVal($arResult["BLOG"]["ID"])>0 ? GetMessage('BLOG_SAVE') : GetMessage('BLOG_CREATE')) ?>">
		<?
		if ($arResult["CAN_UPDATE"]=="Y")
		{
			?>
			<input type="submit" name="apply" value="<?=GetMessage('BLOG_APPLY')?>">
			<input type="submit" name="reset" value="<?=GetMessage('BLOG_CANCEL')?>">
			<?
		}
		?>
		<input type="hidden" name="do_blog" value="Y">
	</form>
	
	<span class="blogtext">
	<br /><br /><?echo GetMessage("STOF_REQUIED_FIELDS_NOTE")?><br /><br />
	</span>
	<?
}
?>
