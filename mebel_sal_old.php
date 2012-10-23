<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Диваны в наличии");
?>
<script language="javascript">



window.onload = function(){
var div_pr = document.getElementById("prokrutka");
if(is_ie) {
var smeshenie = window.document.body.offsetHeight - 181;
div_pr.style.height=smeshenie+"px";
}
else
{
var smeshenie = window.innerHeight - 181;
 div_pr.style.height=smeshenie+"px";
}
}


window.onresize = function(){
var div_pr = document.getElementById("prokrutka");
if(is_ie) {
var smeshenie = window.document.body.offsetHeight - 181;
div_pr.style.height=smeshenie+"px";
}
else
{
var smeshenie = window.innerHeight - 181;
 div_pr.style.height=smeshenie+"px";
}
}





function check_all_types_f(klikid, checkbox_names, this_button_is_all) 
{ 



var klik = document.getElementById(klikid);
var e,i=0,m=0,j=0,l=0; 


if(this_button_is_all) {

while(e=document.getElementsByTagName('input').item(i++)) 
{ 

if(e.type=='checkbox'&&e.name==checkbox_names) 
{ 
if(klik.checked==true) {
if(e.checked==false) e.checked=true;
}
else {
if(e.checked==true) e.checked=false;
}
} 



}
i=0;
}

else {
m=0;
j=0;
l=0;
while(e=document.getElementsByTagName('input').item(i++)) 
{ 
if(e.type=='checkbox'&&e.name==checkbox_names) 
{ 
m++; if(e.checked==false) j++; else l++;
} 
}

if((klik.checked==true) && (m>l)) klik.checked=false;
if((klik.checked==false) && (m==l)) klik.checked=true;

}

}

var razmer_spal_mesta = new Array();
razmer_spal_mesta[0] = new Array("700x2000", "750x1900", "820x2000");
razmer_spal_mesta[1] = new Array("820x2000", "1130x1900", "1150x1960", "1250x1980", "1300x1950");
razmer_spal_mesta[2] = new Array("1130x1900", "1150x1960", "1250x1980", "1300x1950", "1400x2000", "1400x1900", "1460x2100", "1500x1900", "1530x1900", "1580x2130", "1550x1960");
razmer_spal_mesta[3] = new Array("1460x2100", "1580x2130", "1530x1900", "1400x1900", "1500x1900",  "1400x2000", "1600x2000", "1550x1960");
razmer_spal_mesta[4] = new Array("1600x2000", "1950x2000");


var trigger = new Array();
trigger[0] = 0;
trigger[1] = 0;
trigger[2] = 0;
trigger[3] = 0;
trigger[4] = 0;


Array.prototype.EqualsRazmers = function() {

 var not_ravno=0;
 var result= new Array();
 for (var ident=0; ident < 5; ident++) {
 if(trigger[ident]==1) {
  for (var i=0; i < this.length; i++) {
  for (var j=0; j < razmer_spal_mesta[ident].length; j++) {
  if(this[i]==razmer_spal_mesta[ident][j]) result.push(this[i]);
}
}
}
}


var e, i=0;
while(e=document.getElementsByTagName('input').item(i++)) 
{ 
if(e.type=='checkbox'&&e.name=="razmersp[]") 
{ 
  
  for (var b=0; b < this.length; b++) {
    if(e.value==this[b]) { e.checked=false;    for (var r=0; r < result.length; r++) if(result[r]==e.value) e.checked=true;  }  
}
}
}
return false;
}

 		

