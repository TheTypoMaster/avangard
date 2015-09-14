<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css"> 
<script type="text/javascript" src="/shadowbox/shadowbox.js"></script> 
<script type="text/javascript"> 
Shadowbox.init({
    handleOversize: "drag",
    modal: true
});

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
if($arResult["PROPERTIES"]["SEO_TITLE_R"]["VALUE"]) $seotitle = $arResult["PROPERTIES"]["SEO_TITLE_R"]["VALUE"];
else $seotitle = $arResult["NAME"];
$APPLICATION->SetTitle($seotitle);
?>
<?if($_GET[id]) {?><script src="/script.js" type="text/javascript"></script><?}?>

<div class="bottext">

<div class="gray_td" id="extra_controls">
<h1 class="itemtitle"><?=$arResult["PROPERTIES"]["F_TYPE"]["VALUE"]?> <?=$arResult["NAME"]?>
<font style="font-weight:normal; color: #000000; margin-left: 22px;">Коллекция:</font>
<? $id_collection = $arResult["PROPERTIES"]["COLLECTION"]["VALUE"];
$arResColl=GetIBlockElement($id_collection);   echo($arResColl["NAME"]);
if ($arResColl["ID"]==5757) {
	// корпусная мебель id 5757
       $big_height = 440;
} else $big_height = 330;
 ?>
</h1>
</div>

