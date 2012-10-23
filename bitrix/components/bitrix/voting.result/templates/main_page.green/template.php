<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["VOTE"]) && !empty($arResult["QUESTIONS"])):?>
		<?foreach ($arResult["QUESTIONS"] as $arQuestion):?>
				<b><?=$arQuestion["QUESTION"]?></b><br /><br />
				<?foreach ($arQuestion["ANSWERS"] as $arAnswer):?>
						<?=$arAnswer["MESSAGE"]?>
						<div class="graph"><nobr class="bar" style="width: <?=(round($arAnswer["BAR_PERCENT"]))?>%;"><span><?=$arAnswer["COUNTER"]?> (<?=$arAnswer["PERCENT"]?>%)</span></nobr></div>
				<?endforeach?>
				<br />
		<?endforeach?>
<?endif?>