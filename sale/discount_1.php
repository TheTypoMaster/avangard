<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Дисконт-центр мебельной фабрики Авангард в Москве. Купить недорогую мягкую кожаную мебель, диваны-кровати от производителя.");
?>
<script language="javascript">



window.onload = function(){
var div_pr = document.getElementById("prokrutka1");
if(is_ie) {
var smeshenie = window.document.body.offsetHeight - 100;
div_pr.style.height=smeshenie+"px";
}
else
{
var smeshenie = window.innerHeight - 120;
 div_pr.style.height=smeshenie+"px";
}
}


window.onresize = function(){
var div_pr = document.getElementById("prokrutka1");
if(is_ie) {
var smeshenie = window.document.body.offsetHeight - 100;
div_pr.style.height=smeshenie+"px";
}
else
{
var smeshenie = window.innerHeight - 100;
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

// работаем со всеми checkbox на указанной форме
function sdf_checkbox_status(_form_name,_status)
// form_name - название формы
// status - присвоить статус; 0 - все отменить. 1 - всем включить; 2 - поменять местами
{ // делаем короткий объект для обращений к форме
 var f=document.getElementById(_form_name);
 for (i=1;i<=f.length;i++)// пройтись по всем элементам на HTML форме
 { if (f.elements[i-1].type=='checkbox')// если тип элемента checkbox, то
  { 
   switch(_status)
   { case(0): { f.elements[i-1].checked=false; break; }// сбрасываем значения
     case(1): { f.elements[i-1].checked=true; break; }// устанавливаем значения
     case(2): { f.elements[i-1].checked=!f.elements[i-1].checked; break; }// меняем местами
   }
  }
 }
 }
</script>
<style type="text/css">
#catalog_selection div.head2 {
text-transform: uppercase;
font-size: 116.6%;
color: black;
margin-top: 10px;
font-family: "PT Serif",Arial,sans-serif;
margin-bottom: 15px;
}
#catalog_selection .head a.check_link {
color: #393939;
font-size: 12px;
margin-left: 10px;
}
#catalog_selection .head {
font-family: "PT Serif",Arial,sans-serif;
font-size: 116.6%;
margin-bottom: 5px;
color: #A00;
}
#catalog_selection .check_items {
width: 100%;
border-collapse: collapse;
margin-bottom: 20px;
font-family: "PT Sans",Arial,sans-serif;
color: #393939;
}
#catalog_selection .inp_items {
font-family: "PT Sans",Arial,sans-serif;
margin-bottom: 20px;
}
#catalog_selection .inp_items input {
width: 48px;
margin: 0 10px 0 3px;
}
</style>

<?php
$divan_ch = array('','','','','','','');
$divan_typ = array(199,199,198,200,201,203,202);
$divan_sel = array(0);
for ($i = 1; $i <= 6; $i++) {
  if (isset($_REQUEST[ch][$i])) {
    $divan_ch[$i] = 'checked';
    $divan_sel[] = $divan_typ[$i];
  }  
}
/*
if($_REQUEST[price_from] && $_REQUEST[price_till]) {
  $pr_min = $_REQUEST[price_from];
  $pr_max = $_REQUEST[price_till];
  }
  else {
    $pr_min = '';
    $pr_max = '';
  }
*/
?>


<div id="catalog_selection" style="position: absolute; width: 280px; top: 100px; left: 0px; z-index:2; height: 250px; padding: 0 0px 0 30px; border-bottom: solid 1px #f9f9f9;">
							<div class="head2">Подбор мебели</div>
<form method="get" id="selection_form" name="selection_form">
<input type="hidden" name="salon" value="all">
<input type="hidden" name="id" value="all">
<div class="head">Тип мебели <a href="#" class="check_link" onClick="sdf_checkbox_status('selection_form',1)">выбрать все</a></div>
<div class="check_items" id="check_catalogs_cont">
   <table>
   <tbody><tr>
   <td style="padding-right: 25px">
         <div class="check_row"><input id="selfrm_c_1" name="ch[1]" type="checkbox"<?php echo $divan_ch[1];?> ><label for="selfrm_c_1">Диваны угловые</label></div>
               <div class="check_row"><input id="selfrm_c_2" name="ch[2]" type="checkbox" <?php echo $divan_ch[2];?> ><label for="selfrm_c_2">Диваны-кровати</label></div>
               <div class="check_row"><input id="selfrm_c_3" name="ch[3]" type="checkbox" <?php echo $divan_ch[3];?> ><label for="selfrm_c_8">Кровати</label></div>
      </td><td>
               <div class="check_row"><input id="selfrm_c_4" name="ch[4]" type="checkbox" <?php echo $divan_ch[4];?>  ><label for="selfrm_c_5">Кресла</label></div>
               <div class="check_row"><input id="selfrm_c_5" name="ch[5]" type="checkbox" <?php echo $divan_ch[5];?> ><label for="selfrm_c_6">Пуфы</label></div>
               <div class="check_row"><input id="selfrm_c_6" name="ch[6]" type="checkbox" <?php echo $divan_ch[6];?> ><label for="selfrm_c_7">Столы</label></div>
            </td>
   </tr>
   </tbody></table>
