<?
IncludeTemplateLangFile(__FILE__);
$APPLICATION->SetTemplateCSS("iblock/news/calendar.css");

$bNews = ($TYPE=="NEWS")? true : false;
$bshowYear = ($SHOW_YEAR=="Y") ? true: false;
$bShowTime = ($SHOW_TIME=="Y") ? true: false;
$month = $_REQUEST[$month_var_name]; 
$year = $_REQUEST[$year_var_name];

$title_len = (IntVal($TITLE_LEN)>0) ? IntVal($TITLE_LEN) : 0;
$news_count = (IntVal($NEWS_COUNT)>0) ? IntVal($NEWS_COUNT) : false;
$bRewriteTitle = ($REWRITE_TITLE=="Y") ? true: false;
$bShowCurDate = ($SHOW_CURRENT_DATE=="Y") ? true : false;
$bShowMonthList = ($SHOW_MONTH_LIST=="Y") ? true : false;

$IBLOCK_ID = IntVal($IBLOCK_ID);
$week_start = IntVal($week_start);
$DATE_FIELD = trim($DATE_FIELD);
if($bNews)
	$DATE_FIELD = "DATE_ACTIVE_FROM";


$today = time();
$todayYear = IntVal(date("Y", $today));
$todayMonth = IntVal(date("n", $today));
$todayDay = IntVal(date("j", $today));

$currentMonth = (IntVal($month)>0)?IntVal($month):IntVal(date("n", $today));
$currentYear = (IntVal($year)>0)?IntVal($year):IntVal(date("Y", $today));
if($bRewriteTitle)
	$APPLICATION->SetTitle(GetMessage("IBL_NEWS_CAL_M_".date("n", mktime(0,0,0,$currentMonth)))." ".$currentYear);

$CACHE_TIME = IntVal($CACHE_TIME);
$cache_path = "/".SITE_ID."/iblock/news/calendar/";

$cache = new CPHPCache;
$cache_id = "iblock_news_calendar_".serialize($arParams);

