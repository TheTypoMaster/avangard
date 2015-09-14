<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
















                             <table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(), 
              Array("RAND"=>"RAND"), 3, Array("!PROPERTY_HIT"=>false));

 while($arItem = $items->GetNext())
   {
?>
<td width="33%">
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
<?=$arItem["PREVIEW_TEXT"]?>
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
<?    
   }
?>
</tr>
</table>



