<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section">
<div class="gray_td">
	<h1><?=$arResult[NAME]?></h1>
</div>

<?foreach($arResult["ITEMS"] as $arElement):?>
<div class="elem-box" style="height: 200px;">
	<div class="elem-box-cont" style="height: 200px;">
			<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" width="230" height="115"></a><br>
			<div class="elem-info">
			<a class="elem-ref" href="<?=$arElement["DETAIL_PAGE_URL"]?>">
			<span class="elem-name"><?=$arElement["NAME"]?></span><br>
			<span><?=$arElement[PROPERTIES][MATERIAL][VALUE]?></span>
			</a><br>
			</div>
	</div>
</div>
<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

<div style="clear: left"></div>
</div>