</div>
<div class="head">Ценовой диапазон (руб.)</div>
<div class="inp_items">
   от <input name="price_from" type="text" value="" style="margin-right: 20px"> до <input name="price_till" type="text" value="">
</div>

<div class="select_btn_cont"><div class="select_btn"><input type="submit" name="submit" value="Показать" class="button_show"></div></div>
</form>

<script type="text/javascript">
</script>
</div>

<div height="50" class="filter_td1">
<h3 align="center" style="color: #000000; padding: 2px; margin: 2px; font-size: 14px;">&nbsp;</h3>

<div style="width: 100%; height: 40px; border-top: 3px solid rgb(225, 225, 225); "> 	 
  <table width="100%" height="40" cellspacing="0" cellpadding="0" class="zakladki" style="text-align: justify; "> 		 
    <tbody> 			 
      <tr>
	  <td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 20%; "><a href="/" ><font style="font-size: 12px; ">На главную</font></a></td>
	  <td style="vertical-align: middle; text-align: center; width: 30%; "><b><font style="font-size: 14px; ">ДИСКОНТ</font></b>
	  <br><font style="font-size: 12px; ">во всех салонах Москвы и области</font></td>
	  <td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 30%; "><a href="/redesign/where_buy/detail.php?id=5360" ><b><font style="font-size: 14px; ">РАСПРОДАЖА ОБРАЗЦОВ</font></b></a>
	  <br><font style="font-size: 12px; ">со склада фабрики</font></td>
	  <td bgcolor="#e1e1e1" style="vertical-align: middle; text-align: center; width: 20%; "><a href="/redesign/where_buy/" ><font style="font-size: 12px; ">На страницу «Где купить»</font></a></td>
	  </tr>
    </tbody>
  </table>
 </div>

</div>
	<div align="right" height="99%" width="100%" class="prokrutka1"  id="prokrutka1" align="center">
	<div align="center" class="filer_info_about">

<table width="90%" align="center">
<?

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_salon", "PROPERTY_tovar","PROPERTY_price_old", "PROPERTY_price_new", "PROPERTY_mechanizm", "PROPERTY_aktia", "PROPERTY_sold", "PROPERTY_skidka");

$arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", ">PROPERTY_skidka" => 15);

if($price_from && $price_till) $arFilter["><PROPERTY_price_new"] = array($price_from ,$price_till);
else {
if($price_from) $arFilter[">PROPERTY_price_new"] = $price_from;
if($price_till) $arFilter["<PROPERTY_price_new"] = $price_till;
}

if($akcii) $arFilter["PROPERTY_aktia_VALUE"] = "Акция";

$arFilter["PROPERTY_tovar_type"] = $divan_sel;


//echo "<pre>";
//print_r($arFilter);
//echo "</pre>";


$g_counter = 0;
//echo "<pre>";
//print_r($res_items);
//echo "</pre>";


$collections[43]="Mix'Line";
$collections[4228]="BEFRESH";
$collections[42]="Le Roi";
$collections[45]="EKKA";
$collections[2761]="кроватей";
$collections[44]="Искусства & Ремесла";


