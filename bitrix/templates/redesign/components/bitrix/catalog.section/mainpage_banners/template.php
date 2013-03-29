<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="mainpage_banners">
<?$ii=0;foreach($arResult["ITEMS"] as $arItem):$ii++;?>
	<?$img_path = CFile::GetPath($arItem['PROPERTIES']['pictorflash']['VALUE']);?>
	<?$path_parts = pathinfo($img_path); ?>
	<?if($path_parts["extension"] == 'swf'){?>
		<div>
			<embed class="mainpage_banner flash <?=($ii%3==0 ? 'last' : '')?>" width="188" height="134" src="<?=$img_path ?>"></embed>
		</div>
	<?}else{?> 
		<a href="<?=$arItem['PROPERTIES']['href']['VALUE'] ?>" target="_blank"><img class="mainpage_banner <?=($ii%3==0 ? 'last' : '')?>" alt="<?=$arItem['NAME'] ?>" src="<?=$img_path ?>"></a>
	<?}?>
<?endforeach;?>
	<div class="clearall"></div>
</div>