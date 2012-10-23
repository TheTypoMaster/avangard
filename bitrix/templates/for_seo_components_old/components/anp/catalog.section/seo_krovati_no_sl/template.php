<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
 &nbsp;<table align="right" cellspacing="0" cellpadding="0">
  <tbody>
    <tr><td width="100%">
        <div class="gray_td">
          <h1>Кровати</h1>
        </div>
      </td></tr>
    <tr><td>
          <p><img width="188" height="115" src="/upload/iblock/88d/krovati10_12_2.gif" /><font size="2" color="#ff0000"><b>  Скидка 10% на все кровати до 31 декабря</b></font> </p>
          <p><b>Внимание! Цены указаны с учётом текущих скидок и проводимых акций.</b></p><br />         
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
       ?>
	  
	   <?
	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_PRICE", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "SECTION_ID"=>IntVal(73), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COLLECTION"=>IntVal($category[id]));
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
		if($i==1) echo "<tr>";
	    $arElementFields = $obElement->GetFields();  
		
		if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']) $img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); else
		$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
		
		
		
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']) $img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); else $img_path_bl = $img_path;
		          ?>
				
				<td class="catalog_td">
				<a href="/catalog/divan<?=$arElementFields[ID]?>.htm"><img onMouseOver="this.src='<?=$img_path?>';" onMouseOut="this.src='<?=$img_path_bl?>';" class="catalog_picture" src="<?=$img_path_bl?>" alt="<?=$arElementFields[NAME]?>"></a><br>
				<table width="100%"><tr><td><a class="catalog_name" href="/catalog/divan<?=$arElementFields[ID]?>.htm">Кровать <?=$arElementFields[NAME]?></a></td>
				<td id="price_new1"><?=$arElementFields['PROPERTY_PRICE_VALUE']?>
				
				</td></tr></table>
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