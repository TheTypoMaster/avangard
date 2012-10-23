<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<script>
		window.name='list';

		function map()
		{
            id=window.open('/wharetobuy/russia/map.php','map','directories=no,width=1010,height=720,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
		    id.focus();
		}
</script>

<?
//echo ("<pre>"); print_r($_REQUEST); echo("</pre>");

$city = "";
$tab = 0;
?>

<table cellpadding="0" cellspacing="0" border="0">

<?if((!$_REQUEST["CITY_ID"]) && (!$_REQUEST["SALON_ID"])){?>
	<tr>
		<td colspan="2">
  		<div style="margin: 10px 0px 10px 0px;">
  		<a href="#" onClick="window.open('/wharetobuy/russia/map.php','map','directories=no,width=1010,height=720,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no'); return(false);">
		<img src="/wharetobuy/maps/s_russia.gif" width="535" height="185"  title="Салоны в России" alt="Салоны в России" border="0">
		</a>
 		<a href="#" onClick="window.open('/wharetobuy/russia/map.php','map','directories=no,width=1010,height=720,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no'); return(false);">
		Посмотреть на карте &raquo;
		</a>

		</div>
		</td>
	</tr>
<?}?>


	<tr>
		<td valign="top" width="50%">

		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>


<?

	if($arElement["PROPERTIES"]["SALON_CITY"]["VALUE"] != $city){

	$city = $arElement["PROPERTIES"]["SALON_CITY"]["VALUE"];
	$city_link = ruslat($city);

	if(($cell>=(count($arResult["ITEMS"])/2))&&($tab == "0")){
		$tab++;
		?>
		</td><td valign="top" width="50%">
		<?
	}

		if(!$_REQUEST["SALON_ID"]){
		echo("<div class=\"cityname\"><a href=\"/wharetobuy/russia/".$city_link."/\" alt=\"Посмотреть все салоны в этом городе\" title=\"Посмотреть все салоны в этом городе\">".$city."&nbsp;&raquo;</a></div>");
		}

	if($_REQUEST["CITY_ID"]) echo "<br /><br />";

	}
?>

			<table cellpadding="0" cellspacing="2" border="0">
				<tr>
					<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
						<td valign="top">
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arElement["NAME"]?>" /></a><br />
						</td>
					<?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
						<td valign="top">
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arElement["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["DETAIL_PICTURE"]["ALT"]?>" title="<?=$arElement["NAME"]?>" /></a><br />
						</td>
					<?endif?>
					<td valign="top">

					<p class="content">



					<?if((!$_REQUEST["SALON_ID"]) && (!$_REQUEST["CITY_ID"])){?>

					<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><b><?=$arElement["NAME"]?></b></a><br />

                    	<?echo $arElement["DETAIL_TEXT"];

                    	if($arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["VALUE"]!=""){
						echo "<br /><img src=\"/wharetobuy/maps/metro.gif\" style=\"border:0px; margin:0px 5px 0px 0px; padding:0px;\">".$arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["VALUE"];
						}

                    }?>

					<?if($_REQUEST["CITY_ID"]){// список салонов в городе?>

<div style="width:300px; float:left; font-size:11px; color:#666666">
<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" style="font-weight:bold; font-size:14px; color:#666666"><?=$arElement["NAME"]?></a>

<?/*if($arElement["DISPLAY_PROPERTIES"]["SALON_TYPE"]["VALUE"]!=""){
echo $arElement["DISPLAY_PROPERTIES"]["SALON_TYPE"]["VALUE"]."<br />";
}*/?>

<div style="width: 220px; min-height:10px; margin: 0px 0px 0px 0px; border-left:0px solid grey; padding:0px; font-size:11px;">

<?if($arElement["DISPLAY_PROPERTIES"]["SALON_PHONE"]["VALUE"]!=""){
echo "<b>".$arElement["DISPLAY_PROPERTIES"]["SALON_PHONE"]["VALUE"]."</b><br />";
}?>
<?echo $arElement["DETAIL_TEXT"];?><br /><br />

</div>
</div>
					<?}?>



					<?if($_REQUEST["SALON_ID"]){?>

<div style="width:160px; float:right; font-size:11px;">
<br /><br />

<?if($arElement["DISPLAY_PROPERTIES"]["SALON_TIME"]["VALUE"]!=""){?>
<?echo(htmlspecialcharsBack($arElement["DISPLAY_PROPERTIES"]["SALON_TIME"]["VALUE"]));?>
<?}?>
</div>

<div style="width:300px; float:left; font-size:11px; color:#666666">
<br /><br /><span style="font-weight:bold; font-size:14px;"><?=$arElement["NAME"]?></span>

<?/*if($arElement["DISPLAY_PROPERTIES"]["SALON_TYPE"]["VALUE"]!=""){
echo $arElement["DISPLAY_PROPERTIES"]["SALON_TYPE"]["VALUE"]."<br />";
}*/?>

<?if($arElement["DISPLAY_PROPERTIES"]["SALON_PHONE"]["VALUE"]!=""){
echo "<br /><b>".$arElement["DISPLAY_PROPERTIES"]["SALON_PHONE"]["VALUE"]."</b><br /><br />";
}?>

<div style="width: 220px; min-height:50px; border-left:3px solid grey; padding-left:10px; font-size:11px;">
<?if($arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["VALUE"]!=""){
echo "г. ".$arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["VALUE"].", ";
}?>
<?echo $arElement["DETAIL_TEXT"];?>
</div>
<br />
<br />

</div>
<div style="clear:both;"></div>
<div style="float:left; font-size:11px; color:#666666">

<?if($arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["VALUE"]!=""){?>
<img src="/wharetobuy/maps/metro.gif" style="border:0px; margin:0px 5px 0px 0px; padding:0px;" />
<span style="font-size:12px;"><?=$arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["VALUE"];?></span>
<?}?>

<?if($arElement["DISPLAY_PROPERTIES"]["SALON_ROUTE"]["VALUE"]!=""){?>
<div style="width: 500px; font-size:11px;">
<?echo(htmlspecialcharsBack($arElement["DISPLAY_PROPERTIES"]["SALON_ROUTE"]["VALUE"]));?>
</div>
<?}?>

<?if($arElement["DISPLAY_PROPERTIES"]["SALON_SHEMA"]["VALUE"]!=""){
$shema = CFile::GetPath($arElement["DISPLAY_PROPERTIES"]["SALON_SHEMA"]["VALUE"]);

	if($_REQUEST["print"]=="Y"){

	echo CFile::ShowImage($arElement["DISPLAY_PROPERTIES"]["SALON_SHEMA"]["VALUE"], 400, 600, "border=0");

	}

	else{

?>
<br />
<a href="#" onClick="window.open('/wharetobuy/russia/shema.php?shema=<?=$shema?>','shema','directories=no,width=500,height=500,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no'); return(false);">Схема проезда &raquo;</a>
<?/*echo CFile::Show2Images("/wharetobuy/maps/micromap.gif", $arElement["DISPLAY_PROPERTIES"]["SALON_SHEMA"]["VALUE"], 150, 150, "class=preview");*/?>
	<?}

	}?>

</div>



						<?/*foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
							<?=$arProperty["NAME"]?>:&nbsp;<?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?><br />
						<?endforeach*/?>


						<?}?>


						</p>



					</td>
				</tr>
			</table>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

<?if((($_REQUEST["SALON_ID"]) || ($_REQUEST["CITY_ID"])) && (!$_REQUEST["print"])){?>
	<br />
	<br />
     <a href="#" onClick="map(); return(false);">Смотреть карту &raquo;</a>
     <br /><br />
     <a href="<?=htmlspecialchars($APPLICATION->GetCurUri("print=Y"));?>" title="Версия для печати" rel="nofollow" target="_blank">Версия для печати &raquo;</a>
<?}?>

		</td>
	</tr>
</table>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

