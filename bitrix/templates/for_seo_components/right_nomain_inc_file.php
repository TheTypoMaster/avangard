<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> <?
$id=$_GET[id];
 if($id) {$arElement=GetIBlockElement($id);} 
$id_new =$arElement[PROPERTIES][NOVELTY][VALUE];
$id_hit =$arElement[PROPERTIES][HIT][VALUE];
$id_collection = $arElement[PROPERTIES][COLLECTION][VALUE];
$id_category = $arElement[IBLOCK_SECTION_ID];
?> 
<div class="rc_h3"><a href="#">КАТАЛОГ ТОВАРОВ</a></div> 
<div class="rc_spisok">
<?        
         $kol = 0;
	     $kol++;
           $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_NOVELTY", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_NOVELTY"=>false);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	  
         ?> 	 
<div onclick="shiftSubDiv(<?=$kol?>)">
<span id="ic_<?=$kol?>"><?if(!($id_new)) echo "+"; else echo "-";?></span>
<font class="font_red">Новинки (<?=$resElement->SelectedRowsCount()?>)</font>
</div>
<ul id="subDiv<?=$kol?>" <?if(!($id_new)) echo 'style="display:none;"'?>>

<?
/*<div class="spisok_div"> 
          <div class="navHeader" id="navHeader&lt;img id=" bxid_11813"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; onClick=&quot;shiftSubDiv(<?=$kol?>)&quot;&gt; <span id="ic_&lt;img id=" bxid_214706"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot;&gt;<?if(!($id_new)) echo "+"; else echo "-";?></span> <font class="font_red">Новинки (<?=$resElement->SelectedRowsCount()?>)</font> 	 </div>
         
          <ul id="subDiv&lt;img id=" bxid_877883"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; <?if(!($id_new)) echo 'style="display:none;"'?>&gt; 
          */	
	
           $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                ?> 				<?if($arElementFields[ID] == $id):?> 				
            <li><?=$arElementFields[NAME]?></li>
           				<?else:?> 				
            <li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm" ><?=$arElementFields[NAME]?></a></li>
           				<?endif?> 				<?
	    }
	    echo "</ul>";
          
	  	     $kol++;
   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_NOVELTY", "PROPERTY_HIT",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_HIT"=>false);
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	
       ?> 	 
<div onclick="shiftSubDiv(<?=$kol?>)">
<span id="ic_<?=$kol?>"><?if(!($id_hit)) echo "+"; else echo "-";?></span>
<font class="font_red">Хиты продаж(<?=$resElement->SelectedRowsCount()?>)</font>
</div>
<ul id="subDiv<?=$kol?>" <?if(!($id_hit)) echo 'style="display:none;"'?>>
       
       
       
       <?
           /* <div class="navHeader" id="navHeader&lt;img id=" bxid_615762"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; onClick=&quot;shiftSubDiv(<?=$kol?>)&quot;&gt; <span id="ic_&lt;img id=" bxid_154846"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot;&gt;<?if(!($id_hit)) echo "+"; else echo "-";?></span> <font class="font_red">Хиты продаж(<?=$resElement->SelectedRowsCount()?>)</font> 	 </div>
           
            <ul id="subDiv&lt;img id=" bxid_460848"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; <?if(!($id_hit)) echo 'style="display:none;"'?>&gt; 
            */
            
	
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                ?> 				<?if($arElementFields[ID] == $id):?> 				
              <li><?=$arElementFields[NAME]?></li>
             				<?else:?> 				
              <li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm" ><?=$arElementFields[NAME]?></a></li>
             				<?endif?> 				<?
	    }
	    echo "</ul>";
          
	
   ?> <?        

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
<div onclick="shiftSubDiv(<?=$kol?>)">
<span id="ic_<?=$kol?>">+</span>
<font class="font"><?=$category[name]?> (<?=$resElement->SelectedRowsCount()?>)</font>
</div>
<ul id="subDiv<?=$kol?>" <?if($id_category != $category[id]) echo 'style="display:none;"'?>>
	 
	 <? /*
              <div class="navHeader" id="navHeader&lt;img id=" bxid_320083"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; onClick=&quot;shiftSubDiv(<?=$kol?>)&quot;&gt; <span id="ic_&lt;img id=" bxid_159826"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot;&gt;<?if($id_category != $category[id]) echo "+"; else echo "-";?></span> <font><?=$category[name]?> (<?=$resElement->SelectedRowsCount()?>)</font> 	 </div>
             
              <ul id="subDiv&lt;img id=" bxid_10802"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; <?if($id_category != $category[id]) echo 'style="display:none;"'?>&gt; 
              */
	
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                 ?> 				<?if($arElementFields[ID] == $id):?> 				
                <li><font class="a_link_selected"><?=$arElementFields[NAME]?></font></li>
               				<?else:?> 				
                <li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm" ><?=$arElementFields[NAME]?></a></li>
               				<?endif?> 				<?
	    }
	    echo "</ul>";
          
	   } 
	
   ?> </div>

   
    <div class="rc_spisok">
<p class="rc_s_h2">Коллекции</p>
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
<div onclick="shiftSubDiv(<?=$kol?>)">
<span id="ic_<?=$kol?>">+</span>
<font class="font"><?=$category[name]?> (<?=$resElement->SelectedRowsCount()?>)</font>
</div>
<ul id="subDiv<?=$kol?>" <?if($id_collection != $category[id]) echo 'style="display:none;"'?>>

        <? /*
          <div class="navHeader" id="navHeader&lt;img id=" bxid_280137"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; onClick=&quot;shiftSubDiv(<?=$kol?>)&quot;&gt; <span id="ic_&lt;img id=" bxid_804094"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot;&gt;<?if($id_collection != $category[id]) echo "+"; else echo "-";?></span> <font><?=$category[name]?> (<?=$resElement->SelectedRowsCount()?>)</font> 	 </div>
         
          <ul id="subDiv&lt;img id=" bxid_27999"="" src="/bitrix/images/fileman/htmledit2/php.gif" border="0">&quot; <?if($id_collection != $category[id]) echo 'style="display:none;"'?>&gt; 
          */
	
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arElementFields = $obElement->GetFields();  
	//	$img_path = CFile::GetPath($arElementFields['PROPERTY_SLIDER_IMG_VALUE']);
	//	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$img_path);
                 ?> 				<?if($arElementFields[ID] == $id):?> 				
            <li><?=$arElementFields[NAME]?></li>
           				<?else:?> 				
            <li><a class="a_link" href="/catalog/divan<?=$arElementFields[ID]?>.htm" ><?=$arElementFields[NAME]?></a></li>
           				<?endif?> 				<?
	    }
	    echo "</ul>";
          
	   } 
	
   ?>
        </div>

   
    <div class="rc_h3"><a href="#">АКЦИИ</a></div>
    <div id="rc_img">
 <?$APPLICATION->IncludeComponent("bitrix:news.list", "akcii_list2", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "14",
	"NEWS_COUNT" => "50",
	"SORT_BY1" => "SORT",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "href",
		1 => "flashno",
		2 => "pictorflash",
		3 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "N",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "N",
	"DISPLAY_PREVIEW_TEXT" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
</div>