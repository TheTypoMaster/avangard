<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
   <table width="738" align="center">
   <?        
    $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DATE_ACTIVE_FROM", "PROPERTY_IMSORT");
	$arFilter = Array("IBLOCK_ID"=>IntVal(9), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_IMSORT"=>"ASC"), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	$i=0;
	
	$cat_array=array();
	
	while($ob = $res->GetNextElement())
	   { 
	   $i++;
	   $arFields = $ob->GetFields();  
		   //$cat_array[$i][name] = $arFields[NAME];
		   //$cat_array[$i][text] = $arFields[PREVIEW_TEXT];
		   //$cat_array[$i][id] = $arFields[ID];
	   if (IntVal($arFields["PROPERTY_IMSORT_VALUE"]) <= 50) $cat_array[$i] = $arFields[ID];
	   }
	   //array_unshift($cat_array, 43);
       $kolvo_elems = count($cat_array);
       $kol = 0;
       $i=0;
	  foreach($cat_array as $category) 
	    { 
	    $kol++;
       ?>
	  
 
	   <?
	
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_SKIDKA", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT", "PROPERTY_PRICE_IM",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   // $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_SLIDER"=>false,  "PROPERTY_COLLECTION"=>IntVal($category[id]));
           $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COLLECTION"=>IntVal($category), "PROPERTY_INET_SHOP" => 178);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
		if($i==1) echo "<tr>";
	    $arElementFields = $obElement->GetFields();  
		
		if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']) $img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); else
		$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
		
		
		
		$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
		if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']) $img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); else $img_path_bl = $img_path;
		$skidka_div = '';    
		if($arElementFields["PROPERTY_SKIDKA_VALUE"]) {
			$skidka_value = $arElementFields["PROPERTY_SKIDKA_VALUE"]; /* если есть скидка, то выводится введенное значение */
			$skidka_div .='<div style="margin: 0px; padding: 0px; position: absolute; z-index: 90;"><div style="text-align:center; background: url(/images/skidka.gif) no-repeat center center; position: relative; left: 200px; top: -12px; color: #ffffff; height: 36px; width: 36px; " height=36 width=36 ><img src="/images/gif.gif" height="10" width="20"><br>-'.$skidka_value.'%</div></div>';
		}
		          ?>
				
				<td class="catalog_td">
				<? echo $skidka_div; ?>
				<a href="/shop/catalog/divan<?=$arElementFields[ID]?>.htm#top"><img onMouseOver="this.src='<?=$img_path?>';" onMouseOut="this.src='<?=$img_path_bl?>';" class="catalog_picture" src="<?=$img_path_bl?>" alt="<?=$arElementFields[NAME]?>"></a><br>
				
				<table width="100%">
					<tr>
						<td>
							<a class="catalog_name" href="/shop/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a>
						</td>
						<td id="price_new1">
							<?=$arElementFields['PROPERTY_PRICE_IM_VALUE']?>
						</td>
					</tr>
				</table>
				
				
				</td>
		 <?if($i<3) { ?><td width="26"></td> <?}?>
				<?
	    if($i==3) { $i=0; echo "</tr>"; }
         }
	     
	   } 
	
   ?>
     </table>