<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
</td>

</tr></table></td>
<?if($APPLICATION->GetProperty('right_inc_file')!='none') :?>
 <?$left_incl = $left_incl+1; ?>
 <td class="right_inc_file_td">
<?$APPLICATION->IncludeFile($APPLICATION->GetProperty('right_inc_file'), array(), array(
                        "MODE"      => "html",
                        "TEMPLATE"  => "page_inc.php"
                        ));
						?>

</td>
<?endif?>
</tr>
<tr><td colspan="<?=$left_incl?>">
<div  class="bottom_menu_div">
<?$APPLICATION->IncludeComponent("bitrix:menu", "second_bottom_menu", Array(
	"ROOT_MENU_TYPE"	=>	"top_two",
	"MAX_LEVEL"	=>	"2",
	"CHILD_MENU_TYPE"	=>	"part",
	"USE_EXT"	=>	"N"
	)
);?>
 </div>
 <div class="footer_copyr_td"><table width="700"><tr><td width="450" nowrap  valign="middle" align="right"><right>© 2009-2010 Мебельная фабрика Авангард <br><a title="создание сайтов, разработка сайтов на 1С-Битрикс, поддержка сайтов" href="http://www.cava.su" target="_blank">Разработка и поддержка сайта - студия
«CA VA»</a></right></td><td nowrap align="right" valign="middle" style="padding-left:15px; padding-right: 30px;">


</td></tr></table></div>

</td></tr></table>
</div>
</center>
</div>

