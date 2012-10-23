<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr><td width="100%">
                 <div class="gray_td"><h1>НОВИНКИ </h1></div>
           </td></tr>
  
    <tr><td>
    
<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("RAND"=>"RAND"), 1, Array("!PROPERTY_HIT"=>false));
   // цикл по всем новостям
   while($arItem = $items->GetNext())
   {
      ?>
 <div style="height:90; width: 170; display: block; vertical-align: middle;">


 <?echo ShowImage($arItem["PREVIEW_PICTURE"], 170, 100, "class='preview'",
 "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html");
?>
</div>



<a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html" class="newhit"><?=$arItem["NAME"]?></a>


<?
}
?>




      </td></tr>
  </tbody>
</table>
