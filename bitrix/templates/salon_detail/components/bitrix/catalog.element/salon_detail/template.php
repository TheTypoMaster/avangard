<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $img = CFile::ShowImage($arResult["DETAIL_PICTURE"]["ID"], 215, 115, "class=preview");?>
<style>
.pole {
color: #000000;
font-size: 12px;
margin:2px;
padding: 2px;

}
</style>
 
<table width="90%" border="0" cellspacing="0" cellpadding="2">
		<tr><td align="left">
	<table border="0" cellspacing="0" cellpadding="2">
<td valign="top">
<table valign="top"><tr><td valign="top">
<?if($arResult["PROPERTY_SALON_TYPE_2_VALUE"] == "Фирменный Подиум") echo "<h1 class='header'><img alt='Фирменный подиум' 

src='/wharetobuy/maps/podium.gif'>".$arResult["NAME"]."</h1>"; else
echo "<h1 class='header'><img alt='Фирменный салон' src='/wharetobuy/maps/salon.gif'>".$arResult["NAME"]."</h1>"; 
?>
<h5 class="pole"><font style="color: #adadad;">адрес:</font> <br> <?=$arResult["PROPERTIES"]["SALON_ADRESS"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">телефон:</font> <br> <?=$arResult["PROPERTIES"]["SALON_PHONE"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">часы работы:</font><br>  

<?=$arResult["PROPERTIES"]["SALON_TIME"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">доп. инфо:</font><br>  <?=$arResult["PROPERTIES"]["SALON_ROUTE"]["VALUE"]?></h5>
</td><td valign="top"><a href="<?echo CFile::GetPath($arResult["PROPERTIES"]["SALON_PHOTO"]["VALUE"]);?>" class="highslide" 

onclick="return hs.expand(this, {wrapperClassName: 'borderless floating-caption', dimmingOpacity: 0.75, align: 'center'})">
			
			<?echo CFile::ShowImage($arResult["PROPERTIES"]["SALON_PHOTO"]["VALUE"], 250, 200, 

"class=preview");?></a>
		</td></tr></table>	
			
			
			</td>
<td valign="top" width="470" height="380" rowspan="2">
<?echo htmlspecialchars_decode($arResult["PROPERTIES"]["GOOGLE_MAP"]["VALUE"]);?>
	</td>
	</tr>
	
	<tr>
	<td align="center">

	<div align="center"  style="text-align: center; height: 66px; padding: 3px;"><center>
	<?foreach($arResult["PROPERTIES"]["SALON_ITEMS"]["VALUE"] as $PHOTO):?>
<div align="center" style="float: left; margin:5px;"><a href="<?echo CFile::GetPath($PHOTO);?>" class="highslide" 

onclick="return hs.expand(this,
			{wrapperClassName: 'borderless floating-caption', dimmingOpacity: 0.75, align: 'center'})"><? echo 

CFile::ShowImage($PHOTO, 160, 90, "class=preview");?></a></div>
 
                <?endforeach?>
	</center>
	</div>
	</td>
	</tr>
</table></td></tr>
<?if($arResult["PROPERTIES"]["SALON_ACTION_TEXT"]["VALUE"] || $arResult["PROPERTIES"]["SALON_ACTION_PHOTO"]["VALUE"]){?>
	<tr>
	<td align="center" valign="top">
	<h3 align="left">Акция</h3>
	<div align="center"  style="text-align: left; height: 66px; padding: 3px;">
	<?if($arResult["PROPERTIES"]["SALON_ACTION_PHOTO"]["VALUE"]){?><div align="center" style="float: left; margin:5px; 

margin-right: 10px;"><a href="<?echo CFile::GetPath($arResult["PROPERTIES"]["SALON_ACTION_PHOTO"]["VALUE"]);?>" 

class="highslide" onclick="return hs.expand(this,{wrapperClassName: 'borderless floating-caption', dimmingOpacity: 0.75, align: 

'center'})"><img src="<? echo CFile::GetPath($arResult["PROPERTIES"]["SALON_ACTION_PHOTO"]["VALUE"]);?> " border="0" 

width="100" height="60"></a></div><?}?>
   	<?=$arResult["PROPERTIES"]["SALON_ACTION_TEXT"]["VALUE"]?>
   	<?=$arResult["PROPERTIES"]["SALON_ACTION_TEXT_2"]["VALUE"]?>
	</div>




<br />

	</td>
	</tr>
	<?}?>
	<tr>
	<td align="center">
	<h3 align="left"><a href="/mebel_sal.php?id=<?=$_GET['id']?>">Диваны в наличии в этом салоне</a></h3>
	<div align="center"  style="text-align: center; width: 100%; height: 66px; padding: 3px;"><center><?
	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_salon", "PROPERTY_razmeri", 

"PROPERTY_tovar","PROPERTY_color", "PROPERTY_price_old", "PROPERTY_price_new", "PROPERTY_mechanizm",  "PROPERTY_spal", 

"PROPERTY_aktia");
    $arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_salon"=>IntVal($arResult['ID']));
    $res_items = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect); 
	while($ob = $res_items->GetNextElement())
    {
    $arFields = $ob->GetFields(); ?>
	<div align="center" style="background: #e9e9e9; float: left; padding:5px;"><a 

href="/mebel_sal.php?id=<?=$_GET['id']?>"><img src="<? echo CFile::GetPath($arFields['PREVIEW_PICTURE']);?> " border="0" 

width="100" height="60"></a></div>
	<?}?>
	
	</center>
	</div>
	</td>
	</tr>
<tr>
	<td align="left">
<br />


        <?if($arResult["DETAIL_TEXT"]):?>
                <br /><?=$arResult["DETAIL_TEXT"]?><br />
        <?elseif($arResult["PREVIEW_TEXT"]):?>
                <br /><?=$arResult["PREVIEW_TEXT"]?><br />
        <?endif;?>


</td>
	</tr>	
	
	</table>
