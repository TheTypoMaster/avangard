<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $letters = array('a'=>'À', 'ae'=>'Ý', 'b'=>'Á', 'ch'=>'×', 'e'=>'Å', 'h'=>'Õ', 'i'=>'È', 'k'=>'Ê', 'l'=>'Ë', 'm'=>'Ì', 'n'=>'Í', 'o'=>'Î', 'p'=>'Ï', 'r'=>'Ð', 's'=>'Ñ', 't'=>'Ò', 'u'=>'Ó', 'v'=>'Â', 'ya'=>'ß');?>
<div style="width: 954px;" align="center"><table class="spisok_salonov_russia" cellspacing="0" cellpadding="0" border="0" align="center" width="500">

	<?$bgcol = "#ffffff";?>
        <?foreach($arResult["ITEMS"] as $arElement){
			$output[substr($arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"],0,1)][ $arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"] ][$arElement["ID"]] = $arElement["NAME"];
			}
			ksort($output);
			?>
</table>

<?$counter = 0;?>

<table cellpadding="0" cellspacing="0" border="0" width="700">
		<?foreach($output as $cell=>$key):?>
<?$counter++;?>
		<?if($counter == 1):?>
		<tr>
		<?endif;?>
		
		
		<td valign="top" width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%">
   <table><tr><td width="48" align="right"> <?$lettername = array_search($cell, $letters);  echo '<img src="/images/letters/'.$lettername.'.gif" alt="'.$cell.'">';?></td><td width="8"></td><td style=" line-height: 1.3; padding-bottom: 10px;">
<?foreach($key as $city_id=>$city_ar) {
?>
	<a class="city_url" href="#" target="_new" onclick="getHint(event,'/redesign/where_buy/detail.php?city=y<?foreach($city_ar as $c_id=>$city_name) {
echo "&id[]=".$c_id;
}
?>', this.offsetHeight, '<?foreach($city_ar as $c_id=>$city_name) {
echo "&id[]=".$c_id;
}
?>'); return false;" title="<?=$city_id?>"><?=$city_id?></a>
<br>

<?}?>
</td></tr></table>
	</td>
		
		<?$cell++;
		if($counter == 3):?>
			</tr>
                 <?$counter = 0;?>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

			</table>
</div>