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
	
    <a href="#" onClick="window.print(); return false;">Распечатать</a>  

<table width="720" border="0" cellspacing="0" cellpadding="2">
		<tr>
	<td width="250" valign="top">
<?if($arResult["PROPERTY_SALON_TYPE_2_VALUE"] == "Фирменный Подиум") echo "<h1 class='header'><img src='/wharetobuy/maps/podium.gif'>Фирменный подиум</h1>"; else
echo "<h1 class='header'><img src='/wharetobuy/maps/salon.gif'>Фирменный салон</h1>"; 
?>
<h5 class="pole"><font style="color: #adadad;">адрес:</font> <?=$arResult["PROPERTIES"]["SALON_ADRESS"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">телефон:</font> <?=$arResult["PROPERTIES"]["SALON_PHONE"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">часы работы:</font> <?=$arResult["PROPERTIES"]["SALON_TIME"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">доп. инфо:</font> <?=$arResult["DETAIL_TEXT"]?></h5>
<?echo CFile::ShowImage($arResult["PROPERTIES"]["SALON_PHOTO"]["VALUE"], 200, 120, "class=preview");?></td>
<td width="470" height="380">
<?echo htmlspecialchars_decode($arResult["PROPERTIES"]["GOOGLE_MAP"]["VALUE"]);?>
	</td>
	</tr>
	<tr>
	<td colspan="2">
	<div style="width: 710px; height: 66px; padding: 3px;">
	<?foreach($arResult["PROPERTIES"]["SALON_ITEMS"]["VALUE"] as $PHOTO):?>
<div style="float: left; margin:1px;"><a href="<?echo CFile::GetPath($PHOTO);?>" class="highslide" onclick="return hs.expand(this,
			{wrapperClassName: 'borderless floating-caption', dimmingOpacity: 0.75, align: 'center'})"><? echo CFile::ShowImage($PHOTO, 120, 60, "class=preview");?></a></div>
 
                <?endforeach?>
	
	</div>
	</td>
	</tr>
	</table>
