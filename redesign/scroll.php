<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Главная страница");
?>
   <script src="/libs/slider_new.js" type="text/javascript" charset="utf-8"></script>

 <? if(CModule::IncludeModule('iblock')) $incl="Y"; ?>


<center>
<div id="scroll_div_id_one" style="overflow: auto; height: 264px; width: 938px;">
<div id="polotno">
<table valign="bottom" cellpadding="0" cellspacing="0" valign=bottom style="background: url('/images/fon.gif') #d4d5d5 repeat-x 50% 2px ; vertical-align: bottom; height: 248px;"><tr>

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
       ?><td><table cellpadding="0" cellspacing="0" class="slider_fon_<?=$category[id]?>"><tr><?
	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_SLIDER_IMG", "PROPERTY_SLIDER", "PROPERTY_NOVELTY", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_SLIDER"=>false,  "PROPERTY_COLLECTION"=>IntVal($category[id]));
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
		$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                ?><td valign=bottom>
              <div style="vertical-align: bottom; border: solid red 1px; height: 100%;"><a href="/catalogue/<?=$arElementFields[IBLOCK_ID]?>/tov_<?=$arElementFields[ID]?>.html"><img onMouseOver="document.getElementById('tov_<?=$arElementFields[ID]?>').className='sub_name_on'; <?if($arElementFields['PROPERTY_NOVELTY_VALUE']) {?>document.getElementById('new_<?=$arElementFields[ID]?>').src='/images/new_image.gif';<?} ?> <?if($arElementFields['PROPERTY_HIT_VALUE']) {?>document.getElementById('hit_<?=$arElementFields[ID]?>').src='/images/hit_image.gif';<?} ?>" onMouseOut="document.getElementById('tov_<?=$arElementFields[ID]?>').className='sub_name'; <?if($arElementFields['PROPERTY_NOVELTY_VALUE']) {?>document.getElementById('new_<?=$arElementFields[ID]?>').src='/images/new_image_off.gif';<?} ?> <?if($arElementFields['PROPERTY_HIT_VALUE']) {?>document.getElementById('hit_<?=$arElementFields[ID]?>').src='/images/hit_image_off.gif';<?} ?>"   style="border: solid red 1px; " border="0" alt="<?=$arElementFields[NAME]?>" <?echo 'src="'.$img_path.'" {$size[3]}></a>';
		echo '<div class="sub_images">';
		if($arElementFields['PROPERTY_NOVELTY_VALUE']) echo  '<img id="new_'.$arElementFields[ID].'" src="/images/new_image_off.gif" alt="Новинка">';
		if($arElementFields['PROPERTY_HIT_VALUE']) echo  '<img id="hit_'.$arElementFields[ID].'" src="/images/hit_image_off.gif" alt="Хит продаж">';
		echo '</div>';
                echo '<div id="tov_'.$arElementFields[ID].'" class="sub_name"><a href="/catalogue/'.$arElementFields[IBLOCK_ID].'/tov_'.$arElementFields[ID].'.html">'.$arElementFields[NAME].'</a></div>';
                echo '</div></td>';
	    }
	    echo "</tr></table></td>";
          
	   } 
	
   ?>


</tr>
</table></div></div>
<div class="slider_my">

		<div class="dragme" id="polzunok_sam">EKKA</div>
		 <div align="center" id="scroll_colllections">
		<table width="938"><tr>
	   <td width="15%">EKKA</td>
	   <td width="28%">Искусства & Ремесла</a></td>
	   <td width="20%">Le Roi</a></td>
	   <td width="30%">Mix'Line</a></td>
		 </tr></table>
		</div>
		</div> 

</center>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>