//echo "<H3><span style='color:#FF0000;'>Копирование, публикация и использование материалов сайта ЗАПРЕЩЕНЫ!</span></H3><br>";

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
	//print_r($_REQUEST);
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
	$html_temp .= '<td  width="210" height="200" valign="top" class="item_info_td">
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
		<tr><td style="padding-top: 2px; padding-bottom: 2px;  background: #e9e9e9;" nowrap>';
			if($arFields["PROPERTY_RAZMERI_VALUE"]) $html_temp .='&nbsp;'.$arFields["PROPERTY_RAZMERI_VALUE"];
			$html_temp .='</td><td style="padding-top: 2px; padding-bottom: 2px; background: #e9e9e9;" nowrap align="right">';
			if($arFields["PROPERTY_SPAL_VALUE"]) $html_temp .= 'сп.м.&nbsp;'.$arFields["PROPERTY_SPAL_VALUE"].'&nbsp;';
			$html_temp .='</td></tr>
		<tr><td class="item_name_td"><a href="/catalog/divan'.$arFields["PROPERTY_TOVAR_VALUE"].'.htm">';
		$res = CIBlockElement::GetByID($arFields["PROPERTY_TOVAR_VALUE"]);
		if($ar_res = $res->GetNext())  $html_temp .= $ar_res['NAME'];
		$html_temp .='</a>';
		if($arFields["PROPERTY_AKTIA_VALUE"]) $html_temp .=" <font color='#e20a17'>Акция</font>";
		$html_temp .='</td><td';
		if($arFields["PROPERTY_PRICE_NEW_VALUE"]) $html_temp .= ' class="price_new" nowrap';
		$html_temp .='>';
		if($arFields["PROPERTY_PRICE_NEW_VALUE"]) $html_temp .= $arFields["PROPERTY_PRICE_NEW_VALUE"].' р.';
		$html_temp .='</td></tr>
			<tr><td class="all_items_td">';

			$res1 = CIBlockElement::GetByID($arFields["PROPERTY_SALON_VALUE"]);
			if($ar_res1 = $res1->GetNext())  $saloname = $ar_res1['NAME'];

			$html_temp .='<a href="/redesign/where_buy/detail.php?id='.$arFields["PROPERTY_SALON_VALUE"].'">'.$saloname.'</a>';
			$html_temp .='</td><td';
			if($arFields["PROPERTY_PRICE_OLD_VALUE"]) $html_temp .= ' class="price_old" nowrap';
			$html_temp .='>';
			if($arFields["PROPERTY_PRICE_OLD_VALUE"]) $html_temp .= $arFields["PROPERTY_PRICE_OLD_VALUE"].' р.';
			$html_temp .='</td></tr>
	</table>
	
	</td>';
	if($counter==3) { $html_temp .= "</tr>"; $counter=0;}
	
	  }
   }
   if($counter==1) $html_temp .= '<td  width="630" colspan=3></td></tr>'; 
   if($counter==2) $html_temp .= '<td  width="420"colspan=2></td></tr>'; 
   if($counter==3) $html_temp .= '<td width="210"></td></tr>'; 

    //if($c_counter>0)  $html_temp = "<tr><td colspan=4 class='col_name_td'><h1 class='col_name'>Коллекция ".$value."</h1></td></tr>".$html_temp;
 
    echo $html_temp;
   }
   if($g_counter==0) echo "<h1 align='center' class='nobody_founded'>Отсутствуют данные с такими параметрами.</h1>";
   

if($price_min && $price_max) echo '<script language="javascript">document.getElementById("price_diapazon_from").innerHTML = "от ('.$price_min.')"; document.getElementById("price_diapazon_till").innerHTML = "до ('.$price_max.')";</script>';
?>
</table>


</div>
		
	<div align="left"><p >Выбор мягкой мебельной продукции поистине огромен: на рынке представлены сотни моделей, разнящихся по размеру, геометричности форм, механизмам трансформации, такими как: еврокнижка, аккордеон, дельфин и многие другие.</p>
<p>Очень практично и удобно, что купить можно не только элитные дорогие диваны, но и без проблем приобрести диваны подешевле. Тем более, что в настоящее время есть возможность выбрать понравившуюся модель через интернет, здесь представлена вся мягкая мебельная продукция, где вкупе можно купить недорогой диван кровать либо кресла по сниженным ценам.</p>
<p>А как насчет мебели из натуральной кожи? Вы сможете решиться на такое? Или в вашем понимании кожаный диван хоть и заслуживает уважения, но ему место только в престижных салонах загородных особняков? И максимум, что можно себе позволить из кожи в доме - это одиноко стоящее кресло в углу гостиной?</p>
<p>Отбросьте все сомнения - отечественная фабрика мебели Авангард (г. Москва) предлагает недорогие диваны кровати по разумным ценам.</p>
<p>Стоит отметить, что стоимость мягкой мебели зависит от каркаса, размера, степени сложности механизма трансформации, внутреннего наполнителя, а также вида отделочной ткани. Также можно приобрести совершенно недорого диваны по системе дисконт, скидка здесь настолько значима, что вы смело сможете выбрать модель из прошлой модной коллекции.</p>
<p>Несомненно, диваны кровати недорого на металлическом каркасе со съёмными чехлами, позиционируемые как эконом-мебель, будут стоить дешевле, чем мягкий диван кровать с блоками из пружин и чехлом из новой модной коллекции, но это всего лишь дань моде. Модели же, обитые материалами флок и микрофибра, на деревянном или металлическом каркасе с простыми механизмами, будут стоить на порядок меньше моделей, у которых спальное место отделено от стены специальной спинкой, а диван имеет более сложный механизм.</p>
<p>Нужна современная мягкая мебель по сниженным ценам? Приобретайте недорогие модные диваны в дисконт-центре мебельной фабрике Авангард - и успех вашему дому обеспечен!</p></div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>