function set_razmer(img, trigid){

var razmer_spal = new Array();
razmer_spal[0] = document.getElementById("r_thin");
razmer_spal[1] = document.getElementById("r_fat");
razmer_spal[2] = document.getElementById("r_near");
razmer_spal[3] = document.getElementById("r_thinthin");
razmer_spal[4] = document.getElementById("r_fatfat");

for (var ident=0; ident < 5; ident++) {
                 if(razmer_spal[ident].className=="contained_div") {
				 				   if(trigger[ident] == 1) razmer_spal[ident].className="selected_div"; else razmer_spal[ident].className="notselected_div"; 
								  }
          }
	
	
var all_checked = document.getElementById("razmer_all");
if(img.className == "selected_div") {img.className = "deselected_div"; } else { img.className = "selected_div";}
if(trigger[trigid]==0) 

{trigger[trigid]=1; 
  var counter = 0;
for (var ident=0; ident < 5; ident++) {
                 if(trigger[ident] == 1) counter++;
          }
		  
		  if(counter==5) all_checked.checked = true; else all_checked.checked = false;

		  
		  var e, i=0;
while(e=document.getElementsByTagName('input').item(i++)) 
{ 

if(e.type=='checkbox'&&e.name=="razmersp[]") 
{ 

for (var identif=0; identif < 5; identif++) {
	
         for (var cc=0; cc < razmer_spal_mesta[trigid].length; cc++) {
		        if(razmer_spal_mesta[trigid][cc]==e.value) e.checked=true;
          }
    }
	
} 
}

} else { trigger[trigid]=0; razmer_spal_mesta[trigid].EqualsRazmers(); var klik=document.getElementById('razmer_all'); klik.checked = false;	}

	
}

function check_all_razmers(button_all) 
{ 
var razmer_spal = new Array();
razmer_spal[0] = document.getElementById("r_thin");
razmer_spal[1] = document.getElementById("r_fat");
razmer_spal[2] = document.getElementById("r_near");
razmer_spal[3] = document.getElementById("r_thinthin");
razmer_spal[4] = document.getElementById("r_fatfat");

for (var ident=0; ident < 5; ident++) {
                 if(razmer_spal[ident].className=="contained_div") {
				 				   if(trigger[ident] == 1) razmer_spal[ident].className="selected_div"; else razmer_spal[ident].className="notselected_div"; 
								  }
    }
	
	
if(button_all.checked == true) {
for (var ident=0; ident < 5; ident++) {
                 trigger[ident] = 1; razmer_spal[ident].className = "selected_div";
          }
} else {
for (var ident=0; ident < 5; ident++) {
                 trigger[ident] = 0;  razmer_spal[ident].className = "deselected_div";
          }
}
var e, i=0;
while(e=document.getElementsByTagName('input').item(i++)) 
{ 

if(e.type=='checkbox'&&e.name=="razmersp[]") 
{ 
if(button_all.checked==true) {
if(e.checked==false) e.checked=true;
}
else {
if(e.checked==true) e.checked=false;
}
} 
}
}

function check_razmer_item(option_id, button_all_id) 
{
var razmer_spal = new Array();
razmer_spal[0] = document.getElementById("r_thin");
razmer_spal[1] = document.getElementById("r_fat");
razmer_spal[2] = document.getElementById("r_near");
razmer_spal[3] = document.getElementById("r_thinthin");
razmer_spal[4] = document.getElementById("r_fatfat");

for (var ident=0; ident < 5; ident++) {
                 if(razmer_spal[ident].className=="contained_div") {
				 				   if(trigger[ident] == 1) razmer_spal[ident].className="selected_div"; else razmer_spal[ident].className="notselected_div"; 
								  }
    }
   
    var razmer_text;
	razmertext = option_id.value;
	for (var identif=0; identif < 5; identif++) {
	
         for (var cc=0; cc < razmer_spal_mesta[identif].length; cc++) {
		        if(razmer_spal_mesta[identif][cc]==razmertext) {razmer_spal[identif].className = "contained_div";}
          }
    }
	
	var klik=document.getElementById(button_all_id);
	
var e,i=0, m=0,j=0,l=0,i=0;
while(e=document.getElementsByTagName('input').item(i++)) 
{ 
if(e.type=='checkbox'&&e.name==option_id.name) 
{ 
m++; if(e.checked==false) j++; else l++;
} 
}

if((klik.checked==true) && (m>l)) klik.checked=false;
if((klik.checked==false) && (m==l)) klik.checked=true;
	
}	

</script>


<h2>Салон <?$res = CIBlockElement::GetByID($s_val);if($ar_res = $res->GetNext())  echo $ar_res['NAME'];?></h2>


