<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<?$img_path = CFile::GetPath($arItem['PROPERTIES']['pictorflash']['VALUE']);?>
<?$path_parts = pathinfo($img_path);?>
<div style="padding-top: 8px; padding-bottom:8px; text-align: center; width: 187px;">
<?if($path_parts["extension"]=='swf') { ?>

<div>

		  <embed width="188" height="134" src="<?=$img_path?>"></embed>
</div>

<? } else { ?> 
<a href="<?=$arItem['PROPERTIES']['href']['VALUE']?>" target="_blank"><img alt="<?=$arItem['NAME']?>" src="<?=$img_path?>"></a>

<? }?>

                                               </div>
<?endforeach;?>
