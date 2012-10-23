
<?$items0 = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("RAND"=>"RAND"), 1, Array("!PROPERTY_HIT"=>false), Array("NAME", "PREVIEW_TEXT", "IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "PROPERTY_MORE_PHOTO"));

 while($arItem0 = $items0->GetNext())
   {

	$item = $arItem0["ID"];

?>

<?$APPLICATION->IncludeComponent("bitrix:catalog.element", "hit-top-sq4", Array(
	"IBLOCK_TYPE"	=>	"catalogue",
	"IBLOCK_ID"	=>	"5",
	"ELEMENT_ID"	=>	$arItem0["ID"],
	"SECTION_URL"	=>	"section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
	"DETAIL_URL"	=>	"element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
	"BASKET_URL"	=>	"/personal/basket.php",
	"ACTION_VARIABLE"	=>	"action",
	"PRODUCT_ID_VARIABLE"	=>	"id",
	"SECTION_ID_VARIABLE"	=>	"SECTION_ID",
	"META_KEYWORDS"	=>	"-",
	"META_DESCRIPTION"	=>	"-",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"N",
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
	),
	"PRICE_CODE"	=>	"",
	"USE_PRICE_COUNT"	=>	"N",
	"SHOW_PRICE_COUNT"	=>	"1",
	"LINK_IBLOCK_TYPE"	=>	"",
	"LINK_IBLOCK_ID"	=>	"",
	"LINK_PROPERTY_SID"	=>	"",
	"LINK_ELEMENTS_URL"	=>	"link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600"
	)
);?>

<?}?>

<br />

<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("SORT"=>"ASC", "ID"=>"DESC"), 1000, Array("!PROPERTY_HIT"=>false, "!ID"=>$item));
 $cnt = 0;
 $arParams = 3;
 while($arItem = $items->GetNext())
   {

   $arRes[$cnt] = $arItem;
   $cnt++;

   }

   if(count($arRes) < 3){
   $arRes[$cnt] = GetIBlockElement($item);
   }
?>




<table cellpadding="0" cellspacing="0" border="0">
		<?foreach($arRes as $cell=>$arItem):?>

		<?if($cell%$arParams == 0):?>
		<tr>
		<?endif;?>

		<td valign="top" width="<?=round(100/$arParams)?>%">

                             <table cellpadding="0" cellspacing="3" border="0" width="100%">
				<tr>
					<td valign="top" height="110">


<?   echo ShowImage($arItem["PREVIEW_PICTURE"], 156, 97,
                     "border='0'", "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html"); ?>


					</td>
				</tr>


<tr>
<td class="hit-title" valign= "top">
<li><a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html"><?=$arItem["NAME"]?></a></li>
</td>
</tr>
<tr>
<td class="hit-text" valign= "top" height="150">
<div style="display:block; width:auto; height:146px; overflow:hidden;">
<?=$arItem["PREVIEW_TEXT"]?>
</div>
</td>
</tr>
<tr>
<td align="right" valign="middle" class="hit-continue"><a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html">
Продолжить <img src="/bitrix/templates/avangard/images/bullet2.gif" class="noborder" /></a>
</td>
</tr>
<tr>
<td>
<hr class="hline" width="100%">
</td>
</tr>
</table>
		</td>

		<?$cell++;
		if($cell%$arParams == 0):?>
			</tr>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		<?while(($cell++)%$arParams != 0):?>
			<td valign="bottom" style="padding:3px;"><hr class="hline" width="100%"></td>
		<?endwhile;?>

</table>
