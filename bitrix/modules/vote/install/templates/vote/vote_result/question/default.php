<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// template for displaying color bar diagram
// free user answers are included in total diagram

$GLOBALS["APPLICATION"]->SetTemplateCSS("vote/vote.css");
if (CModule::IncludeModule("vote")) :

	IncludeTemplateLangFile(__FILE__);

?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr>
		<? if (intval($arQuestion["IMAGE_ID"])>0) : ?>
		<td valign="center" width="0%"><?echo ShowImage($arQuestion["IMAGE_ID"], 50, 50, "hspace='0' vspace='0' align='left' border='0'", "", true, GetMessage("VOTE_ENLARGE"));?></td>
		<? endif; ?>
		<td valign="center" width="100%" <? if (intval($arQuestion["IMAGE_ID"])<=0) echo "colspan='2'" ?>><font class="text"><b><?=$arQuestion["QUESTION"]?></b></font></td>
	</tr>
	<tr>
		<td colspan="2" width="100%">
			<table cellspacing="0" cellpadding="2" width="100%">
				<?
				$a_total = 0;
				$max = 0;
				//$sum = $arVote["COUNTER"];
				$sum = 0;
				$arCounters = array();
				if (is_array($arAnswers[$QUESTION_ID]))
					reset($arAnswers[$QUESTION_ID]);
				else
					$arAnswers[$QUESTION_ID]=array();

				// calculating the sum and maximum value
				while (list($key,$arAnswer)=each($arAnswers[$QUESTION_ID]))
				{
					if (strlen($arAnswer["COLOR"])<=0) $a_total++;
					if ($arAnswer["FIELD_TYPE"]=="4" or $arAnswer["FIELD_TYPE"]=="5")
					{
						if (is_array($arGroupAnswers[$arAnswer["ID"]]))
						{
							reset($arGroupAnswers[$arAnswer["ID"]]);
							while (list(,$arM)=each($arGroupAnswers[$arAnswer["ID"]])) 
							{
								$arCounters[] = $arM["COUNTER"];
								$sum = $sum + intval($arM["COUNTER"]);
							}
						}
					}
					else 
					{
						$arCounters[] = $arAnswer["COUNTER"];
						$sum = $sum + intval($arAnswer["COUNTER"]);
					}
				}
				$max = count($arCounters)>0 ? max($arCounters) : 0;
				$i = 0;
				$max_width = 70; // maximum width in percents
				$max_relation = ($max*100)/$max_width; 
				// building a diagram
				$def_color = "";

				$arrAnswers	= $arAnswers[$QUESTION_ID];

				if (!function_exists("_default_vote_sort"))
				{
					// answers sorting
					function _default_vote_sort($ar1, $ar2)
					{
						global $find_data_type;
						if ($ar1["COUNTER"]<$ar2["COUNTER"]) return 1;
						if ($ar1["COUNTER"]>$ar2["COUNTER"]) return -1;
						return 0;
					}
				}
				uasort($arrAnswers, "_default_vote_sort");
				
				reset($arrAnswers);
				while (list(,$arAnswer)=each($arrAnswers)) :
					if ($max_relation>0)
						$w = round(($arAnswer["COUNTER"]*100)/$max_relation);
					if ($sum>0)
						$q = number_format(($arAnswer["COUNTER"]*100)/$sum, 2, ',', '');

					if ($arAnswer["FIELD_TYPE"]!="4" and $arAnswer["FIELD_TYPE"]!="5") :

					if (strlen($arAnswer["COLOR"])<=0) 
					{
						GetNextColor($color, $def_color, $a_total, "0066CC", "FFFF00");
					}
					else $color = $arAnswer["COLOR"];
				?>
				<tr>
					<td width="30%" valign="top"><font class="smalltext"><?=$arAnswer["MESSAGE"]?></font></td>
					<td width="70%" valign="top" nowrap><table cellspacing=0 cellpadding=0 width="100%" border=0>
							<tr>
								<td width="<?=$max_width?>%"><table border=0 align="left" cellspacing=0 cellpadding=0 width="100%">
									<?$width=($w==0) ? 0 : $w;?>
									<tr>
										<td width="<?=$width."%"?>" style="background-color: <?=$color?>"><font class="smalltext">&nbsp;</font></td>
										<td width="<?=(100-$width)."%"?>" nowrap><font class="smalltext">&nbsp;&nbsp;<?echo $q."%&nbsp;(".$arAnswer["COUNTER"].")"?></font></td>
									</tr></table></td>
							</tr>
						</table>
					</td>
				</tr>
				<? 
					endif;

					if (($arAnswer["FIELD_TYPE"]=="4" or $arAnswer["FIELD_TYPE"]=="5") and is_array($arGroupAnswers[$arAnswer["ID"]])) :

						reset($arGroupAnswers[$arAnswer["ID"]]);
						$u_total = count($arGroupAnswers[$arAnswer["ID"]]);
						while (list(,$arM)=each($arGroupAnswers[$arAnswer["ID"]])) :
							if ($max_relation>0)
								$w = round(($arM["COUNTER"]*100)/$max_relation);
							if ($sum>0)
							{
								$q = ($arM["COUNTER"]*100)/$sum;
//								if ($q<1) continue;
								$q = number_format($q, 2, ',', '');
							}
							GetNextColor($color1, $color1, $u_total, "66CC00" ,"FFFF99");						
				?>
				<tr>
					<td width="30%" valign="top"><font class="smalltext"><?=nl2br(TruncateText($arM["MESSAGE"],100))."&nbsp;"?></font></td>
					<td width="70%" valign="top" nowrap><table cellspacing=0 cellpadding=0 width="100%">
							<tr>
								<td width="<?=$max_width?>%"><table align="left" cellspacing=0 cellpadding=0 width="<?echo ($w==0) ? "0%" : $w."%"?>"><tr><td width="100%" style="background-color: <?=$color1?>"><font class="smalltext">&nbsp;</font></td></tr></table><font class="smalltext">&nbsp;&nbsp;<?echo $q."%&nbsp;(".$arM["COUNTER"].")"?></font></td>
							</tr>
						</table>
					</td>
				</tr>
						<? 
						endwhile; 
					endif;
				endwhile; 
				?>
			</table>
		</td>
	</tr>
</table>
<?endif;?>