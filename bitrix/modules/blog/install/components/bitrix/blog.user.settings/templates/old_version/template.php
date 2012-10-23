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
	if(strlen($arResult["OK_MESSAGE"])>0)
	{
		?>
		<span class='notetext'><?=$arResult["OK_MESSAGE"]?></span><br /><br />
		<?
	}
	
	if(!empty($arResult["Candidate"]))
	{
	?>
	
		<h2><?=GetMessage("B_B_US_LIST_WANTED")?></h2>
		<table class="blog-user-settings-table">
		<tr>
			<th><?=GetMessage("B_B_US_VISIT")?></th>
			<th><?=GetMessage("B_B_US_ACTIONS")?></th>
		</tr>
		<?
		foreach($arResult["Candidate"] as $arUser)
		{
			?>
			<tr>
				<td valign="top">
						<a href="<?=$arUser["urlToUser"]?>"><?=$arUser["NameFormated"]?></a>
				</td>
				<td valign="top">
						<a href="<?=$arUser["urlToEdit"]?>"><?=GetMessage("B_B_US_EDIT")?></a><br />
						<a href="<?=$arUser["urlToDelete"]?>"><?=GetMessage("B_B_US_DELETE")?></a><br />
				</td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
	if(!empty($arResult["Users"]))
	{
	?>
	
		<h2><?=GetMessage("B_B_US_EDIT_FR_LIST")?></h2>
		<table class="blog-user-settings-table">
		<tr>
			<th><?=GetMessage("B_B_US_FR_VISITOR")?></th>
			<th><?=GetMessage("B_B_US_FR_GROUPS")?></th>
			<th><?=GetMessage("B_B_US_FR_ACTIONS")?></th>
		</tr>
		<?
		foreach($arResult["Users"] as $arUser)
		{
			?>
			<tr>
				<td valign="top"><a href="<?=$arUser["urlToUser"]?>"><?=$arUser["NameFormated"]?></a></td>
				<td valign="top"><?=$arUser["groupsFormated"]?></td>
				<td valign="top">
						<a href="<?=$arUser["urlToEdit"]?>"><?=GetMessage("B_B_US_EDIT")?></a><br />
						<a href="<?=$arUser["urlToDelete"]?>"><?=GetMessage("B_B_US_DELETE")?></a><br />
				</td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
	?>
	
	<script language="JavaScript">
	var user_count = 1;

	function addField()
	{
		var bl_name = "add_friend_";
		var new_field = false;
		user_count++;

		var current = document.getElementById(bl_name+user_count);
		if (!current)
			return false;

		var parent = current.parentNode;
		if (!parent)
			return false;

		var add_block = document.createElement("div");
		add_block.id = bl_name+user_count;    
		add_block.innerHTML = 
			"<b>" + user_count + ".</b>&nbsp;&nbsp;" +
			"<input type=\"text\" name=\"add_friend[]\" value=\"\" size='40'>" +
			"<br />";
		parent.replaceChild(add_block, current);

		var num = user_count + 1;
		add_block = document.createElement("div");
		add_block.id = bl_name + num;
		add_block.innerHTML = 
			"<b>" + num + ".</b>&nbsp;&nbsp;<a onclick=\"return addField();\" href=\"\"><?=GetMessage("B_B_US_1_M_F")?></a><br />";
		parent.appendChild(add_block);

		return false;
	}
	</script>

	<h2><?=GetMessage("B_B_US_AD_NEW_FR")?></h2>
	<span class="blogtext">
		<?=GetMessage("B_B_US_AD_NEW_FR_BY")?>
	</span>
	<form name="add_friends" action="<?=POST_FORM_ACTION_URI?>" method="post">
	<?=bitrix_sessid_post()?>
		<div class="blogtext">
			<div id="add_friend_1">
				<b>1.</b>&nbsp;&nbsp;<input type="text" name="add_friend[]" size="40" value="">
				<br />
			</div>
			<div id="add_friend_2">
				<b>2.</b>&nbsp;
					<a onclick="return addField();" href=""><?=GetMessage("B_B_US_1_M_F")?></a>
				<br />
			</div>
		</div>
		<br />
		<input type="submit" value="<?=GetMessage("B_B_US_ADD")?>">
	</form>
	<?
}
?>