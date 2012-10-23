<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$id=$_GET[id];
 if($id) {$arElement=GetIBlockElement($id);} 
$id_new =$arElement[PROPERTIES][NOVELTY][VALUE];
$id_hit =$arElement[PROPERTIES][HIT][VALUE];
$id_collection = $arElement[PROPERTIES][COLLECTION][VALUE];
$id_category = $arElement[IBLOCK_SECTION_ID];
?>

<table cellspacing="0" cellpadding="0" width="182" border="0">
  <tbody>
  	<?
	global $USER;
	 if ($USER->GetId()) {
	 	?>
		<tr><td width="100%">
	        <div class="gray_td">
			  <h1>КОРЗИНА</h1>
			</div>
		  </td>
		</tr>
		<tr><td width="100%"><div id="basket_text">Корзина пуста</div><br><br>
		<script language="javascript">
		$(document).ready(function(){
		   countSubject();
		  });
		  </script>
		  </td>
		</tr>
		<?
		/*echo "<!-- user показать авторизованному пользователю -->";
		echo "Предметы модельного ряда<br>";
		print_r($_GET);
		$arSelect = Array("ID", "NAME", "PROPERTY_WIDTH", "PROPERTY_HEIGHT", "PROPERTY_DEPTH", "PROPERTY_SALONS.NAME", "PROPERTY_SALONS.ID", "PROPERTY_TRANSFORMATION", "PROPERTY_TRANSFORMATION.NAME");
		$arFilter = Array("IBLOCK_ID"=>19, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COLLECTION" => $_GET[id]);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		echo "<pre>arFields - ";
		while($ob = $res->GetNextElement()){
		  $arFields = $ob->GetFields();
		  $arProperty = CIBlockProperty::GetByID("SRC", false, "company_news");
		  print_r($arFields);
		  echo "<br><br>arProperty - ";
		  print_r($arProperty);
		}
		echo "</pre>";*/
	}
	else {
		//echo "<!-- user не нужно показывать -->";
	}
	?>
    <tr><td width="100%">
        <div class="gray_td">
          <h1>КАТАЛОГ ТОВАРОВ</h1>
        </div>
      </td>
	</tr>
  
   
     <tr><td>
   
   <?        
         $kol = 0;
	     $kol++;
           $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_NOVELTY", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_NOVELTY"=>false);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	  
         ?>
	  <div class="spisok_div"> <div class="navHeader" id="navHeader<?=$kol?>" onClick="shiftSubDiv(<?=$kol?>)">
       <span id = "ic_<?=$kol?>"><?if(!($id_new)) echo "+"; else echo "-";?></span> <font class="font_red">Новинки (<?=$resElement->SelectedRowsCount()?>)</font>
	   </div>
       <ul id="subDiv<?=$kol?>" <?if(!($id_new)) echo 'style="display:none;"'?>>
       <?
	
	
           $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                ?>
				<?if($arElementFields[ID] == $id):?>
				<li><font class="a_link_selected"><?=$arElementFields[NAME]?></font></li>
				<?else:?>
				<li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a></li>
				<?endif?>
				<?
	    }
	    echo "</ul>";
          
	  	     $kol++;
   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_NOVELTY", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_HIT"=>false);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	
       ?>
	   <div class="navHeader" id="navHeader<?=$kol?>" onClick="shiftSubDiv(<?=$kol?>)">
        <span id = "ic_<?=$kol?>"><?if(!($id_hit)) echo "+"; else echo "-";?></span> <font class="font_red">Хиты продаж(<?=$resElement->SelectedRowsCount()?>)</font>
	   </div>
       <ul id="subDiv<?=$kol?>" <?if(!($id_hit)) echo 'style="display:none;"'?>>
       <?
	
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                ?>
				<?if($arElementFields[ID] == $id):?>
				<li><font class="a_link_selected"><?=$arElementFields[NAME]?></font></li>
				<?else:?>
				<li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a></li>
				<?endif?>
				<?
	    }
	    echo "</ul>";
          
	
   ?>
  
  
   <?        

   // если $ID не задан или это не число, тогда 
   // $ID будет =0, выбираем корневые разделы
   $ID = IntVal(0);
   // выберем папки из информационного блока $BID и раздела $ID
   $items = GetIBlockSectionList(IntVal(5), $ID, Array("sort"=>"asc"), 50);
  $i=0;
  $cat_array=array();
 
  while($arItem = $items->GetNext())
   {
      $i++;
	   $cat_array[$i][name] = $arItem[NAME];
	   $cat_array[$i][id] = $arItem[ID];
	   }

    
	  foreach($cat_array as $category) 
	    { 
	    $kol++;
         $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_NOVELTY", "SECTION_ID", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "SECTION_ID"=>IntVal($category[id]));
	    $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	 ?>
	   <div class="navHeader" id="navHeader<?=$kol?>" onClick="shiftSubDiv(<?=$kol?>)">
        <span id = "ic_<?=$kol?>"><?if($id_category != $category[id]) echo "+"; else echo "-";?></span> <font><?=$category[name]?> (<?=$resElement->SelectedRowsCount()?>)</font>
	   </div>
       <ul id="subDiv<?=$kol?>" <?if($id_category != $category[id]) echo 'style="display:none;"'?>>
       <?
	
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                 ?>
				<?if($arElementFields[ID] == $id):?>
				<li><font class="a_link_selected"><?=$arElementFields[NAME]?></font></li>
				<?else:?>
				<li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a></li>
				<?endif?>
				<?
	    }
	    echo "</ul>";
          
	   } 
	
   ?>
   </div>
   
   
  </td></tr>
  
 
	
   <tr><td>
        
          <h1>Коллекции</h1>
      
      </td></tr>
  
   
   <tr><td>
  <div class="spisok_div"> 
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
       
	  foreach($cat_array as $category) 
	    { 
	    $kol++;
           $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_NOVELTY", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COLLECTION"=>IntVal($category[id]));
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	

       ?>
	   <div class="navHeader" id="navHeader<?=$kol?>" onClick="shiftSubDiv(<?=$kol?>)">
        <span id = "ic_<?=$kol?>"><?if($id_collection != $category[id]) echo "+"; else echo "-";?></span> <font><?=$category[name]?> (<?=$resElement->SelectedRowsCount()?>)</font>
	   </div>
       <ul id="subDiv<?=$kol?>" <?if($id_collection != $category[id]) echo 'style="display:none;"'?>>
       <?
	
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                 ?>
				<?if($arElementFields[ID] == $id):?>
				<li><font class="a_link_selected"><?=$arElementFields[NAME]?></font></li>
				<?else:?>
				<li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm"><?=$arElementFields[NAME]?></a></li>
				<?endif?>
				<?
	    }
	    echo "</ul>";
          
	   } 
	
   ?>
  </div>
  </td></tr>
  
   <?
  if ($USER->GetId()) {
	 	?>
		<tr><td width="100%">
	        <div class="gray_td">
			  <h1>ВИДЫ ОБИВОК</h1>
			</div>
		  </td>
		</tr>
		<tr><td width="100%"><div class="spisok_div">
		<a href="/texture"><b>Каталог обивок</b></a><br><br>
		<?
		
		$arFilter = Array('IBLOCK_ID'=>20, 'GLOBAL_ACTIVE'=>'Y', array());
		  $db_list = CIBlockSection::GetList(array(), $arFilter, true);
		  while($ar_result = $db_list->GetNext()){
			echo '<div class="navHeader">
        <font><a href="/texture/section_'.$ar_result['ID'].'.html">'.$ar_result['NAME'].'</a></font>
	   </div>';
		  }
		?>
		
		</div>
		  </td>
		</tr>
		<?
		/*echo "<!-- user показать авторизованному пользователю -->";
		echo "Предметы модельного ряда<br>";
		print_r($_GET);
		$arSelect = Array("ID", "NAME", "PROPERTY_WIDTH", "PROPERTY_HEIGHT", "PROPERTY_DEPTH", "PROPERTY_SALONS.NAME", "PROPERTY_SALONS.ID", "PROPERTY_TRANSFORMATION", "PROPERTY_TRANSFORMATION.NAME");
		$arFilter = Array("IBLOCK_ID"=>19, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COLLECTION" => $_GET[id]);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		echo "<pre>arFields - ";
		while($ob = $res->GetNextElement()){
		  $arFields = $ob->GetFields();
		  $arProperty = CIBlockProperty::GetByID("SRC", false, "company_news");
		  print_r($arFields);
		  echo "<br><br>arProperty - ";
		  print_r($arProperty);
		}
		echo "</pre>";*/
	}
	?>

  <tr><td width="100%">
        <div class="gray_td">
          <h1>АКЦИИ </h1>
        </div>
      </td></tr>
  
    <tr><td> <?$APPLICATION->IncludeComponent("bitrix:news.list", "akcii_list2", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"14",
	"NEWS_COUNT"	=>	"50",
	"SORT_BY1"	=>	"ACTIVE_FROM",
	"SORT_ORDER1"	=>	"DESC",
	"SORT_BY2"	=>	"SORT",
	"SORT_ORDER2"	=>	"ASC",
	"FILTER_NAME"	=>	"",
	"FIELD_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
		2	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"flashno",
		1	=>	"href",
		2	=>	"pictorflash",
		3	=>	"",
	),
	"DETAIL_URL"	=>	"",
	"AJAX_MODE"	=>	"N",
	"AJAX_OPTION_SHADOW"	=>	"Y",
	"AJAX_OPTION_JUMP"	=>	"N",
	"AJAX_OPTION_STYLE"	=>	"Y",
	"AJAX_OPTION_HISTORY"	=>	"N",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"CACHE_FILTER"	=>	"N",
	"PREVIEW_TRUNCATE_LEN"	=>	"",
	"ACTIVE_DATE_FORMAT"	=>	"d.m.Y",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"N",
	"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
	"ADD_SECTIONS_CHAIN"	=>	"N",
	"HIDE_LINK_WHEN_NO_DETAIL"	=>	"N",
	"PARENT_SECTION"	=>	"",
	"DISPLAY_TOP_PAGER"	=>	"N",
	"DISPLAY_BOTTOM_PAGER"	=>	"N",
	"PAGER_TITLE"	=>	"Новости",
	"PAGER_SHOW_ALWAYS"	=>	"N",
	"PAGER_TEMPLATE"	=>	"",
	"PAGER_DESC_NUMBERING"	=>	"N",
	"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	"36000",
	"DISPLAY_DATE"	=>	"N",
	"DISPLAY_NAME"	=>	"Y",
	"DISPLAY_PICTURE"	=>	"N",
	"DISPLAY_PREVIEW_TEXT"	=>	"N"
	)
);?></td></tr>
   
  </tbody>
</table>

