<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? $letters = array('g'=>'�', 'a'=>'�', 'ae'=>'�', 'b'=>'�', 'ch'=>'�', 'e'=>'�', 'g'=>'�', 'h'=>'�', 'i'=>'�', 'k'=>'�', 'l'=>'�', 'm'=>'�', 'n'=>'�', 'o'=>'�', 'p'=>'�', 'r'=>'�', 's'=>'�', 't'=>'�', 'u'=>'�', 'v'=>'�', 'ya'=>'�');?>
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
            $out .= '<tr>';  /* �����  ����� ������� */
        }
        $out .= '<td valign="top" width="33%">';  /* ������� ����� */
        $out .= '<table><tr>';  /* ������� ����� */
        $out .= '<td width="48" align="right">';  /* ������� ����� */
        $lettername = array_search($cell, $letters);
        $out .= '<img src="/images/letters/'.$lettername.'.gif"></td>';
        $out .= '<td width="8"></td>';  /* ������ �� ����� */
        $out .= '<td><div class="city_name_tab">';  /* ������ */
        while(list($k, $v) = each($key)) {
            if (is_array($v)) {
                    $out .= "<strong><a href=\"/redesign/where_buy/region.php?t=".translit($k)."\">".$k."</a></strong>"; /* ����� */
                    $out .= '<ul class="city_sal_list" type="square">';
                    while(list($k1, $v1) = each($v)) {  /* �������� ������ */
                         $out .= '<li><a class="salon_name_hr" target="_blank" href="';
                         $out .= '/redesign/where_buy/detail.php?id='.$k1;
                         $out .= '">'.$v1.'</a></li>'; 
                    }
                    $out .= '</ul>';
            }            
        } /* while ������ */
        $out .= '</div></td>';  /* ����� ������� */
        $out .= '</tr></table>';  /* ����� ������� ����� */
        $out .= '</td>';  /* ����� ������� ����� */
        /* $cell++;   ?????? */
        if($counter == 3) {
            $out .= '</tr>';  /* ����� ������ ������� */
            $counter = 0;  /* ��������� �������� �������� */
        }
    }  /* foreach $output  */
    echo $out;  /* ����� ������� */
    ?>


</table>
</div>


<?php
function translit ($str) {				
	$from = "���������������������"; 
    $in   = "abvgdeziklmnoprstufcy"; 
    $fromin = array(  
        "�" => "jj", "�" => "e", "�" => "zh", "�" => "kh", "�" => "ch",  
        "�" => "sh", "�" => "shh", "�" => "je", "�" => "yu", "�" => "ya", 
        "�" => "tv", "�" => "mgz",);
         							
     //������ strtolower
     $str = strtolower($str);
     $str = preg_replace( "/[^�-���-�Ũa-zA-Z0-9- ]+/", "", $str );
     $str = str_replace(array("�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","\"",".",",","!","?","(",")","�","�","�","&quot;","&","�","*",":"), array("�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","","","","","","","","","","","","","","",""), $str);
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