<table width="720" border="0" cellspacing="2" cellpadding="0">

 <tr>
    <td align="center" valign="top">

	
	<?
	if(count($arResult["MORE_PHOTO"])>0){

	reset($arResult["MORE_PHOTO"]);
	$M_PHOTO = current($arResult["MORE_PHOTO"]);

	$m_folder = str_replace($M_PHOTO["FILE_NAME"], "", $M_PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
    $m_s_puth=$m_folder."s_".$M_PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

    $m_puth=$m_folder.$M_PHOTO["FILE_NAME"];//получаем путь до основной картинки



	}
?>

<script>
<!--

var url="<?=$m_puth?>";

-->
</script>

	
	<?
	if(count($arResult["MORE_PHOTO"])>0){
	?>
		<img height="<?=$big_height?>" src="<?=$m_puth?>" name="mainimg" class="preview">
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
        if(count($arResult["MORE_PHOTO"])>0):

$t_width = 5;
$height = 75;

    foreach($arResult["MORE_PHOTO"] as $PHOTO):
	$arFile = CFile::GetFileArray($PHOTO["ID"]);
	$width =  $arFile["WIDTH"] / $arFile["HEIGHT"] * 50;
	$t_width = $t_width + $width + 10;
	endforeach;


	if ($t_width > 720){
		$height = 90;
	}
 ?>

<div  style="background-color: #f2f2f2; width: 720px; height:<?=$height?>px; overflow: auto; border: 1px solid #e4e4e4;">


<table height="70">
<tr>
<td valign="bottom">
<nobr>




            <?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
<?
   $folder = str_replace($PHOTO["FILE_NAME"], "", $PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
   $s_puth=$folder."s_".$PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

   $puth=$folder.$PHOTO["FILE_NAME"];//получаем путь до основной картинки
 ?>

<a href="#null" onClick="MM_swapImage('mainimg','','<?=$puth?>',1); javascript:url='<?=$puth?>';return false">
<?echo CFile::ShowImage($s_puth, 150, 50, "class=preview");?>
</a>
                <?endforeach?>

</nobr>
</tr>
</td>
</table>
</div>

<?endif?>


  </td>
  </tr>
</table>

<br />
<? 
if ($arResColl["ID"]==5757) {
	// корпусная мебель id 5757
       include( $_SERVER['DOCUMENT_ROOT']. '/8days/act_im_korpus.php' );
} 
?>
<table width="100%"><tr><td>
<? if($arResult["PROPERTIES"]["HREF_3D"]["VALUE"]) echo '<a href="/catalog/vybor_tkani.php?id='.$arResult["PROPERTIES"]["HREF_3D"]["VALUE"].'" target="_blank"><img src="/images/btn3D.png" alt="Переодевание 3D" border=0></a>';?>
</td><td></td></tr></table>

<br />


<?if(($arResult["PROPERTIES"]["ACTIA_DESCRIPTION"]["VALUE"]) || ($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]) || 

($arResult["PROPERTIES"]["NOVELTY_DESCRIPTION"]["VALUE"]))  $width_td=' width="100%"';?>

<br>
</div>
<br><br>
<div>
 <?
 global $USER;
 /*if ($USER->GetId()) {*/
 	$APPLICATION->AddHeadScript("/basket/basket.js");
 	//echo "<!-- user показать авторизованному пользователю -->";
	
	//print_r($_GET);
	$arSelect = Array("ID", "NAME", "PROPERTY_ARTICLE", "PROPERTY_WIDTH", "PROPERTY_HEIGHT", "PROPERTY_DEPTH", "PROPERTY_DEKOR", "PROPERTY_TRANSFORMATION", "PROPERTY_TRANSFORMATION.NAME", "PROPERTY_SKELETON", "PROPERTY_SKELETON.NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_PLACE_NEW");
	$arFilter = Array("IBLOCK_ID"=>19, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COLLECTION" => $_GET[id]);
	$res19 = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
	if ($res19->SelectedRowsCount()>0) echo "<h3>Предметы модельного ряда</h3><hr>";
	while($ob19 = $res19->GetNextElement()){
		
		$ar19Fields = $ob19->GetFields();
		//$res19File = CFile::GetById($ar19Fields[PREVIEW_PICTURE]);
		$res19File = CFile::GetById($ar19Fields[DETAIL_PICTURE]);
		$ar19File = $res19File -> Fetch();
		/*$arProperty = CIBlockProperty::GetByID("SRC", false, "company_news");
		echo "<pre>arFields - ";
		print_r($ar19Fields);
		echo "<br><br>arFile - ";
		print_r($ar19File); 
		echo "<br><br>arProperty - ";
		print_r($arProperty);
		echo "</pre>";*/
		$scr = "/upload/".$ar19File[SUBDIR]."/".$ar19File[FILE_NAME];
		$detail_url = "/shop/catalog/subject/".$ar19Fields[ID].".html";
		$transform_url = "/catalog/transform/".$ar19Fields[PROPERTY_TRANSFORMATION_VALUE].".html";
		######################  выбираем все комбинации у данного предмета   ######################
		$arSelect = Array("ID"); 
		$arFilter = Array("IBLOCK_ID"=>24, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_SUBJECT" => $ar19Fields[ID]);
		$res24 = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		$arCombinacID = array();
		while($ob24 = $res24->GetNextElement()){
		
			$arCombFields = $ob24->GetFields();
			$arCombinacID[] = array("PROPERTY_COMBINAC" => $arCombFields[ID]);		
		}
		if (sizeof($arCombinacID)>0) 
			$arCombinacID["LOGIC"] =  "OR";
		######################

		?>
		<div class="product_table">
			<div class="img" style="float:left; width:320px;">
				<img src="<?=$scr?>">
				<!-- <a href="<?=$detail_url?>"><img src="<?=$scr?>"></a> -->
				<!-- <br><a href="<?=$detail_url?>">Подробности и цены</a> -->
			</div>
			<div>
				<div align="left"><h3><?=$ar19Fields[NAME]?></h3></div>
				<table cellspacing="0" cellpadding="0" class="gabsinmod11"><tbody><tr><td style="text-align: left;">
				<? if($ar19Fields[PROPERTY_ARTICLE_VALUE]) { ?> <nobr><b>Артикул:</b>	<?=$ar19Fields[PROPERTY_ARTICLE_VALUE]?></nobr><br><? } ?>
				<b>Габариты</b><br>
				<? if($ar19Fields[PROPERTY_WIDTH_VALUE]) { ?> <nobr>ширина: <?=$ar19Fields[PROPERTY_WIDTH_VALUE]?> см</nobr><br><? } ?>
				<? if($ar19Fields[PROPERTY_DEPTH_VALUE]) { ?> <nobr>глубина: <?=$ar19Fields[PROPERTY_DEPTH_VALUE]?> см</nobr><br><? } ?>
				<? if($ar19Fields[PROPERTY_HEIGHT_VALUE]) { ?> <nobr>высота:	<?=$ar19Fields[PROPERTY_HEIGHT_VALUE]?> см</nobr><br><? } ?>
				<? if($ar19Fields[PROPERTY_PLACE_NEW_VALUE]) { ?> <br><nobr><b>Размер спального места:</b>	<?=$ar19Fields[PROPERTY_PLACE_NEW_VALUE]?> см</nobr><br><? } ?>
				<? if($ar19Fields[PROPERTY_TRANSFORMATION_NAME]) { ?> <br><nobr><b>Механизм:</b> <?=$ar19Fields[PROPERTY_TRANSFORMATION_NAME]?></nobr><? } ?>
				<? if($ar19Fields[PROPERTY_SKELETON_NAME]) { ?> <br><nobr><b>Материалы:</b> <?=$ar19Fields[PROPERTY_SKELETON_NAME]?></nobr><? } ?>
				<? if($ar19Fields[PROPERTY_DEKOR_VALUE]) { ?> <br><nobr><b>Отделка:</b> <?=$ar19Fields[PROPERTY_DEKOR_VALUE]?></nobr><? } ?>
				<? if($ar19Fields[PREVIEW_TEXT]) { ?> <br><br><b>Описание:</b> <?=$ar19Fields[PREVIEW_TEXT]?><? } ?>
<? /*echo "<pre>arCombinacID - ";
print_r($arCombinacID);
echo "</pre>"; */ ?>
				</td></tr></tbody></table>
			</div>
			<div style="clear:left"></div>
<?

###########################  выбираем рекомендуемые материалы   #############################
	if (is_array($arCombinacID) && sizeof($arCombinacID)>0) { // найдены комбинации
		$arSelect = Array("ID", "NAME", "PROPERTY_PRICE", "PROPERTY_COMBINAC", "PROPERTY_COMBINAC.PREVIEW_PICTURE", "PREVIEW_PICTURE", "DETAIL_PICTURE");
		$arFilter = Array("IBLOCK_ID"=>21, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", $arCombinacID);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		//if ($res->SelectedRowsCount()>0) echo "<h3 style='color:#d80207;'>Рекомендуемые материалы:</h3>";




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
				
				############  получаем картинку ################################
				$resFileRecommTexture = CFile::GetById($arRecommTexture[PREVIEW_PICTURE]);
				$arFileRecommTexture = $resFileRecommTexture->Fetch();
				#################################################################
				array_push ($arFileRecommTexture, $arFieldsTexture[PROPERTY_TEXTURE_VALUE]);
				$srcTexture[] = $arFileRecommTexture;
				reset($srcTexture);
				$first_key = key($srcTexture);
				//echo "<pre>$srcTexture - ";
				//print_r($srcTexture);
				//echo "</pre>";
				//$srcTexture[FILE][] = "<img src=\"/upload/".$arFileRecommTexture[SUBDIR]."/".$arFileRecommTexture[FILE_NAME]."\" />";
			}
			if ($srcTexture[$first_key][SUBDIR]) echo "<h3 style='color:#d80207;'>Рекомендуемые материалы:</h3>"; 
			?>
			<div class="recomm" align="center" style="float:left; width:300px;">
			<div class="img">							
			<?
			if ($srcTexture[$first_key][SUBDIR]) {
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
				<a href="javascript:" value1="<?=$name?>" value2="<?=$path?>"><img src="<?=$src_texture?>" class="texture_sample" align="" width="40" /></a>
				<?
			  }
			}

					//print_r($arFileTexture);
					
			?>
				<div style="clear:left"></div>
				<div class="left_block" style="float:left;">
					Цена: <?=$arFields["PROPERTY_PRICE_VALUE"]." руб."?>
					<a href="/shop/basket/?add=1&subj=<?=$arFields[ID]?>&comb=<?=$arFields[PROPERTY_COMBINAC_VALUE]?>"></a>
					<div align="left" id="subject_id_<?=$arFields['ID']?>" class="">
						<img src="/images/basket.jpg"><a onclick="addSubject(<?=$ar19Fields[ID]?>, <?=$arFields[PROPERTY_COMBINAC_VALUE]?>, <?=$arFields['ID']?>); return false;" href="javascript://">Положить в корзину</a><br><br><br>
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
			<div style="clear: both;"></div><hr>
		</div>

		<?
		
	}
	
	

	
/* }
 else {
 	//echo "<!-- user не нужно показывать -->";
 }*/
 ?>
 </div>

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