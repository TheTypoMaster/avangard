<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<table align="center" class="second_menu_bottom_table"><tr>
<?$kolvo = count($arResult); $b = 0; ?>
<?foreach($arResult as $arItem):?>

	
		<?if ($arItem["PERMISSION"] > "D"):?>
                         <? $b++; ?>
			<td>
                        <?if (!($arItem["SELECTED"])):?>
                                    <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                        <?else:?>
                                     <?=$arItem["TEXT"]?>
                       <?endif?>
                       </td>
			
                        <?if($b<$kolvo): ?>
                         <td width="6">|</td>
                         <?endif?>
                        
		<?endif?>

<?endforeach?>
</tr></table>
<?endif?>