<div height="169" class="filter_td">

<table height="169" class="filter_table" cellpadding="0" cellspacing="0">
<form name="filter_form" method="get" id="filter_form">
<?if($salon) $s_val = $salon; else $s_val=$id; ?>

<input type="hidden" name="salon" value="<?=$s_val?>">
<tr>
<td valign="top" style="padding: 2px;">
<center>
<h3>Фильтр</h3>
<h4>по каталогу</h4>
</center>
<table>

<?if($all_models) { ?>
<tr><td></td><td><a href="/catalog/divan<?=$all_models?>.htm?back=y"><?$res = CIBlockElement::GetByID(intVal($all_models));if($ar_res = $res->GetNext())  echo $ar_res['NAME'];?></a></td></tr>
<?if($from_catalog!="y") {?><tr><td><input type="radio" name="id" value="<?=$salon?>"></td><td>Салон <?$res = CIBlockElement::GetByID($s_val);if($ar_res = $res->GetNext())  echo $ar_res['NAME'];?></td></tr><?}?>
<tr><td><input type="radio" name="id" checked value="all"></td><td>Все салоны</td></tr>
<?
} else {?>
<tr><td><input type="radio" name="id" <?if($id!='all') {?>checked<?}?> value="<?if($id=='all') echo $s_val; else echo $id;?>"></td><td>Салон <?$res = CIBlockElement::GetByID($s_val);if($ar_res = $res->GetNext())  echo $ar_res['NAME'];?></td></tr>
<tr><td><input type="radio" name="id"  <?if($id=='all') {?>checked<?}?> value="all"></td><td>Все салоны</td></tr>
<? } ?>

</table>
</td>
<td align="center" valign="top" class="filter">
<table><tr><td align="left" style="color: #e20a17;">Название:</td><td align="right"><table><tr><td><input type="checkbox" name="type_all" id="type_all"  onClick="check_all_types_f('type_all', 'type[]', this);" ></td><td>все</td></tr></table></td></tr>
<tr><td colspan="2">
<div style="height: 120px; overflow-y: auto; border: solid #e1e1e1 1px; margin-bottom: 6px;">

<table align="left">
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
		if($kol>0) echo '<tr><td colspan="2"><div style="font-size: 8px; white-space: none; color: gray; width: 100%; border-top: solid gray 1px;">'.$category[name].'</div></td></tr>';
	    $kol++;
       ?>
	  <?
	   $arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_SLIDER_IMG", "PROPERTY_SLIDER", "PROPERTY_NOVELTY", "PROPERTY_HIT",   "PROPERTY_ACTIA",  "PROPERTY_COLLECTION", "IBLOCK_ID");
	
	   $arElementFilter = Array("IBLOCK_ID"=>IntVal(5), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_SLIDER"=>false,  "PROPERTY_COLLECTION"=>IntVal($category[id]));
	
	   $resElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arElementFilter, false, Array("nPageSize"=>50), $arElementSelect);
	   $i=0;
	   while($obElement = $resElement->GetNextElement())
	    { 
	    $i++;
	    $arFields = $obElement->GetFields();  
		?><tr><td width="12"><input type="checkbox" onClick="check_all_types_f('type_all', 'type[]');" value="<?=$arFields[ID]?>" name="type[]" id="type_<?=$arFields[ID]?>"></td><td align="left"><?=$arFields[NAME]?></td></tr><?
		}
	    echo '<tr><td colspan="2"><div style="font-size: 8px; white-space: none; color: gray; width: 100%;">'.$category[name].'</div></td></tr>';
	   
		}
		
?>
</table>


</div>
</td></tr></table>
</td>
<td align="center" valign="top" class="filter">
<table><tr><td align="left" style="color: #e20a17;">Механизм:</td><td align="right"><table><tr><td width="10" nowrap></td><td><input type="checkbox" name="mech_all" id="mech_all"  onClick="check_all_types_f('mech_all', 'mech[]', this);"></td><td>все</td></tr></table></td></tr>
<tr><td colspan="2">

