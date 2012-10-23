<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile(
	"blog/blog/user.php", 
	array(
		"ID" => IntVal($arFolders[1])
	)
);
*/
if(!CModule::IncludeModule("blog"))
{
	ShowError(GetMessage("B_B_USER_NO_MODULE"));
	return;
}
$ID=intval($ID);
$is404 = ($is404=='N') ? false: true;
$bEdit = ($_GET['mode']=='edit' && ($USER->GetID()==$ID || $USER->IsAdmin()));
$strErrorMessage = "";

$dbUser = CUser::GetByID($ID);
$arUser = $dbUser->Fetch();
if(!is_array($arUser)):?>
	<p class="errortext"><?=GetMessage("B_B_USER_NO_USER")?></p>
	<?return;
endif;
$BlogUser=CBlogUser::GetByID($ID, BLOG_BY_USER_ID);
$arSex = array(
	"M"=>GetMessage("B_B_USER_SEX_M"),
	"F"=>GetMessage("B_B_USER_SEX_F"),
);
$userName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
$str_userName = htmlspecialchars($userName);
if($bEdit && is_array($_SESSION["BLOG_USER_PROFILE_ERROR"]))
{
	$strErrorMessage = $_SESSION["BLOG_USER_PROFILE_ERROR"]["MESSAGE"];
	$arFields = $_SESSION["BLOG_USER_PROFILE_ERROR"]["FIELDS"];
	$str_BLOG_USER_ALIAS = htmlspecialchars($arFields["BLOG_USER_ALIAS"]);
	$str_BLOG_USER_DESCRIPTION = htmlspecialchars($arFields["BLOG_USER_DESCRIPTION"]);
	$str_USER_PERSONAL_WWW = htmlspecialchars($arFields["USER_PERSONAL_WWW"]);
	$str_USER_PERSONAL_GENDER = htmlspecialchars($arFields["USER_PERSONAL_GENDER"]);
	$str_USER_PERSONAL_BIRTHDAY = htmlspecialchars($arFields["USER_PERSONAL_BIRTHDAY"]);
	$str_BLOG_USER_INTERESTS = htmlspecialchars($arFields["BLOG_USER_INTERESTS"]);
	$str_BLOG_USER_LAST_VISIT = htmlspecialchars($arFields["BLOG_USER_LAST_VISIT"]);
}
else
{
	$str_BLOG_USER_ALIAS = htmlspecialchars($BlogUser["ALIAS"]);
	$str_BLOG_USER_DESCRIPTION = htmlspecialchars($BlogUser["DESCRIPTION"]);
	$str_USER_PERSONAL_WWW = htmlspecialchars($arUser["PERSONAL_WWW"]);
	$str_USER_PERSONAL_GENDER = htmlspecialchars($arUser["PERSONAL_GENDER"]);
	$str_USER_PERSONAL_BIRTHDAY = htmlspecialchars($arUser["PERSONAL_BIRTHDAY"]);
	$str_BLOG_USER_INTERESTS = htmlspecialchars($BlogUser["INTERESTS"]);
	$str_BLOG_USER_LAST_VISIT = htmlspecialchars($BlogUser["LAST_VISIT"]);
}
$str_USER_PERSONAL_PHOTO = htmlspecialchars($arUser["PERSONAL_PHOTO"]);
$str_BLOG_USER_AVATAR = htmlspecialchars($BlogUser["AVATAR"]);
if($bEdit)
	$APPLICATION->SetTitle(GetMessage("B_B_USER_TITLE")." ".$userName);
else
	$APPLICATION->SetTitle(GetMessage("B_B_USER_TITLE_VIEW")." ".$userName);
unset($_SESSION["BLOG_USER_PROFILE_ERROR"]);
?>
<?if($strErrorMessage!=""):?>
<p class="errortext"><?=$strErrorMessage?></p>
<?endif;?>
<?
$sitePath = CBlogSitePath::GetBySiteID(SITE_ID);
if($bEdit):?>
<?
if($is404)
	$urlToEdit = "../post_user.php";
else
	$urlToEdit = $sitePath["PATH"]."/post_user.php";
