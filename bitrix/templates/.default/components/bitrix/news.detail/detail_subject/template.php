<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css"> 
<script type="text/javascript" src="/shadowbox/shadowbox.js"></script> 
<script type="text/javascript"> 
Shadowbox.init({
    handleOversize: "drag",
    modal: true
});
</script>
<script type="text/javascript">
$(function(){
  $("#texture_box").dialog({
	position: ["center","center"],
				width: 325,
				height: 450, // 'auto'
	resizable : false,
	autoOpen: false
  });
});
</script>


<?
//print_r($_GET);
?>
<table class="product_table">
 <tr>
   <td>			
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?></h3>
	<?endif;?>			
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"  title="<?=$arResult["NAME"]?>" /></a>
	<?endif?>
	<br>
	<div style="clear:both"></div>
				
	<?
	if ($arResult[DISPLAY_PROPERTIES][WIDTH][DISPLAY_VALUE]) echo "<strong>Ширина:</strong> ".$arResult[DISPLAY_PROPERTIES][WIDTH][DISPLAY_VALUE]." см<br>";
	if ($arResult[DISPLAY_PROPERTIES][HEIGHT][DISPLAY_VALUE]) echo "<strong>Высота:</strong> ".$arResult[DISPLAY_PROPERTIES][HEIGHT][DISPLAY_VALUE]." см<br>";
	if ($arResult[DISPLAY_PROPERTIES][DEPTH][DISPLAY_VALUE]) echo "<strong>Глубина:</strong> ".$arResult[DISPLAY_PROPERTIES][DEPTH][DISPLAY_VALUE]." см<br>";
	if ($arResult[DISPLAY_PROPERTIES][DEPTH][LENGTH_PLACE]) echo "<strong>Длина спального места:</strong> ".$arResult[DISPLAY_PROPERTIES][DEPTH][LENGTH_PLACE]." см<br>";
	if ($arResult[DISPLAY_PROPERTIES][DEPTH][WIDTH_PLACE]) echo "<strong>Ширина спального места:</strong> ".$arResult[DISPLAY_PROPERTIES][DEPTH][WIDTH_PLACE]." см<br>";
	if ($arResult[PROPERTIES][PLACE_NEW][VALUE]) echo "<strong>Размер спального места:</strong> ".$arResult[PROPERTIES][PLACE_NEW][VALUE]." см<br>";
	//if ($arResult[PROPERTIES][PLACE_NEW][VALUE]) echo "<strong>Декор:</strong> ".$arResult[PROPERTIES][DEKOR][VALUE]." <br>";
	//if ($arResult[DISPLAY_PROPERTIES][COLLECTION][DISPLAY_VALUE]) echo "<strong>Коллекция:</strong> ".$arResult[DISPLAY_PROPERTIES][COLLECTION][DISPLAY_VALUE]."<br>";
	//print_r($arResult[DISPLAY_PROPERTIES][SALONS][DISPLAY_VALUE]);		
	if ($arResult[DISPLAY_PROPERTIES][TRANSFORMATION][DISPLAY_VALUE]) echo "<strong>Механизм трансформации: </strong>".strip_tags($arResult[DISPLAY_PROPERTIES][TRANSFORMATION][DISPLAY_VALUE])."<br><br>";
	//print_r($arResult[DISPLAY_PROPERTIES][TRANSFORMATION]);
	/*echo "<pre>DISPLAY_PROPERTIES - ";
	print_r($arResult[DISPLAY_PROPERTIES]);
	echo "</pre>";*/
	?>

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
		if ($res->SelectedRowsCount()>0) echo "<h3 style='color:#d80207;'>Рекомендуемые материалы:</h3>";
		/*echo "<pre>arFilter - ";
		print_r($arFilter);
		echo "</pre>";*/
		while($ob = $res->GetNextElement()){
			
			$arFields = $ob->GetFields();
			
			// один материал
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
			
			?>
			<div class="recomm" align="center" style="float:left; width:300px;">
			<div class="img">							
			<?
			foreach($srcTexture as $key => $val){
				$src_texture = "/upload/".$val[SUBDIR]."/".$val[FILE_NAME];
				##########################################
				$idtx = $val[0];
				$arTxSelect = Array("ID", "NAME", "DETAIL_PICTURE");
				$arTxFilter = Array("IBLOCK_ID"=>20, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $idtx);
				$resTx = CIBlockElement::GetList(Array(), $arTxFilter, false, false, $arTxSelect);
				$obTx = $resTx->GetNextElement();
				$arTxFields = $obTx->GetFields();
				$resFileTx = CFile::GetByID($arTxFields[DETAIL_PICTURE]);
				$arFileTx = $resFileTx->Fetch();
				//echo "<img src=\"/upload/".$arFileTx[SUBDIR]."/".$arFileTx[FILE_NAME]."\" />";
				$name = $arTxFields[NAME];
				$path = '/upload/'.$arFileTx[SUBDIR].'/'.$arFileTx[FILE_NAME];
				##########################################
				?>
				<a href="#" value1="<?=$name?>" value2="<?=$path?>"><img src="<?=$src_texture?>" class="texture_sample" align="" width="40" /></a>
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
	 }
#############################################################################################################################
	?>
   </td>
 </tr>	
</table>

<?//echo "<pre>"; print_r($arResult); echo "</pre>";?>

<div id="texture_box" title="Название ткани">IMAGE</div>

<script type="text/javascript">
$(document).ready(function(){
var x_pos = Math.round(document.body.clientWidth/2) + 65;
$("#texture_box").dialog( "option", "position", [x_pos, "center"] );
$('.texture_sample').hover(
  function(){
  var name = $(this).parent().attr("value1");
        var path = $(this).parent().attr("value2");
        //alert(path);
        path = '<img src="http://www.avangard.biz' + path + '" />';
        $("#texture_box").html(path);
        $("#texture_box").dialog( "option", "title", name );
        if (!$("#texture_box").dialog("isOpen"))
              $("#texture_box").dialog("open");
  },
  function(){
  if ($("#texture_box").dialog("isOpen"))
              $("#texture_box").dialog("close");
  });
});	
</script>