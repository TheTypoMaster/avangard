<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<SCRIPT language=JavaScript>
<!--

function showInfo(id){ 
	w = window.open('/redesign/where_buy/detail.php?id='+id, 'detail','location=yes,resizable=yes,toolbar=yes,menubar=yes,status=no,scrollbars=yes,width=970,height=700');
	w.focus(); 
}

function showDivans(id){ 
	w = window.open('/wharetobuy/mebel_in_salon.php?id='+id, 'detail','location=yes,resizable=yes,toolbar=yes,menubar=yes,status=no,scrollbars=yes,width=970,height=700');
	w.focus(); 
}
//-->

</SCRIPT>
<table class="spisok_salonov" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr>
		<td width="270"><center>��. �����, �����</center></td><td  width="50"><center>�����</center></td><td  width="190"><center>�����</center></td><td  width="310"  align="center"><center>�����</center></td><td  width="80"><center>� �������</center></td>
		
	</tr>
	</thead>
	<?$bgcol = "#ffffff";?>
        <?foreach($arResult["ITEMS"] as $arElement):?>
	<tr bgcolor="<?=$bgcol?>">
		<td>
			<? if($arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"] =='������')
echo "<img src='/wharetobuy/maps/metro.gif'> ".$arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["DISPLAY_VALUE"]; 
else if($arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"] =='�����-���������')
echo "<img src='/wharetobuy/maps/metroS-Pb.gif'> ".$arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["DISPLAY_VALUE"];
else echo $arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"];
/* echo "<br><span class = 'sale_salon_text'>������ �� 1 �������</span>";
/* echo "<img src='/wharetobuy/maps/no_sale_aug.gif'> ";  */

switch ($arElement["ID"]) {
case 328:
    echo "<img src='/wharetobuy/maps/sale70.png'>";
}

/*
if($arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["DISPLAY_VALUE"] =='�������') echo "<img src='/wharetobuy/maps/kor_skidka_.gif'> " 
*/
?>
		</td>
		<td  align="center">
			<?if($arElement["DISPLAY_PROPERTIES"]["SALON_ACTION_TEXT"]["DISPLAY_VALUE"]) echo '<center><img width="24" height="24" border="0" src="/images/akciya.gif" alt="'.$arElement["DISPLAY_PROPERTIES"]["SALON_ACTION_TEXT"]["DISPLAY_VALUE"].'"></center>';?>
			<?
//			switch ($arElement["ID"]) {
//			
//			echo '<center><img width="32" src="/wharetobuy/maps/BF.png" alt="��������� BEFRESH"></center>';
//			}
			switch ($arElement["ID"]) {

			case 326:
			case 329:
			case 4311:
			case 3616:
			echo '<center><img width="30" src="/wharetobuy/maps/31.png" alt="������ 30%"></center>';
			}
			switch ($arElement["ID"]) {
			case 327:
			echo '<center><img width="30" src="/wharetobuy/maps/50.png" alt="������ 30%"></center>';
			}
?>
			<?if($arElement["DISPLAY_PROPERTIES"]["SALON_ACTION_TEXT_2"]["DISPLAY_VALUE"]) echo '<center><img width="47" height="20" border="0" src="/images/podarki.gif" alt="'.$arElement["DISPLAY_PROPERTIES"]["SALON_ACTION_TEXT_2"]["DISPLAY_VALUE"].'"></center>';?>
		</td>
		<td>
			<a href="/redesign/where_buy/detail.php?id=<?=$arElement["ID"]?>" onClick="showInfo(<?=$arElement["ID"]?>); return false;"    target="_new"><?=$arElement["NAME"]?></a>
		</td>
		<td><?if($arElement["DISPLAY_PROPERTIES"]["SALON_ADRESS"]["DISPLAY_VALUE"]) echo $arElement["DISPLAY_PROPERTIES"]["SALON_ADRESS"]["DISPLAY_VALUE"]; ?>
<?
//switch ($arElement["ID"]) {
//case 326:
//echo '<br />', $arElement["DISPLAY_PROPERTIES"]["SALON_PHONE"]["DISPLAY_VALUE"];
//}
?>
		</td>
		<td align="center">
	<center>						
<?	

$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=>IntVal(15), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_salon"=>$arElement["ID"]);
$res_items = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>500), $arSelect);
$counter = 0;
//$res_items->NavStart(500);
$ob = $res_items->SelectedRowsCount();
if($ob>0){?><a style="text-decoration: none; font-size: 14px; " href="/wharetobuy/mebel_in_salon.php?id=<?=$arElement["ID"]?>" onClick="showDivans(<?=$arElement["ID"]?>); return false;"    target="_new"><img style="padding-top: 3px;" src="/images/camera.gif" border="0" alt="������ � �������"> (<?=$ob?>)
</a>
<?}?></center>
		</td>
		
	</tr>
        <?if($bgcol == "#ffffff") $bgcol = "#ededed"; else $bgcol = "#ffffff";?>
	<?endforeach;?>
</table>