?>
<form method="POST" name="form1" action="<?=$urlToEdit?>" enctype="multipart/form-data">
<table cellpadding="3" cellspacing="1" border="0" class="blogtableborder">
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_ALIAS")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><input type=text size="47" name="BLOG_USER_ALIAS" value="<?=$str_BLOG_USER_ALIAS?>"></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=GetMessage("B_B_USER_ALIAS_COM")?></font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_ABOUT")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><textarea name="BLOG_USER_DESCRIPTION" style="width:100%" rows="5"><?=$str_BLOG_USER_DESCRIPTION?></textarea></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=GetMessage("B_B_USER_ABOUT_COM")?></font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_SITE")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><input type=text size="47" name="USER_PERSONAL_WWW" value="<?=$str_USER_PERSONAL_WWW?>"></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=GetMessage("B_B_USER_SITE_COM")?></font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_SEX")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><?=SelectBoxFromArray("USER_PERSONAL_GENDER", array("reference"=>array_values($arSex),"reference_id"=>array_keys($arSex)), $str_USER_PERSONAL_GENDER, "(".GetMessage("B_B_USER_NOT_SET").")", "  ");?></font></td>
	<td class="blogtablebody"><font class="blogtext"> </font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_BIRTHDAY")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><?=CalendarDate("USER_PERSONAL_BIRTHDAY",$str_USER_PERSONAL_BIRTHDAY,"form1")?></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=GetMessage("B_B_USER_BIRTHDAY_COM")?> (<?=FORMAT_DATE?>).</font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_PHOTO")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><?=CFile::InputFile("USER_PERSONAL_PHOTO", 30, $str_USER_PERSONAL_PHOTO, false, 0, "IMAGE", " ", 0, " ", "", False);?><br><?=CFile::ShowImage($str_USER_PERSONAL_PHOTO, 150, 150, "border=0", "", true);?></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=GetMessage("B_B_USER_PHOTO_COM")?></font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_AVATAR")?></b></font></td>
	<td class="blogtablebody"><font class="tablefieldtext"><?=CFile::InputFile("BLOG_USER_AVATAR", 30, $str_BLOG_USER_AVATAR, false, 0, "IMAGE", " ", 0, " ", "", False);?><br><?=CFile::ShowImage($str_BLOG_USER_AVATAR, 150, 150, "border=0", "", true);?></font></td>
	<td class="blogtablebody"><font class="blogtext"> </font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_INTERESTS")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><textarea name="BLOG_USER_INTERESTS" style="width:100%" rows="5"><?=$str_BLOG_USER_INTERESTS?></textarea></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=GetMessage("B_B_USER_INTERESTS_COM")?></font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_LAST_AUTH")?></b></font></td>
	<td colspan="2" class="blogtablebody"><font class="blogtext">&nbsp;<?=$str_BLOG_USER_LAST_VISIT?></font></td>
</tr>
</table>
<input type="hidden" name="BLOG_USER_ID" value="<?=htmlspecialchars($BlogUser["ID"])?>">
<?=bitrix_sessid_post()?>
<input type="hidden" name="back_url_error" value="<?=$APPLICATION->GetCurUri("mode=edit")?>">
<?if($is404):?>
	<input type="hidden" name="back_url_ok" value="<?=$APPLICATION->GetCurPage()?>">
<?else:?>
	<input type="hidden" name="back_url_ok" value="<?=$APPLICATION->GetCurPage()."?user_id=".$ID?>">
<?endif;?>
<input type="hidden" name="mode" value="edit">
<br>
<input type="submit" name="save" class="inputbutton" value="<?=GetMessage("B_B_USER_SAVE")?>">
<input type="reset" name="cancel" class="inputbutton" value="<?=GetMessage("B_B_USER_CANCEL")?>" OnClick="window.location='<?=$APPLICATION->GetCurPage()?>'">
</form>
<?else:?>
<?
if($is404)
	$urlToEditMode = $APPLICATION->GetCurPage().'?mode=edit';
else
	$urlToEditMode = $APPLICATION->GetCurPage().'?mode=edit&user_id='.$ID;
if($ID == ($USER->GetID()) || $USER->IsAdmin())
{
	?>
	<p class="text"><?=GetMessage("B_B_USER_TEXT2")?> <a href="<?=$urlToEditMode?>"><?=GetMessage("B_B_USER_TEXT3")?></a>.</p>
	<?
}
?>
<?
$arUBlog = CBlog::GetByOwnerID($ID);
?>
<table cellpadding="3" cellspacing="1" border="0" class="blogtableborder">
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_USER")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=$str_userName?><br><br>
	<?=$str_BLOG_USER_DESCRIPTION?><br><br>
	</font></td>