<div style="height: 120px; overflow-y: auto; border: solid #e1e1e1 1px; margin-bottom: 6px;">
<table align="left">
<tr><td width="12"><input type="checkbox" onClick="check_all_types_f('mech_all', 'mech[]');" value="141" name="mech[]" id="type_141"></td><td align="left"><b>нет</b></td></tr>
<?
 $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
 $arFilter = Array("IBLOCK_ID"=>IntVal(11), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	
	
	while($ob = $res->GetNextElement())
	   { 
	   $arFields = $ob->GetFields();  
	if(($arFields[NAME]!="авангард 2") && ($arFields[NAME]!="нет")){?><tr><td width="12"><input type="checkbox" onClick="check_all_types_f('mech_all', 'mech[]');" value="<?=$arFields[ID]?>" name="mech[]" id="type_<?=$arFields[ID]?>"></td><td align="left"><?=$arFields[NAME]?></td></tr><?}
	
	   }
		
?>

</table>
</div>
</td></tr></table>
</td>
<td align="center" valign="top" class="filter">
<table><tr><td align="left" colspan="2"style="color: #e20a17;">Размер спального места:</td><td align="right"><table><tr><td colspan="2"><input type="checkbox" id="razmer_all" name="razmer" onClick="check_all_razmers(this);"></td><td>все</td></tr></table></td></tr>
<tr><td colspan="4">
<table class="fat_n_thin"><tr>
<td><div class="notselected_div" onClick="set_razmer(this, 0)" id="r_thin"><img class="notyet" src="/images/filter_thin.gif" alt="На одного человека"></div></td>
<td><div class="notselected_div" onClick="set_razmer(this, 1)" id="r_fat"><img class="notyet" src="/images/filter_fat.gif" alt="На одного крупного человека" ></div></td>
<td><div class="notselected_div" onClick="set_razmer(this, 2)" id="r_near"><img class="notyet" src="/images/filter_near.gif" alt="На двоих крепок обнявшихся людей" ></div></td>
<td><div class="notselected_div" onClick="set_razmer(this, 3)" id="r_thinthin"><img class="notyet" src="/images/filter_thinthin.gif" alt="На двоих" ></div></td>
<td><div class="notselected_div" onClick="set_razmer(this, 4)" id="r_fatfat"><img class="notyet" src="/images/filter_fatfat.gif" alt="На двух крупных людей"></div></td>
</tr></table>
</td></tr>
<tr><td colspan="4" align="left">

<div style="height: 54px; overflow-y: auto; border: solid #e1e1e1 1px; margin-bottom: 6px;">

<table align="left">
<?
$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IntVal(15), "CODE"=>"spal"));

$tmp_count = 0;
while($enum_fields = $property_enums->GetNext()){  
$tmp_count++;
if($tmp_count==1) echo "<tr>"; 
?>
<td width="8"><input type="checkbox" value="<?=$enum_fields["VALUE"]?>" onClick="check_razmer_item(this, 'razmer_all');" name="razmersp[]" id="razmersp_<?=$enum_fields["ID"]?>"></td><td align="left"><?=$enum_fields["VALUE"]?></td>
<?
if($tmp_count==1) echo '<td width="6" style="width:8px;">'; 
if($tmp_count==2) { echo "</tr>";  $tmp_count=0;}
}
if($tmp_count==1) { echo "</tr>";}
?>
</table>


</div>

</td></tr></table>
</td>

