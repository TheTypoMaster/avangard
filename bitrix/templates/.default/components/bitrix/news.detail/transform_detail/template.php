<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



<center><table border="0" cellpadding="0" cellspacing="5" width="100%">
<tr>
<td rowspan="9" width="300">
<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"  title="<?=$arResult["NAME"]?>" />
	<?endif?> <br>
</td>
<td align="right" width="150"><b>������������</b></td>
<td><?=$arResult[NAME]?></td>

</tr>
<tr>
<td align="right"><b>�������</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][SHADE][VALUE]?></td>
</tr>
<tr>
<td align="right"><b>�������</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][PICTURE][VALUE]?></td>
</tr>
<tr>
<td align="right"><b>��������</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][TEXTURE][VALUE]?></td>

</tr>
<tr>
<td align="right"><b>������� ���������</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][PRICE_CAT][DISPLAY_VALUE]?></td>
</tr>
<tr>
<td align="right"><b>�������</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][PRESENCE][VALUE]?></td>
</tr>
<tr>
<td align="right"><b>���� �� �����</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][DAYS][VALUE]?></td>

</tr>
<tr>
<td align="right"><b>�������</b></td>
<td><?=$arResult["DISPLAY_PROPERTIES"][ARTICUL][VALUE]?></td>
</tr>
<tr>
<td align="right">������������� ���� ���������� ����� ���������� �� ��������� !</td>
<td style="vertical-align:bottom">
<input type="submit" value="������� ����" onClick="window.close()" style="font:11px;"><br><b><a href="/">��������� �� ����</a></b><br>


</td>
</tr>
</table></center>


<?
//echo "<pre>";
//print_r($arResult["DISPLAY_PROPERTIES"]);
//echo "</pre>";
?>
		