if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
{
$cache->Output();
}
else
{
	if ($CACHE_TIME > 0)
		$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);
	
	CModule::IncludeModule("iblock");

	$arWDays = Array(
			GetMessage("IBL_NEWS_CAL_D_0"), 
			GetMessage("IBL_NEWS_CAL_D_1"),
			GetMessage("IBL_NEWS_CAL_D_2"),
			GetMessage("IBL_NEWS_CAL_D_3"),
			GetMessage("IBL_NEWS_CAL_D_4"),
			GetMessage("IBL_NEWS_CAL_D_5"),
			GetMessage("IBL_NEWS_CAL_D_6")
		);
	if($week_start == 1)
	{
		$arWDays[] = $arWDays[0];
		unset($arWDays[0]);
	}

	$arFilter = Array(
			"ACTIVE" => "Y",
			">=".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth,1,$currentYear)),
			"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth+1,1,$currentYear)),
			"IBLOCK_ID" => $IBLOCK_ID
		);
	if($bNews)
		$arFilter["<".$DATE_FIELD] = date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$todayMonth,$todayDay+1,$todayYear));
	
	$arSelectedFields = Array("ACTIVE", $DATE_FIELD, "ID", "IBLOCK_ID", "SITE_ID", "DETAIL_PAGE_URL", "NAME", "LANG_DIR", "SORT", "IBLOCK_TYPE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE");

	$dbItems = CIBlockElement::GetList(array($DATE_FIELD=>"ASC", "ID"=>"ASC"), $arFilter, false, false, $arSelectedFields);
	while($arItem = $dbItems->GetNext())
	{
		$arDay[ConvertDateTime($arItem[$DATE_FIELD], CLang::GetDateFormat("SHORT"))][] = $arItem;
	}

	if(empty($arDay))
	{
		$arSelectedFields = Array("ACTIVE", $DATE_FIELD, "ID", "IBLOCK_ID", "SORT");
		
		if(mktime(0,0,0, $currentMonth, 1, $currentYear) > mktime(0,0,0, $todayMonth, 1, $todayYear))
		{
			$arFilter = Array(
				"ACTIVE" => "Y",
				"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth,1,$currentYear)),
				"IBLOCK_ID" => $IBLOCK_ID
			);
			$dbItems = CIBlockElement::GetList(array($DATE_FIELD=>"DESC", "ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), $arSelectedFields);
		}
		else
		{
			$arFilter = Array(
				"ACTIVE" => "Y",
				">".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth,1,$currentYear)),
				"IBLOCK_ID" => $IBLOCK_ID
			);
			$dbItems = CIBlockElement::GetList(array($DATE_FIELD=>"ASC", "ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), $arSelectedFields);
		}
		
		if($arItem = $dbItems -> Fetch())
		{
			$currentMonth = IntVal(ConvertDateTime($arItem[$DATE_FIELD], "MM"));
			$currentYear = IntVal(ConvertDateTime($arItem[$DATE_FIELD], "YYYY"));
			if($bRewriteTitle)
				$APPLICATION->SetTitle(GetMessage("IBL_NEWS_CAL_M_".date("n", mktime(0,0,0,$currentMonth)))." ".$currentYear);

			$arFilter = Array(
					"ACTIVE" => "Y",
					">=".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth,1,$currentYear)),
					"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth+1,1,$currentYear)),
					"IBLOCK_ID" => $IBLOCK_ID
				);
			if($bNews)
				$arFilter["<".$DATE_FIELD] = date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$todayMonth,$todayDay+1,$todayYear));
			
			$arSelectedFields = Array("ACTIVE", $DATE_FIELD, "ID", "IBLOCK_ID", "SITE_ID", "DETAIL_PAGE_URL", "NAME", "LANG_DIR", "SORT", "IBLOCK_TYPE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE");

			$dbItems = CIBlockElement::GetList(array($DATE_FIELD=>"ASC", "ID"=>"ASC"), $arFilter, false, false, $arSelectedFields);
			while($arItem = $dbItems->GetNext())
			{
				$arDay[ConvertDateTime($arItem[$DATE_FIELD], CLang::GetDateFormat("SHORT"))][] = $arItem;
			}
		}
	}
	
	$firstDate = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
	$firstDay = IntVal(date("w", $firstDate));
	if($week_start == "1")
		$firstDay--;
	if ($firstDay == -1)
		$firstDay = 6;

	$bPrevM = false;
	$bNextM = false;
	$bPrevY = false;
	$bNextY = false;
	if($bShowCurDate)
		echo '<p align="right" class="NewsCalMonthNav"><b>'.GetMessage("IBL_NEWS_CAL_M_".$currentMonth).' '.$currentYear.'</b></p>';

	if(!($bNews && $currentMonth > $todayMonths))	
	{
		$arFilter = Array(
				"ACTIVE" => "Y",
				">=".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth-1,1,$currentYear)),
				"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth,1,$currentYear)),
				"IBLOCK_ID" => $IBLOCK_ID
			);
		$dbItems = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), Array("ID", "ACTIVE", "IBLOCK_ID", $DATE_FIELD));
		if($arItem = $dbItems->GetNext())
			$bPrevM = true;
		
		if($bshowYear)
		{
			$arFilter = Array(
					"ACTIVE" => "Y",
					">=".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,1,1,$currentYear-1)),
					"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,1,1,$currentYear)),
					"IBLOCK_ID" => $IBLOCK_ID
				);
			$dbItems = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), Array("ID", "ACTIVE", "IBLOCK_ID", $DATE_FIELD));
			if($arItem = $dbItems->GetNext())
				$bPrevY = true;
		}

	}
	
	if(!$bNews || ($bNews && $currentMonth < $todayMonth  && $currentYear < $todayYear))
	{
		$arFilter = Array(
				"ACTIVE" => "Y",
				">=".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth+1,1,$currentYear)),
				"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$currentMonth+2,1,$currentYear)),
				"IBLOCK_ID" => $IBLOCK_ID
			);
		$dbItems = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), Array("ID", "ACTIVE", "IBLOCK_ID", $DATE_FIELD));
		if($arItem = $dbItems->GetNext())
			$bNextM = true;

		if($bshowYear)
		{
			$arFilter = Array(
					"ACTIVE" => "Y",
					">=".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,1,1,$currentYear+1)),
					"<".$DATE_FIELD => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,1,1,$currentYear+2)),
					"IBLOCK_ID" => $IBLOCK_ID
				);
			$dbItems = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), Array("ID", "ACTIVE", "IBLOCK_ID", $DATE_FIELD));
			if($arItem = $dbItems->GetNext())
				$bNextY = true;
		}
	}
	echo '<table width="100% border="0" cellspacing="0" cellpadding="0">';
	echo '<tr>';
	echo '<td class="NewsCalMonthNav" align="left">';
	if($bPrevM)
	{
		$navM = date("n", mktime(0,0,0,$currentMonth-1, 1, $currentYear));
		$navY = date("Y", mktime(0,0,0,$currentMonth-1, 1, $currentYear));
		echo '<a href="'.$APPLICATION->GetCurPageParam($month_var_name."=".$navM."&".$year_var_name."=".$navY, Array($month_var_name, $year_var_name)).'" title="'.GetMessage("IBL_NEWS_CAL_M_".$navM).'">'.GetMessage("IBL_NEWS_CAL_PR_M").'</a>';
	}

	if($bPrevM && $bNextM && !$bShowMonthList)
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
	if($bShowMonthList)
	{
		?>
		&nbsp;&nbsp;<select onChange="b_result()" name="MONTH_SELECT" id="month_sel">
            <?for($i=1;$i<=12;$i++)
			{?>
				<option value="<?=$APPLICATION->GetCurPageParam($month_var_name."=".$i."&".$year_var_name."=".$currentYear, Array($month_var_name, $year_var_name))?>" <?if($currentMonth == $i) echo "selected";?>><?=GetMessage("IBL_NEWS_CAL_M_".$i)?></option>
            <?
			}?>
        </select>&nbsp;&nbsp;
		<?
	}

	if($bNextM)
	{
		$navM = date("n", mktime(0,0,0,$currentMonth+1, 1, $currentYear));
		$navY = date("Y", mktime(0,0,0,$currentMonth+1, 1, $currentYear));
		echo '<a href="'.$APPLICATION->GetCurPageParam($month_var_name."=".$navM."&".$year_var_name."=".$navY, Array($month_var_name, $year_var_name)).'" title="'.GetMessage("IBL_NEWS_CAL_M_".$navM).'">'.GetMessage("IBL_NEWS_CAL_N_M").'</a>';
	}

	echo '</td>';
	
	if($bshowYear)
	{
		echo '<td class="NewsCalMonthNav" align="right">';
		if($bPrevY)
		{
			$navM = date("n", mktime(0,0,0,$currentMonth, 1, $currentYear));
			$navY = date("Y", mktime(0,0,0,$currentMonth, 1, $currentYear-1));
			echo '<a href="'.$APPLICATION->GetCurPageParam($month_var_name."=".$navM."&".$year_var_name."=".$navY, Array($month_var_name, $year_var_name)).'" title="'.$navY.'">'.GetMessage("IBL_NEWS_CAL_PR_Y").'</a>';
		}

		if($bPrevY && $bNextY)
			echo '&nbsp;&nbsp;|&nbsp;&nbsp;';

		if($bNextY)
		{
			$navM = date("n", mktime(0,0,0,$currentMonth, 1, $currentYear));
			$navY = date("Y", mktime(0,0,0,$currentMonth, 1, $currentYear+1));
			echo '<a href="'.$APPLICATION->GetCurPageParam($month_var_name."=".$navM."&".$year_var_name."=".$navY, Array($month_var_name, $year_var_name)).'" title="'.$navY.'">'.GetMessage("IBL_NEWS_CAL_N_Y").'</a>';
		}
		echo '</td>';
	}
	echo '</tr>';
	echo '</table><br>';
	
	echo '<table cellspacing="0" cellpadding="0" border="0" width="100%" class="NewsCalTableBorder"><tr><td>';
	echo "<table width='100%' border='0' cellspacing='1' cellpadding='4' class='NewsCalTable'>";
	echo "<tr>";
	foreach($arWDays as $WDay)
		echo "<td class='NewsCalHeader'>".$WDay."</td>";
	echo "</tr>";

	$bBreak = false;
	for ($i = 0; $i < 6; $i++)
	{
		
		$row = $i * 7;
		for ($j = 0; $j < 7; $j++)
		{
			$date = mktime(0, 0, 0, $currentMonth, 1 - $firstDay + $row + $j, $currentYear);
			$y = intval(date("Y", $date));
			$m = intval(date("n", $date));
			$d = intval(date("j", $date));
			$itm = date("w", $date);

			if ($i > 0 && $j == 0 && $currentMonth != $m)
			{
				$bBreak = true;
				break;
			}

			$dayClassName = "NewsCalDay";
			if ($d == $todayDay && $m == $todayMonth && $y == $todayYear && $row + $j + 1 > $firstDay && !$bBreak)
				$defaultClassName = "NewsCalToday";
			elseif ($currentMonth != $m)
			{
				$defaultClassName = "NewsCalOtherMonth";
				$dayClassName = "NewsCalDayOther";
			}
			elseif ($itm == 0 || $itm == 6)
				$defaultClassName = "NewsCalWeekend";
			else
				$defaultClassName = "NewsCalDefault";

			if($j==0)
				echo "<tr>";
			echo "<td align=\"left\" valign=\"top\" class='".$defaultClassName."' width=\"14%\"><font class=\"".$dayClassName."\"><b>".$d."</b></font>";
			$tmpDate = date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$m,$d,$y));
			if(is_set($arDay[$tmpDate]))
			{
				$nn = 0;
				foreach($arDay[$tmpDate] as $dayNews)
				{
					$nn++;
					$eTime = "";
					$arTime = Array();
					if($bShowTime)
					{
						$arTime = ParseDateTime($dayNews["DATE_ACTIVE_FROM"], CLang::GetDateFormat("FULL"));
						if(IntVal($arTime["HH"])>0 || $arTime["MI"]>0)
							$eTime = $arTime["HH"].":".$arTime["MI"]."&nbsp;";
					}
					if($dayNews["PREVIEW_TEXT_TYPE"] == "text" && strlen($dayNews["PREVIEW_TEXT"])>0)
						$sTitle = TruncateText($dayNews["PREVIEW_TEXT"], 100);
					else
						$sTitle = $dayNews["NAME"];
					if(IntVal($title_len)>0)
						$title = TruncateText($dayNews["NAME"], $title_len);
					else
						$title = $dayNews["NAME"];
					echo "<div class=\"NewsCalNews\" style=\"padding-top:5px;\">".$eTime."<a href=\"".$dayNews["DETAIL_PAGE_URL"]."\" title=\"".$sTitle."\">".$title."</a></div>";
					if($news_count && $news_count<=$nn)
						break;
				}
			}

			echo "</td>";
			if($j == 6)
				echo "</tr>";
		}
		if ($bBreak)
			break;
	}
	echo "</table>";
	echo "</td></tr></table>";

	if ($CACHE_TIME > 0)
		$cache->EndDataCache();

}
if($bShowMonthList)
{?>
<SCRIPT language=javascript>
function b_result()
{
	var idx=document.getElementById("month_sel").selectedIndex;
	window.document.location.href=document.getElementById("month_sel").options[idx].value;
}
</SCRIPT>
<?}?>