<td align="center" valign="top" class="filter">
<table><tr><td align="left" colspan="2"style="color: #e20a17;">Декор:</td><td  colspan="2" align="right"><table><tr><td><input type="checkbox" name="decor_all" id="decor_all" onClick="check_all_types_f('decor_all', 'decor[]', this);"></td><td>все</td></tr></table></td></tr>
<tr><td colspan="2"><img src="/images/filter_decor1.gif" alt="Декор декапе"></td>
<td colspan="2"><img src="/images/filter_decor2.gif" alt="Декор махагон"></td></tr>
<tr><td><input onClick="check_all_types_f('decor_all', 'decor[]');" type="checkbox" name="decor[]" value="декапе"></td><td>декапе</td>
<td><input onClick="check_all_types_f('decor_all', 'decor[]');"  type="checkbox" name="decor[]" value="орех"></td><td>орех</td></tr>
<tr><td colspan="2"><img src="/images/filter_decor3.gif" alt="Декор орех"></td>
<td colspan="2"><img src="/images/filter_decor4.gif" alt="Декор венге"></td></tr>
<tr><td><input onClick="check_all_types_f('decor_all', 'decor[]');"  type="checkbox" name="decor[]" value="махагон"></td><td>махагон</td>
<td><input onClick="check_all_types_f('decor_all', 'decor[]');"  type="checkbox" name="decor[]" value="венге"></td><td>венге</td></tr>
</table>
</td>



<td valign="top" class="filter">
<table>
<tr><td colspan="4" align="left" style="color: #e20a17;">Цена(в рублях):</td></tr>

<tr><td>от</td><td><input type="text" size="6" name="price_from"></td><td>до</td><td><input type="text" size="6" name="price_till"></td></tr>
<tr><td colspan="4" align="left" style="font-size: 9px; color: #7c7c7c;">Есть в наличии:</td></tr>
<tr><td colspan="4" id="price_diapazon" align="left" style="font-size: 9px; color: #7c7c7c;"></td></tr>

<tr><td><input type="checkbox" name="akcii"></td><td colspan="3" align="left">
Только модели, на которые<br>
идут Акции или Скидки</td></tr>

</table>

</td>

</tr>
</table>


</div>
<div valign="middle" id="submit_div"><input type="submit" name="submit" value="Показать" class="button_show"></div>
</form>

	<div align="right" height="100%" width="100%" class="prokrutka"  id="prokrutka" align="left">
	<div width="100%" align="left" class="filer_info_about">

<h3 align="left">
Вы выбрали:	
</h3>
<?if($id && $id!='all') {?><b>Салон:</b> <? $res = CIBlockElement::GetByID($id); if($ar_res = $res->GetNext())  echo $ar_res['NAME']; ?>
<br /><?}?>
<?if($type) {?><b>Название:</b> <?foreach($type as $item) { $res = CIBlockElement::GetByID($item); if($ar_res = $res->GetNext())  echo $ar_res['NAME']."; "; }?>
<br /><?}?>
<?if($mech) {?><b>Механизм:</b> <?foreach($mech as $item) { $res = CIBlockElement::GetByID($item); if($ar_res = $res->GetNext())  echo $ar_res['NAME']."; "; }?>
<br /><?}?>
<?if($razmersp) {?><b>Размер спального места:</b> <?foreach($razmersp as $item) { echo $item."; "; }?>
<br /><?}?>
<?if($decor) {?><b>Декор:</b> <?foreach($decor as $item) { echo $item."; "; }?>
<br /><?}?>
<?if($pricemin || $pricemax) {?><b>Цена:</b> <?if($price_from) echo "от ".$price_from; if($price_till) echo "до ".$price_till;?>
<br /><?}?>
<?if($akcii) { echo "<b>Только модели, на которые идут акции и скидки</b>"; }?>
<br />
</div>

<table width="100%">
<?

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_salon", "PROPERTY_tovar","PROPERTY_color", "PROPERTY_price_old", "PROPERTY_price_new", "PROPERTY_mechanizm",  "PROPERTY_spal", "PROPERTY_aktia");



if($all_models) $arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_tovar"=>IntVal($all_models));
else 
{

if($id=="all") 
{
$arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
}
else 
{
if($id>0) $id=$id; else  $id='';
$arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_salon"=>IntVal($id));
}

 
if(count($type)>1) {
$arFilter["PROPERTY_tovar"] = array();
foreach($type as $for_filter) array_push($arFilter["PROPERTY_tovar"] , $for_filter);
} else {
$arFilter["PROPERTY_tovar"] = $type[0];
}
}

