<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "SEARCH");
$APPLICATION->SetTitle("����� �� ������");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php");

if (CModule::IncludeModule("search")):
	$q = Trim($_REQUEST["q"]);
	?>
	<form action="search.php">
	<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr><td>
		<table width="100%" border="0" cellspacing="1" cellpadding="1">
			<tr><td colspan="2" align="center" class="forumhead"><font class="forumheadtext"><b>�����</b></font></td></tr>
			<tr>
				<td class="forumbody" align="right"><font class="forumheadtext">�������� �����:</font></td>
				<td class="forumbody"><font class="forumbodytext">
					<input type="text" name="q" value="<?echo htmlspecialchars($q)?>" size="40">
				</font></td>
			</tr>
			<tr>
				<td class="forumbody" align="right"><font class="forumheadtext">������ � ������:</font></td>
				<td class="forumbody"><font class="forumbodytext">
					<select name="FORUM_ID">
						<option value="0">��� ������</option>
						<?
						$arFilter = array("SITE_ID" => SITE_ID);
						if (!$USER->IsAdmin())
						{
							$arFilter["PERMS"] = array($USER->GetGroups(), 'A');
							$arFilter["ACTIVE"] = "Y";
						}
						$db_Forum = CForumNew::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
						while ($ar_Forum = $db_Forum->Fetch()):
							?><option value="<?echo $ar_Forum["ID"]?>"<?if (IntVal($ar_Forum["ID"])==IntVal($FORUM_ID)) echo " selected";?>><?echo $ar_Forum["NAME"]?></option><?
						endwhile;
						?>
					</select>
				</font></td>
			</tr>
			<tr><td colspan="2" align="center" class="forumbody"><font class="forumbodytext">
				<input type="submit" name="s" value="������">
			</font></td></tr>
		</table>
	</td></tr></table>
	</form>

	<?
	if (strlen($q)>0):
		$FORUM_ID = IntVal($_REQUEST["FORUM_ID"]);
		if ($FORUM_ID<=0) $FORUM_ID = false;
		$obSearch = new CSearch($q, SITE_ID, "forum", false, $FORUM_ID);
		if ($obSearch->errorno!=0):
			?>
			<font class="text">� ��������� ����� ���������� ������:</font> 
			<?echo ShowError($obSearch->error);?>
			<font class="text">��������� ��������� ����� � ��������� �����.</font><br><br>

			<font class="text">
			<b>��������� ���������� �������:</b><br><br>
			������ ������ ������������ �� ���� ������ ���� ��� ��������� ����, 
			��������: <br>	<i>���������� ����������</i><br> �� ������ ������� ����� 
			������� ��������, �� ������� ����������� ��� ����� �������. <br><br> 
			���������� ��������� ��������� ������� ����� ������� �������, ��������: 
			<br> <i>���������� ���������� ��� �������</i><br> �� ������ ������� 
			����� ������� ��������, �� ������� ����������� ���� ����� 
			&quot;����������&quot; � &quot;����������&quot;, ���� ����� 
			&quot;�������&quot;.<br><br> <i>���������� ���������� �� �������</i><br> 
			�� ������ ������� ����� ������� ��������, �� ������� ����������� ���� 
			����� &quot;����������&quot; � &quot;����������&quot;, �� �� ����������� 
			����� &quot;�������&quot;.<br> �� ������ ������������ ������ ��� 
			���������� ����� ������� ��������.<br><br> <b>���������� ���������:</b> 
			<table border="0" cellpadding="5">
				<tr>
					<td align="center" valign="top"><font class="text">��������</font></td>
					<td valign="top"><font class="text">��������</font></td>
					<td><font class="text">��������</font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">�</font></td>
					<td valign="top"><font class="text">and, &, +</font></td>
					<td><font class="text">�������� <i>���������� &quot;�&quot;</i> ���������������, ��� ����� ��������: ������ &quot;���������� ����������&quot; ��������� ������������ ������� &quot;���������� � ����������&quot;.</font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">���</font></td>
					<td valign="top"><font class="text">or, |</font></td>
					<td><font class="text">�������� <i>���������� &quot;���&quot;</i> ��������� ������ ������, ���������� ���� �� ���� �� ���������. </font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">��</font></td>
					<td valign="top"><font class="text">not, ~</font></td>
					<td><font class="text">�������� <i>���������� &quot;��&quot;</i> ������������ ����� �������, �� ���������� �����, ��������� ����� ���������. </font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">( )</font></td>
					<td valign="top"><font class="text">&nbsp;</font></td>
					<td><font class="text"><i>������� ������</i> ������ ������� �������� ���������� ����������. </font></td>
				</tr>
			</table>
			</font>			
			<?
		else:
			$obSearch->NavStart(20, false);
			$obSearch->NavPrint("���������� ������");
			?>
			<br><br>
			<?
			$bEmptyFlag = True;
			while ($arResult = $obSearch->Fetch()):
				$bEmptyFlag = False;
				?>
				<font class="text">
				<a href="<?echo $arResult["URL"]?>"><?echo $arResult["TITLE_FORMATED"]?></a><br>
				<?echo $arResult["BODY_FORMATED"]?>
				<hr size="1">
				</font>
				<?
			endwhile;

			$obSearch->NavPrint("���������� ������");

			if ($bEmptyFlag)
			{
				?>
				<font class="text">
				������ �� �������. ���������� ����������������� ������.
				</font>
				<?
			}
		endif;
	endif;
else:
	?><font class="text">������ ������ �� ����������.</font><?
endif;

//*******************************************************
else:
	?>
	<font class="text"><b>������ ������ �� ����������</b></font>
	<?
endif;
?>