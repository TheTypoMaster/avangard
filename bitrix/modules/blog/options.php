<?
$module_id = "blog";
$BLOG_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($BLOG_RIGHT>="R") :

global $MESS;
include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/", "/options.php"));
include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/blog/lang/", "/options.php"));

include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/blog/include.php");

if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0 && $BLOG_RIGHT=="W" && check_bitrix_sessid())
{
	COption::RemoveOption("blog");
	$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
		$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
}

$arAllOptions = array(
	array("avatar_max_size", GetMessage("BLO_AVATAR_MAX_SIZE"), "1000000", Array("text", 10)),
	//array("avatar_max_width", GetMessage("BLO_AVATAR_MAX_WIDTH"), "150", Array("text", 10)),
	//array("avatar_max_height", GetMessage("BLO_AVATAR_MAX_HEIGHT"), "150", Array("text", 10)),
	array("image_max_width", GetMessage("BLO_IMAGE_MAX_WIDTH"), "600", Array("text", 10)),
	array("image_max_height", GetMessage("BLO_IMAGE_MAX_HEIGHT"), "600", Array("text", 10)),
	array("image_max_size", GetMessage("BLO_IMAGE_MAX_SIZE"), "1000000", Array("text", 10)),
	array("allow_alias", GetMessage("BLO_ALLOW_ALIAS"), "Y", Array("checkbox")),
	array("block_url_change", GetMessage("BLOG_URL_BLOCK"), "N", Array("checkbox")),
	array("show_ip", GetMessage("BLOG_SHOW_IP"), "Y", Array("checkbox")),
	array("enable_trackback", GetMessage("BLOG_ENABLE_TRACKBACK"), "Y", Array("checkbox")),
	array("allow_html", GetMessage("BLOG_ALLOW_HTML"), "N", Array("checkbox")),
	array("allow_video", GetMessage("BLOG_ALLOW_VIDEO"), "Y", Array("checkbox")),
	array("parser_nofollow", GetMessage("BLOG_PARSER_NOFOLLOW"), "N", Array("checkbox")),
	array("use_autosave", GetMessage("BLOG_USE_AUTOSAVE"), "Y", Array("checkbox")),
	array("use_image_perm", GetMessage("BLOG_USE_IMAGE_PERM"), "N", Array("checkbox")),
	array("captcha_choice", GetMessage("BLOG_CAPTCHA_CHOICE"), "U", Array("selectbox"), Array("U" => GetMessage("BLOG_CAPTCHA_CHOICE_U"), "A" => GetMessage("BLOG_CAPTCHA_CHOICE_A"), "D" => GetMessage("BLOG_CAPTCHA_CHOICE_D"))),
	array("send_blog_ping", GetMessage("BLOG_SEND_BLOG_PING"), "N", Array("checkbox")),
	array("send_blog_ping_address", GetMessage("BLOG_SEND_BLOG_PING_ADDRESS"), "http://ping.blogs.yandex.ru/RPC2\r\nhttp://rpc.weblogs.com/RPC2", Array("textarea", 5, 40)),
	array("post_everyone_max_rights", GetMessage("BLOG_POST_EVERYONE_MAX_RIGHTS"), "I", Array("selectbox"), $GLOBALS["AR_BLOG_PERMS"]),
	array("comment_everyone_max_rights", GetMessage("BLOG_COMMENT_EVERYONE_MAX_RIGHTS"), "P", Array("selectbox"), $GLOBALS["AR_BLOG_PERMS"]),
	array("post_auth_user_max_rights", GetMessage("BLOG_POST_AUTH_USER_MAX_RIGHTS"), "I", Array("selectbox"), $GLOBALS["AR_BLOG_PERMS"]),
	array("comment_auth_user_max_rights", GetMessage("BLOG_COMMENT_AUTH_USER_MAX_RIGHTS"), "P", Array("selectbox"), $GLOBALS["AR_BLOG_PERMS"]),
	array("post_group_user_max_rights", GetMessage("BLOG_POST_GROUP_USER_MAX_RIGHTS"), "W", Array("selectbox"), $GLOBALS["AR_BLOG_PERMS"]),
	array("comment_group_user_max_rights", GetMessage("BLOG_COMMENT_GROUP_USER_MAX_RIGHTS"), "W", Array("selectbox"), $GLOBALS["AR_BLOG_PERMS"]),
);

