<?
IncludeTemplateLangFile(__FILE__);
if ($USER->IsAuthorized()):
	if (CModule::IncludeModule("forum")):
//*******************************************************

$UID = IntVal($_REQUEST["UID"]);
if (!$USER->IsAdmin() || $UID<=0)
{
	$UID = IntVal($USER->GetParam("USER_ID"));
}

$bUserFound = False;
$db_userX = CUser::GetByID($UID);
if ($ar_userX = $db_userX->Fetch())
{
	$bUserFound = True;

	while (list($key, $val) = each($ar_userX))
		${"f_".$key} = htmlspecialchars($val);
}


ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "PROFILE");
$strErrorMessage = "";
$strOKMessage = "";
$bVarsFromForm = false;

if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["ACTION"]=="EDIT" && $bUserFound)
{
	if (strlen($_POST["NAME"])<=0)
		$strErrorMessage .= "Заполните поле \"Имя\". \n";

	if (strlen($_POST["LAST_NAME"])<=0)
		$strErrorMessage .= "Заполните поле \"Фамилия\". \n";

	if (strlen($_POST["EMAIL"])<=0)
		$strErrorMessage .= "Заполните поле \"E-Mail адрес\". \n";
	elseif (!check_email($_POST["EMAIL"]))
		$strErrorMessage .= "E-Mail адрес не верен. \n";

	if (strlen($_POST["LOGIN"])<3)
		$strErrorMessage .= "Заполните поле \"Логин\". Логин не может быть коротче трех символов. \n";

	if (strlen($_POST["new_password"])>0 || strlen($_POST["password_confirm"])>0)
	{
		if (strlen($_POST["new_password"])<3)
			$strErrorMessage .= "Заполните поле \"Пароль\". Пароль не может быть коротче трех символов. \n";

		if ($_POST["new_password"]!=$_POST["password_confirm"])
			$strErrorMessage .= "Новый пароль не подтвержден. \n";
	}

	if (strlen($strErrorMessage)<=0)
	{
		$z = $DB->Query("SELECT PERSONAL_PHOTO FROM b_user WHERE ID='$UID'");
		$zr = $z->Fetch();
		$arPERSONAL_PHOTO = $_FILES["PERSONAL_PHOTO"];
		$arPERSONAL_PHOTO["old_file"] = $zr["PERSONAL_PHOTO"];
		$arPERSONAL_PHOTO["del"] = $_POST["PERSONAL_PHOTO_del"];

		$arFields = Array(
			"NAME" => $_POST["NAME"],
			"LAST_NAME" => $_POST["LAST_NAME"],
			"EMAIL" => $_POST["EMAIL"],
			"LOGIN" => $_POST["LOGIN"],
			"PERSONAL_ICQ" => $_POST["PERSONAL_ICQ"],
			"PERSONAL_WWW" => $_POST["PERSONAL_WWW"],
			"PERSONAL_PROFESSION" => $_POST["PERSONAL_PROFESSION"],
			"PERSONAL_BIRTHDATE" => $_POST["PERSONAL_BIRTHDATE"],
			"PERSONAL_CITY" => $_POST["PERSONAL_CITY"],
			"PERSONAL_COUNTRY" => $_POST["PERSONAL_COUNTRY"],
			"PERSONAL_PHOTO" => $arPERSONAL_PHOTO,
			"PERSONAL_GENDER" => $_POST["PERSONAL_GENDER"]
		);

		if (strlen($_POST["new_password"])>0)
		{
			$arFields["PASSWORD"] = $_POST["new_password"];
			$arFields["CONFIRM_PASSWORD"] = $_POST["password_confirm"];
		}

		$res = $USER->Update($UID, $arFields);
		if (!$res)
			$strErrorMessage .= $USER->LAST_ERROR.". \n";
	}

	if (strlen($strErrorMessage)<=0)
	{
		$arFields = array(
			"SHOW_NAME" => ($_POST["SHOW_NAME"]=="Y") ? "Y" : "N",
			"DESCRIPTION" => $_POST["DESCRIPTION"],
			"INTERESTS" => $_POST["INTERESTS"],
			"SIGNATURE" => $_POST["SIGNATURE"],
			"AVATAR" => $_FILES["AVATAR"]
			);
		$arFields["AVATAR"]["del"] = $_POST["AVATAR_del"];

		if ($USER->IsAdmin())
		{
			$arFields["ALLOW_POST"] = (($_POST["ALLOW_POST"]=="Y") ? "Y" : "N");
		}

		$ar_res = CForumUser::GetByUSER_ID($UID);
		if ($ar_res)
		{
			$arFields["AVATAR"]["old_file"] = $ar_res["AVATAR"];
			$ID = IntVal($ar_res["ID"]);

			$ID1 = CForumUser::Update($ID, $arFields);
			if (IntVal($ID1)<=0)
				$strErrorMessage .= "Ошибка изменения профиля. \n";
		}
		else
		{
			$arFields["USER_ID"] = $UID;

			$ID = CForumUser::Add($arFields);
			$ID = IntVal($ID);
			if ($ID<=0)
				$strErrorMessage .= "Ошибка добавления профиля. \n";
		}
	}

	if (strlen($strErrorMessage)>0)
	{
		$bVarsFromForm = true;
	}
	else
	{
		if ($f_LOGIN!=$_POST["LOGIN"] || strlen($_POST["new_password"])>0)
		{
			$USER->SendUserInfo($USER->GetParam("USER_ID"), LANG, "Изменение регистрационной информации");
		}
	}
}

