<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<style>
.pole {
color: #000000;
font-size: 12px;
margin:2px;
padding: 2px;

}
</style>

<div class="catalog-element">
	<table width="720" border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td width="300"><h1><?=$arResult["NAME"]?></h1>

<h5 class="pole"><font style="color: #adadad;">адрес:</font> <?=$arResult["PROPERTIES"]["SALON_ADRESS"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">телефон:</font> <?=$arResult["PROPERTIES"]["SALON_PHONE"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">часы работы:</font> <?=$arResult["PROPERTIES"]["SALON_TIME"]["VALUE"]?></h5>
<h5 class="pole"><font style="color: #adadad;">доп. инфо:</font> <?=$arResult["PROPERTIES"]["SALON_ROUTE"]["VALUE"]?></h5>
<?if($arResult["PROPERTIES"]["SALON_SITE"]["VALUE"]){?><h5 class="pole"><font style="color: #adadad;">сайт: </font> <a target="_blank" href="http://<?=$arResult["PROPERTIES"]["SALON_SITE"]["VALUE"]?>"><?=$arResult["PROPERTIES"]["SALON_SITE"]["VALUE"]?></a></h5><?}?>


</td>
<td></td>
<td width=20>
</td>
<td align="center" width="200">
<a href="<?echo CFile::GetPath($arResult["DETAIL_PICTURE"]["ID"]);?>" class="highslide" onclick="return hs.expand(this,
			{wrapperClassName: 'borderless floating-caption', dimmingOpacity: 0.75, align: 'center'})"><? echo CFile::ShowImage($arResult["DETAIL_PICTURE"]["ID"], 180, 90, "class=preview");?></a></td>
<td align="right" valign="top" width="180">
<a href="<?echo CFile::GetPath($arResult["PREVIEW_PICTURE"]["ID"]);?>" class="highslide" onclick="return hs.expand(this,
			{wrapperClassName: 'borderless floating-caption', dimmingOpacity: 0.75, align: 'center'})"><? echo CFile::ShowImage($arResult["PREVIEW_PICTURE"]["ID"], 180, 90, "class=preview");?></a></td>


         </tr>
<?if($arResult["PROPERTIES"]["SALON_ACTION_LINK"]["VALUE"]) {?><tr><td colspan="6">
<h4 class="actia"><a href="<?=$arResult["PROPERTIES"]["SALON_ACTION_LINK"]["VALUE"]?>">УЧАСТНИК АКЦИИ</a></h4></td></tr><?}?>



<tr><td colspan="6" height="2"><div align="center" width="700" height="2" style="border-top: solid gray 1px; margin-top: 3px;"><img height="1" src="/images/gif.gif"></div></td></tr>
	
	</table>
	
	
</div>

