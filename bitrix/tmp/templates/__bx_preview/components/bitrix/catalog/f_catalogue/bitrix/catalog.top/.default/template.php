<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><div class="catalog-top">

	<?foreach($arResult["ROWS"] as $arItems):?>
	<?foreach($arItems as $arElement):?>
		

                        <br class="brspace">
<hr class="hline">
                  <br class="brspace">


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


<?endforeach?>

<?endforeach?>



</div>