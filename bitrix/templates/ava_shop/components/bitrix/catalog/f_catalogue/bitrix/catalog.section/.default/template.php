<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section">

<br class="brspace">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>


	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>	

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="200" align="left" valign="top" class="new">

<?if(is_array($arElement["PREVIEW_PICTURE"])):?>

<?
    echo CFile::ShowImage($arElement["PREVIEW_PICTURE"]["SRC"],156, 97, "border=0", $arElement["DETAIL_PAGE_URL"]);
 ?>


<?endif?>
<br />
<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?>
    </td>
    <td valign="top" class="bottext"><?=$arElement["PREVIEW_TEXT"]?></td>
  </tr>
 <tr> 
    <td width="200">&nbsp;</td>
    <td align="right" valign="bottom"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"
class="continue">Продолжить &gt;&gt;</a></td>
  </tr>
</table>


                        <br class="brspace">
<hr class="hline">
                  <br class="brspace">

<?endforeach?>



<div class="bottext"><?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?></div>
<?endif;?>
</div>
