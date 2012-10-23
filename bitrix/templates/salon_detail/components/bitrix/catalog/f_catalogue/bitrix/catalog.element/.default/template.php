<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="bottext">

<table width="505px" border="0" cellspacing="2" cellpadding="0">
<tr>

<td height="50" valign="middle" align="left">

<div class="itemtitle"><li><?=$arResult["NAME"]?></li></div>
<div class="f_type"><?=$arResult["PROPERTIES"]["F_TYPE"]["VALUE"]?></div>
<div class="f_type">Коллекция:


 <?
   $arResColl=GetIBlockElement($arResult["PROPERTIES"]["COLLECTION"]["VALUE"]);
   echo($arResColl["NAME"]);
   ?>

</div>

</td>
</tr>
 <tr>
    <td align="left" valign="top" WIDTH="355">

<?
	if(count($arResult["MORE_PHOTO"])>0){

	reset($arResult["MORE_PHOTO"]);
	$M_PHOTO = current($arResult["MORE_PHOTO"]);

	$m_folder = str_replace($M_PHOTO["FILE_NAME"], "", $M_PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
    $m_s_puth=$m_folder."s_".$M_PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

    $m_puth=$m_folder.$M_PHOTO["FILE_NAME"];//получаем путь до основной картинки



?>


	<img src="<?=$m_s_puth?>" name="mainimg">
<?
  }
?>


   </td>
   </tr>
</table>





                <br />
<table width="510px" cellspacing="1" cellpadding="4" border="0" bgcolor="#ffffff" class="s">
  <tr>
  <td>

        <?if($arResult["DETAIL_TEXT"]):?>
                <br /><?=$arResult["DETAIL_TEXT"]?><br />
        <?elseif($arResult["PREVIEW_TEXT"]):?>
                <br /><?=$arResult["PREVIEW_TEXT"]?><br />
        <?endif;?>
    </td>
    </tr>
    </table>
<br />


<table width="510px" cellspacing="1" cellpadding="4" border="0" bgcolor="#e8e8e8" class="s">
  <tbody>
    <tr><td height="25" align="center" colspan="6"><b>ГАБАРИТНЫЕ РАЗМЕРЫ</b></td></tr>

    <tr>
    <td height="25" bgcolor="#f8f8f8" align="center"><b>Комплектность</b></td>
    <td bgcolor="#f8f8f8" align="center"><b>Длина</b></td>
    <td bgcolor="#f8f8f8" align="center"><b>Ширина</b></td>
    <td bgcolor="#f8f8f8" align="center"><b>Высота</b></td>
    <td bgcolor="#f8f8f8" align="center"><b>Спальное место</b></td>
    <td bgcolor="#f8f8f8" align="center"><b>Механизм трансформации</b></td>
    </tr>


<?
$cnt=0;
foreach($arResult["PROPERTIES"]["COMPLECT"]["VALUE"] as $complect):

$arResC=GetIBlockElement($complect);

?>
 <tr>
 <td height="25" bgcolor="#ffffff"><?echo($arResC["NAME"])?></td>
 <td bgcolor="#ffffff" align="right"><?=$arResult["PROPERTIES"]["LENGTH"]["VALUE"][$cnt]?></td>
 <td bgcolor="#ffffff" align="right"><?=$arResult["PROPERTIES"]["WIDTH"]["VALUE"][$cnt]?></td>
 <td bgcolor="#ffffff" align="right"><?=$arResult["PROPERTIES"]["HEIGHT"]["VALUE"][$cnt]?></td>
 <td bgcolor="#ffffff" align="center"><?=$arResult["PROPERTIES"]["PLACES"]["VALUE"][$cnt]?></td>
 <?$arResTr=GetIBlockElement($arResult["PROPERTIES"]["TRANSFORMATION"]["VALUE"][$cnt]);?>
 <td bgcolor="#ffffff" align="center"><?echo($arResTr["NAME"])?></td>
 </tr>


<?
$cnt++;
endforeach;
?>

   </tbody>
</table>

</div>
