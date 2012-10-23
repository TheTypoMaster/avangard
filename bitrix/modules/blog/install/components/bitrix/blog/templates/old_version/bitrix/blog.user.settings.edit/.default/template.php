<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["FATAL_ERROR"])>0)
{
	?>
	<span class='errortext'><?=$arResult["FATAL_ERROR"]?></span><br /><br />
	<?
}
else
{
	if(strlen($arResult["ERROR_MESSAGE"])>0)
	{
		?>
		<span class='errortext'><?=$arResult["ERROR_MESSAGE"]?></span><br /><br />
		<?
	}

	?>
	<form action="<?=POST_FORM_ACTION_URI?>" method="post">
	<table class="blog-user-settings-edit-table">
		<tr>
			<th colspan="2">
				<?= str_replace("#NAME#", $arResult["Blog"]["NAME"], GetMessage("B_B_USE_TITLE_BLOG")) ?>
			</th>
		</tr>
		<tr>
			<td width="50%" class="head">
					<?=GetMessage("B_B_USE_USER")?>
			</td>
			<td width="50%" valign="top">
					<a href="<?=$arResult["urlToUser"]?>"><?=$arResult["userName"]?></a>
			</td>
		</tr>
		<tr>
			<td class="head">
					<?=GetMessage("B_B_USE_U_GROUPS")?>
			</td>
			<td>
					<?
					if(!empty($arResult["Groups"]))
					{
					foreach($arResult["Groups"] as $arBlogGroups)
					{
						?>
						<input type="checkbox" id="add2groups_<?= $arBlogGroups["ID"] ?>" name="add2groups[]" value="<?= $arBlogGroups["ID"] ?>"<?if (in_array($arBlogGroups["ID"], $arResult["arUserGroups"])) echo " checked";?>>
						<label for="add2groups_<?= $arBlogGroups["ID"] ?>"><?=$arBlogGroups["NAME"]?></label><br />
						<?
					}
					}
					?>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" value="<?=GetMessage("B_B_USE_SAVE")?>">
				<input type="submit" name="cancel" value="<?=GetMessage("B_B_USE_CANCEL")?>">
				<input type="hidden" name="user_action" value="Y">
				<?=bitrix_sessid_post()?>
			</td>
		</tr>
	</table>
	</form>
	<?
}
?>