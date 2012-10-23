<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile("blog/blog/calendar.php", 
	Array(
		"BLOG_URL" => $arFolders[0],
		"MONTH" => $MONTH,
		"YEAR" => $YEAR,
		"DAY" => $DAY,
		"CACHE_TIME" => 0
	)
);
*/
if (CModule::IncludeModule("blog")):
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

CBlogUser::SetLastVisit();

$BLOG_URL = Trim($BLOG_URL);
$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);
$is404 = ($is404=='N') ? false: true;

$today = time();
$todayYear = IntVal(date("Y", $today));
$todayMonth = IntVal(date("n", $today));
$todayDay = IntVal(date("j", $today));

$MONTH = IntVal($MONTH);
if ($MONTH < 1 || $MONTH > 12)
	$MONTH = $todayMonth;

$YEAR = IntVal($YEAR);
if ($YEAR < 1990 || $YEAR > 2020)
	$YEAR = $todayYear;

$DAY = IntVal($DAY);
$bSelectDay = (($DAY > 0) ? True : False);
if ($DAY < 1 || $DAY > 31)
	$DAY = $todayDay;

if ($YEAR > $todayYear || $YEAR == $todayYear && $MONTH > $todayMonth)
{
	$MONTH = $todayMonth;
	$YEAR = $todayYear;
}

$CACHE_TIME = IntVal($CACHE_TIME);