</tr>
<?if(strlen($arUBlog["URL"])>0):?>
<tr>
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_BLOG")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><a href="<?=CBlog::PreparePath($arUBlog["URL"])?>"><?=htmlspecialchars($arUBlog["NAME"])?></a></font></td>
</tr>
<?endif;?>
<?if($str_USER_PERSONAL_WWW!=""):?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_SITE")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=$str_USER_PERSONAL_WWW!=''?'<a href="'.((strpos($str_USER_PERSONAL_WWW, "http") === false)? "http://" : "").$str_USER_PERSONAL_WWW.'">'.$str_USER_PERSONAL_WWW.'</a>':''?></font>&nbsp;</td>
</tr>
<?endif;?>
<?if($arSex[$str_USER_PERSONAL_GENDER]!=""):?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_SEX")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=htmlspecialchars($arSex[$str_USER_PERSONAL_GENDER])?></font>&nbsp;</td>
</tr>
<?endif;?>
<?if($str_USER_PERSONAL_BIRTHDAY!=""):?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_BIRTHDAY")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=$str_USER_PERSONAL_BIRTHDAY?></font>&nbsp;</td>
</tr>
<?endif;?>
<?if($str_USER_PERSONAL_PHOTO!=""):?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_PHOTO")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=CFile::ShowImage($str_USER_PERSONAL_PHOTO, 150, 150, "border=0", "", true);?></font>&nbsp;</td>
</tr>
<?endif;?>
<?if($str_BLOG_USER_AVATAR!=""):?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_AVATAR")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=CFile::ShowImage($str_BLOG_USER_AVATAR, 150, 150, "border=0", "", true);?></font>&nbsp;</td>
</tr>
<?endif;?>
<?
$arHobby = explode(", ", $str_BLOG_USER_INTERESTS);
$arHobbyHtml=array();
foreach($arHobby as $Hobby)
{
	if($Hobby!="")
		$arHobbyHtml[]='<a title="'.GetMessage("B_B_USER_INT_TITLE").'" href="'.htmlspecialchars($sitePath["PATH"].'/search.php?where=USER&q='.urlencode($Hobby)).'">'.$Hobby.'</a>';
}
?>
<?if(count($arHobbyHtml)>0):?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_INTERESTS")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=implode(", ", $arHobbyHtml)?></font>&nbsp;</td>
</tr>
<?endif;?>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate"><b><?=GetMessage("B_B_USER_LAST_AUTH")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext"><?=$str_BLOG_USER_LAST_VISIT?>&nbsp;</font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate">
<b><?=GetMessage("B_B_FR_FR_OF")?></b></font></td>
	<td class="blogtablebody"><font class="blogtext">
<?
$bNeedComa = False;
$dbFriends = CBlogUser::GetUserFriends($ID, True);
while ($arFriends = $dbFriends->Fetch())
{
	if ($bNeedComa)
		echo ", ";
	?><a href="<?= CBlog::PreparePath($arFriends["URL"], false, $is404) ?>"><?= htmlspecialchars($arFriends["NAME"]) ?></a><?
	$bNeedComa = True;
}
if (!$bNeedComa)
	echo "<i>".GetMessage("B_B_FR_NO")."</i>";
?>
</font></td>
</tr>
<tr valign="top">
	<td align="right" nowrap class="blogtablehead"><font class="blogpostdate">
<b><?=GetMessage("B_B_FR_FR")?></b></td>
	<td class="blogtablebody"><font class="blogtext">
<?
$bNeedComa = False;
$dbFriends = CBlogUser::GetUserFriends($ID, False);
while ($arFriends = $dbFriends->Fetch())
{
	if ($bNeedComa)
		echo ", ";
	?><a href="<?= CBlog::PreparePath($arFriends["URL"], false, $is404) ?>"><?= htmlspecialchars($arFriends["NAME"]) ?></a><?
	$bNeedComa = True;
}
if (!$bNeedComa)
	echo "<i>".GetMessage("B_B_FR_NO")."</i>";
?>
</font></td>
</tr>
</table>
<?endif;?>