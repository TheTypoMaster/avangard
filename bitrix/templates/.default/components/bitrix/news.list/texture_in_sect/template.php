<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div>
<table width="100%">
<tbody><tr><td>
<?
if (sizeof($arResult["ITEMS"])==0) echo "<i>��� ��������.</i>";
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<div class="textura">
<strong><?=$arItem["NAME"]?></strong><div class="img">
<a href="javascript:void(winPop('/catalog/texture/<?=$arItem["ID"]?>.html',%20'mww',%20550,%20410));"><img title="������� ��� ����������" alt="��������� �������� <?=$arItem["NAME"]?>" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"></a><br>
<?
if ($_GET['key']){
echo '<a href="/basket/?key='.$_GET['key'].'&num='.$_GET[num].'&texture_id='.$arItem[ID].'">����������</a>';
}
?>
</div>
<div style="height: 1px;"><table cellspacing="0" cellpadding="0" class="gabsinmod"><tbody><tr><td style="text-align: right;">
<nobr><span class="mini">���.: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][PICTURE][DISPLAY_VALUE]?></span></nobr><br><nobr><span class="mini">������� ���.: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][PRICE_CAT][DISPLAY_VALUE]?></span></nobr><br><nobr><span class="mini">�������: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][PRESENCE][DISPLAY_VALUE]?></span></nobr><br><nobr><span class="mini">���� �� �����: </span><span class="info"><?=$arItem["DISPLAY_PROPERTIES"][DAYS][DISPLAY_VALUE]?></span></nobr><br>
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
