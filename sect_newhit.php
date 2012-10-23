<table height="130" width="188" border="0" cellspacing="0" cellpadding="5">
   <tr>
   <td align="left" bgcolor="#efefef"  style="padding-left: 10px" valign="top">
   <div class="hit"><a href="/hits/">хиты продаж</a></div>




<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("RAND"=>"RAND"), 1, Array("!PROPERTY_HIT"=>false));

   // цикл по всем новостям
   while($arItem = $items->GetNext())
   {
      ?>
 <div style="height:90; width: 170; overflow: hidden; display: block; vertical-align: middle;" align="left" valign="bottom">




 <?
 echo ShowImage($arItem["PREVIEW_PICTURE"], 170, 100, "class='preview'",
 "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html");
?>
</div>



<a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html" class="newhit"><?=$arItem["NAME"]?></a>


</td>
                          </tr>
                        </table>



<?
}
?>




