<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table width="100%" cellspacing="0" cellpadding="0" border="0"> 
  <tbody> 
    <tr><td width="100%" colspan="2"> 
        <div class="gray_td_main"> 
          <h1>�������</h1>
         </div>
       </td></tr>
   
    <tr><?
       $arElementSelect = Array("ID", "NAME", "PROPERTY_NOVELTY", "PROPERTY_NEW_IMG", "PROPERTY_COLLECTION", "PREVIEW_TEXT", "IBLOCK_ID");
       $arNovinkiId = array("2760","1683");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE"=>"Y", "!PROPERTY_NOVELTY"=>false, "ID"=>$arNovinkiId);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"RAND", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>2), $arElementSelect);
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
		$img_path = CFile::GetPath($arElementFields['PROPERTY_NEW_IMG_VALUE']);
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		$arCollection = GetIBlockElement($arElementFields[PROPERTY_COLLECTION_VALUE]);
                if($i==1) $align="left"; else {$align="right"; $i=0;} 
		
                echo '<td width="50%" class="new_td"><table  align="'.$align.'" cellspacing="0" cellpadding="0"><tr><td align="'.$align.'"><a href="/redesign/catalog/byid.php?id='.$arElementFields[ID].'" ><img class="new_img" border="0" alt="'.$arElementFields[NAME].'" src="'.$img_path.'" {$size[3]}></a></td></tr>';
?> 
 </tr>
   
    <tr><td> 
        <div class="scrolling_div" onmouseout="this.className='scrolling_div';" onmouseover="this.className='scrolling_div_on';"> 
		
		<? echo '<h3 class="divan"><a class="divan_a" href="/redesign/catalog/byid.php?id='.$arElementFields[ID].'" > '.$arElementFields[NAME].'</a></h3><h3 class="collection">��������� <strong>'.$arCollection[NAME].'</strong></h3>'.$arElementFields[PREVIEW_TEXT].'</div></td></tr></table></td>';    }?> 
		
		</div>
       </td></tr>
   </tbody>
 </table>
