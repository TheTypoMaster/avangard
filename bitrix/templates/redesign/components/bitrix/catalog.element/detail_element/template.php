<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
if($arResult["PROPERTIES"]["SEO_TITLE_R"]["VALUE"])
	$seotitle = $arResult["PROPERTIES"]["SEO_TITLE_R"]["VALUE"];
else
	$seotitle = $arResult["NAME"];
$APPLICATION->SetTitle($seotitle);
?>
<?if($_GET[id]){ ?><script src="/script.js" type="text/javascript"></script><? } ?>

<div class="bottext">
	<div class="gray_td" id="extra_controls">
		<h1 class="itemtitle"><?=$arResult["PROPERTIES"]["F_TYPE"]["VALUE"] ?> <?=$arResult["NAME"] ?>
			<? if ($arResult["PROPERTIES"]["COLLECTION"]["VALUE"]) {
				$id_collection = $arResult["PROPERTIES"]["COLLECTION"]["VALUE"];
				$arResColl = GetIBlockElement($id_collection);
				echo '<font style="font-weight:normal; color: #000000; margin-left: 22px;">Коллекция:</font>'.$arResColl["NAME"];
			}?>
			<a name="top" target="_new" href="/wharetobuy/mebel_in_salon.php?all_models=<?=$arResult["ID"] ?>"><font style="font-weight:bold; color: #000000; margin-left: 22px;">В салонах</font></a>
		</h1>
	</div>

	<table width="720" border="0" cellspacing="2" cellpadding="0">
		<tr>
			<td align="center" valign="top">
				<?
				if(count($arResult["MORE_PHOTO"]) > 0){

					reset($arResult["MORE_PHOTO"]);
					$M_PHOTO = current($arResult["MORE_PHOTO"]);

					$m_folder = str_replace($M_PHOTO["FILE_NAME"], "", $M_PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
					$m_s_puth = $m_folder . "s_" . $M_PHOTO["FILE_NAME"]; //получаем путь до маленькой картинки

					$m_puth = $m_folder . $M_PHOTO["FILE_NAME"]; //получаем путь до основной картинки
				}
				?>
				<script>
					var url="<?=$m_puth ?>";
				</script>
				<?
				if(count($arResult["MORE_PHOTO"]) > 0){
					?>
					<img height="330" src="<?=$m_puth ?>" name="mainimg" class="preview">
					<?
				}
				?>
			</td>
		</tr>
		<tr>
			<td style="padding-top:7px;" valign="bottom">
				<?
				// additional photos
				$LINE_ELEMENT_COUNT = 3; // number of elements in a row
				if(count($arResult["MORE_PHOTO"]) > 0){
					$t_width = 5;
					$height = 75;

					foreach($arResult["MORE_PHOTO"] as $PHOTO){
						$arFile = CFile::GetFileArray($PHOTO["ID"]);
						$width = $arFile["WIDTH"] / $arFile["HEIGHT"] * 50;
						$t_width = $t_width + $width + 10;
					}
					if($t_width > 720){
						$height = 90;
					}
					?>
					<div style="background-color: #f2f2f2; width: 720px; height:<?=$height ?>px; overflow: auto; border: 1px solid #e4e4e4;">
						<table height="70">
							<tr>
								<td valign="bottom">
									<nobr>
									<?foreach($arResult["MORE_PHOTO"] as $PHOTO){
										$folder = str_replace($PHOTO["FILE_NAME"], "", $PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
										$s_puth = $folder . "s_" . $PHOTO["FILE_NAME"]; //получаем путь до маленькой картинки

										$puth = $folder . $PHOTO["FILE_NAME"]; //получаем путь до основной картинки
									?>
										<a href="#null" onclick="MM_swapImage('mainimg','','<?=$puth ?>',1); javascript:url='<?=$puth ?>';return false">
											<?echo CFile::ShowImage($s_puth, 150, 50, "class=preview"); ?>
										</a>
									<?}?>
									</nobr>
								</td>
							</tr>
						</table>
					</div>
				<?}?>
			</td>
		</tr>
	</table>
	<br />
<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>
<?
$tab_matras = ($arResult["PROPERTIES"]["MATRESS"]["VALUE"]) ? true : false;
$tab_massiv = ($arResult["PROPERTIES"]["CARCASS"]["VALUE"]) ? true : false;
$tab_cover = ($arResult["PROPERTIES"]["COVER"]["VALUE"]) ? true : false;
/*if($arResult[SECTION][IBLOCK_SECTION_ID] = 110) {
	$tab_matras = false;
	$tab_massiv = false;
	$tab_cover = false;
}*/
?>

<div class="mainpage_tabs">
	<div id="tabs">
		<ul>
		<?if($tab_matras): ?><li><a href="#tabs-1">матрасы</a></li>	<?endif; ?>
		<?if($tab_massiv): ?><li><a href="#tabs-2">цвет массива бука</a></li><?endif; ?>
		<?if($tab_cover):  ?><li><a href="#tabs-3">варианты чехла</a></li><?endif; ?>
		<li><a href="#tabs-4">размеры</a></li>
		<li><a href="#tabs-5">информация</a></li>
		</ul>
<?if($tab_matras): ?>
		<div id="tabs-1">
<div>
	<? if($arResult["PROPERTIES"]["MATRESS"]["VALUE"]) {?>
	<p style="margin-top:15px;">Используются ортопедические матрасы:</p>
		<a style="text-decoration: none;" href="/8days/orthopedic_furniture.php">
	<?  foreach($arResult["PROPERTIES"]["MATRESS"]["VALUE"] as $matress_id) {    
			$resMatress = CIBlockElement::GetByID($matress_id);
			$arMatress = $resMatress->Fetch();
			############  получаем картинку ################################
			$resFileMatress = CFile::GetById($arMatress[PREVIEW_PICTURE]);
			$arFileMatress = $resFileMatress->Fetch();
			$src_matr = "/upload/".$arFileMatress[SUBDIR]."/".$arFileMatress[FILE_NAME]; ?>
			<div style="margin-bottom:10px;">
				<img style="border-style:none; height:32px;" src="<?=$src_matr?>" title="<?=$arMatress[NAME]?>" alt="<?=$arMatress[NAME]?>">
				&nbsp;&nbsp;<?if($arMatress["PREVIEW_TEXT"]) echo $arMatress["PREVIEW_TEXT"]; ?>
				<p><?if($arMatress["DETAIL_TEXT"]) echo $arMatress["DETAIL_TEXT"]; ?></p>
			</div>
	<?  } ?>
		</a>
		<div style="clear: left;"></div>
	<? } ?>
</div>
		</div>
<?endif; ?>
<?if($tab_massiv): ?>
		<div id="tabs-2">
<div>
	<? if($arResult["PROPERTIES"]["CARCASS"]["VALUE"]) {?>
		<h1 class="itemtitle" style="margin-top: 8px;">ЦВЕТ МАССИВА БУКА</h1>
	<?  foreach($arResult["PROPERTIES"]["CARCASS"]["VALUE"] as $carcass_id) {    
			$resCarcass = CIBlockElement::GetByID($carcass_id);
			$arCarcass = $resCarcass->Fetch();
			############  получаем картинку ################################
			$resFileCarcass = CFile::GetById($arCarcass[PREVIEW_PICTURE]);
			$arFileCarcass = $resFileCarcass->Fetch();
			$src_texture = "/upload/".$arFileCarcass[SUBDIR]."/".$arFileCarcass[FILE_NAME]; ?>
			<div class="carcass">
				<img src="<? echo $src_texture; ?>" style="border: 2px solid #C5C5C5; margin-bottom: 4px;" width="110" height="65" />
				<br><? echo $arCarcass[NAME]; ?>
			</div>
	<?  } ?>
		<div style="clear: left"></div>
	<? } ?>
</div>
		</div>
<?endif; ?>
<?if($tab_cover):  ?>
		<div id="tabs-3">
<div>
	<? if($arResult["PROPERTIES"]["COVER"]["VALUE"]) {?>
		<h1 class="itemtitle" style="margin-top: 8px;">ВАРИАНТЫ ЧЕХЛА</h1>
	<?  foreach($arResult["PROPERTIES"]["COVER"]["VALUE"] as $chehol_id) {    
			$resChehol = CIBlockElement::GetByID($chehol_id);
			$arChehol = $resChehol->Fetch();
			############  получаем картинку ################################
			$resFileChehol = CFile::GetById($arChehol[PREVIEW_PICTURE]);
			$arFileChehol = $resFileChehol->Fetch();
			$src_texture = "/upload/".$arFileChehol[SUBDIR]."/".$arFileChehol[FILE_NAME]; ?>
			<div class="chehol">
				<img src="<? echo $src_texture; ?>" style="border: 2px solid #C5C5C5; margin-bottom: 4px;" width="110" height="65" />
				<br><? echo $arChehol[NAME]; ?>
			</div>
	<?  } ?>
		<div style="clear: left"></div>
	<? } ?>
</div>
		</div>
<?endif; ?>
		<div id="tabs-4">
	<table width="100%">
		<tr>
			<td<?=$width_td ?>>
				<br><br>
				<?if(($arResult["PROPERTIES"]["ACTIA"]["VALUE"]) || ($arResult["PROPERTIES"]["NOVELTY"]["VALUE"]) || $arResult["PROPERTIES"]["HIT"]["VALUE"]) echo '<table width="100%">'; ?>

				<?if(($arResult["PROPERTIES"]["NOVELTY"]["VALUE"])) echo '<tr><td><img src="/images/new_image.gif" alt="Новинка" border=0></td>'; ?>

				<?if($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]) echo '<td width="100%">' . $arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"] . '<br><br></td>'; ?>

				<?if(($arResult["PROPERTIES"]["NOVELTY"]["VALUE"])) echo '</tr>'; ?>


				<?if(($arResult["PROPERTIES"]["ACTIA"]["VALUE"])) echo '<tr><td><img src="/images/act_image.gif" alt="Акция" border=0></td>'; ?>

				<?if($arResult["PROPERTIES"]["ACTIA_DESCRIPTION"]["VALUE"]) echo '<td width="100%">' . $arResult["PROPERTIES"]["ACTIA_DESCRIPTION"]["VALUE"] . '<br><br></td>'; ?>

				<?if(($arResult["PROPERTIES"]["ACTIA"]["VALUE"])) echo '</tr>'; ?>


				<?if(($arResult["PROPERTIES"]["HIT"]["VALUE"])) echo '<tr><td><img src="/images/hit_image.gif" alt="Хит продаж" border=0></td>'; ?>

				<?if($arResult["PROPERTIES"]["HIT_DESCRIPTION"]["VALUE"]) echo '<td width="100%">' . $arResult["PROPERTIES"]["HIT_DESCRIPTION"]["VALUE"] . '<br><br></td>'; ?>

				<?if(($arResult["PROPERTIES"]["HIT"]["VALUE"])) echo '</tr>'; ?>


<?if(($arResult["PROPERTIES"]["NOVELTY"]["VALUE"]) || ($arResult["PROPERTIES"]["HIT"]["VALUE"]) || ($arResult["PROPERTIES"]["ACTIA"]["VALUE"])) echo '</table>'; ?>

			</td>
		</tr>
		<tr>
			<td align="center">
				<table width="100%" align="center" cellspacing="1" cellpadding="4" border="0" bgcolor="#e8e8e8" class="s" >
					<tbody>
						<tr>
							<td height="25" align="center" colspan="6">
								<b>ГАБАРИТНЫЕ РАЗМЕРЫ</b>
							</td>
						</tr>
						<tr class="header_tr">
							<td height="25" bgcolor="#f8f8f8" align="center"><b>Комплектность</b></td>
							<td bgcolor="#f8f8f8" align="center"><b>Ширина</b></td>
							<td bgcolor="#f8f8f8" align="center"><b>Глубина</b></td>
							<td bgcolor="#f8f8f8" align="center"><b>Высота</b></td>
							<td bgcolor="#f8f8f8" align="center"><b>Спальное место</b></td>
							<td bgcolor="#f8f8f8" align="center"><b>Механизм трансформации</b></td>
							<td bgcolor="#f8f8f8" align="center"><b>Цена</b> от (руб.)</td>
							<td bgcolor="#f8f8f8" align="center" style= "border-right: solid #e8e8e8 1px;"><b>Цена со скидкой</b> от (руб.)</td>
						</tr>
						<?
						$cnt = 0;
						foreach($arResult["PROPERTIES"]["COMPLECT"]["VALUE"] as $complect):
							$arResC = GetIBlockElement($complect);
							?>
							<tr>
								<td height="25" bgcolor="#ffffff"><?echo($arResC["NAME"]) ?></td>
								<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["LENGTH"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["WIDTH"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["HEIGHT"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PLACES"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center">
									<?
									$id_TRANSFORMATION = $arResult["PROPERTIES"]["TRANSFORMATION"]["VALUE"][$cnt];
									$arResTRANSFORMATION = GetIBlockElement($id_TRANSFORMATION);
									echo($arResTRANSFORMATION["NAME"]);
									?></td>
								<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PRICE"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center" style= "border-right: solid #e8e8e8 1px;">
									<?
									if ($arResult["PROPERTIES"]["PRICE"]["VALUE"][$cnt]!='' && $arResult["PROPERTIES"]["SKIDKA"]["VALUE"]!='') {
									  $skidka = round(intval($arResult["PROPERTIES"]["PRICE"]["VALUE"][$cnt])/100*intval($arResult["PROPERTIES"]["SKIDKA"]["VALUE"]));
									  echo intval($arResult["PROPERTIES"]["PRICE"]["VALUE"][$cnt])-$skidka;
									}
									?></td>
							</tr>


							<?
							$cnt++;
						endforeach;?>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<br>
		</div>
		<div id="tabs-5">
	<?if($arResult["DETAIL_TEXT"]): ?>
		<?=$arResult["DETAIL_TEXT"] ?><br />
	<?elseif($arResult["PREVIEW_TEXT"]): ?>
		<br /><?=$arResult["PREVIEW_TEXT"] ?><br />
	<?endif; ?>
	<br />
	<?if(($arResult["PROPERTIES"]["ACTIA_DESCRIPTION"]["VALUE"]) || ($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]) ||
			($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]))
		$width_td = ' width="100%"';
	?>
	<br>
	<strong><a style="color: #000000;" target="_new" href="/mebel_sal.php?all_models=<?=$arResult["ID"] ?>">Данная модель во всех салонах Москвы и Московской области</a></strong>
	<br>
		</div>
	</div>
</div>

<div>
	<br>
	<? if($arResult["PROPERTIES"]["HREF_3D"]["VALUE"]) {?>
		<div style="margin:10px 10px 20px 0; float:left;"><a href="/catalog/vybor_tkani.php?id=<?=$arResult["PROPERTIES"]["HREF_3D"]["VALUE"]?>" target="_blank"><img src="/images/btn3D.png" title="Переодевание 3D" alt="Переодевание 3D" border=0></a></div>
	<? } ?>
	<div style="margin:10px 10px 20px 0; float:left;"><a href="/redesign/where_buy/" ><img  src="/images/msk_shop.png" style="border-style:none; height:24px;" title="адреса салонов" alt="адреса салонов" /></a></div>
	<div style="margin:10px 10px 20px 0; float:left;"><a href="/shop/catalog/divan<?=$arResult["ID"] ?>.htm" ><img src="/images/i_shop.png" style="border-style:none; height:24px;" title="посмотреть цену" alt="посмотреть цену" /></a></div>
</div>
<div style="clear: left"></div>




	<br />

</div>