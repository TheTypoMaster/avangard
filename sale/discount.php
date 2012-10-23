<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Дисконт");
?>
<script language="javascript">



window.onload = function(){
var div_pr = document.getElementById("prokrutka");
if(is_ie) {
var smeshenie = window.document.body.offsetHeight - 75;
div_pr.style.height=smeshenie+"px";
}
else
{
var smeshenie = window.innerHeight - 75;
 div_pr.style.height=smeshenie+"px";
}
}


window.onresize = function(){
var div_pr = document.getElementById("prokrutka");
if(is_ie) {
var smeshenie = window.document.body.offsetHeight - 75;
div_pr.style.height=smeshenie+"px";
}
else
{
var smeshenie = window.innerHeight - 75;
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



<?if($salon) $s_val = $salon; else $s_val=$id; ?>

<div height="50" class="filter_td">
<h3 align="center" style="color: #000000; padding: 2px; margin: 2px; font-size: 14px;">
	<a style="font-size: 14px;" href="/">На главную</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<?if($id=='all' || $all_models){
		echo "ДИСКОНТ.  Во всех салонах Москвы и Московской области";
	}else{
		$res = CIBlockElement::GetByID($s_val);
		if($ar_res = $res->GetNext())  
			echo '<a style=" font-size: 14px; color: #000000;" href="/redesign/where_buy/detail.php?id='.$s_val.'">'.$ar_res['NAME'].'</a>';
		}
	?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a style="font-size: 14px;" href="/redesign/where_buy/">На страницу «Где купить»</a>
</h3>

<table style="border-top: solid #e20a17 2px;" height="30" class="filter_table" cellpadding="0" cellspacing="0">
<tr>
<form name="filter_form" method="get" id="filter_form">

<input type="hidden" name="salon" value="<?=$s_val?>">
<td style="padding-left: 6px;">Мебель в наличии</td>
<td>

<?if($all_models) { ?>
<td><a href="/catalog/divan<?=$all_models?>.htm"><?$res = CIBlockElement::GetByID(intVal($all_models));if($ar_res = $res->GetNext())  echo $ar_res['NAME'];?></a></td>
<?if($from_catalog!="y") {?><td><input type="radio" disabled name="id" value="<?=$salon?>"></td><td style="color: #cccccc;">В этом салоне</td><?}?>
<td><input type="radio" name="id" checked value="all"></td><td>Во всех салонах</td>
<?
} else {?>
<td><input type="radio" name="id" <?if($id!='all') {?>checked<?}?> value="<?if($id=='all') echo $s_val; else echo $id;?>"></td><td>В этом салоне</td>
<td><input type="radio" name="id"  <?if($id=='all') {?>checked<?}?> value="all"></td><td>Во всех салонах</td>
<? } ?>


<td width="15%" nowrap style="color: #e20a17;" align="right">Цена(в рублях):</td><td width="12">&nbsp;</td>
<td align="right" id="price_diapazon_from">от</td><td width="4">&nbsp;</td><td align="left"><input type="text" size="12" name="price_from"></td><td width="12">&nbsp;</td><td align="right" id="price_diapazon_till">до</td><td width="4">&nbsp;</td><td align="left"><input type="text" size="12" name="price_till"></td>


<td width="100" align="center" style="width: 100px; padding: 3px; background: #e20a17;"><input type="submit" name="submit" value="Показать" class="button_show"></td>
</form>
</tr>
</table>


</div>
	<div align="right" height="100%" width="100%" class="prokrutka"  id="prokrutka" align="center">
	<div align="center" class="filer_info_about">

<table width="90%" align="center">
<?

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_salon", "PROPERTY_razmeri", "PROPERTY_tovar","PROPERTY_color", "PROPERTY_price_old", "PROPERTY_price_new", "PROPERTY_mechanizm",  "PROPERTY_spal", "PROPERTY_aktia", "PROPERTY_sold", "PROPERTY_skidka");



if($all_models) $arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_tovar"=>IntVal($all_models), ">PROPERTY_skidka" => 14);
else 
{

if($id=="all") 
{
$arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", ">PROPERTY_skidka" => 14);
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

$collections[44]="Искусства & Ремесла";
$collections[4228]="BEFRESH";
$collections[43]="Mix'Line";
$collections[42]="Le Roi";
$collections[45]="EKKA";
$collections[2761]="кроватей";

echo "<H3>При заказе цена может быть более выгодной для Вас.<br>Уточняйте стоимость выбранной Вами модели у продавцов салонов,<br>с учетом проводимых акций и действующих скидок.</H3><br>";
if ($id=="566") echo "<H3>Справки по телефону: 8(498) 720-50-44 доб.155.</H3><br>";
echo "<H3><span style='color:#FF0000;'>Копирование, публикация и использование материалов сайта ЗАПРЕЩЕНЫ!</span></H3><br>";

foreach ($collections as $key => $value) {
 $c_counter = 0;
  
  $html_temp = '';
$res_items = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>500), $arSelect);


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
	$html_temp .= '<td  width="210" height="150" valign="top" class="item_info_td">
	<table cellpadding="0" cellspacing="0" align="center" class="item_table" width="200">
	
    <tr><td colspan="2" class="item_image_td"  style="padding: 0px; margin: 0px;" height="100">';
		
	if(($arFields["PROPERTY_PRICE_NEW_VALUE"] && $arFields["PROPERTY_PRICE_OLD_VALUE"]) || $arFields["PROPERTY_SKIDKA_VALUE"])  {
       if($arFields["PROPERTY_PRICE_NEW_VALUE"] && $arFields["PROPERTY_PRICE_OLD_VALUE"]) {
	     $skidka_value = ceil((($arFields["PROPERTY_PRICE_OLD_VALUE"] - $arFields["PROPERTY_PRICE_NEW_VALUE"])/$arFields["PROPERTY_PRICE_OLD_VALUE"])*100);
		}
       if($arFields["PROPERTY_SKIDKA_VALUE"]) $skidka_value = $arFields["PROPERTY_SKIDKA_VALUE"]; /* если есть скидка, то берется введенное значение */
       $html_temp .='<div style="margin: 0px; padding: 0px; position: absolute; z-index: 90;"><div style="text-align:center; background: url(/images/skidka.gif) no-repeat center center; position: relative; left: 170px; top: -12px; color: #ffffff; height: 36px; width: 36px; " height=36 width=36 ><img src="/images/gif.gif" height="10" width="20"><br>-'.$skidka_value.'%</div></div>';
    }	
			
	
	
	if($arFields["PROPERTY_SOLD_VALUE"]) $html_temp .='<img style="position: absolute; z-index: 90; filter:alpha(opacity=50); /* IE 5.5+*/
-moz-opacity: 0.5; /* Mozilla 1.6 и ниже */
-khtml-opacity: 0.5; /* Konqueror 3.1, Safari 1.1 */
opacity: 0.5; /* CSS3 - Mozilla 1.7b +, Firefox 0.9 +, Safari 1.2+, Opera 9 */ " alt="Товар продан" src="/images/sold.gif">';
	$html_temp .='<a href="';
	$html_temp .=CFile::GetPath($arFields['PREVIEW_PICTURE']);
	$html_temp .='" class="highslide" onclick="return hs.expand(this,{wrapperClassName: \'borderless floating-caption\', dimmingOpacity: 0.75, align: \'center\'})">
	<img style="padding: 0px; margin: 0px;" src="';
	$html_temp .=CFile::GetPath($arFields['PREVIEW_PICTURE']);
	$html_temp .='" border="0" width="200" height="100"></a><div class="highslide-caption">'.$arFields['PREVIEW_TEXT'].'</div></td></tr>
		<tr><td style="padding-top: 2px; padding-bottom: 2px;  background: #e9e9e9;" nowrap>&nbsp;'.$arFields["PROPERTY_RAZMERI_VALUE"].'</td><td style="padding-top: 2px; padding-bottom: 2px; background: #e9e9e9;" nowrap align="right">сп.м.&nbsp;'.$arFields["PROPERTY_SPAL_VALUE"].'&nbsp;</td></tr>
		<tr><td class="item_name_td"><a href="/catalog/divan'.$arFields["PROPERTY_TOVAR_VALUE"].'.htm">';
		$res = CIBlockElement::GetByID($arFields["PROPERTY_TOVAR_VALUE"]);
		if($ar_res = $res->GetNext())  $html_temp .= $ar_res['NAME'];
		$html_temp .='</a>';
		if($arFields["PROPERTY_AKTIA_VALUE"]) $html_temp .=" <font color='#e20a17'>Акция</font>";
		$html_temp .='</td><td';
		if($arFields["PROPERTY_PRICE_NEW_VALUE"]) $html_temp .= ' class="price_new" nowrap';
		$html_temp .='>';
		if($arFields["PROPERTY_PRICE_NEW_VALUE"] && $id=="all") $html_temp .= $arFields["PROPERTY_PRICE_NEW_VALUE"].' р.';
		$html_temp .='</td></tr>
			<tr><td class="all_items_td">';
			// if($all_models) $html_temp .='<a href="/redesign/where_buy/detail.php?id='.$arFields["PROPERTY_SALON_VALUE"].'">Показать салон</a>';
			// else $html_temp .='<a href="?salon='.$s_val.'&all_models='.$arFields["PROPERTY_TOVAR_VALUE"].'">Все модели '.$ar_res['NAME'].'</a>';
			$html_temp .='<a href="/redesign/where_buy/detail.php?id='.$arFields["PROPERTY_SALON_VALUE"].'">Показать салон</a>';
			$html_temp .='</td><td';
			if($arFields["PROPERTY_PRICE_OLD_VALUE"]) $html_temp .= ' class="price_old" nowrap';
			$html_temp .='>';
			if($arFields["PROPERTY_PRICE_OLD_VALUE"] && $id=="all") $html_temp .= $arFields["PROPERTY_PRICE_OLD_VALUE"].' р.';
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
   

if($price_min && $price_max) echo '<script language="javascript">document.getElementById("price_diapazon_from").innerHTML = "от ('.$price_min.')"; document.getElementById("price_diapazon_till").innerHTML = "до ('.$price_max.')";</script>';
?>
</table>


</div>
		
		

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>