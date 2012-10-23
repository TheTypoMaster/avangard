<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

   <div width="100%" class="gray_td_left"><a style="text-decoration:none;" href="/uglovie_divani/" ><h1>сцкнбше дхбюмш</h1></a></div>
   <div width="100%" class="gray_td_left"><a style="text-decoration:none;" href="/divani_i_kresla/" ><h1>дхбюмш х йпеякю</h1></a></div>
   <div width="100%" class="gray_td_left"><a style="text-decoration:none;" href="/modulnie_sistemi/" ><h1>лндскэмше яхярелш</h1></a></div>

   <div width="100%" class="gray_td_left"><h1>ухрш опндюф!</h1></div>

<table cellspacing="0" cellpadding="0" width="100%" border="0">
  <tbody>
      
  <?
       $arElementSelect = Array("ID", "NAME", "PROPERTY_HIT", "PROPERTY_HIT_IMG", "PROPERTY_COLLECTION", "PREVIEW_TEXT", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE"=>"Y", "!PROPERTY_HIT"=>false);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"RAND", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>4), $arElementSelect);
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
		$img_path = CFile::GetPath($arElementFields['PROPERTY_HIT_IMG_VALUE']);
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		$arCollection = GetIBlockElement($arElementFields[PROPERTY_COLLECTION_VALUE]);
                echo '<tr><td class="hit_td"><table cellspacing="0" cellpadding="0"><tr><td><a href="/redesign/catalog/byid.php?id='.$arElementFields[ID].'"><img class="hit_img" border="0" alt="'.$arElementFields[NAME].'" src="'.$img_path.'" {$size[3]}></a></td></tr><tr><td>';

?><div onMouseOver="this.className='scrolling_div_hit_on';"  onMouseOut="this.className='scrolling_div_hit';"  class="scrolling_div_hit"><?
echo '<h3 class="divan"><a class="divan_a" href="/redesign/catalog/byid.php?id='.$arElementFields[ID].'">ДХБЮМ '.$arElementFields[NAME].'</a></h3><h3 class="collection">йНККЕЙЖХЪ <strong>'.$arCollection[NAME].'</strong></h3>'.$arElementFields[PREVIEW_TEXT].'</div></td></tr></table></td></tr>';
	    }
 	    
?>

    
  </tbody>
</table>