$strWarning = "";
if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $BLOG_RIGHT=="W" && check_bitrix_sessid() && strlen($use_sonnet_button) <= 0)
{
	for ($i = 0; $i < count($arAllOptions); $i++)
	{
		$name = $arAllOptions[$i][0];
		$val = $$name;
		if ($arAllOptions[$i][3][0] == "checkbox" && $val != "Y")
			$val = "N";
		COption::SetOptionString("blog", $name, $val, $arAllOptions[$i][1]);
	}

	$arPaths = array();
	$arPathsNullType = array();
	$dbPaths = CBlogSitePath::GetList();
	while ($arPath = $dbPaths->Fetch())
	{
		if(strlen($arPath["TYPE"])>0)
			$arPaths[$arPath["SITE_ID"]][$arPath["TYPE"]] = $arPath["ID"];
		else
			$arPathsNullType[$arPath["SITE_ID"]] = $arPath["ID"];
	}
	
	$arType = array("B", "P", "U", "G", "H");
	/*
	"B" - user blog, 
	"P" - user post, 
	"U" - just user, 
	"G" - group blog,
	"H" - group post
	*/
	$dbSites = CSite::GetList(($b = ""), ($o = ""), array("ACTIVE" => "Y"));
	while ($arSite = $dbSites->Fetch())
	{
		BXClearCache(True, "/".$arSite["LID"]."/blog/");

		foreach($arType as $type)
		{
			if (IntVal($arPaths[$arSite["LID"]][$type])>0)
			{
				if (strlen(${"SITE_PATH_".$arSite["LID"]."_".$type}) > 0)
					CBlogSitePath::Update($arPaths[$arSite["LID"]][$type], array("PATH" => ${"SITE_PATH_".$arSite["LID"]."_".$type}, "TYPE"=>$type));
				else
					CBlogSitePath::Delete($arPaths[$arSite["LID"]][$type]);
			}
			else
			{
				CBlogSitePath::Add(
					array(
						"SITE_ID" => $arSite["LID"],
						"PATH" => ${"SITE_PATH_".$arSite["LID"]."_".$type},
						"TYPE" => $type
					)
				);
			}
		}
		unset($arPaths[$arSite["LID"]]);
		
		if(strlen(${"SITE_PATH_".$arSite["LID"]})>0)
			${"SITE_PATH_".$arSite["LID"]} = "/".trim(str_replace("\\", "/", ${"SITE_PATH_".$arSite["LID"]}), "/");
		if (array_key_exists($arSite["LID"], $arPathsNullType))
		{
			if (strlen(${"SITE_PATH_".$arSite["LID"]}) > 0)
				CBlogSitePath::Update($arPathsNullType[$arSite["LID"]], array("PATH" => ${"SITE_PATH_".$arSite["LID"]}));
			else
				CBlogSitePath::Delete($arPathsNullType[$arSite["LID"]]);
		}
		else
		{
			CBlogSitePath::Add(
				array(
					"SITE_ID" => $arSite["LID"],
					"PATH" => ${"SITE_PATH_".$arSite["LID"]}
				)
			);
		}
		unset($arPathsNullType[$arSite["LID"]]);
	}
	
	foreach ($arPaths as $key)
		foreach($key as $val)
			CBlogSitePath::Delete($val);
}


