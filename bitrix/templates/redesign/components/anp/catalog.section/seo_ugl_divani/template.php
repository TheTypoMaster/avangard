<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
 &nbsp;<table align="right" cellspacing="0" cellpadding="0">
  <tbody>
    <tr><td width="100%">
        <div class="gray_td">
          <h1>”гловые диваны</h1>
        </div>
      </td></tr>
  
   <tr><td>
   <table width="738" align="center">
   <?        
    $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DATE_ACTIVE_FROM");
	$arFilter = Array("IBLOCK_ID"=>IntVal(9), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	$i=0;
	
	$cat_array=array();
	
	while($ob = $res->GetNextElement())
	   { 
	   $i++;
	   $arFields = $ob->GetFields();  
	   $cat_array[$i][name] = $arFields[NAME];
	    $cat_array[$i][text] = $arFields[PREVIEW_TEXT];
	   $cat_array[$i][id] = $arFields[ID];
	   }
       $kolvo_elems = count($cat_array);
       $kol = 0;
	  foreach($cat_array as $category) 
	    { 
	    $kol++;
     	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "SECTION_ID"=>IntVal(3), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_SLIDER"=>false,  "PROPERTY_COLLECTION"=>IntVal($category[id]));
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   
	  if (intval($resElement->SelectedRowsCount())>0){
?>
	  <?if(($kol>1) && ($kol <= $kolvo_elems)):?>
          <tr><td colspan="5" class="gray_line_small"></td></tr>
         <?endif?>
 <tr><td colspan="5"><b><?=$category[name]?></b>
 </td></tr>
	   <?
	   }
	   
	   
	   $i=0;
	   $rows_count= $resElement->SelectedRowsCount();
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
		$rows_count--;
		if($i==1) echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
	    $arElementFields = $obElement->GetFields();  
		
		if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']) $img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); else
		$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
		
		
		
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']) $img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); else $img_path_bl = $img_path;
		          ?>
				
				<td class="catalog_td">
				<a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><img onMouseOver="this.src='<?=$img_path?>';" onMouseOut="this.src='<?=$img_path_bl?>';" class="catalog_picture" src="<?=$img_path_bl?>" alt="<?=$arElementFields[NAME]?>"></a><br>
				<a class="catalog_name" href="/catalog/divan<?=$arElementFields[ID]?>.htm">”гловой диван <?=$arElementFields[NAME]?></a>
				
				
				</td>
		 <?if($i<3) { ?><td width="26"></td> <?}?>
				<?
	    if($i==3) { $i=0; echo "</tr>"; }
         }
	     
	   } 
	
   ?>
     </table>
      </td></tr>
  
  </tbody>
</table>
