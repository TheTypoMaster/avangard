<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css"> 
<script type="text/javascript" src="/shadowbox/shadowbox.js"></script> 
<script type="text/javascript"> 
Shadowbox.init({
    handleOversize: "drag",
    modal: true
});
</script>


<?
//print_r($_GET);
?>
<table class="product_table">
	<tr>
		<td>
			<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
				<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"  title="<?=$arResult["NAME"]?>" /></a><br>
			<?endif?>
			<br>
			<?
			//$APPLICATION->AddHeadScript("/shop/basket/basket.js");
			global $USER;
			$arFileTexture = array();
				//echo "<!-- user показать авторизованному пользователю -->";
				
##########################  выбираем все комбинации у данного предмета   ######################
	$arSelect = Array("ID"); // , "NAME", "PROPERTY_PRICE", "PROPERTY_COMBINAC", "PROPERTY_COMBINAC.PREVIEW_PICTURE"
	$arFilter = Array("IBLOCK_ID"=>24, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_SUBJECT" => $_GET[ELEMENT_ID]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	$arCombinacID = array();
	while($ob = $res->GetNextElement()){
		
		$arFields = $ob->GetFields();
		$arCombinacID[] = array("PROPERTY_COMBINAC" => $arFields[ID]);
		/*$resFileCombinac = CFile::GetById($arFields[PROPERTY_COMBINAC_PREVIEW_PICTURE]);
		$arFileCombinac = $resFileCombinac -> Fetch();
		$resProperty = CIBlockProperty::GetByID("SRC", false, "company_news");
		$arProperty = $resProperty->Fetch();*/
		
	}
	if (sizeof($arCombinacID)>0) 
		$arCombinacID["LOGIC"] =  "OR";
	/*echo "<pre>arCombinacID - ";
	print_r($arCombinacID);
	echo "</pre>";*/
###############################################################################################				
				
				
				
				//print_r($_GET);
###########################  выбираем рекомендуемые материалы   #############################
			if (is_array($arCombinacID) && sizeof($arCombinacID)>0) { // найдены комбинации
				$arSelect = Array("ID", "NAME", "PROPERTY_PRICE", "PROPERTY_COMBINAC", "PROPERTY_COMBINAC.PREVIEW_PICTURE", "PREVIEW_PICTURE", "DETAIL_PICTURE");
				$arFilter = Array("IBLOCK_ID"=>21, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", $arCombinacID);
				$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
				if ($res->SelectedRowsCount()>0) echo "<div class=\"gray_td_left\" width=\"400px\"><h1>РЕКОМЕНДУЕМЫЕ МАТЕРИАЛЫ</h1></div>";
				/*echo "<pre>arFilter - ";
				print_r($arFilter);
				echo "</pre>";*/
				while($ob = $res->GetNextElement()){
					
					$arFields = $ob->GetFields();
					$arFieldsBIG = $ob->GetFields();
					
					$pic = !empty($arFields[PREVIEW_PICTURE]) ? $arFields[PREVIEW_PICTURE] : $arFields[PROPERTY_COMBINAC_PREVIEW_PICTURE];
					$picBIG = !empty($arFieldsBIG[DETAIL_PICTURE]) ? $arFieldsBIG[DETAIL_PICTURE] : $arFields[PROPERTY_COMBINAC_PREVIEW_PICTURE];
					
					$resFileCombinac = CFile::GetById($pic);
					$resFileCombinacBIG = CFile::GetById($picBIG);
					
					$arFileCombinac = $resFileCombinac -> Fetch();
					$arFileCombinacBIG = $resFileCombinacBIG -> Fetch();
					
					$resProperty = CIBlockProperty::GetByID("SRC", false, "company_news");
					$arProperty = $resProperty->Fetch();
					
					/*if (is_array($arFields[PROPERTY_TEXTURE_VALUE])) { // несколько материалов
						foreach($arFields[PROPERTY_TEXTURE_VALUE] as $val){
							$resFileTexture = CFile::GetById($arFields[PROPERTY_TEXTURE_PREVIEW_PICTURE]);
							$arFileTexture[$arFields[PROPERTY_COMBINAC_VALUE]][] = $resFileTexture -> Fetch();
						}
					}
					else { */ // один материал
					$arSelectTexture = Array("ID", "NAME", "PROPERTY_TEXTURE");
					$arFilterTexture = Array("IBLOCK_ID"=>21, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COMBINAC" => $arFields[PROPERTY_COMBINAC_VALUE], "ID" => $arFields[ID]);
					$resTexture = CIBlockElement::GetList(Array(), $arFilterTexture, false, false, $arSelectTexture);
					$srcTexture = array();
					while($obTextureb = $resTexture->GetNextElement()){
						$arFieldsTexture = $obTextureb->GetFields();
						$resRecommTexture = CIBlockElement::GetByID($arFieldsTexture[PROPERTY_TEXTURE_VALUE]);
						$arRecommTexture = $resRecommTexture->Fetch();
						
						############  получаем картинку ################################
						$resFileRecommTexture = CFile::GetById($arRecommTexture[PREVIEW_PICTURE]);
						$arFileRecommTexture = $resFileRecommTexture->Fetch();
						#################################################################
						array_push ($arFileRecommTexture, $arFieldsTexture[PROPERTY_TEXTURE_VALUE]);
						$srcTexture[] = $arFileRecommTexture;
						//echo "<pre>arFieldsTexture - ";
						//print_r($arFieldsTexture);
						//echo "</pre>";
						//$srcTexture[FILE][] = "<img src=\"/upload/".$arFileRecommTexture[SUBDIR]."/".$arFileRecommTexture[FILE_NAME]."\" />";
					}
						/*echo "<pre>srcTexture - ";
						print_r($srcTexture);
						
						echo "</pre>";*/
					
						/*$arSelect = Array("ID", "NAME", "PROPERTY_TEXTURE", "PROPERTY_PRICE", "PREVIEW_PICTURE");
						$arFilter = Array("IBLOCK_ID"=>21, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $arCombinac[ID]);
						$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
						while($ob = $res->GetNextElement()){
							$arFields = $ob->GetFields();
							
							$resRecommTexture = CIBlockElement::GetByID($arFields[PROPERTY_TEXTURE_VALUE]);
							$arRecommTexture = $resRecommTexture->Fetch();
							
							############  получаем картинку ################################
							$resFileRecommTexture = CFile::GetById($arRecommTexture[PREVIEW_PICTURE]);
							$arFileRecommTexture = $resFileRecommTexture->Fetch();
							#################################################################
							
							$srcTexture[FILE][] = "<img src=\"/upload/".$arFileRecommTexture[SUBDIR]."/".$arFileRecommTexture[FILE_NAME]."\" />";
							
							$priceRecommend = $arFields[PROPERTY_PRICE_VALUE];
							
						}*/
					//}
					
					
					//echo "<pre>arFields - ";
					//echo "<br><br>srcTexture - ";
					//print_r($srcTexture);
					//echo "</pre>";
					$scr = "/upload/".$arFileCombinac[SUBDIR]."/".$arFileCombinac[FILE_NAME];
					$scrBIG = "/upload/".$arFileCombinacBIG[SUBDIR]."/".$arFileCombinac[FILE_NAME];
					//echo $arFileCombinacBIG[SUBDIR];
					$detail_url = "/catalog/subject/".$arFields[ID].".html";
					$transform_url = "/catalog/transform/".$arFields[PROPERTY_TRANSFORMATION_VALUE].".html";
					?>
					<div class="recomm" align="center" style="float:left; width:300px;">
						<font size="4"><?=$arFields[NAME]?></font><br><br>
						<div class="img">
							
							<a href="<?=$scrBIG?>" rel="shadowbox[dop<?=$arItem["ID"]?>]"><img class="detail_picture" border="0" src="<?=$scr?>" align="left"/></a>
							
							<?
							foreach($srcTexture as $key => $val){
								$src_texture = "/upload/".$val[SUBDIR]."/".$val[FILE_NAME];
								?>
								<a href="javascript:void(winPop('/catalog/texture/<?=$val[0]?>.html',%20'mww',%20550,%20410));"><img src="<?=$src_texture?>" align="" width="40" /></a>
								<?
							}
							
							//print_r($arFileTexture);
							
							?>
							<div style="clear:left"></div>
							<div class="left_block">
								Цена: <?=$arFields["PROPERTY_PRICE_VALUE"]." руб."?>
								<a href="/shop/basket/?add=1&subj=<?=$arFields[ID]?>&comb=<?=$arFields[PROPERTY_COMBINAC_VALUE]?>"></a>
								<div align="left" id="subject_id_<?=$arFields['ID']?>" class="">
									<img src="/images/basket.jpg"><a onclick="addSubject(<?=$_GET[ELEMENT_ID]?>, <?=$arFields[PROPERTY_COMBINAC_VALUE]?>, <?=$arFields['ID']?>); return false;" href="javascript://">Положить в корзину</a><br><br><br>
								</div>
							</div>
							<div class="right_block">
								<div class="credit_price">
									за <?=(str_replace(" ", "", $arFields[PROPERTY_PRICE_VALUE])*0.1)?> руб. в месяц
								</div>
								<div class="credit_button">
									<a title="Купить в кредит" onclick='
										yescreditdialog([{MODEL: "<?=addslashes($arFields["NAME"])?>", COUNT:"1", PRICE:"<?=str_replace(" ", "", $arFields[PROPERTY_PRICE_VALUE])?>"}],2);' 
									href="javascript:;">
										<img src="/images/credit_btn.png"/>
									</a>
								</div>
							</div>
							<div style="clear:both"></div>
						</div>
						
							
					</div>
					<?
				}
#############################################################################################################################
			 }
			?>
</td>
		<td>
			<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
				<h3><?=$arResult["NAME"]?></h3>
			<?endif;?>
			<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
				<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
			<?endif;?>

			<?if($arResult["NAV_RESULT"]):?>
				<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
				<?echo $arResult["NAV_TEXT"];?>
				<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
			<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
				<?echo $arResult["DETAIL_TEXT"];?>
			<?else:?>
				<?echo $arResult["PREVIEW_TEXT"];?>
			<?endif?>
			<div style="clear:both"></div>
			<br />
			<?foreach($arResult["FIELDS"] as $code=>$value):?>
					<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
					<br />
			<?endforeach;?>
			<? /*foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			
				<?=$arProperty["NAME"]?>:&nbsp;
				<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
					<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
				<?else:?>
					<?=$arProperty["DISPLAY_VALUE"];?>
				<?endif?>
				<br />
			<?endforeach; */ ?>
			
			<?
			if ($arResult[DISPLAY_PROPERTIES][WIDTH][DISPLAY_VALUE]) echo "<strong>Ширина:</strong> ".$arResult[DISPLAY_PROPERTIES][WIDTH][DISPLAY_VALUE]." см<br>";
			if ($arResult[DISPLAY_PROPERTIES][HEIGHT][DISPLAY_VALUE]) echo "<strong>Высота:</strong> ".$arResult[DISPLAY_PROPERTIES][HEIGHT][DISPLAY_VALUE]." см<br>";
			if ($arResult[DISPLAY_PROPERTIES][DEPTH][DISPLAY_VALUE]) echo "<strong>Глубина:</strong> ".$arResult[DISPLAY_PROPERTIES][DEPTH][DISPLAY_VALUE]." см<br>";
                        if ($arResult[DISPLAY_PROPERTIES][DEPTH][LENGTH_PLACE]) echo "<strong>Длина спального места:</strong> ".$arResult[DISPLAY_PROPERTIES][DEPTH][LENGTH_PLACE]." см<br>";
                        if ($arResult[DISPLAY_PROPERTIES][DEPTH][WIDTH_PLACE]) echo "<strong>Ширина спального места:</strong> ".$arResult[DISPLAY_PROPERTIES][DEPTH][WIDTH_PLACE]." см<br>";
                        if ($arResult[PROPERTIES][PLACE_NEW][VALUE]) echo "<strong>Размер спального места:</strong> ".$arResult[PROPERTIES][PLACE_NEW][VALUE]." см<br>";
                        if ($arResult[PROPERTIES][PLACE_NEW][VALUE]) echo "<strong>Декор:</strong> ".$arResult[PROPERTIES][DEKOR][VALUE]." <br>";
			if ($arResult[DISPLAY_PROPERTIES][COLLECTION][DISPLAY_VALUE]) echo "<strong>Коллекция:</strong> ".$arResult[DISPLAY_PROPERTIES][COLLECTION][DISPLAY_VALUE]."<br>";
			//print_r($arResult[DISPLAY_PROPERTIES][SALONS][DISPLAY_VALUE]);
			
			if (is_array($arResult[DISPLAY_PROPERTIES][SALONS][DISPLAY_VALUE])) 
				echo "Продаётся в салонах: ".implode("&nbsp;/&nbsp;", $arResult[DISPLAY_PROPERTIES][SALONS][DISPLAY_VALUE])."<br>";
			elseif ($arResult[DISPLAY_PROPERTIES][SALONS][DISPLAY_VALUE]) 
				echo "Продаётся в салонах: ".$arResult[DISPLAY_PROPERTIES][SALONS][DISPLAY_VALUE]."<br>";
				
			if ($arResult[DISPLAY_PROPERTIES][TRANSFORMATION][DISPLAY_VALUE]) echo "Трансформация: <!--a href=\"javascript:void(winPop('/catalog/transform/".$arResult[DISPLAY_PROPERTIES][TRANSFORMATION][VALUE].".html',%20'mww',%20550,%20410));\"-->".strip_tags($arResult[DISPLAY_PROPERTIES][TRANSFORMATION][DISPLAY_VALUE])."<!--/a--><br>";
//print_r($arResult[DISPLAY_PROPERTIES][TRANSFORMATION]);
			if ($arResult[DISPLAY_PROPERTIES][ALSO][DISPLAY_VALUE]) {
				echo "<div class=\"also\">Смотрите также</div> ";
				if (is_array($arResult[DISPLAY_PROPERTIES][ALSO][DISPLAY_VALUE])) echo implode("<br>", $arResult[DISPLAY_PROPERTIES][ALSO][DISPLAY_VALUE])."<br>";
				else echo $arResult[DISPLAY_PROPERTIES][ALSO][DISPLAY_VALUE];
			}
			/*echo "<pre>DISPLAY_PROPERTIES - ";
			print_r($arResult[DISPLAY_PROPERTIES]);
			echo "</pre>";*/
			?>
		</td></tr>
<?/* ############################   комбинации     #############################
?>
<tr><td colspan="2">
<?

	$arSelect = Array("ID", "NAME", "PROPERTY_PRICE", "PROPERTY_COMBINAC", "PREVIEW_PICTURE");
	$arFilter = Array("IBLOCK_ID"=>24, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_SUBJECT" => $_GET[ELEMENT_ID]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	if ($res->SelectedRowsCount()>0) echo "<div class=\"gray_td_left\" width=\"400px\"><h1>КОМБИНАЦИИ</h1></div>";
	while($ob = $res->GetNextElement()){
		
		$arFields = $ob->GetFields();
		$resFileCombinac = CFile::GetById($arFields[PREVIEW_PICTURE]);
		$arFileCombinac = $resFileCombinac -> Fetch();
		$resProperty = CIBlockProperty::GetByID("SRC", false, "company_news");
		$arProperty = $resProperty->Fetch();
		
		$scr = "/upload/".$arFileCombinac[SUBDIR]."/".$arFileCombinac[FILE_NAME];
		$detail_url = "/catalog/subject/".$arFields[ID].".html";
		$transform_url = "/catalog/transform/".$arFields[PROPERTY_TRANSFORMATION_VALUE].".html";
		?>
		<div class="combin" style="float:left; width:230px;">
			<strong><?=$arFields[NAME]?></strong>
			<div class="img" align="left">
				<img src="<?=$scr?>"><br>
				<?//foreach($arFileTexture as $key => $val){
				//	$src_texture = "/upload/".$val[SUBDIR]."/".$val[FILE_NAME];
				//	?>
				//	<a href="/texture/<?=$key?>.html"><img src="<?=$src_texture?>" align="left" width="40" /></a>
				//	<?
				//}?>
				<a href="/shop/basket/?add=1&subj=<?=$arFields[ID]?>&comb=<?=$arFields[ID]?>"></a>
				<div align="left" id="subject_id_<?=$arFields['ID']?>" class="">
					<img src="/images/basket.jpg"><a onclick='javascript: addSubject(<?=$_GET[ELEMENT_ID]?>, <?=$arFields[ID]?>, 0);  return false;' href="javascript://">Положить в корзину</a>
				</div>
			</div>
			

				
		</div>
		<? 
	}

?>
<br><br><br></td></tr>
<?

##################################################################  */?>
	
</table>
<?//echo "<pre>"; print_r($arResult); echo "</pre>";?>