if (strlen($strWarning) > 0)
	CAdminMessage::ShowMessage($strWarning);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("BLO_TAB_SET"), "ICON" => "blog_settings", "TITLE" => GetMessage("BLO_TAB_SET_ALT")),
	array("DIV" => "edit2", "TAB" => GetMessage("BLO_TAB_RIGHTS"), "ICON" => "blog_settings", "TITLE" => GetMessage("BLO_TAB_RIGHTS_ALT")),
);
	
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?
$tabControl->Begin();
?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>"><?
bitrix_sessid_post();
$tabControl->BeginNextTab();

	for ($i = 0; $i < count($arAllOptions); $i++):
		$Option = $arAllOptions[$i];
		$val = COption::GetOptionString("blog", $Option[0], $Option[2]);
		$type = $Option[3];
		?>
		<tr>
			<td valign="top" width="50%"><?
				if ($type[0]=="checkbox")
					echo "<label for=\"".htmlspecialchars($Option[0])."\">".$Option[1]."</label>";
				else
					echo $Option[1];
			?></td>
			<td valign="middle" width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
				<?elseif($type[0]=="textarea"):?>
					<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
				<?elseif($type[0]=="selectbox"):?>
					<select name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>">
						<?foreach($Option[4] as $v => $k)
						{
							?><option value="<?=$v?>"<?if($val==$v)echo" selected";?>><?=$k?></option><?
						}
						?>
					</select>
				<?endif?>
			</td>
		</tr>
	<?endfor;?>


	<tr class="heading">
		<td colspan="2"><?=GetMessage("BLO_SITE_PATH")?></td>
	</tr>
	<?
	$arPaths = array();
	$dbPaths = CBlogSitePath::GetList();
	while ($arPath = $dbPaths->Fetch())
	{
		if(strlen($arPath["TYPE"])<=0)
			$arPaths[$arPath["SITE_ID"]] = $arPath["PATH"];
	}

	$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y"));
	while ($arSite = $dbSites->Fetch())
	{
		?>
		<tr>
			<td valign="top" width="50%">
				<?= str_replace("#SITE#", $arSite["LID"], GetMessage("BLO_SITE_PATH_SITE")) ?></td>
			<td valign="middle" width="50%">
				<input type="text" size="40" value="<?echo htmlspecialchars($arPaths[$arSite["LID"]])?>" name="SITE_PATH_<?= $arSite["LID"] ?>">
			</td>
		</tr>
		<?
	}
	?>
	
	
	
	<tr class="heading">
		<td colspan="2"><?=GetMessage("BLO_SITE_PATH2")?></td>
	</tr>
	<?
	$arPaths = array();
	$dbPaths = CBlogSitePath::GetList();
	while ($arPath = $dbPaths->Fetch())
		$arPaths[$arPath["SITE_ID"]][$arPath["TYPE"]] = $arPath["PATH"];

	$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y"));
	while ($arSite = $dbSites->Fetch())
	{
		?>
		<tr>
			<td valign="top" colspan="2" align="center"><?= str_replace("#SITE#", $arSite["LID"], GetMessage("BLO_SITE_PATH_SITE")) ?>:</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<table cellspacing="2" width="100%">
				<tr>
					<td align="right" width="50%"><?=GetMessage("BLO_SITE_PATH_SITE_BLOG")?>:</td>
					<td width="50%"><input type="text" size="40" value="<?echo htmlspecialchars($arPaths[$arSite["LID"]]["B"])?>" name="SITE_PATH_<?= $arSite["LID"] ?>_B"></td>
				</tr>
				<tr>
					<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_POST")?>:</td>
					<td><input type="text" size="40" value="<?echo htmlspecialchars($arPaths[$arSite["LID"]]["P"])?>" name="SITE_PATH_<?= $arSite["LID"] ?>_P"></td>
				</tr>
				<tr>
					<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_USER")?>:</td>
					<td><input type="text" size="40" value="<?echo htmlspecialchars($arPaths[$arSite["LID"]]["U"])?>" name="SITE_PATH_<?= $arSite["LID"] ?>_U"></td>
				</tr>
				<tr>
					<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_GROUP_BLOG")?>:</td>
					<td><input type="text" size="40" value="<?echo htmlspecialchars($arPaths[$arSite["LID"]]["G"])?>" name="SITE_PATH_<?= $arSite["LID"] ?>_G"></td>
				</tr>
				<tr>
					<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_GROUP_POST")?>:</td>
					<td><input type="text" size="40" value="<?echo htmlspecialchars($arPaths[$arSite["LID"]]["H"])?>" name="SITE_PATH_<?= $arSite["LID"] ?>_H"></td>
				</tr>
				</table>
			</td>
		</tr>
		<?
	}
	?>
	<tr>
		<td valign="top" align="center" colspan="2"><?=GetMessage("BLO_PATH_EXAMPLE")?>:</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table cellspacing="2" width="0%">
			<tr>
				<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_BLOG")?>:</td>
				<td>/blog/#blog#/</td>
			</tr>
			<tr>
				<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_POST")?>:</td>
				<td>/blog/#blog#/#post_id#.php</td>
			</tr>
			<tr>
				<td align="right"><?=GetMessage("BLO_SITE_PATH_SITE_USER")?>:</td>
				<td>/blog/user/#user_id#.php</td>
			</tr>
			</table>
		</td>
	</tr>


<?$tabControl->BeginNextTab();?>

	<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
	if (confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)."&".bitrix_sessid_get();?>";
}
</script>

<input type="submit" <?if ($BLOG_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="button" <?if ($BLOG_RIGHT<"W") echo "disabled" ?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<?$tabControl->End();?>
</form>
<?endif;?>