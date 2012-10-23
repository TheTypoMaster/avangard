<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div>
<table width="100%">
<tbody><tr><td>
<?
if (sizeof($arResult["ITEMS"])==0) echo "<i>Нет образцов.</i>";
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<div class="textura">
<strong><?=$arItem["NAME"]?></strong><div class="img">
<a href="javascript:void(winPop('/catalog/texture/<?=$arItem["ID"]?>.html',%20'mww',%20550,%20410));"><img title="Нажмите для увеличения" alt="Обивочный материал <?=$arItem["NAME"]?>" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"></a><br>
<?
if ($_GET['key']){
echo '<a href="/basket/?key='.$_GET['key'].'&num='.$_GET[num].'&texture_id='.$arItem[ID].'">Установить</a>';
}
?>
</div>
<div style="height: 1px;"><table cellspacing="0" cellpadding="0" class="gabsinmod"><tbody><tr><td style="text-align: right;">
<nobr><span class="mini">рис.: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][PICTURE][DISPLAY_VALUE]?></span></nobr><br><nobr><span class="mini">ценовая кат.: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][PRICE_CAT][DISPLAY_VALUE]?></span></nobr><br><nobr><span class="mini">наличие: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][PRESENCE][DISPLAY_VALUE]?></span></nobr><br><nobr><span class="mini">дней на заказ: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][DAYS][DISPLAY_VALUE]?></span></nobr><br>
</td></tr></tbody></table></div>
</div>

<?endforeach;?>

</td></tr>
<tr><td>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

</td></tr>
</tbody></table>
</div>
