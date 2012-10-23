<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<center><table border="0" cellpadding="0" cellspacing="5" width="100%">
<tr>
<td rowspan="3" width="300" valign="top">
<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"  title="<?=$arResult["NAME"]?>" />
	<?endif?> <br></td>
<td align="left"><b>Описание</b></td>
</tr>
<tr>
  <td ><?=$arResult[DETAIL_TEXT]?></td>
</tr>
<tr>
<td><input type="submit" value="закрыть окно" onClick="window.close()" style="font:11px;"><br><b><a href="/">вернуться на сайт</a></b><br></td>
</tr>


</table>
</center>
		