<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/***********************************************************************
Component for empty form representation for filling

This universal component is for a blog post edit. This is a standard component, it is included to module distributive

example of using:

$APPLICATION->IncludeFile("blog/blog/post_edit.php", array(
));

Parameters:


***********************************************************************/

global $USER, $APPLICATION, $strError, $DB;
IncludeTemplateLangFile(__FILE__);

if (CModule::IncludeModule("blog"))
{
	$USER_ID = intval($USER->GetID());
	$BLOG_ID = intval($BLOG_ID);
	$ID = intval($ID);
	$is404 = ($is404=='N') ? false: true;
	if(strlen($_POST['blog'])>0 && strlen($OWNER)<=0)
		$OWNER = $_POST['blog'];
	
	if ($BLOG_ID)
		$arBlog = CBlog::GetByID($BLOG_ID);
	else
	{
		$res = CBlog::GetList(array(),array("URL" => $OWNER));
		$arBlog = $res->Fetch();
		$BLOG_ID = intval($arBlog['ID']);
	}

	if ($arBlog)
	{
		$APPLICATION->SetTitle($arBlog["NAME"]);
		if (CBlog::CanUserManageBlog($BLOG_ID, ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )))
		{
			if ($_POST['save'] && check_bitrix_sessid()) // Сохраняем если нажали соотв. кнопку
			{
				$arFields=array(
					'NAME' => $_POST['NAME'],
				);

				if (IntVal($_POST['ID']) > 0) // Проверяем: новая запись или изменение старой
				{
					$res = CBlogUserGroup::GetList(array(),array("BLOG_ID" => $BLOG_ID, "ID" => IntVal($_POST['ID']))); // Смотрим чтобы ID записи соответствовал BLOG_ID
					if ($res->Fetch())
					{
						if ($_POST['group_del']=="Y")
							CBlogUserGroup::Delete(IntVal($_POST['ID']));
						else
							$newID = CBlogUserGroup::Update(IntVal($_POST['ID']), $arFields);
					}
					else
						die(GetMessage("BLOG_ERR_NO_RIGHTS"));
				}
				else
				{
					$arFields['BLOG_ID'] = $BLOG_ID;
					$newID = CBlogUserGroup::Add($arFields);
				}
			
/*				if (!$newID) // Запись не сохранена
				{
					if ($ex = $APPLICATION->GetException())
						$strError = $ex->GetString()."<br>";
					else
						$strError = "Error saving data to database.<br>";
				}
*/
				LocalRedirect($_POST['BACK_URL']);
				die();
			}

			##############################################################
			# Начало вывода формы
			##############################################################
#			if (strlen($strError) > 0)
#				echo ShowError($strError);
			?>
			
			<form action="<?=($is404? "../group_edit.php" : "");?>" name=REPLIER method=POST enctype="multipart/form-data">
			<?=bitrix_sessid_post();?>
			<?$add_back = "";
			if(!$is404)
				$add_back = "?blog=".htmlspecialchars($arBlog["URL"]);
			?>
			<input type=hidden name=ID id=group_id>
			<input type=hidden name=group_del id=group_del>
			<input type=hidden name=BLOG_ID value="<?=$BLOG_ID?>">
			<input type=hidden name=blog value="<?=htmlspecialchars($arBlog["URL"])?>">
			<input type=hidden name=BACK_URL value="<?=$APPLICATION->GetCurPage().$add_back;?>">
			
			<table border=0 cellspacing=1 cellpadding=3  class="blogtableborder" width=300>
<?
$res=CBlogUserGroup::GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array("BLOG_ID" => $BLOG_ID), array("ID", "NAME", "BLOG_ID", "COUNT" => "USER2GROUP_ID"));
while ($arUGroup=$res->Fetch())
{
	$arCount[$arUGroup['ID']] = $arUGroup['CNT'];
}

$res=CBlogUserGroup::GetList($arOrder = Array("ID" => "ASC"), $arFilter = Array("BLOG_ID" => $BLOG_ID));
while ($arUGroup=$res->Fetch())
{
	$count = intval($arCount[$arUGroup['ID']]);
	$name = htmlspecialchars($arUGroup['NAME']);
	$id = $arUGroup['ID'];

	print "
	<input type=hidden id=count_$id value=$count>
	<input type=hidden id=name_$id value='$name'>
	<tr>
		<td class=\"blogtablebody\" width=100% nowrap><font class=blogtext>$name ($count)</font></td>
		<td class=\"blogtablebody\"><a href='javascript:group_edit($id)' class=blogButton title='".GetMessage("BLOG_NAME_CHANGE")."'><img src='/bitrix/templates/.default/blog/images/edit_button.gif' width=18 height=18 border=0></a></td>
		<td class=\"blogtablebody\"><a href='javascript:group_del($id)' class=blogButton title='".GetMessage("BLOG_GROUP_DELETE")."'><img src='/bitrix/templates/.default/blog/images/delete_button.gif' width=18 height=18 border=0></a></td>
	</tr>";
}
?>
			<tr>
				<td align=center valign=center colspan=3 class="blogtablebody"><a href='javascript:group_edit(0)' class=blogtext title='<?=GetMessage("BLOG_GROUP_ADD")?>'><img align='absmiddle' src='/bitrix/templates/.default/blog/images/add_button.gif' border=0 width='18' height='18' ></a>&nbsp;<a href='javascript:group_edit(0)' class=blogtext title='<?=GetMessage("BLOG_GROUP_ADD")?>'><?=GetMessage("BLOG_ADD")?></a>

				<div id=edit_form style=display:none>
				<table width=100% cellspacing=2 cellpadding=0 border=0 class=blogtext>
						<td colspan=2>
						<?=GetMessage("BLOG_GROUP_NAME")?><br>
						<input name="NAME" id=group_name style="width:100%" maxlength=255>
					</tr>
					<tr>
						<td align=center>
							<input type=hidden name=save value='Y'>
							<input type=submit name=save value=' OK ' class="inputbutton">
							<input type=button onclick='show_form(0)' value='<?=GetMessage("BLOG_CANCEL")?>' class="inputbutton">
						</td>
					</tr>
				</table>
				</div>
				</td>
				</tr>
			</table>
			</form>
<script language=JavaScript>
	function group_edit(id)
	{
		if (id == 0)
			document.getElementById("group_name").value = '';
		else
			document.getElementById("group_name").value = document.getElementById("name_" + id).value;
		document.getElementById("group_id").value = id;
		show_form(1);
	}

	function group_del(id)
	{
		if (document.getElementById("count_" + id).value == 0 || confirm("<?=GetMessage("BLOG_CONFIRM_DELETE")?>"))
		{
			document.getElementById("group_id").value = id;
			document.getElementById("group_del").value = "Y";
			document.REPLIER.submit();
		}
	}

	function show_form(flag)
	{
		if (flag==1)
		{
			document.getElementById("edit_form").style.display = 'block';
			document.getElementById("group_name").focus();
		}
		else
			document.getElementById("edit_form").style.display = 'none';
	}
</script>
			<?
		}
		else
			echo ShowError("".GetMessage("BLOG_ERR_NO_RIGHTS")."");
	}
	else
		echo ShowError("".GetMessage("BLOG_ERR_NO_BLOG")."");
}
else
	echo ShowError("".GetMessage("BLOG_NOT_INSTALLED")."");
?>
