<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="bottext">
<script src="/script.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript">

var sh='0';

function showmail(id)
	{
        if (sh != 0){
			document.getElementById(id).style.display = 'none';
			sh = '0';
	              }
		else {
			document.getElementById(id).style.display = 'block';
			sh = '1'
			}
    };

function hidemail(id)
	{
      document.getElementById(id).style.display = 'none';
	  sh = '0';
   }
</script>


<table width="505px" border="0" cellspacing="2" cellpadding="0">
<tr>

<td height="50" valign="middle" align="left">

<div class="itemtitle"><li><?=$arResult["NAME"]?></li></div>
<div class="f_type"><?=$arResult["PROPERTIES"]["F_TYPE"]["VALUE"]?></div>
<div class="f_type">Коллекция:
<a href="/search/collection/index.php?collection[1]=<?=$arResult["PROPERTIES"]["COLLECTION"]["VALUE"]?>">

 <?
   $arResColl=GetIBlockElement($arResult["PROPERTIES"]["COLLECTION"]["VALUE"]);
   echo($arResColl["NAME"]);
   ?>
   </a>
</div>

</td>
<td align="right" valign="top" style="padding-top: 10px;">




<select name="sel" style="width: 145px;" onchange="MM_jumpMenu('parent',this,0)">
<option value="" selected>список изделий</option>;
<?
    CModule::IncludeModule("iblock");
    $IBLOCK_TYPE = "catalogue";   // тип инфо-блока
    $IBLOCK_ID = 5;            // ID инфо-блока
    $SECTION_ID = $arResult["IBLOCK_SECTION_ID"];

   $arFilterSect["ACTIVE"] = "Y";
//   $arFilterSect["DEPTH_LEVEL"] = 1;
   $arFilterSect["INCLUDE_SUBSECTIONS"] = "Y";

        // иначе собираем разделы

        $rsSections = GetIBlockSectionList($IBLOCK_ID, false, array("SORT" => "ASC", "ID" => "DESC"), false, $arFilterSect);
            if ($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, $IBLOCK_ID, false, array("NAME" => "ASC"), false,
                            array("ACTIVE" => "Y", "IBLOCK_ID" => $IBLOCK_ID, "SECTION_ID" => $SECTION_ID), array()))
            {
                while ($arElement = $rsElements->GetNext())
                     {

                       $arrAddLinks[] = $arElement["DETAIL_PAGE_URL"];
                       if ($arElement["ID"]==$arResult["ID"]) echo "<option selected value='/catalogue/".$SECTION_ID."/tov_".$arElement["ID"].".html' style='font-size: 11px;'>&nbsp;&nbsp;&nbsp;&nbsp;".$arElement["NAME"]. "</option>";
                        else
                       echo "<option value='/catalogue/".$SECTION_ID."/tov_".$arElement["ID"].".html' style='font-size: 11px;'>&nbsp;&nbsp;&nbsp;&nbsp;".$arElement["NAME"]. "</option>";
                     }
            }

?>
</select>

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
		<a href="#null" onclick="javascript:window.open(url); return false" name="photo" target="_blanc" language="Javascript"><img src="<?=$m_s_puth?>" name="mainimg" class="preview"></a>
	<?
	}
?>



   </td>
    <td width="150" height="25%" align="right" valign="top">
<a href="#null" onclick="javascript:window.open(url); return false" name="zoom" target="_blanc" language="Javascript">
    <img src="/bitrix/templates/avangard/images/zoom.gif" width="146" height="35" class="noborder"></a></td>
  </tr>
  <tr>
    <td width="150" height="25%" align="right" valign="top"><a href="/wharetobuy/">
    <img src="/bitrix/templates/avangard/images/price.gif" width="146" height="35" class="noborder"></a></td>
  </tr>
  <tr>
    <td width="150" height="25%" align="right" valign="top"><a href="<?=htmlspecialchars($APPLICATION->GetCurUri("print=Y"));?>" target="_blank">
    <img src="/bitrix/templates/avangard/images/print.gif" width="146" height="35" class="noborder"></a></td>
  </tr>
  <tr>
    <td width="150" height="25%" align="right" valign="top" onclick="showmail('mb')"><a href="#mb">
    <img src="/bitrix/templates/avangard/images/send.gif" width="146" height="35" class="noborder"></a></td>
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


<div  style="background-color: #f2f2f2; width: 505px; height:<?=$height?>px; overflow: auto;
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

<a href="#null" onclick="MM_swapImage('mainimg','','<?=$s_puth?>',1); javascript:url='<?=$puth?>';return false">
<?echo CFile::ShowImage($s_puth, 150, 50, "class=preview");?>
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


        <?if($arResult["DETAIL_TEXT"]):?>
                <br /><?=$arResult["DETAIL_TEXT"]?><br />
        <?elseif($arResult["PREVIEW_TEXT"]):?>
                <br /><?=$arResult["PREVIEW_TEXT"]?><br />
        <?endif;?>



<br />


<table width="100%" cellspacing="1" cellpadding="4" border="0" bgcolor="#e8e8e8" class="s">
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





