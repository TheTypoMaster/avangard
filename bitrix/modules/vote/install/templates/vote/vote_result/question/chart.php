<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// template for displaying pie chart

$GLOBALS["APPLICATION"]->SetTemplateCSS("vote/vote.css");
if (CModule::IncludeModule("vote")) :

	IncludeTemplateLangFile(__FILE__);
	global $arrSaveColor;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");
	$arrAnswers = $arAnswers[$QUESTION_ID];

?>
<table cellspacing="0" cellpadding="10" width="100%">
	<tr>
		<? if (intval($arQuestion["IMAGE_ID"])>0) : ?>
		<td valign="center" width="0%"><?echo ShowImage($arQuestion["IMAGE_ID"], 50, 50, "hspace='0' vspace='0' align='left' border='0'", "", true, GetMessage("VOTE_ENLARGE"));?></td>
		<? endif; ?>
		<td valign="center" width="100%" <? if (intval($arQuestion["IMAGE_ID"])<=0) echo "colspan='2'" ?>><font class="text"><b><?=$arQuestion["QUESTION"]?></b></font></td>
	</tr>
	<tr>
		<?
		$file = str_replace("\\", "/", dirname(__FILE__));
		$file = str_replace("//", "/", $file);
		$root = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
		$root = str_replace("//", "/", $root);
		$file = eregi_replace($root, "", $file);
		$file = "/".$file."/chart/chart.php";
		$file = str_replace("\\", "/", $file);
		$file = str_replace("//", "/", $file);

		$DIAMETER = "150";
		?>
		<td width="0%" valign="top"><img width="<?=$DIAMETER?>" height="<?=$DIAMETER?>" src="/bitrix/tools/vote_chart.php?file=<?=urlencode($file)?>&qid=<?echo $QUESTION_ID?>&dm=<?=$DIAMETER?>"></td>
		<td width="100%" valign="top"><table cellspacing=0 cellpadding=1 width="100%">
				<?
				if (!is_array($arrAnswers))
					$arrAnswers = Array();

				usort($arrAnswers, create_function('$v1,$v2','if ($v1[\'COUNTER\']>$v2[\'COUNTER\']) return -1; elseif ($v1[\'COUNTER\']<$v2[\'COUNTER\']) return 1;'));
				$sum = $total = 0;
				$arr = array();
				while (list(,$arAnswer) = each($arrAnswers))	
				{
					$total++;
					$sum += $arAnswer["COUNTER"];
					$arr[] = array(
						"ORIGINAL_COLOR"	=> TrimEx($arAnswer["COLOR"],"#"), 
						"COUNTER"			=> $arAnswer["COUNTER"], 
						"MESSAGE"			=> $arAnswer["MESSAGE"]
					);
				}
				$color = "";
				reset($arr);
				while (list($key,$ar)=each($arr))
				{
					$ar["PROCENT"] = ($sum>0) ? number_format(($ar["COUNTER"]*100)/$sum, 2, ',', '') : 0;
					$color = GetNextRGB($color, $total);
					$ar["SHOW_COLOR"] = (strlen($ar["ORIGINAL_COLOR"])<=0) ? $color : $ar["ORIGINAL_COLOR"];
					$arr[$key] = $ar;
				}
				reset($arr);
				foreach($arr as $ar): 
				?>
				<tr>
					<td  valign="top"><table cellspacing="0" cellpadding="0">
						<tr>
							<td style="background-color: <?=$ar["SHOW_COLOR"]?>"><img src="/bitrix/images/1.gif" width="10" height="10" border=0 alt=""></td>
						</tr>
					</table></td>
					<td nowrap valign="top"><font class="smalltext">&nbsp;&nbsp;<?echo $ar["PROCENT"]."%"?></font></td>
					<td nowrap valign="top"><font class="smalltext"><?echo "(".$ar["COUNTER"].")"?>&nbsp;&nbsp;</font></td>
					<td width="100%"><font class="smalltext"><?=$ar["MESSAGE"]?></font></td>
				</tr>
				<? endforeach; ?>
			</table></td>
	</tr>
</table>
<?endif;?>