<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<table class="second_menu_table" cellpadding="0" cellspacing="0"><tr>
<?
$i = 0;
foreach($arResult as $arItem):?>

	
		<?if ($arItem["PERMISSION"] > "D"):?>
                   <?$i++;?>
			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<td width="189" align="center"><a style="text-decoration: none;" href="<?=$arItem["LINK"]?>"><div  
<?if ($arItem["SELECTED"]){?> 
 class="second_menu_table_div_on" 
<?} else {?>
onMouseOver="this.className='second_menu_table_div_on';" onMouseOut="this.className='second_menu_table_div';" class="second_menu_table_div" style="cursor: pointer;"
<? } ?>
><p style="padding:0; margin:0; text-decoration: none; font-size: 14px;"><?=$arItem["TEXT"]?></p></div></a></td>
			<?else:?>
				<td width="189" align="center"> <div class="second_menu_table_div_on" <?if ($arItem["SELECTED"]):?> class="second_menu_item-selected"<?endif?>><?=$arItem["TEXT"]?></div></a></td>
			<?endif?>

		<?endif?>
<?if($i<5):?>
<td width="7"></td>
<?endif?>
	
<?endforeach?>


</tr></table>
<div width="100%" height="20"></div>
<?endif?>