if(count($mech)>1) {
$arFilter["PROPERTY_mechanizm"] = array();
foreach($mech as $for_filter) array_push($arFilter["PROPERTY_mechanizm"] , $for_filter);
} else {
$arFilter["PROPERTY_mechanizm"] = $mech[0];
}

if($List1) $arFilter["PROPERTY_W_SPAL"] = $List1;
if($List2) $arFilter["PROPERTY_L_SPAL"] = $List2;


if(count($decor)>1) {
$arFilter["PROPERTY_color_VALUE"] = array();
foreach($decor as $for_filter) array_push($arFilter["PROPERTY_color_VALUE"] , $for_filter);
} else {
$arFilter["PROPERTY_color_VALUE"] = $decor[0];
}

if(count($razmersp)>1) {
$arFilter["PROPERTY_spal_VALUE"] = array();
foreach($razmersp as $for_filter) array_push($arFilter["PROPERTY_spal_VALUE"] , $for_filter);
} else {
$arFilter["PROPERTY_spal_VALUE"] = $razmersp[0];
}



if($price_from && $price_till) $arFilter["><PROPERTY_price_new"] = array($price_from ,$price_till);
else {
if($price_from) $arFilter[">PROPERTY_price_new"] = $price_from;
if($price_till) $arFilter["<PROPERTY_price_new"] = $price_till;
}

if($akcii) $arFilter["PROPERTY_aktia_VALUE"] = "Акция";



//echo "<pre>";
//print_r($arFilter);
//echo "</pre>";


$g_counter = 0;
//echo "<pre>";
//print_r($res_items);
//echo "</pre>";

$collections[42]="Le Roi";
$collections[43]="Mix'Line";
$collections[44]="Искусства & Ремесла";
$collections[45]="EKKA";



foreach ($collections as $key => $value) {
 $c_counter = 0;
  
  $html_temp = '';
$res_items = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);


