<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum")):
//*******************************************************

ForumSetLastVisit();
define("FORUM_MODULE_PAGE", "SEARCH");
$APPLICATION->SetTitle("Поиск по форуму");
$APPLICATION->SetTemplateCSS("forum/forum_tmpl_2/forum.css");

$APPLICATION->IncludeFile("forum/forum_tmpl_2/menu.php");

if (CModule::IncludeModule("search")):
	$q = Trim($_REQUEST["q"]);
	?>
	<form action="search.php">
	<table width="100%" border="0" cellspacing="1" cellpadding="0" class="forumborder"><tr><td>
		<table width="100%" border="0" cellspacing="1" cellpadding="1">
			<tr><td colspan="2" align="center" class="forumhead"><font class="forumheadtext"><b>Поиск</b></font></td></tr>
			<tr>
				<td class="forumbody" align="right"><font class="forumheadtext">Ключевые слова:</font></td>
				<td class="forumbody"><font class="forumbodytext">
					<input type="text" name="q" value="<?echo htmlspecialchars($q)?>" size="40">
				</font></td>
			</tr>
			<tr>
				<td class="forumbody" align="right"><font class="forumheadtext">Искать в форуме:</font></td>
				<td class="forumbody"><font class="forumbodytext">
					<select name="FORUM_ID">
						<option value="0">Все форумы</option>
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
				<input type="submit" name="s" value="Искать">
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
			<font class="text">В поисковой фразе обнаружена ошибка:</font> 
			<?echo ShowError($obSearch->error);?>
			<font class="text">Исправьте поисковую фразу и повторите поиск.</font><br><br>

			<font class="text">
			<b>Синтаксис поискового запроса:</b><br><br>
			Обычно запрос представляет из себя просто одно или несколько слов, 
			например: <br>	<i>контактная информация</i><br> По такому запросу будут 
			найдены страницы, на которых встречаются оба слова запроса. <br><br> 
			Логические операторы позволяют строить более сложные запросы, например: 
			<br> <i>контактная информация или телефон</i><br> По такому запросу 
			будут найдены страницы, на которых встречаются либо слова 
			&quot;контактная&quot; и &quot;информация&quot;, либо слово 
			&quot;телефон&quot;.<br><br> <i>контактная информация не телефон</i><br> 
			По такому запросу будут найдены страницы, на которых встречаются либо 
			слова &quot;контактная&quot; и &quot;информация&quot;, но не встречается 
			слово &quot;телефон&quot;.<br> Вы можете использовать скобки для 
			построения более сложных запросов.<br><br> <b>Логические операторы:</b> 
			<table border="0" cellpadding="5">
				<tr>
					<td align="center" valign="top"><font class="text">Оператор</font></td>
					<td valign="top"><font class="text">Синонимы</font></td>
					<td><font class="text">Описание</font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">и</font></td>
					<td valign="top"><font class="text">and, &, +</font></td>
					<td><font class="text">Оператор <i>логическое &quot;и&quot;</i> подразумевается, его можно опускать: запрос &quot;контактная информация&quot; полностью эквивалентен запросу &quot;контактная и информация&quot;.</font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">или</font></td>
					<td valign="top"><font class="text">or, |</font></td>
					<td><font class="text">Оператор <i>логическое &quot;или&quot;</i> позволяет искать товары, содержащие хотя бы один из операндов. </font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">не</font></td>
					<td valign="top"><font class="text">not, ~</font></td>
					<td><font class="text">Оператор <i>логическое &quot;не&quot;</i> ограничивает поиск страниц, не содержащих слово, указанное после оператора. </font></td>
				</tr>
				<tr>
					<td align="center" valign="top"><font class="text">( )</font></td>
					<td valign="top"><font class="text">&nbsp;</font></td>
					<td><font class="text"><i>Круглые скобки</i> задают порядок действия логических операторов. </font></td>
				</tr>
			</table>
			</font>			
			<?
		else:
			$obSearch->NavStart(20, false);
			$obSearch->NavPrint("Результаты поиска");
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

			$obSearch->NavPrint("Результаты поиска");

			if ($bEmptyFlag)
			{
				?>
				<font class="text">
				Ничего не найдено. Попробуйте переформулировать запрос.
				</font>
				<?
			}
		endif;
	endif;
else:
	?><font class="text">Модуль поиска не установлен.</font><?
endif;

//*******************************************************
else:
	?>
	<font class="text"><b>Модуль форума не установлен</b></font>
	<?
endif;
?>