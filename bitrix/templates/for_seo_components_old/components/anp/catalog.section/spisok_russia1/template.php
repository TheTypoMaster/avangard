<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? $letters = array('g'=>'Г', 'a'=>'А', 'ae'=>'Э', 'b'=>'Б', 'ch'=>'Ч', 'e'=>'Е', 'g'=>'Г', 'h'=>'Х', 'i'=>'И', 'k'=>'К', 'l'=>'Л', 'm'=>'М', 'n'=>'Н', 'o'=>'О', 'p'=>'П', 'r'=>'Р', 's'=>'С', 't'=>'Т', 'u'=>'У', 'v'=>'В', 'ya'=>'Я');?>
<div style="width: 954px;" align="center"><table class="spisok_salonov_russia" cellspacing="0" cellpadding="0" border="0" align="center" width="500">

	<?$bgcol = "#ffffff";?>
        <?foreach($arResult["ITEMS"] as $arElement){
			$output[substr($arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"],0,1)][ $arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"] ][$arElement["ID"]] = $arElement["NAME"];
        }
			ksort($output);
			//echo "<pre>"; print_r($output); echo "</pre>";
			?>
</table>


<?$counter = 0;
$out='';?>
<table cellpadding="0" cellspacing="0" border="0" width="700">
	
	<?foreach($output as $cell=>$key) {
        $counter++;
        if($counter == 1) {
            $out .= '<tr>';  /* новая  линия таблицы */
        }
        $out .= '<td valign="top" width="33%">';  /* столбец буквы */
        $out .= '<table><tr>';  /* таблица буквы */
        $out .= '<td width="48" align="right">';  /* столбец буквы */
        $lettername = array_search($cell, $letters);
        $out .= '<img src="/images/letters/'.$lettername.'.gif"></td>';
        $out .= '<td width="8"></td>';  /* отступ от буквы */
        $out .= '<td><div class="city_name_tab">';  /* города */
        while(list($k, $v) = each($key)) {
            if (is_array($v)) {
                    $out .= "<strong><a href=\"/redesign/where_buy/region.php?t=".translit($k)."\">".$k."</a></strong>"; /* город */
                    $out .= '<ul class="city_sal_list" type="square">';
                    while(list($k1, $v1) = each($v)) {  /* название салона */
                         $out .= '<li><a class="salon_name_hr" target="_blank" href="';
                         $out .= '/redesign/where_buy/detail.php?id='.$k1;
                         $out .= '">'.$v1.'</a></li>'; 
                    }
                    $out .= '</ul>';
            }            
        } /* while города */
        $out .= '</div></td>';  /* конец городов */
        $out .= '</tr></table>';  /* конец таблицы буквы */
        $out .= '</td>';  /* конец столбца буквы */
        /* $cell++;   ?????? */
        if($counter == 3) {
            $out .= '</tr>';  /* конец строки таблицы */
            $counter = 0;  /* обнуление счетчика столбцов */
        }
    }  /* foreach $output  */
    echo $out;  /* вывод таблицы */
    ?>


</table>
</div>


<?php
function translit ($str) {				
	$from = "абвгдезиклмнопрстуфцы"; 
    $in   = "abvgdeziklmnoprstufcy"; 
    $fromin = array(  
        "й" => "jj", "ё" => "e", "ж" => "zh", "х" => "kh", "ч" => "ch",  
        "ш" => "sh", "щ" => "shh", "э" => "je", "ю" => "yu", "я" => "ya", 
        "ъ" => "tv", "ь" => "mgz",);
         							
     //замена strtolower
     $str = strtolower($str);
     $str = preg_replace( "/[^а-яеёА-ЯЕЁa-zA-Z0-9- ]+/", "", $str );
     $str = str_replace(array("А","Б","В","Г","Д","Е","Ё","Ж","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я","\"",".",",","!","?","(",")","—","«","»","&quot;","&","“","*",":"), array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","","","","","","","","","","","","","","",""), $str);
     $str = preg_replace( "/\s+/ms", "_", $str );
     $str = strtr($str, $from, $in);
     $str = strtr($str, $fromin);
     $str = substr($str, 0, 50);
     if (substr($str, 0, 1) == "-") {
        $str = substr($str, 1);
     }
     if (substr($str, -1) == "-") {
        $str = substr($str, 0, bcsub(strlen($str), 1));
     }
     $str = str_replace("--", "-", $str);
     $str = str_replace("--", "-", $str);
								
     return $str;								
}
?>