$counter = 0;
while($ob = $res_items->GetNextElement())
   {
    
    $arFields = $ob->GetFields(); 
	//echo "<pre>";
	//print_r($arFields);
	//echo "</pre>";
$db_props = CIBlockElement::GetProperty(intVal(5), $arFields["PROPERTY_TOVAR_VALUE"], "sort", "asc", Array("CODE"=>"COLLECTION"));
if($ar_props = $db_props->Fetch())  { $this_collection = $ar_props['VALUE'];}

 // echo "<H1>".$key."=".$this_collection."</H1>";  
  
if($this_collection == $key){ 

 if(!($price_min))  $price_min = $arFields["PROPERTY_PRICE_NEW_VALUE"];
 if(!($price_max))  $price_max = $arFields["PROPERTY_PRICE_NEW_VALUE"];

 if($price_min>$arFields["PROPERTY_PRICE_NEW_VALUE"]) $price_min = $arFields["PROPERTY_PRICE_NEW_VALUE"];
  if($price_max<$arFields["PROPERTY_PRICE_NEW_VALUE"]) $price_max = $arFields["PROPERTY_PRICE_NEW_VALUE"];


  $counter++;  
    $g_counter++;	
     $c_counter++;
//	echo "<pre>"; print_r($arFields); echo "</pre>";
     if($counter==1)  $html_temp .=  "<tr>";
	$html_temp .= '<td  width="210" height="152" valign="top" class="item_info_td">';
	     $html_temp .= '<div style="position: relative; z-index: 100; padding-top: 12px;">';
	        $html_temp .= ' <div onMouseOver="this.style.display=\'block\';"  onMouseOut="this.style.display=\'none\';" id="inf_'.$arFields["ID"].'"';
			
			if($counter>2)$html_temp .=' class="pop-div-left"'; else $html_temp .=' class="pop-div-right"';
			
			$html_temp .='>						
												<table width="220">
												<tr><td>Механизм трансформации: </td><td>';
												
												$res = CIBlockElement::GetByID($arFields["PROPERTY_MECHANIZM_VALUE"]);
												if($ar_res = $res->GetNext())  
												$html_temp .= $ar_res['NAME'];
												$html_temp .='</td></tr>
												<tr><td>Размер спального места</td><td>'.$arFields["PROPERTY_SPAL_VALUE"].'</td></tr>
                                               	<tr><td>Декор:</td><td>'.$arFields["PROPERTY_COLOR_VALUE"].'</td></tr>
                                                 <tr><td colspan="2">



Выставлен в: <a href="/redesign/where_buy/detail.php?id='.$arFields["PROPERTY_SALON_VALUE"].'">';

$res = CIBlockElement::GetByID($arFields["PROPERTY_SALON_VALUE"]);

if($ar_res = $res->GetNext())  $html_temp .= $ar_res['NAME'];
$html_temp .='</a></td></tr>
                                                  </table>
				      	</div>          
			</div>			
						
	<table cellpadding="0" cellspacing="0" align="center" class="item_table" width="200">
	
    <tr><td colspan="2" class="item_image_td">	<a href="';
	
	$html_temp .=CFile::GetPath($arFields['PREVIEW_PICTURE']);
	$html_temp .='" class="highslide" onclick="return hs.expand(this,{wrapperClassName: \'borderless floating-caption\', dimmingOpacity: 0.75, align: \'center\'})">
	<img onMouseOver="document.getElementById(\'inf_'.$arFields["ID"].'\').style.display=\'block\';" onMouseOut="document.getElementById(\'inf_'.$arFields["ID"].'\').style.display=\'none\';" src="';
	$html_temp .=CFile::GetPath($arFields['PREVIEW_PICTURE']);
	$html_temp .='" border="0" width="200" height="100"></a>
				</td></tr>
		<tr><td class="item_name_td"><a href="/catalog/divan'.$arFields["PROPERTY_TOVAR_VALUE"].'.htm?back=y">';
		$res = CIBlockElement::GetByID($arFields["PROPERTY_TOVAR_VALUE"]);
		if($ar_res = $res->GetNext())  $html_temp .= $ar_res['NAME'];
		$html_temp .='</a>';
		if($arFields["PROPERTY_AKTIA_VALUE"]) $html_temp .=" <font color='#e20a17'>Акция</font>";
		$html_temp .='</td><td';
		if($arFields["PROPERTY_PRICE_NEW_VALUE"]) $html_temp .= ' class="price_new" nowrap';
		$html_temp .='>';
		if($arFields["PROPERTY_PRICE_NEW_VALUE"]) $html_temp .= $arFields["PROPERTY_PRICE_NEW_VALUE"].' р.';
		$html_temp .='</td></tr>
			<tr><td class="all_items_td"><a href="?salon='.$s_val.'&all_models='.$arFields["PROPERTY_TOVAR_VALUE"].'">Все модели '.$ar_res['NAME'].'</a></td><td';
			if($arFields["PROPERTY_PRICE_OLD_VALUE"]) $html_temp .= ' class="price_old" nowrap';
			$html_temp .='>';
			if($arFields["PROPERTY_PRICE_OLD_VALUE"]) $html_temp .= $arFields["PROPERTY_PRICE_OLD_VALUE"].' р.';
			$html_temp .='</td></tr>
	</table>
	
	</td>';
	if($counter==4) { $html_temp .= "</tr>"; $counter=0;}
	
	  }
   }
   if($counter==1) $html_temp .= '<td  width="630" colspan=3></td></tr>'; 
   if($counter==2) $html_temp .= '<td  width="420"colspan=2></td></tr>'; 
   if($counter==3) $html_temp .= '<td width="210"></td></tr>'; 
	
    if($c_counter>0)  $html_temp = "<tr><td colspan=4 class='col_name_td'><h1 class='col_name'>Коллекция ".$value."</h1></td></tr>".$html_temp;  
 
    echo $html_temp;
   }
   if($g_counter==0) echo "<h1 align='center' class='nobody_founded'>Отсутствуют данные с такими параметрами.</h1>";
   

if($price_min && $price_max) echo '<script language="javascript">document.getElementById("price_diapazon").innerHTML = "от '.$price_min.' до '.$price_max.'";</script>';
?>
</table>


</div>
		
		

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>