<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<br />

                        <table width="525" height="175" border="0" cellpadding="0" cellspacing="5"  style="BACKGROUND: 

url(/bitrix/templates/avangard/images/bg_sq.gif) no-repeat left top">
                          <tr> 
                            <td rowspan="3" align="left" valign="middle" style="padding:7px 0px 7px 7px " width="300">


<?$items = GetIBlockElementListEx("catalogue", "furniture", Array(), 
              Array("RAND"=>"RAND"), 1, Array("!PROPERTY_HIT"=>false));

 while($arItem = $items->GetNext())
   {

   echo ShowImage($arItem["DETAIL_PICTURE"], 280, 140, 
                     "border='0'", "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html"); ?>



                            </td>
                            <td style="padding: 0px 7px" class="hit-title-month" height="35">В этом месяце "<?echo $arItem["NAME"]?>"<br />
<span class="hit-title-hit">Хит Продаж!</span></td>
                          </tr>
                          <tr> 
                            <td class="bottext">
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<div style="	height: 65px;
	overflow: hidden;
	display: block;
"><?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
</td>
                          </tr>
                          <tr> 
                            <td height="35" align="right" valign="top" style="padding: 0px 7px" >
					
			     
                              <table width="200" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td align="right" valign="middle"  class="hit-continue">
<a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html">Продолжить</a></td>
                                  <td width="30" align="right">
<a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html"><img src="/bitrix/templates/avangard/images/top_next.jpg" width="21" height="20" class="noborder"></a></td>
                                </tr>
                              </table>


                            </td>
                          </tr>
</table>					


<?    
   }
?>




