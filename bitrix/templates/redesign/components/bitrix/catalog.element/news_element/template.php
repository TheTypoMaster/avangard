<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="gray_td" ><h1><?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<?=$arResult["NAME"]?>
	<?endif;?>
</h1></div>

                  
<div class="bottext" width="726">
<?if(count($arResult["MORE_PHOTO"])>0){?>
<script src="/script.js" type="text/javascript"></script>

<table width="726" border="0" cellspacing="2" cellpadding="0">
<tr>

<td align="right" valign="top" style="padding-top: 10px;">

<?
	if(count($arResult["MORE_PHOTO"])>0){

	reset($arResult["MORE_PHOTO"]);
	$M_PHOTO = current($arResult["MORE_PHOTO"]);

	$m_folder = str_replace($M_PHOTO["FILE_NAME"], "", $M_PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
    $m_s_puth=$m_folder."s_".$M_PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

    $m_puth=$m_folder.$M_PHOTO["FILE_NAME"];//получаем путь до основной картинки



	}
?>

<script>
<!--

var url="<?=$m_puth?>";

-->
</script>


</td>
</tr>
 <tr>
    <td rowspan="4" align="left" valign="top" WIDTH="355">

<?
	if(count($arResult["MORE_PHOTO"])>0){
	?>
		<a href="#null" onclick="javascript:window.open(url); return false" name="photo" target="_blanc" language="Javascript"><img src="<?=$m_puth?>" name="mainimg" class="preview" style="height: 350px;"></a>
	<?
	}
?>



   </td>
    <td width="150" height="25%" align="right" valign="top"></td>
  </tr>
  <tr>
    <td width="150" height="25%" align="right" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td width="150" height="25%" align="right" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td width="150" height="25%" align="right" valign="top">&nbsp;</td>
  </tr>
  <tr>
  <td colspan="2" style="padding-top:7px;" valign="bottom">


<?
// additional photos
        $LINE_ELEMENT_COUNT = 3; // number of elements in a row
        if(count($arResult["MORE_PHOTO"])>0):

$t_width = 5;
$height = 75;

    foreach($arResult["MORE_PHOTO"] as $PHOTO):
	$arFile = CFile::GetFileArray($PHOTO["ID"]);
	$width =  $arFile["WIDTH"] / $arFile["HEIGHT"] * 50;
	$t_width = $t_width + $width + 10;
	endforeach;


	if ($t_width > 505){
		$height = 90;
	}
 ?>


<div  style="background-color: #f2f2f2; width: 726px; height:<?=$height?>px; overflow: auto;
border: 1px solid #e4e4e4;
scrollbar-3dlight-color:#F0F0F0;
scrollbar-arrow-color:#AA0000;
scrollbar-base-color:#F0F0F0;
scrollbar-darkshadow-color:#F0F0F0;
scrollbar-face-color:#F0F0F0;">


<table height="70">
<tr>
<td valign="bottom">
<nobr>




            <?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
<?
   $folder = str_replace($PHOTO["FILE_NAME"], "", $PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
   $s_puth=$folder."s_".$PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

   $puth=$folder.$PHOTO["FILE_NAME"];//получаем путь до основной картинки
 ?>

<a href="#null" onclick="MM_swapImage('mainimg','','<?=$puth?>',1); javascript:url='<?=$puth?>';return false">
<img class="preview" width="71" height="50" alt="" src="<?=$puth?>">
</a>
                <?endforeach?>

</nobr>
</tr>
</td>
</table>
</div>

<?endif?>


  </td>
  </tr>
</table>
  <br />

<?}?>



        <?if($arResult["DETAIL_TEXT"]):?>
                <br /><?=$arResult["DETAIL_TEXT"]?><br />
        <?elseif($arResult["PREVIEW_TEXT"]):?>
                <br /><?=$arResult["PREVIEW_TEXT"]?><br />
        <?endif;?>



<br />

	<?if(is_array($arResult["SECTION"])):?>
		<br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
	<?endif?>
</div>
