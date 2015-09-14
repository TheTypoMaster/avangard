<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (!empty($arResult)): ?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="w_50"></td>
			<? $kolvo = count($arResult);
			$b = 0; ?>
		<?foreach($arResult as $arItem):?>
			<?if($arItem["PERMISSION"]>"D"){$b++;?>
				<td class="f_m_<?=($b < $kolvo ? 1 : 2)?> menu_item">
				<?if(!($arItem["SELECTED"])):?>
					<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
				<?else:?>
					<?=$arItem["TEXT"]?>
				<?endif?>
				<?if($arItem["PARAMS"]["hover_win"]!=""){?>
					<div class="bigwindow">
						<div class="closew"></div>
						<?=$arItem["PARAMS"]["hover_win"]?>
					</div>
				<?}?>
				</td>
			<?}?>
		<?endforeach?>
			<td class="w_50"></td>
		</tr>
	</table>
<?endif?>