<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$kol=0;
$russia = $_GET[russia];
  $moscow = $_GET[moscow]; ?>


<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAVZyZ-VmpCwe0xh3JfTy1kRQyxjH9nx-Gcm4qIhBuRwc_jr_RiRS_rdNfLeBOcUL2wQ5Y37D_dLB-mA
"     type="text/javascript"></script>
    <script type="text/javascript">
    function initialize() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map_canvas"));
 <? if($russia) {?>       map.setCenter(new GLatLng(55.733524,85.625677), 2);
<?} else { ?>   map.setCenter(new GLatLng(55.733524,37.625677), 10);
<?}?>
        map.addControl(new GLargeMapControl());
  



        // Create a base icon for all of our markers that specifies the
        // shadow, icon dimensions, etc.
        var baseIcon = new GIcon(G_DEFAULT_ICON);
        baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
        baseIcon.iconSize = new GSize(20, 34);
        baseIcon.shadowSize = new GSize(37, 34);
        baseIcon.iconAnchor = new GPoint(9, 34);
        baseIcon.infoWindowAnchor = new GPoint(9, 2);

        // Creates a marker whose info window displays the letter corresponding
        // to the given index.

var blueIcon = new GIcon(G_DEFAULT_ICON);blueIcon.image = "http://www.avangard.biz/images/salonA.png";       
 markerOptions = { icon:blueIcon };

 
var coords1=new GLatLng(55.7241232,37.6890305);



 var marker326 = new GMarker(coords1,{ title: "салон 1",  icon:blueIcon});

 GEvent.addListener(marker326, "click", function() {

	var t = this.title || this.name || null;
	var a = "/redesign/where_buy/detail.php?id=326";
	var g = this.rel || false;
	tb_show(t,a,g);
	this.blur();
	return false;
	   });

map.addOverlay(marker326);

     
      }
    }
    </script>

<div style="position: relative; top: -13px; width: 240px; height: 40px; border:solid 1px #e1e1e1;">

<table class="zakladki" cellpadding="0" cellspacing="0" height="40"><tr><td style="vertical-align:middle; text-align:center; width:120px;" 
<?if(!$russia) echo 'bgcolor="#e1e1e1"';?>>
<?if($russia) echo '<a href="?moscow=1">';?>Москва и МО<?if($russia) echo '</a>';?></td><td <?if($russia) echo 'bgcolor="#e1e1e1"';?> style="vertical-align:middle; text-align:center; width:120px;"><?if(!($russia)) echo '<a href="?russia=1">';?>Россия<?if(!($russia)) echo '</a>';?></td></tr></table>
</div>
	
<table width="920"><tr><td width="280">
<?
$city = "";
?>
 <table cellpadding="0" cellspacing="0" border="0">


	<tr>
		<td valign="top" width="50%">

		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>


<?

	if($arElement["PROPERTIES"]["SALON_TYPE"]["VALUE"] != $city){
 

// 	echo("<pre>"); print_r($arElement["PROPERTIES"]["SALON_TYPE"]); echo("</pre>");

	$city = $arElement["PROPERTIES"]["SALON_TYPE"]["VALUE"];
	$city_link = ruslat($city);

	
    	if(!$_REQUEST["SALON_ID"]){ 

$kol++;

if($kol>1) echo "</div><br><br>";
?>

 <table>
	<tr>
 <?
    	if($arElement["PROPERTIES"]["SALON_TYPE"]["VALUE_ENUM_ID"] == "6"){?>
         <td>

    	 <img src='/wharetobuy/maps/salon.gif' width='25' height='20' alt='' border='0' style="border:0px;">

    	 </td>

		<?}
    	if($arElement["PROPERTIES"]["SALON_TYPE"]["VALUE_ENUM_ID"] == "7"){?>
         <td>

    	 <img src='/wharetobuy/maps/podium.gif' width='25' height='20' alt='' border='0' style="border:0px;">

    	 </td>

     	<?}?>
        <td>
          <div class="gray_td" style="margin-bottom:5px;"><?=$city?></div>
		</td>
	</tr>
 </table>
<div style='width: 260px; overflow-x: none;

 <?if($russia && $kol==1) echo "overflow-y: none; height: 60px;"; else {?>overflow-y: auto; height: <?$w_sp = 150 * $kol; echo $w_sp; ?>px;
<?}?>
'>

 <?		}
 
	}
?>
	

			<table cellpadding="0" cellspacing="2" border="0">
				<tr>
					
					<td valign="top">

					<p class="content">


                    <?if(!$_REQUEST["SALON_ID"]){?>
					<span style="line-height:20px;">
<a href="javascript:void(0);" onclick="getHint(event,'/redesign/where_buy/detail.php?id=<?=$arElement['ID']?>');return false;" title="Актюбинск, Казахстан"><b><?=$arElement["NAME"]?></b></a></span><br />
<?                    	if($arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["VALUE"]!=""){?>
						<img src="/wharetobuy/maps/metro.gif" style="border:0px; margin:0px 5px -1px 0px; padding:0px;">
						<?=$arElement["DISPLAY_PROPERTIES"]["SALON_METRO"]["VALUE"];?>
						<?}else{echo "г. ".$arElement["DISPLAY_PROPERTIES"]["SALON_CITY"]["VALUE"];}

                    }?>



					</td>
				</tr>
			</table>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		</td>
	</tr>
</table>


</td>
<td>

 <div id="map_canvas" style="position: relative; top: -56px; width: 640px; height: 593px"></div>

</td></tr></table>


