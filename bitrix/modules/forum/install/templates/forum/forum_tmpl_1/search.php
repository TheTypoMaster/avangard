<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

define("FORUM_MODULE_PAGE", "SEARCH");
$APPLICATION->SetTitle(GetMessage("FS_FTITLE"));
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_1/forum.css");


$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

if (CModule::IncludeModule("search")):
	$q = Trim($_REQUEST["q"]);
	?>
	<form action="search.php">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="forumborder"><tr><td>
		<table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr><td colspan="2" align="center" class="forumhead"><font class="forumheadtext"><b><?echo GetMessage("FS_SEARCH")?></b></font></td></tr>
			<tr>
				<td class="forumbody" align="right"><font class="forumheadtext"><?echo GetMessage("FS_KEYWORDS")?></font></td>
				<td class="forumbody"><font class="forumbodytext">
					<input type="text" name="q" value="<?echo htmlspecialchars($q)?>" size="40" class="inputtext">
				</font></td>
			</tr>
			<tr>
				<td class="forumbody" align="right"><font class="forumheadtext"><?echo GetMessage("FS_FORUM")?></font></td>
				<td class="forumbody"><font class="forumbodytext">
					<select name="FORUM_ID" class="inputselect">
						<option value="0"><?echo GetMessage("FS_ALL_FORUMS")?></option>
						<?
						$arFilter = array("SITE_ID" => SITE_ID);
						if (!$USER->IsAdmin())
						{
							$arFilter["PERMS"] = array($USER->GetGroups(), 'A');
							$arFilter["ACTIVE"] = "Y";
						}
						$db_Forum = CForumNew::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
						while ($ar_Forum = $db_Forum->Fetch())
						{
							?><option value="<?= $ar_Forum["ID"]?>"<?if (IntVal($ar_Forum["ID"])==IntVal($_REQUEST["FORUM_ID"])) echo " selected";?>><?= htmlspecialcharsEx($ar_Forum["NAME"])?></option><?
						}
						?>
					</select>
				</font></td>
			</tr>
			<tr><td colspan="2" align="center" class="forumbody"><font class="forumbodytext">
				<input type="submit" name="s" value="<?echo GetMessage("FS_DO_SEARCH")?>" class="inputbutton">
			</font></td></tr>
		</table>
	</td></tr></table>
	</form>
	<br>

	<?
	if (strlen($q)>0):
		?><table width="99%" align="center" border="0" cellspacing="0" cellpadding="0"><tr><td><?
		$FORUM_ID = IntVal($_REQUEST["FORUM_ID"]);
		if ($FORUM_ID<=0) $FORUM_ID = false;
		$obSearch = new CSearch();
		$obSearch->Search(Array(
			"MODULE_ID" => "forum",
			"PARAM1" => $FORUM_ID,
			"SITE_ID" => SITE_ID,
			"QUERY" => $q
			));
		if ($obSearch->errorno!=0):
			?>
			<font class="text"><?echo GetMessage("FS_PHRASE_ERROR")?></font> 
			<?echo ShowError($obSearch->error);?>
			<font class="text"><?echo GetMessage("FS_PHRASE_ERROR_CORRECT")?></font><br><br>

			<font class="text">
			<b><?echo GetMessage("FS_PHRASE_ERROR_SYNTAX")?></b><br><br>
			<?echo GetMessage("FS_SEARCH_DESCR")?>
			</font>			
			<?
		else:
			$obSearch->NavStart(20, false);
			$obSearch->NavPrint(GetMessage("FS_SEARCH_RESULTS"));

			if ($arResult = $obSearch->Fetch())
			{
				$strCurDirName = dirname($APPLICATION->GetCurPage());
				?>
				<br>
				<ul>
				<?
				do
				{
					$strURL = $arResult["URL"];
					if (substr($arResult["URL"], 0, strlen($strCurDirName))!=$strCurDirName)
					{
						$strQStr = "";
						$iQStrPos = strpos($arResult["URL"], "?");
						if ($iQStrPos!==false)
							$strQStr = substr($arResult["URL"], $iQStrPos);

						$strStr = substr($arResult["URL"], 0, $iQStrPos);
						$iStrPos = strrpos($arResult["URL"], "/");
						if ($iStrPos!==false)
							$strStr = substr($strStr, $iStrPos);

						$strURL = $strCurDirName.$strStr.$strQStr;
					}
					?>
					<li>
					<font class="text">
					<a href="<?echo $strURL?>"><?= $arResult["TITLE_FORMATED"] ?></a><br>
					<?
					echo preg_replace("#\[/?(quote|b|i|u|code|url).*?\]#i", "", $arResult["BODY_FORMATED"]);
					?>
					</font><br><br>
					</li>
					<?
				}
				while ($arResult = $obSearch->Fetch());
				?>
				</ul>
				<?
			}
			else
			{
				?>
				<font class="text">
				<b><?echo GetMessage("FS_EMPTY")?></b>
				</font>
				<?
			}
			$obSearch->NavPrint(GetMessage("FS_SEARCH_RESULTS"));
		endif;
		?></td></tr></table><?
	endif;
else:
	?><font class="text"><?echo GetMessage("FS_NO_SEARCH_MODULE")?></font><?
endif;

echo "<br><br><br>";
$APPLICATION->IncludeFile("forum/forum_tmpl_1/menu.php");

//*******************************************************
else:
	?>
	<font class="text"><b><?echo GetMessage("FS_NO_MODULE")?></b></font>
	<?
endif;
?>