<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $img = CFile::ShowImage($arResult["DETAIL_PICTURE"]["ID"], 215, 115, "class=preview");?>
<div class="catalog-element">
	<table width="520" border="0" cellspacing="0" cellpadding="2">
		<tr>
	<td>
<?echo htmlspecialchars_decode($arResult["PROPERTIES"]["GOOGLE_MAP"]["VALUE"]);?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAVZyZ-VmpCwe0xh3JfTy1kRQyxjH9nx-Gcm4qIhBuRwc_jr_RiRS_rdNfLeBOcUL2wQ5Y37D_dLB-mA
"     type="text/javascript"></script>
    <script type="text/javascript">
    function initialize() {
      if (GBrowserIsCompatible()) {
        var mapnew = new GMap2(document.getElementById("mapnew_canvas"));
 <? if($russia) {?>       mapnew.setCenter(new GLatLng(55.733524,85.625677), 2);
<?} else { ?>   mapnew.setCenter(new GLatLng(55.733524,37.625677), 10);
<?}?>
        mapnew.addControl(new GSmallMapControl());
        mapnew.addControl(new GMapTypeControl());



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



 var marker = new GMarker(coords1,{ title: "салон 1",  icon:blueIcon});

 GEvent.addListener(marker, "click", function() {
            marker.openInfoWindowHtml('<?echo '<h1>'.$arResult["NAME"].'</h1>'.$arResult["PROPERTIES"]["SALON_TYPE_2"]["VALUE"].'<br>'.$arResult["PROPERTIES"]["SALON_PHONE"]["VALUE"].'<br>График работы:'.$arResult["PROPERTIES"]["SALON_TIME"]["VALUE"].'<br>'.$img;?>');
          });

mapnew.addOverlay(marker);

     
      }
    }
    </script>
   <body onload="initialize()" onunload="GUnload()">
<?// <div id="mapnew_canvas" style="width: 520px; height: 300px"></div>?>

	</td>
	</tr>
	<tr>
	<td>
	<div style="background-color: #f2f2f2;  overflow-x: auto; overflow-y: none; width: 520px; height: 60px;">
	<nobr>
	   <?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
<?
   $folder = str_replace($PHOTO["FILE_NAME"], "", $PHOTO["SRC"]); // получаем папку, где хранится картинка (убираем из пути название файла)
   $s_puth=$folder."s_".$PHOTO["FILE_NAME"];//получаем путь до маленькой картинки

   $puth=$folder.$PHOTO["FILE_NAME"];//получаем путь до основной картинки
 ?>

<a href="#null" onclick="MM_swapImage('mainimg','','<?=$puth?>',1); javascript:url='<?=$puth?>';return false">
<?echo CFile::ShowImage($s_puth, 150, 50, "class=preview");?>
</a>
                <?endforeach?>
	</nobr>
	</div>
	</td>
	</tr>



		

	
	</table>
	
	
</div>

