<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div style="padding-top:10px;"></div>
<table width="525" height="175" border="0" cellpadding="0" cellspacing="0">
<tr>
<td style="BACKGROUND:url(/bitrix/templates/avangard/images/bg_sq_head.gif) no-repeat left top" height="10">
</td>
</tr>

<tr>
<td align="left" valign="top" style="padding:0 7 0 12; BACKGROUND:url(/bitrix/templates/avangard/images/bg_sq_mid.gif) repeat-y left top" height="150">


<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="200" height="150" rowspan="3">

    <?
	if(count($arResult["MORE_PHOTO"])>0){

	reset($arResult["MORE_PHOTO"]);
	$M_PHOTO = current($arResult["MORE_PHOTO"]);

	$m_folder = str_replace($M_PHOTO["FILE_NAME"], "", $M_PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
    $m_s_puth=$m_folder."s_".$M_PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

    $m_puth=$m_folder.$M_PHOTO["FILE_NAME"];//получаем путь до основной картинки

      echo ShowImage($m_s_puth, 280, 200,
                     "border='0'", "/catalogue/".$arResult["IBLOCK_SECTION_ID"]."/tov_".$arResult["ID"].".html");


	}
    ?>

    </td>
    <td style="padding: 0px 7px" class="hit-title-month" height="35" valign="top">В этом месяце "<?echo $arResult["NAME"]?>"<br />
<span class="hit-title-hit">Хит Продаж!</span></td>
  </tr>
  <tr>
     <td class="bottext">
<?echo $arResult["PREVIEW_TEXT"];?>
</td>
  </tr>
  <tr>
                                <td height="35" align="right" valign="bottom" style="padding: 0px 7px" >
                                 <table width="200" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td align="right" valign="middle"  class="hit-continue">
<a href="/catalogue/<?=$arResult["IBLOCK_SECTION_ID"]?>/tov_<?=$arResult["ID"]?>.html">Продолжить</a></td>
                                  <td width="30" align="right">
<a href="/catalogue/<?=$arResult["IBLOCK_SECTION_ID"]?>/tov_<?=$arResult["ID"]?>.html"><img src="/bitrix/templates/avangard/images/top_next.jpg" width="21" height="20" class="noborder"></a></td>
                                </tr>
                              </table>
                             </td>
  </tr>
</table>

</td>
</tr>

<tr>
<td style="BACKGROUND:url(/bitrix/templates/avangard/images/bg_sq_foot.gif) no-repeat left top" height="15">
</td>
</tr>
</table>