<table id="main_table" border="0" cellpadding="0" cellspacing="0">
  
  
<tr bgcolor="#E20A17"><td colspan="2" height="46">
<td align="center">
<table width="1000"><tr><td width="200"></td>
<td align="right">
<?$APPLICATION->IncludeComponent("bitrix:menu", "main_top_menu", array(
	"ROOT_MENU_TYPE" => "top_one",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "part",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
</td>
<td width="100" align="right" valign="middle">
<?$APPLICATION->IncludeComponent("bitrix:search.form", "top_search", Array(
	"PAGE"	=>	"/redesign/catalog/search.php"
	)
);?>
</td><td width="16"></td>
</tr>
</table>
</td>
<td colspan="2">
</td></tr>

  
  
  
<tr id="logotype_tr"><td colspan="2" height="70">
<td align="center">
<table align="center"><tr><td align="left"><a href="/"><img border="0" src="/images/logotype.gif"></a></td>
<td></td>
<td  align="right"><font class="phonecode">(495)</font> <font class="phonenum"><span id="ya-phone-1">981-66-44</span></font><br><font class="phonetext">многоканальный телефон</font></td>
</tr>
</table>
</td>
<td colspan="2">
</td></tr>
 
 <tr id="top_menu_tr">
<td  class="lines" colspan="5" align="center">


<div  style="background: #ffffff; width: 1000px; height: 400px;">
<?$APPLICATION->IncludeComponent("bitrix:menu", "second_top_menu", Array(
	"ROOT_MENU_TYPE"	=>	"top_two",
	"MAX_LEVEL"	=>	"2",
	"CHILD_MENU_TYPE"	=>	"part",
	"USE_EXT"	=>	"N"
	)
);?>
<br><br><br><br>
 </div>

</td>
</tr>


 <tr id="slider_tr">
<td class="lines" colspan="5" align="center"  style="padding-top:-3px; margin:0px;">





</td>
</tr>


</table>
<div align="center" style="width: 100%; position: absolute; top: 150px;">
<div style="width:1000px; height: 264px; background: #ffffff; margin-top: 7px;padding-bottom: 0px;">
<div id="seriy_block">

<div id="wn">

    <div id="lyr1" onmouseover="dw_scrollObj.stopScroll('wn')" onmouseout="dw_scrollObj.initScroll('wn','right', 100)">
 
<table valign="bottom" cellpadding="0" cellspacing="0"  id="t1" class="polotno_table"><tr><td id="n_1">
                  <?        
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	$arFilter = Array("IBLOCK_ID"=>IntVal(9), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	$i=0;
	
	$cat_array=array();
	
	while($ob = $res->GetNextElement())
	   { 
	   $i++;
	   $arFields = $ob->GetFields();  
	   $cat_array[$i][name] = $arFields[NAME];
	   $cat_array[$i][id] = $arFields[ID];
	   }
       $kol = 0;
	  foreach($cat_array as $category) 
	    { 
	    $kol++;
       ?><td valign="top"><table height="224" valign="bottom" cellpadding="0" border="0" cellspacing="0"  class="slider_fon_<?=$category[id]?>"><tr><?
	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_SLIDER_IMG", "PROPERTY_SLIDER", "PROPERTY_NOVELTY", "PROPERTY_HIT",   "PROPERTY_ACTIA",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_SLIDER"=>false,  "PROPERTY_COLLECTION"=>IntVal($category[id]));
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
		$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                ?><td  id="divan<?=$arElementFields[ID]?>"  valign="top" height="210"><table height="210" border="0"><tr><td style="padding: 0px; height:40px;" valign="top">

				
				
<?

if($_GET[id]) {


echo    '<div style="height:32px; text-align: right; padding: 0px;  padding-top: 6px; margin: 0px; padding-right: 10px;">';
		if($arElementFields['PROPERTY_NOVELTY_VALUE']) { echo  '<img id="new_'.$arElementFields[ID].'"';  if($_GET[id] == $arElementFields[ID]) echo 'src="/images/new_image.gif"'; else echo 'src="/images/new_image_off.gif"';  echo 'alt="Новинка">';}
		if($arElementFields['PROPERTY_ACTIA_VALUE']) { echo  '<img id="act_'.$arElementFields[ID].'"';  if($_GET[id] == $arElementFields[ID]) echo 'src="/images/act_image.gif"'; else  echo 'src="/images/act_image_off.gif"'; echo 'alt="Акция">';}
	    if($arElementFields['PROPERTY_HIT_VALUE']) { echo  '<img id="hit_'.$arElementFields[ID].'"'; if($_GET[id] == $arElementFields[ID]) echo 'src="/images/hit_image.gif"'; else  echo 'src="/images/hit_image_off.gif"';  echo 'alt="Хит продаж">';}
		echo '</div>';
                  

?>
</td></tr><tr><td valign="bottom" style="height:146px; vertical-align: bottom; padding: 0px; margin: 0px;">
<?if($_GET[id] != $arElementFields[ID]) {?><a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><img onMouseOver="document.getElementById('name_<?=$arElementFields[ID]?>').className='slider_name_td_on'; <?if($arElementFields['PROPERTY_NOVELTY_VALUE']) {?>document.getElementById('new_<?=$arElementFields[ID]?>').src='/images/new_image.gif';<?} ?> <?if($arElementFields['PROPERTY_ACTIA_VALUE']) {?>document.getElementById('act_<?=$arElementFields[ID]?>').src='/images/act_image.gif';<?} ?>  <?if($arElementFields['PROPERTY_HIT_VALUE']) {?>document.getElementById('hit_<?=$arElementFields[ID]?>').src='/images/hit_image.gif';<?} ?>" onMouseOut="document.getElementById('name_<?=$arElementFields[ID]?>').className='slider_name_td'; <?if($arElementFields['PROPERTY_NOVELTY_VALUE']) {?>document.getElementById('new_<?=$arElementFields[ID]?>').src='/images/new_image_off.gif';<?} ?> <?if($arElementFields['PROPERTY_ACTIA_VALUE']) {?>document.getElementById('act_<?=$arElementFields[ID]?>').src='/images/act_image_off.gif';<?} ?>  <?if($arElementFields['PROPERTY_HIT_VALUE']) {?>document.getElementById('hit_<?=$arElementFields[ID]?>').src='/images/hit_image_off.gif';<?} ?>"  alt="<?=$arElementFields[NAME]?>" 
<?echo 'src="'.$img_path.'"></a>'; } else {?><img alt="<?=$arElementFields[NAME]?>" <?echo 'src="'.$img_path.'"></a>';  }   ?>
</td></tr><tr><td id="name_<?=$arElementFields[ID]?>" class="slider_name_td">
	<?if($_GET[id] != $arElementFields[ID]) {?><a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a><?} else {
	 ?><font style="font-weight:bold; color: #e20a17;"><?=$arElementFields[NAME]?></font><?}?>
</td></tr></table></td><?
	    }
		
else {
echo    '<div style="height:32px; text-align: right; padding: 0px;  padding-top: 6px; margin: 0px; padding-right: 10px;">';
		if($arElementFields['PROPERTY_NOVELTY_VALUE']) { echo  '<img id="new_'.$arElementFields[ID].'" src="/images/new_image_off.gif" alt="Новинка">';}
		if($arElementFields['PROPERTY_ACTIA_VALUE']) { echo  '<img id="act_'.$arElementFields[ID].'" src="/images/act_image_off.gif" alt="Акция">';}
	    if($arElementFields['PROPERTY_HIT_VALUE']) { echo  '<img id="hit_'.$arElementFields[ID].'" src="/images/hit_image_off.gif" alt="Хит продаж">';}
		echo '</div>';
                  

?>

</td></tr><tr>
<td  valign="bottom" style="height:146px; vertical-align: bottom; padding: 0px; margin: 0px;">

<a href="/catalog/divan<?=$arElementFields[ID]?>.htm">
<img onMouseOver="document.getElementById('name_<?=$arElementFields[ID]?>').className='slider_name_td_on'; <?if($arElementFields['PROPERTY_NOVELTY_VALUE']) {?>document.getElementById('new_<?=$arElementFields[ID]?>').src='/images/new_image.gif';<?} ?> <?if($arElementFields['PROPERTY_ACTIA_VALUE']) {?>document.getElementById('act_<?=$arElementFields[ID]?>').src='/images/act_image.gif';<?} ?>  <?if($arElementFields['PROPERTY_HIT_VALUE']) {?>document.getElementById('hit_<?=$arElementFields['ID']?>').src='/images/hit_image.gif';<?} ?>" onMouseOut="document.getElementById('name_<?=$arElementFields[ID]?>').className='slider_name_td'; <?if($arElementFields['PROPERTY_NOVELTY_VALUE']) {?>document.getElementById('new_<?=$arElementFields[ID]?>').src='/images/new_image_off.gif';<?} ?> <?if($arElementFields['PROPERTY_ACTIA_VALUE']) {?>document.getElementById('act_<?=$arElementFields[ID]?>').src='/images/act_image_off.gif';<?} ?>  <?if($arElementFields['PROPERTY_HIT_VALUE']) {?>document.getElementById('hit_<?=$arElementFields[ID]?>').src='/images/hit_image_off.gif';<?} ?>"  alt="<?=$arElementFields[NAME]?>" src="<?=$img_path?>"></a>
</td></tr>
<tr><td id="name_<?=$arElementFields[ID]?>" <?if($_GET[id] == $arElementFields[ID]) echo 'class="slider_name_td_on"'; else echo 'class="slider_name_td"';?>>
	<a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a></td></tr></table><?
	 }
	 }
	    echo "</tr></table>
</td>";
        }
	
   ?>



</tr>
</table>
</div>
</div>
<div id="scrollbar">
    
  <div id="left_ear"><a class="mouseover_left" href="#"><img src="/images/left_ear.gif"  alt="" border="0" /></a></div>

<div id="left"><a class="mouseover_left" href="#"><img src="/images/btn-lft.gif"  alt="" border="0" /></a></div>

    <div id="track">
         <div id="dragBar"></div>
    </div>
    <div id="right"><a class="mouseover_right" href="#"><img src="/images/btn-rt.gif"  alt="" border="0" /></a></div>

 <div id="right_ear"><a class="mouseover_right" href="#"><img src="/images/right_ear.gif"  alt="" border="0" /></a></div>


  </div>
</div>
</div>

<!--****************************Счётчики************************************-->
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter11801303 = new Ya.Metrika({
                    id:11801303,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/11801303" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
 


<!-- Yandex.Metrika Marked Phone -->
<script type="text/javascript" src="//mc.yandex.ru/metrika/phone.js?counter=11801303" defer="defer"></script>
<!-- /Yandex.Metrika Marked Phone -->
<!--****************************Счётчики************************************-->

</body>
</html>


