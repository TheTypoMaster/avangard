<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div style="padding-left: 12px; margin-bottom: 10px; font-weight: 600; font-size: 14px;">
<?=$arResult[NAME]; ?>
</div>
<div class="photo-section">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<table cellpadding="0" cellspacing="0" border="0" class="data-table">
	<?foreach($arResult["ROWS"] as $arItems):?>
		<tr class="head-row" valign="top">
		<?foreach($arItems as $arItem):?>
			<?if(is_array($arItem)):?>
				<td width="<?=$arResult["TD_WIDTH"]?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					&nbsp;
						<?if(is_array($arItem["PICTURE"])):?>
							<a rel="for_child" href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>"><img border="0" src="<?=$arItem["PICTURE"]["SRC"]?>" width="<?=$arItem["PICTURE"]["WIDTH"]?>" height="<?=$arItem["PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>1" title="<?=$arItem["NAME"]?>1" /></a><br />
						<?endif?>
				</td>
			<?else:?>
				<td width="<?=$arResult["TD_WIDTH"]?>" rowspan="<?=$arResult["nRowsPerItem"]?>">
					&nbsp;
				</td>
			<?endif;?>
		<?endforeach?>
		</tr>
		<tr class="data-row">
		<?foreach($arItems as $arItem):?>
			<?if(is_array($arItem)):?>
				<th valign="top" width="<?=$arResult["TD_WIDTH"]?>" class="data-cell">
					&nbsp;
						<?=$arItem["NAME"]?><br />

				</th>
			<?endif;?>
		<?endforeach?>
		</tr>

	<?endforeach?>
</table>
</div>
<br />
<br />
<br />
<div>
<? echo $arResult[DESCRIPTION]; ?>
</div>
<br />
<br />
<br />

<script type="text/javascript" src="/bitrix/templates/redesign/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="/bitrix/templates/redesign/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="/bitrix/templates/redesign/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
$(document).ready(function() {
    $("a[rel=for_child]").fancybox();
});
</script>
