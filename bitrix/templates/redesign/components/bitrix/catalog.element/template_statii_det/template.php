<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table cellspacing="0" cellpadding="0" align="left" width="100%"> 
  <tbody> 
    <tr><td width="100%"> 
        <div class="gray_td"> 
          <h1><?=$arResult['NAME']?></h1>
         </div>
       </td></tr>
   
    <tr><td>
	<?if($arResult["DETAIL_TEXT"]):?>
		<br /><?=$arResult["DETAIL_TEXT"]?><br />
	<?elseif($arResult["PREVIEW_TEXT"]):?>
		<br /><?=$arResult["PREVIEW_TEXT"]?><br />
	<?endif;?>
	</td></tr>
	
	<?if($arResult["DISPLAY_PROPERTIES"]["tegs"]):?>
	<tr><td>
		<table>
  <?
	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_SEO_TAGS"=>$arResult["DISPLAY_PROPERTIES"]["tegs"]["VALUE"]);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   $i=0;
	   $rows_count= $resElement->SelectedRowsCount();
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
		$rows_count--;
		if($i==1) echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
	    $arElementFields = $obElement->GetFields();  
		
		if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']) $img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); else
		$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
		
		
		
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']) $img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); else $img_path_bl = $img_path;
		          ?>
				
				<td class="catalog_td">
				<a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><img onMouseOver="this.src='<?=$img_path?>';" onMouseOut="this.src='<?=$img_path_bl?>';" class="catalog_picture" src="<?=$img_path_bl?>" alt="<?=$arElementFields[NAME]?>"></a><br>
				<a class="catalog_name" href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a>
				
				
				</td>
		 <?if($i<3) { ?><td width="26"></td> <?}?>
				<?
	    if($i==3) { $i=0; echo "</tr>"; }
         }
		 

?>

	</table>
		</td></tr>
		

<?elseif(count($arResult["DISPLAY_PROPERTIES"]["models"])>0):?>
		<tr><td>
		<table>
  <?
	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID"=>$arResult["DISPLAY_PROPERTIES"]["models"]["VALUE"]);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   $i=0;
	   $rows_count= $resElement->SelectedRowsCount();
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
		$rows_count--;
		if($i==1) echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
	    $arElementFields = $obElement->GetFields();  
		
		if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']) $img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); else
		$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
		
		
		
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']) $img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); else $img_path_bl = $img_path;
		          ?>
				
				<td class="catalog_td">
				<a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><img onMouseOver="this.src='<?=$img_path?>';" onMouseOut="this.src='<?=$img_path_bl?>';" class="catalog_picture" src="<?=$img_path_bl?>" alt="<?=$arElementFields[NAME]?>"></a><br>
				<a class="catalog_name" href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a>
				
				
				</td>
		 <?if($i<3) { ?><td width="26"></td> <?}?>
				<?
	    if($i==3) { $i=0; echo "</tr>"; }
         }
		 

?>



	</table>
		</td></tr>


<?endif?>


<?if(count($arResult["DISPLAY_PROPERTIES"]["salons"])>0):?>
<tr><td>
<table class="spisok_salonov_sm" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr>
		<td width="100"><center>—“. Ã≈“–Œ, √Œ–Œƒ</center></td><td  width="40"><center>¿ ÷»ﬂ</center></td><td  width="100"><center>—¿ÀŒÕ</center></td><td  width="100"  align="center"><center>¿ƒ–≈—</center></td><td  width="70"><center>¬ Õ¿À»◊»»</center></td>
		
	</tr>
	</thead>
	<?$bgcol = "#ffffff";?>
    <?$arElementSelectSalon = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_SALON_METRO", "PROPERTY_SALON_CITY", "PROPERTY_SALON_ACTION", "PROPERTY_SALON_ADRESS",  "PROPERTY_SALON_ACTION_TEXT", "IBLOCK_ID");
	
	  $arElementFilterSalon = Array("IBLOCK_ID"=>IntVal(8), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID"=>$arResult["DISPLAY_PROPERTIES"]["salons"]["VALUE"]);

	$resElementSalon = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arElementFilterSalon, false, Array("nPageSize"=>50), $arElementSelectSalon);

	  $i=0;
	   $rows_count= $resElement->SelectedRowsCount();
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
		$rows_count--;
		if($i==1) echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
        $arSalon = $obElementSalon->GetFields();  ?>
		
	<tr bgcolor="<?=$bgcol?>">
		<td>
			<? if($arSalon["PROPERTY_SALON_CITY_VALUE"] =='ÃÓÒÍ‚‡') echo "<img src='/wharetobuy/maps/metro.gif'> ".$arSalon["PROPERTY_SALON_METRO_VALUE"]; else echo $arSalon["PROPERTY_SALON_CITY_VALUE"];  ?>
		</td>
		<td  align="center">
			<?if($arSalon["PROPERTY_SALON_ACTION_TEXT_VALUE"]) echo '<center><img width="24" height="24" border="0" src="/images/akciya.gif" alt="'.$arSalon["PROPERTY_SALON_ACTION_TEXT_VALUE"].'"></center>';?>
		</td>
		<td>
			<a href="/redesign/where_buy/detail.php?id=<?=$arSalon["ID"]?>" onClick="showInfo(<?=$arSalon["ID"]?>); return false;"    target="_new"><?=$arSalon["NAME"]?></a>
		</td>
		<td>
			<?=$arSalon["PROPERTY_SALON_ADRESS_VALUE"]?>
		</td>
		<td align="center">
	<center>						
<?	

$arSelectSub = Array("ID", "NAME");
$arFilterSub = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_salon"=>$arSalon["ID"]);
$res_items_sub = CIBlockElement::GetList(Array(), $arFilterSub, false, Array("nPageSize"=>500), $arSelectSub);
$counter_sub = 0;
//$res_items->NavStart(500);
$ob_sub = $res_items_sub->SelectedRowsCount();
if($ob_sub>0){?><a style="text-decoration: none; font-size: 14px; " href="/mebel_sal.php?id=<?=$arSalon["ID"]?>" onClick="showDivans(<?=$arSalon["ID"]?>); return false;"    target="_new"><img style="padding-top: 3px;" src="/images/camera.gif" border="0" alt="ƒË‚‡Ì˚ ‚ Ì‡ÎË˜ËË"> (<?=$ob_sub?>)
</a>
<?}?></center>
		</td>
		
	</tr>
        <?if($bgcol == "#ffffff") $bgcol = "#ededed"; else $bgcol = "#ffffff";?>

<?}?>
	</table>
</td></tr>
<?endif?>
</table>
	