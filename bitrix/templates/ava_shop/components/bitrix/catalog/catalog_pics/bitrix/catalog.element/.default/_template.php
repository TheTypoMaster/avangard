<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><div class="bottext">
<br class="brspace">


<table width="100%" border="0" cellspacing="2" cellpadding="0">
<tr>
<td colspan="2" valign="middle" align="left" class="itemtitle"><li><?=$arResult["NAME"]?></li></td>
</tr>
 <tr> 
    <td rowspan="4" align="left" valign="top" WIDTH="355">


	<?if($arResult["PREVIEW_PICTURE"]):?>

<?
    echo CFile::ShowImage($arResult["PREVIEW_PICTURE"]["SRC"],350, 160, "border=0", $arResult["DETAIL_PAGE_URL"]);
 ?>

	
	<?elseif($arResult["DETAIL_PICTURE"]):?>
<?
    echo CFile::ShowImage($arResult["PREVIEW_PICTURE"]["SRC"],350, 160, "border=0", $arResult["DETAIL_PAGE_URL"]);
 ?>
		
	<?endif;?>


   </td>
    <td width="150" height="25%" align="right" valign="top"><a href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" TARGET="_blanc">
    <img src="/bitrix/templates/avangard/images/zoom.gif" width="146" height="35" border="0"></a></td>
  </tr>
  <tr> 
    <td width="150" height="25%" align="right" valign="top"><a href="/wharetobuy/">
    <img src="/bitrix/templates/avangard/images/price.gif" width="146" height="35" border="0"></a></td>
  </tr>
  <tr> 
    <td width="150" height="25%" align="right" valign="top"><a href="<?=htmlspecialchars($APPLICATION->GetCurUri("print=Y"));?>" target="_blank">
    <img src="/bitrix/templates/avangard/images/print.gif" width="146" height="35" border="0"></a></td>
  </tr>
  <tr> 
    <td width="150" height="25%" align="right" valign="top"><a href="<?=htmlspecialchars($APPLICATION->GetCurUri("mail=Y"));?>">
    <img src="/bitrix/templates/avangard/images/send.gif" width="146" height="35" border="0"></a></td>
  </tr>
</table>



		
	
		<br />
	<?if($arResult["DETAIL_TEXT"]):?>
		<br /><?=$arResult["DETAIL_TEXT"]?><br />
	<?elseif($arResult["PREVIEW_TEXT"]):?>
		<br /><?=$arResult["PREVIEW_TEXT"]?><br />
	<?endif;?>
	<?if(count($arResult["LINKED_ELEMENTS"])>0):?>
		<br /><b><?=$arResult["LINKED_ELEMENTS"][0]["IBLOCK_NAME"]?>:</b>
		<ul>
	<?foreach($arResult["LINKED_ELEMENTS"] as $arElement):?>
		<li><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></li>
	<?endforeach;?>
		</ul>
	<?endif?>

                               



                        <br class="brspace">
<hr class="hline">
                       <br class="brspace">

Технические характеристики: <br />
				<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
					<?=$arProperty["NAME"]?>:<b>&nbsp;<?

			if(is_array($arProperty["DISPLAY_VALUE"])):
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					?><?
					else:
						echo $arProperty["DISPLAY_VALUE"];?></b><br />
					<?endif?>

				<?endforeach?>

	
<br class="brspace">
<hr class="hline"> 
<br class="brspace">



<?
// additional photos
	$LINE_ELEMENT_COUNT = 3; // number of elements in a row
	if(count($arResult["MORE_PHOTO"])>0):?>
		<center><a name="more_photo">Дополнительные фотографии</a></center><br />
                <?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>


<?
    echo CFile::Show2Images($PHOTO["SRC"], $PHOTO["SRC"], 100, 50 );
 ?>			
                  
		<?endforeach?>
	<?endif?>

<br class="brspace">
	<?if(is_array($arResult["SECTION"])):?>
		<br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
	<?endif?>


</div>