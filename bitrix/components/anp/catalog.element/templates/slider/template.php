<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/bxslider/jquery.bxslider.min.js"></script>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/bxslider/jquery.bxslider.css">
<?
	//echo '<pre>';
	//print_r($arResult["MORE_PHOTO"]);
	//print_r($arResult["PREVIEW_SLIDER"]);
	//print_r($arResult["PROPERTIES"]);
	//echo '</pre>';
?>
<?
### картинка чехла
function getcoverimg($cover_id=0) {
	if($cover_id == 0) {
		$cover_img = '';
		}
		else {
				$resCover = CIBlockElement::GetByID($cover_id);
				$arCover = $resCover->Fetch(); 
				$resFileCover = CFile::GetById($arCover[PREVIEW_PICTURE]);
				$arFileCover = $resFileCover->Fetch();
				$cover_path = "/upload/".$arFileCover[SUBDIR]."/".$arFileCover[FILE_NAME];
				$cover_img = '<img src="'.$cover_path.'" style="border: 2px solid #C5C5C5;" width="110" height="65">';				
		}
	return $cover_img;
}
?>

	<?if(count($arResult["MORE_PHOTO"])>0):?>
		<?$sliderContent = '';?>
		<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
			<?$sliderContent .= '<li><img src="'.$PHOTO["SRC"].'" /></li>';?>
		<?endforeach?>
	<?endif?>
	<?if(count($arResult["PREVIEW_SLIDER"])>0):?>
		<?$sliderPreview = '';?>
		<?foreach($arResult["PREVIEW_SLIDER"] as $ind => $PHOTO_SM):?>
			<?$sliderPreview .= '<a data-slide-index="'.$ind.'" href=""><img src="'.$PHOTO_SM["src"].'" /></a>';?>
		<?endforeach?>
	<?endif?>
<script type="text/javascript">
  $(document).ready(function(){    
$('.bxslider').bxSlider({
  pagerCustom: '#bx-pager',
  pager: false,
  auto: false
});
  });
</script>
<div class="gray_td">
	<h1><?=$arResult[NAME]?></h1>
</div>
<br />
<div class="catalog-element">
	<ul class="bxslider"><?=$sliderContent?></ul>
	<div id="bx-pager"><?=$sliderPreview?></div>
</div>

<? /*
	// additional photos
	$LINE_ELEMENT_COUNT = 2; // number of elements in a row
	if(count($arResult["MORE_PHOTO"])>0):?>
		<!-- <a name="more_photo"></a> -->
		<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
			<img border="0" src="<?=$PHOTO["SRC"]?>" width="<?=$PHOTO["WIDTH"]?>" height="<?=$PHOTO["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /><br />
		<?endforeach?>
	<?endif?>
*/ ?>


<div style="margin: 10px 0 10px 0;">
	<h1 class="itemtitle" style="margin-top: 8px;">Материал обивки: <?=$arResult["PROPERTIES"]["MATERIAL"]["VALUE"]?></h1>
</div>
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
<? /*
<div align="left" id="subject_id_2105" class="" style="margin: 40px 0 40px 0;">
   <img src="/images/basket.jpg">
   <a onclick="addSubject(<?=$arResult[ID]?>, 0, 2105); return false;" href="javascript://">Положить в корзину</a>
</div> */
?>
<?if ($arResult["PROPERTIES"]["COMPLECTATION"]["VALUE"]):?>
<table width="100%">
<tr>
	<td align="center">
		<table width="100%" align="center" cellspacing="1" cellpadding="4" border="0" bgcolor="#e8e8e8" class="s" >
			<tbody>
				<tr>
					<td height="25" align="center" colspan="8">
						<b>ХАРАКТЕРИСТИКИ</b>
					</td>
				</tr>
				<tr class="header_tr">
					<td bgcolor="#f8f8f8" rowspan="2" align="center"><b>Комплектация</b></td>
					<td bgcolor="#f8f8f8" rowspan="2" align="center"><b>Размер<br>спального<br>места</b></td>
					<td bgcolor="#f8f8f8" rowspan="2" align="center"><b>Размер<br>габаритный</b><br>-ширина<br>-глубина<br>-высота</td>
					<td bgcolor="#f8f8f8" rowspan="2" align="center"><b>Механизм<br>расладывания</b></td>
					<td bgcolor="#f8f8f8" colspan="2" align="center">
						<table>
							<tr><td height="25" colspan="2" align="center"><b>Вариант кроя 0,1</b></td></tr>
							<tr><td height="75"><b>0</b></td>
								<td><?=getcoverimg($arResult["PROPERTIES"]["COVER_0"]["VALUE"])?> </td>
							</tr>
							<tr><td height="75"><b>1</b></td>
								<td><?=getcoverimg($arResult["PROPERTIES"]["COVER_1"]["VALUE"])?></td>
							</tr>
						</table>
					</td>
					<td bgcolor="#f8f8f8" colspan="2" align="center">
						<table>
							<tr><td height="25" colspan="2" align="center"><b>Вариант кроя 5,6</b></td></tr>
							<tr><td><b>5</b></td>
								<td height="75"><?=getcoverimg($arResult["PROPERTIES"]["COVER_5"]["VALUE"])?></td>
							</tr>
							<tr><td><b>6</b></td>
								<td height="75"><?=getcoverimg($arResult["PROPERTIES"]["COVER_6"]["VALUE"])?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="header_tr">					
					<td bgcolor="#f8f8f8" align="center"><b>Цена</b></td>					
					<td bgcolor="#f8f8f8" align="center"><b>Со скидкой</b></td>					
					<td bgcolor="#f8f8f8" align="center"><b>Цена</b></td>
					<td bgcolor="#f8f8f8" align="center"><b>Со скидкой</b></td>
				</tr>
				<?
				$cnt = 0;
				foreach($arResult["PROPERTIES"]["COMPLECTATION"]["VALUE"] as $complect):?>
					<tr>
						<td height="25" bgcolor="#ffffff"><?=($complect) ?></td>
						<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["SLEEPER_SIZE"]["VALUE"][$cnt] ?></td>
						<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["GABARIT_SIZE"]["VALUE"][$cnt] ?></td>
						<td bgcolor="#ffffff" align="center">
							<?
							$id_TRANSFORMATION = $arResult["PROPERTIES"]["MECHANISM"]["VALUE"][$cnt];
							$arResTRANSFORMATION = GetIBlockElement($id_TRANSFORMATION);
							echo($arResTRANSFORMATION["NAME"]);
							?></td>
						<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PRICE_01"]["VALUE"][$cnt] ?></td>
						<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PRICE_01_30"]["VALUE"][$cnt] ?></td>
						<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PRICE_56"]["VALUE"][$cnt] ?></td>
						<td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PRICE_56_30"]["VALUE"][$cnt] ?></td>
					</tr>
					<?
					$cnt++;
				endforeach;?>
			</tbody>
		</table>
	</td>
</tr>
</table>
<?endif?>
<br />
<?
// возврат в раздел
if(is_array($arResult["SECTION"])):?>
	<br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
<?endif?>