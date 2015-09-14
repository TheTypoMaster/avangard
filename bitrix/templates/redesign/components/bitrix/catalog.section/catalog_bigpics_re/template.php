<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<table width="738" align="center">
	<?
	$collection_count = count($arResult['CAT_ARRAY']);
	$kol = 0;
	foreach($arResult['CAT_ARRAY'] as $category){
		$kol++;
		?>
		<?if(($kol > 1) && ($kol <= $collection_count)): ?>
			<tr><td colspan="5" class="gray_line_small"></td></tr>
		<?endif ?>
		<tr>
			<td colspan="5"><b><?=$category["name"] ?></b></td>
		</tr>
		<?$i=0;
		foreach($arResult['ITEMS'] as $el){
			if($el["DISPLAY_PROPERTIES"]["COLLECTION"]["VALUE"]!=$category["id"])//Пропускаем если это не текущая коллекция
				continue;
			$i++;
			$rows_count--;
			if($i == 1)
				echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
			if($el["DISPLAY_PROPERTIES"]["FULLCOLOR_PIC"]["FILE_VALUE"]["SRC"]!='')
				$img_path = $el["DISPLAY_PROPERTIES"]["FULLCOLOR_PIC"]["FILE_VALUE"]["SRC"]; 
			else
				$img_path = $el["PREVIEW_PICTURE"]["SRC"];
		$skidka_div = '';    
		if($el["DISPLAY_PROPERTIES"]["SKIDKA"]["VALUE"]) {
			$skidka_value = $el["DISPLAY_PROPERTIES"]["SKIDKA"]["VALUE"]; /* если есть скидка, то выводится введенное значение */
			$skidka_div .='<div style="margin: 0px; padding: 0px; position: absolute; z-index: 90;"><div style="text-align:center; background: url(/images/skidka.gif) no-repeat center center; position: relative; left: 200px; top: -12px; color: #ffffff; height: 36px; width: 36px; " height=36 width=36 ><img src="/images/gif.gif" height="10" width="20"><br>-'.$skidka_value.'%</div></div>';
		}
		?>
			<td class="catalog_td">
				<? echo $skidka_div; ?>
				<a href="/catalog/divan<?=$el["ID"] ?>.htm"><!--
					--><img onMouseOver="this.src='<?=$img_path ?>';" onMouseOut="this.src='<?=$el["DISPLAY_PROPERTIES"]["BLACKWHITE_PIC"]["FILE_VALUE"]["SRC"]?>';" class="catalog_picture" src="<?=$el["DISPLAY_PROPERTIES"]["BLACKWHITE_PIC"]["FILE_VALUE"]["SRC"]?>" alt="<?=$el["NAME"] ?>"><!--
				--></a><br>
				<table width="100%">
					<tr>
						<td>
							<a class="catalog_name" href="/catalog/divan<?=$el["ID"] ?>.htm"><?=$el["NAME"] ?></a>
						</td>
						<td id="price_new1">
							<?=$el["DISPLAY_PROPERTIES"]["PRICE"]["VALUE"][0]?>
						</td>
					</tr>
				</table>
			<?if($i < 3){ ?>
				<td width="26"></td> 
			<? } ?>
<?
			if($i == 3){
				$i = 0;
				echo "</tr>";
			}
		}
	}
	?>
</table>