if (StrLen($BLOG_URL) > 0)
{
	$dbBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID"));
	if ($arBlog = $dbBlog->Fetch())
	{
		$arUserGroups = CBlogUser::GetUserGroups(($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0), $arBlog["ID"], "Y", BLOG_BY_USER_ID);
		$numUserGroups = count($arUserGroups);
		for ($i = 0; $i < $numUserGroups - 1; $i++)
		{
			for ($j = $i + 1; $j < $numUserGroups; $j++)
			{
				if ($arUserGroups[$i] > $arUserGroups[$j])
				{
					$tmpGroup = $arUserGroups[$i];
					$arUserGroups[$i] = $arUserGroups[$j];
					$arUserGroups[$j] = $tmpGroup;
				}
			}
		}

		$strUserGroups = "";
		for ($i = 0; $i < $numUserGroups; $i++)
			$strUserGroups .= "_".$arUserGroups[$i];

		$cache = new CPHPCache;
		$cache_id = "blog_calendar_".$BLOG_URL."_".$MONTH."_".$YEAR.$strUserGroups;
		$cache_path = "/".SITE_ID."/blog/".$BLOG_URL."/calendar/";

		if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
		{
			$cache->Output();
		}
		else
		{
			if ($CACHE_TIME > 0)
				$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

			$dateFrom = mktime(0, 0, 0, $MONTH, 1, $YEAR);
			$dateTo = mktime(0, 0, 0, $MONTH + 1, 1, $YEAR);

			$arDates = CBlogPost::GetListCalendar($arBlog["ID"], $YEAR, $MONTH, false);

			$arDays = array();
			for ($i = 0; $i < count($arDates); $i++)
				$arDays[IntVal($arDates[$i]["DAY"])] = true;

			$currentYear = $YEAR;
			$currentMonth = $MONTH;

			$lastMonthYear = $YEAR;
			$lastMonth = $MONTH - 1;
			if ($lastMonth < 1)
			{
				$lastMonth = 12;
				$lastMonthYear = $lastMonthYear - 1;
			}

			$nextMonthYear = $YEAR;
			$nextMonth = $MONTH + 1;
			if ($nextMonth > 12)
			{
				$nextMonth = 1;
				$nextMonthYear = $nextMonthYear + 1;
			}
			if($is404)
				$urlToBlog = CBlog::PreparePath($BLOG_URL, False)."?";
			else
				$urlToBlog = CBlog::PreparePath($BLOG_URL, SITE_ID, $is404)."&";
			?>
			<table border="0" cellspacing="0" cellpadding="0" class="blogCalBack">
			<tr>
				<td><img src="/bitrix/templates/.default/blog/images/calendar/l_t_c.gif" width="2" height="2" border="0"></td>
				<td background="/bitrix/templates/.default/blog/images/calendar/t_b.gif"><img src="/bitrix/images/1.gif" width="2" height="2" border="0"></td>
				<td><img src="/bitrix/templates/.default/blog/images/calendar/r_t_c.gif" width="2" height="2" border="0"></td>
			</tr>
			<tr>
				<td background="/bitrix/templates/.default/blog/images/calendar/l_b.gif"><img src="/bitrix/images/1.gif" width="2" height="2" border="0"></td>
				<td style="padding:7px;">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="0%" align="left" class="blogCalTitle"><?
							if ($currentYear >= 1990):
								?><a title="<?=GetMessage("BLOG_BLOG_CLNDR_P_M")?>" style="text-decoration:none" href="<?=$urlToBlog?>YEAR=<?= $lastMonthYear ?>&amp;MONTH=<?= $lastMonth ?><?//= (($s = DeleteParam(array("MONTH", "YEAR", "DAY"))) <> "" ? "&amp;$s":"") ?>">&laquo;&nbsp;&nbsp;</a><?
							else:
								?><font class="blogCalDisable">&laquo;&nbsp;&nbsp;</font><?
							endif;
						?></td>
						<td width="100%" align="center" class="blogCalTitle"><b><?= GetMessage("BLOG_BLOG_CLNDR_M_".$currentMonth)." ".$currentYear ?></b></td>
						<td width="0%" align="right" class="blogCalTitle"><?
							if ($currentYear < $todayYear || $currentYear == $todayYear && $currentMonth < $todayMonth):
								?><a title="<?=GetMessage("BLOG_BLOG_CLNDR_N_M")?>" style="text-decoration:none" href="<?= $urlToBlog ?>YEAR=<?= $nextMonthYear ?>&amp;MONTH=<?= $nextMonth ?><?//= (($s = DeleteParam(array("MONTH", "YEAR", "DAY"))) <> "" ? "&amp;$s" : "") ?>">&nbsp;&nbsp;&raquo;</a><?
							else:
								?><font class="blogCalDisable">&nbsp;&nbsp;&raquo;</font><?
							endif;
						?></td>
					</tr>
				</table>
				<div style="padding-top:2px;">
				<div style="height:1px; overflow:hidden; background-color:#D6D6D6;"></div>
				</div>

				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<!--<td><img src="/images/1.gif" alt="" width="4" height="1" border="0"></td>!-->
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_1")?>&nbsp;</td>
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_2")?>&nbsp;</td>
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_3")?>&nbsp;</td>
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_4")?>&nbsp;</td>
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_5")?>&nbsp;</td>
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_6")?>&nbsp;</td>
						<td align="center" class="blogCalWeek">&nbsp;<?=GetMessage("BLOG_BLOG_CLNDR_D_7")?>&nbsp;</td>
						<!--<td><img src="/images/1.gif" alt="" width="1" height="1" border="0"></td>!-->
					</tr>
					<tr>
						<td colspan="7"><img src="/bitrix/images/1.gif" width="1" height="3" border="0"></td>
					</tr>
					<?
					$firstDate = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
					$firstDay = IntVal(date("w", $firstDate) - 1);
					if ($firstDay == -1)
						$firstDay = 6;

					$bBreak = false;
					for ($i = 0; $i < 6; $i++)
					{
						if($i<>0)
							echo "<tr><td colspan=\"7\" style=\"padding-top:2px; padding-bottom:2px;\"><div style=\"height:1px; overflow:hidden; background-color:#D6D6D6;\"></div></td></tr>";
						echo "<tr>";
						$row = $i * 7;
						for ($j = 0; $j < 7; $j++)
						{
							$date = mktime(0, 0, 0, $currentMonth, 1 - $firstDay + $row + $j, $currentYear);
							$y = intval(date("Y", $date));
							$m = intval(date("n", $date));
							$d = intval(date("j", $date));
							if ($i > 0 && $d == 1)
								$bBreak = true;

							$defaultClassName = "blogCalDefault";
							if ($bSelectDay && $d == $DAY && $m == $MONTH && $y == $YEAR && $row + $j + 1 > $firstDay && !$bBreak)
								$defaultClassName = "blogCalSelected";
							elseif ($d == $todayDay && $m == $todayMonth && $y == $todayYear && $row + $j + 1 > $firstDay && !$bBreak)
								$defaultClassName = "blogCalToday";
							elseif ($j == 5 || $j == 6)
								$defaultClassName = "blogCalWeekend";

							//echo "<td align=\"center\" class='".$defaultClassName."' onMouseOver=\"this.className='blogCalHighlight'\" onMouseOut=\"this.className='".$defaultClassName."'\">";
							if ($row + $j + 1 > $firstDay && !$bBreak)
								echo "<td align=\"center\" class='".$defaultClassName."' onMouseOver=\"this.className='blogCalHighlight'\" onMouseOut=\"this.className='".$defaultClassName."'\">".($arDays[$d] == true ? 
										"<a href=\"".$urlToBlog."YEAR=".$y."&amp;MONTH=".$m."&amp;DAY=".$d."\">".$d."</a>" 
										: $d);
							else
								echo "<td align=\"center\">&nbsp;";
							echo "</td>";
						}
						echo "</tr>";

						if ($bBreak)
							break;
					}
					?>
				</table>
				</td>
				<td background="/bitrix/templates/.default/blog/images/calendar/r_b.gif"><img src="/bitrix/images/1.gif" width="2" height="2" border="0"></td>
			</tr>
			<tr>
				<td><img src="/bitrix/templates/.default/blog/images/calendar/l_b_c.gif" width="2" height="2" border="0"></td>
				<td background="/bitrix/templates/.default/blog/images/calendar/b_b.gif"><img src="/bitrix/images/1.gif" width="2" height="2" border="0"></td>
				<td><img src="/bitrix/templates/.default/blog/images/calendar/r_b_c.gif" width="2" height="2" border="0"></td>
			</tr>
			</table>
			<?
			if ($CACHE_TIME > 0)
				$cache->EndDataCache(array());
		}
	}
}
//*******************************************************
endif;
?>