$APPLICATION->SetTitle("Профиль");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php");

if (!$bUserFound)
	$strErrorMessage .= "Пользователь с кодом $UID не найден. \n";
?>

<?echo ShowMessage(array("MESSAGE" => $strErrorMessage, "TYPE" => "ERROR"));?>
<?echo ShowMessage(array("MESSAGE" => $strOKMessage, "TYPE" => "OK"));?>

<?
if ($bUserFound):

	$ar_forum_user = CForumUser::GetByUSER_ID($UID);
	while (list($key, $val) = each($ar_forum_user))
		${"f_".$key} = htmlspecialchars($val);

	if ($bVarsFromForm)
	{
		$arUserFields = &$DB->GetTableFieldsList("b_forum_user");
		for ($i = 0; $i < count($arUserFields); $i++)
			if (array_key_exists($arUserFields[$i], $_REQUEST))
				${"f_".$arUserFields[$i]} = htmlspecialchars($_REQUEST[$arUserFields[$i]]);

		$arUserFields = &$DB->GetTableFieldsList("b_user");
		for ($i = 0; $i < count($arUserFields); $i++)
			if (array_key_exists($arUserFields[$i], $_REQUEST))
				${"f_".$arUserFields[$i]} = htmlspecialchars($_REQUEST[$arUserFields[$i]]);
	}
	?>
	<form action="<?echo $APPLICATION->GetCurPage();?>" method="post" name="form1" enctype="multipart/form-data">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumborder"><tr><td>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr>
			<td class="forumhead" align="center" colspan="2" height="25" valign="middle">
				<font class="forumheadtext"><b>Регистрационная информация</b></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody" colspan="2">
				<font class="forumbodytext">&nbsp;Поля со звездочкой (<font color="#FF0000">*</font>) обязательны для заполнения</font>
			</td>
		</tr>
		<tr>
			<td class="forumbody" width="38%">
				<font class="forumheadtext">&nbsp;Имя: <font color="#FF0000">*</font></font>
			</td>
			<td class="forumbody">
				<input type="text" name="NAME" size="30" maxlength="50" value="<?echo $f_NAME; ?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody" width="38%">
				<font class="forumheadtext">&nbsp;Фамилия: <font color="#FF0000">*</font></font>
			</td>
			<td class="forumbody">
				<input type="text" name="LAST_NAME" size="30" maxlength="50" value="<?echo $f_LAST_NAME; ?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody" width="38%">
				<font class="forumheadtext">&nbsp;Логин: <font color="#FF0000">*</font></font>
			</td>
			<td class="forumbody">
				<input type="text" name="LOGIN" size="30" maxlength="50" value="<?echo $f_LOGIN; ?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;E-Mail адрес: <font color="#FF0000">*</font></font>
			</td>
			<td class="forumbody">
				<input type="text" name="EMAIL" size="30" maxlength="255" value="<?echo $f_EMAIL; ?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Новый пароль:<br>
				<small>&nbsp;Введите, если вы хотите поменять пароль<br></small></font>
			</td>
			<td class="forumbody">
				<input type="password" name="new_password" size="30" maxlength="100" value="">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Новый пароль еще раз:<br>
				<small>&nbsp;Введите, если вы хотите поменять пароль<br></small></font>
			</td>
			<td class="forumbody">
				<input type="password" name="password_confirm" size="30" maxlength="100" value="">
			</td>
		</tr>
		<tr>
			<td class="forumbody" colspan="2" height="28">&nbsp;</td>
		</tr>
		<tr>
			<td class="forumhead" align="center" colspan="2" height="25" valign="middle">
				<font class="forumheadtext"><b>Личные данные</b></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Профессия:</font>
			</td>
			<td class="forumbody">
				<input type="text" name="PERSONAL_PROFESSION" size="30" maxlength="255" value="<?=$f_PERSONAL_PROFESSION?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Web-сайт:</font>
			</td>
			<td class="forumbody">
				<input type="text" name="PERSONAL_WWW" size="30" maxlength="255" value="<?if (strlen($f_PERSONAL_WWW)>0) echo $f_PERSONAL_WWW; else echo "http://";?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Номер ICQ:</font>
			</td>
			<td class="forumbody">
				<input type="text" name="PERSONAL_ICQ" size="30" maxlength="255" value="<?=$f_PERSONAL_ICQ?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Пол:</font>
			</td>
			<td class="forumbody">
				<?
				$arr = array("reference"=>array("Мужской", "Женский"), "reference_id"=>array("M", "F"));
				echo SelectBoxFromArray("PERSONAL_GENDER", $arr, $f_PERSONAL_GENDER, "&lt;неизвестно&gt;");
				?>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Дата рождения (<?echo CLang::GetDateFormat("SHORT")?>):</font>
			</td>
			<td class="forumbody">
				<?echo CalendarDate("PERSONAL_BIRTHDATE", $f_PERSONAL_BIRTHDATE, "form1", "15")?>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Фотография:</font>
			</td>
			<td class="forumbody"><font class="forumbodytext">
				<input type="hidden" name="MAX_FILE_SIZE" value="500000">
				<input name="PERSONAL_PHOTO" size="20" type="file"><br>
				<input type="checkbox" name="PERSONAL_PHOTO_del" value="Y"> Удалить файл 
				<?if (strlen($f_PERSONAL_PHOTO)>0):?>
					<br>
					<?echo CFile::ShowImage($f_PERSONAL_PHOTO, 150, 150, "border=0", "", true)?>
				<?endif;?></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Страна:</font>
			</td>
			<td class="forumbody">
				<?echo SelectBoxFromArray("PERSONAL_COUNTRY", GetCountryArray(), $f_PERSONAL_COUNTRY, "&lt;неизвестно&gt;");?>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Город:</font>
			</td>
			<td class="forumbody">
				<input type="text" name="PERSONAL_CITY" size="30" maxlength="255" value="<?=$f_PERSONAL_CITY?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody" colspan="2" height="28">&nbsp;</td>
		</tr>
		<tr>
			<td class="forumhead" colspan="2" align="center" height="25" valign="middle">
				<font class="forumheadtext"><b>Профиль</b></font>
			</td>
		</tr>
		<?if ($USER->IsAdmin()):?>
			<tr>
				<td class="forumbody">
					<font class="forumheadtext">&nbsp;Разрешено писать:</font>
				</td>
				<td class="forumbody">
					<input type="checkbox" name="ALLOW_POST" value="Y" <?if ($f_ALLOW_POST=="Y") echo "checked";?>>
				</td>
			</tr>
		<?endif;?>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Показывать имя:<br>
				<small>&nbsp;Показывать ли имя пользователя или только логин</small><br></font>
			</td>
			<td class="forumbody">
				<input type="checkbox" name="SHOW_NAME" value="Y" <?if ($f_SHOW_NAME=="Y") echo "checked";?>>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Пояснение:<br>
				<small>&nbsp;Выводится под псевдонимом</small></font>
			</td>
			<td class="forumbody">
				<input type="text" name="DESCRIPTION" size="30" maxlength="64" value="<?echo $f_DESCRIPTION; ?>">
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Интересы:</font>
			</td>
			<td class="forumbody">
				<textarea name="INTERESTS" rows="3" cols="35"><?echo $f_INTERESTS; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Подпись:</font>
			</td>
			<td class="forumbody">
				<font class="forumbodytext">
				<textarea name="SIGNATURE" rows="3" cols="35"><?echo $f_SIGNATURE; ?></textarea><br>
				<small>Подпись будет выводиться под каждым вашим сообщением. Допустимо использование любых псевдо-тегов, разрешенных на данном форуме.</small><br>
				</font>
			</td>
		</tr>
		<tr>
			<td class="forumbody">
				<font class="forumheadtext">&nbsp;Аватар:<br>
				<small>&nbsp;Изображение размером не более 10 kb и разрешением не более 90x90 px</small></font>
			</td>
			<td class="forumbody"><font class="forumbodytext">
				<input name="AVATAR" size="20" type="file"><br>
				<input type="checkbox" name="AVATAR_del" value="Y"> Удалить файл 
				<?if (strlen($f_AVATAR)>0):?>
					<br>
					<?echo CFile::ShowImage($f_AVATAR, 90, 90, "border=0", "", true)?>
				<?endif;?></font>
			</td>
		</tr>
		<tr>
			<td class="forumbody" colspan="2" height="28">&nbsp;</td>
		</tr>
		<tr>
			<td class="forumhead" colspan="2" align="center" height="25" valign="middle">
				<font class="forumheadtext"><b><a href="subscr_list.php">Подписка [Изменить]</a></b></font>
		  </td>
		</tr>
		<tr>
			<td class="forumbody" colspan="2" align="center" height="28">
				<input type="hidden" name="ACTION" value="EDIT">
				<input type="hidden" name="UID" value="<?echo $UID; ?>">
				<input type="hidden" name="old_LOGIN" value="<?echo $f_LOGIN; ?>">
				<input type="submit" name="submit" value="Сохранить">&nbsp;&nbsp;<input type="reset" value="Отмена" name="reset">
			</td>
		</tr>
	</table>
	<td><tr></table>
	</form>
	<?
endif;

//*******************************************************
	else:
		?>
		<font class="text"><b>Модуль форума не установлен</b></font>
		<?
	endif;
else:
	?>
	<font class="text"><b>Для просмотра этой страницы вы должны быть авторизованы</b></font>
	<?
endif;
?>