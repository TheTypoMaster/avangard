<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("SORT"=>"ASC", "ID"=>"DESC"), 1000, Array("!PROPERTY_NOVELTY"=>false, "!ID"=>$item));
 $cnt = 0;
 $arParams = 3;
 while($arItem = $items->GetNext())
   {

   $arRes[$cnt] = $arItem;
   $cnt++;

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



