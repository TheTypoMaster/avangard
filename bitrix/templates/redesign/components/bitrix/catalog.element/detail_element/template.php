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
			<font style="font-weight:normal; color: #000000; margin-left: 22px;">Коллекция:</font>
			<?
			$id_collection = $arResult["PROPERTIES"]["COLLECTION"]["VALUE"];
			$arResColl = GetIBlockElement($id_collection);
			echo($arResColl["NAME"]);
			?>
			<a name="top" target="_new" href="/mebel_sal.php?all_models=<?=$arResult["ID"] ?>"><font style="font-weight:bold; color: #000000; margin-left: 22px;">Эта модель в салонах</font></a>
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
	<?if($arResult["DETAIL_TEXT"]): ?>
		<br /><?=$arResult["DETAIL_TEXT"] ?><br />
	<?elseif($arResult["PREVIEW_TEXT"]): ?>
		<br /><?=$arResult["PREVIEW_TEXT"] ?><br />
	<?endif; ?>
	<br />
	<?if(($arResult["PROPERTIES"]["ACTIA_DESCRIPTION"]["VALUE"]) || ($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]) ||
			($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]))
		$width_td = ' width="100%"';
	?>
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
							<td bgcolor="#f8f8f8" align="center" style= "border-right: solid #e8e8e8 1px;"><b>Рассчитать цену</b></td>
						</tr>
						<?
						$cnt = 0;
						foreach($arResult["PROPERTIES"]["COMPLECT"]["VALUE"] as $complect):
							$arResC = GetIBlockElement($complect);
							?>
							<tr>
								<td height="25" bgcolor="#ffffff"><?echo($arResC["NAME"]) ?></td>
								<td bgcolor="#ffffff" align="right"><?=$arResult["PROPERTIES"]["LENGTH"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="right"><?=$arResult["PROPERTIES"]["WIDTH"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="right"><?=$arResult["PROPERTIES"]["HEIGHT"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PLACES"]["VALUE"][$cnt] ?></td>
								<td bgcolor="#ffffff" align="center">
									<?
									$id_TRANSFORMATION = $arResult["PROPERTIES"]["TRANSFORMATION"]["VALUE"][$cnt];
									$arResTRANSFORMATION = GetIBlockElement($id_TRANSFORMATION);
									echo($arResTRANSFORMATION["NAME"]);
									?></td>
								<td bgcolor="#ffffff"></td>
								<td bgcolor="#ffffff" style= "border-right: solid #e8e8e8 1px;"></td>
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
	<strong><a style="color: #000000;" target="_new" href="/mebel_sal.php?all_models=<?=$arResult["ID"] ?>">Данная модель во всех салонах Москвы и Московской области</a></strong>
	<br>
</div>

<?//echo "<pre>"; print_r($arResult); echo "</pre>"; ?>
