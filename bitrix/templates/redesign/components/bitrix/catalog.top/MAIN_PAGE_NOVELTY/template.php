<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<br />
<table cellpadding="0" cellspacing="0" border="0">
                <tr>

<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("RAND"=>"RAND"), 2, Array("!PROPERTY_NOVELTY"=>false));

 while($arItem = $items->GetNext())
   {
?>

                                        <td valign="bottom" height="120" width="175" align="left">



<?   echo ShowImage($arItem["PREVIEW_PICTURE"], 156, 97,
                     "border='0'", "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html"); ?>

<div class="new_main">
<a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html"><?=$arItem["NAME"]?></a>
</div>

<?
   }
?>
<td valign="top"  style="padding-top: 3px"><a href="/mydivan/inter_foto.php"><img src="/flash/konkurs.gif" width="156" height="94"></td>
</tr>
</table>