<?// блок отправки по e-mail?>
<div class="hmail" id="mb">



<br class="brspace">
<hr class="hline">
<br class="brspace">
<?$url= SITE_SERVER_NAME."/catalogue/".$arResult["IBLOCK_SECTION_ID"]."/tov_".$arResult["ID"].".html"; ?>


<table width="100%" class="mailtab">
<tr>
<td align="left">
Отправить описание на e-mail:
</td>
<td align="right"><a href="#null" onclick="hidemail('mb')">[&nbsp;Скрыть&nbsp;]</a>
</td>
</tr>
</table>

<form action="/catalogue/mail.php" method="post"  onsubmit="return SendForm();">
<input type="hidden" name="action" value="save">

    <table width="500" border="0" cellspacing="5" cellpadding="0" class="content">
    <tr>
      <td width="200">e-mail получателя:</td>
      <td><input name="email" type="text" id="email" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <td>Ваш e-mail:</td>
      <td><input name="uemail" type="text" id="uemail" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <td>Ваше имя:</td>
      <td><input name="uname" type="text" id="uname" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <td>Тема сообщения:</td>
      <td><input name="usubj" type="text" id="usubj" value="Интересное предложение..." size="50" maxlength="100"></td>
    </tr>
    <tr>
      <td>Ваш комментарий:</td>
      <td><textarea name="descr" cols="32" rows="5">Хорошая мебель, интересное предложение...</textarea></td>
    </tr>


<?$arResElementm=CIBlockElement::GetByID($arResult["PROPERTIES"]["COLLECTION"]["VALUE"]);?>
          <?$arElementm=$arResElementm->GetNext();?>


<div class="hidden">

   <input name="id" type="hidden" value="<?=$arResult["ID"];?>">
   <input name="collection" type="hidden" value="<?=$arElementm["NAME"];?>">
   <input name="name" type="hidden" value="<?=$arResult["NAME"];?>">
   <input name="url" type="hidden" value="<?=$url;?>">

   <input name="pic_path" type="hidden" value="<?=$m_s_puth?>">
   <input name="pic_name" type="hidden" value="<?=$arResult["PREVIEW_PICTURE"][FILE_NAME];?>">
   <input name="pic_w" type="hidden" value="<?=$arResult["PREVIEW_PICTURE"][WIDTH];?>">
   <input name="pic_h" type="hidden" value="<?=$arResult["PREVIEW_PICTURE"][HEIGHT];?>">
   <input name="f_type" type="hidden" value="<?=$arResult["PROPERTIES"]["F_TYPE"]["VALUE"];?>">



   <input name="tech" type="hidden" value="<?=$tech;?>">

</div>
       <tr>
      <td>
        Защита от автозаполнения:

      </td>
      <td>
<?
$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();
?>
<input type="hidden" name="captcha_sid" value="<?= htmlspecialchars($capCode) ?>">
<img align="left" src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialchars($capCode) ?>" width="180" height="40">

</td>
    </tr>


   <tr>
      <td>

      Введите символы, изображенные на картинке:

      </td>
      <td><input type="text" name="captcha_word" size="30" maxlength="50" value=""> </td>
    </tr>


        <tr>
      <td align="right"><input type="submit" name="submit" value="Отправить"></td>
      <td allign="left"><input type="reset" name="reset" value="Очистить"></td>
    </tr>
  </table>
</form>





     <script language="JavaScript">
<!--

required = new Array(
"email",
"uemail",
"uname"
);


required_show = new Array(
"e-mail получателя",
"Ваш e-mail",
"Ваше имя"
);

function SendForm () {

var rrr, j;

for(j=0; j<required.length; j++) {
    for (rrr=0; rrr<document.forms[0].length; rrr++)

     {
        if (document.forms[0].elements[rrr].name == required[j] &&
  document.forms[0].elements[rrr].value == "" ) {
            alert('Пожалуйста, введите ' + required_show[j]);
            document.forms[0].elements[rrr].focus();
            return false;
        }
    }
}

p_email = document.forms[0].email.value.toString();
if (p_email != "")
{
t = p_email.indexOf("@");
if((p_email.indexOf(".") == -1) || (t == -1) || (t < 1) || (t > p_email.length - 5) || (p_email.charAt(t - 1) == '.') || (p_email.charAt(t + 1) == '.'))
{
alert("Некорректно указан e-mail получателя !");
document.forms[0].email.focus();
return false;
}
}

s_email = document.forms[0].uemail.value.toString();
if (s_email != "")
{
t = s_email.indexOf("@");
if((s_email.indexOf(".") == -1) || (t == -1) || (t < 1) || (t > s_email.length - 5) || (s_email.charAt(t - 1) == '.') || (s_email.charAt(t + 1) == '.'))
{
alert("Некорректно указан Ваш e-mail !");
document.forms[0].uemail.focus();
return false;
}
}


return true;
}

//-->

</script>
</div>


<?//здесь кончается отправка письма ?>



        <?if(is_array($arResult["SECTION"])):?>
                <br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
        <?endif?>
