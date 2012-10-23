<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("");
$section_id= (int)$_GET["id"];
$coord1= 0;
$coord2= 0;
$zoom= 10;
//Получаем параметры карты из свойств раздела
$section_res = CIBlockSection::GetList(Array(), array("IBLOCK_ID"=>8, "ID"=>$section_id), false, array("UF_*"));
if($section = $section_res->GetNext()){
	$coord1= $section["UF_COORD1"];
	$coord2= $section["UF_COORD2"];
	$zoom= $section["UF_ZOOM"];
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=windows-1251"/>
		<title>Фирменные салоны</title>
		<!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyCrMLOPZyKM4XplJI36MZbF4FdlrjctAVY" type="text/javascript"></script>-->
		<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAVZyZ-VmpCwe0xh3JfTy1kRQyxjH9nx-Gcm4qIhBuRwc_jr_RiRS_rdNfLeBOcUL2wQ5Y37D_dLB-mA"
		type="text/javascript"></script>
		<script type="text/javascript">
			var geocoder = null;
			
			function initialize() {
				if (GBrowserIsCompatible()) {
					var map = new GMap2(document.getElementById("map_canvas"));
					geocoder = new GClientGeocoder();
					map.setCenter(new GLatLng(<?=$coord1?>, <?=$coord2?>), <?=$zoom?>);
					map.addControl(new GSmallMapControl());
     
					// Create a base icon for all of our markers that specifies the
					// shadow, icon dimensions, etc.
					var baseIcon = new GIcon(G_DEFAULT_ICON);
					baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
					baseIcon.iconSize = new GSize(20, 34);
					baseIcon.shadowSize = new GSize(37, 34);
					baseIcon.iconAnchor = new GPoint(9, 34);
					baseIcon.infoWindowAnchor = new GPoint(9, 2);

     
					function showAddressMy(address, textsalon, type) {
						if (geocoder) {
							geocoder.getLatLng(
							address,
							function(point) {
								if (point) {
									var letteredIcon = new GIcon(baseIcon);
									if(type == "Фирменный Подиум") 
									{
										letteredIcon.image = "http://www.avangard.biz/images/salonA.png";
										var typetext = "<h1 class='header'><img src='/wharetobuy/maps/podium.gif'>Фирменный подиум</h1>";
									} else {
										letteredIcon.image = "http://www.avangard.biz/images/podium.png";
										var typetext = "<h1 class='header'><img src='/wharetobuy/maps/salon.gif'>Фирменный салон</h1>";

									}
									markerOptions = { icon:letteredIcon };
									var marker = new GMarker(point, markerOptions);
									map.addOverlay(marker);
									GEvent.addListener(marker, "click", function() {
										marker.openInfoWindowHtml(typetext + textsalon);
									});   
								}
							}
						);
						}
					}

					function showAddressCoord(coords_one, coords_two, textsalon, type) {
   
						var point = new GLatLng(coords_one, coords_two);
						if (point) {
							var letteredIcon = new GIcon(baseIcon);
							if(type == "Фирменный Подиум") 
							{
								letteredIcon.image = "http://www.avangard.biz/images/salonA.png";
								var typetext = "<h1 class='header'><img src='/wharetobuy/maps/podium.gif'>Фирменный подиум</h1>";
							} else {
								letteredIcon.image = "http://www.avangard.biz/images/podium.png";
								var typetext = "<h1 class='header'><img src='/wharetobuy/maps/salon.gif'>Фирменный салон</h1>";

							}
							markerOptions = { icon:letteredIcon };
							var marker = new GMarker(point, markerOptions);
							map.addOverlay(marker);
							GEvent.addListener(marker, "click", function() {
								marker.openInfoWindowHtml(typetext + textsalon);
							});   
						}
					}
					// Add 10 markers to the map at random locations
					var bounds = map.getBounds();
					var southWest = bounds.getSouthWest();
					var northEast = bounds.getNorthEast();
					var lngSpan = northEast.lng() - southWest.lng();
					var latSpan = northEast.lat() - southWest.lat();
       
			<?
				$arSelect = Array(
					"ID", 
					"NAME", 
					"IBLOCK_SECTION_ID", 
					"PROPERTY_SALON_TYPE_2", 
					"PROPERTY_SALON_COORDS",
					"PROPERTY_SALON_TIME", 
					"PROPERTY_SALON_CITY", 
					"PROPERTY_SALON_ADRESS", 
					"PROPERTY_SALON_PHONE", 
					"DATE_ACTIVE_FROM"
				);
				$arFilter = Array("IBLOCK_ID" => 8, "SECTION_ID"=>$section_id, "ACTIVE" => "Y");
				$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 500), $arSelect);
				while ($ob = $res->GetNextElement()) {
					$arFields = $ob->GetFields();
					if ($arFields["PROPERTY_SALON_COORDS_VALUE"]) {
			?>
						showAddressCoord(<?= $arFields["PROPERTY_SALON_COORDS_VALUE"] ?>, "<? echo "<h1 class='on_map'>" . $arFields["NAME"] . "</h1> <br>Город: " . $arFields["PROPERTY_SALON_CITY_VALUE"] . "<br>Телефон: <b>" . $arFields["PROPERTY_SALON_PHONE_VALUE"] . "</b><br>Часы работы: <b>" . $arFields["PROPERTY_SALON_TIME_VALUE"] . "</b><br>"; ?>", "<?= $arFields["PROPERTY_SALON_TYPE_2_VALUE"] ?>");
					<?}else if ($arFields["PROPERTY_SALON_ADRESS_VALUE"]) {
						$searchphrase = str_replace(",", "", $arFields["PROPERTY_SALON_CITY_VALUE"] . " " . $arFields["PROPERTY_SALON_ADRESS_VALUE"]);
						$searchphrase = str_replace(".", "", $searchphrase);
						$searchphrase = str_replace('"', "", $searchphrase);
						$searchphrase = trim($searchphrase);
					?>
						showAddressMy("<?= $searchphrase ?>", "<? echo "<h1 class='on_map'>" . $arFields["NAME"] . "</h1> <br>Город: " . $arFields["PROPERTY_SALON_CITY_VALUE"] . "<br>Телефон: <b>" . $arFields["PROPERTY_SALON_PHONE_VALUE"] . "</b><br>Часы работы: <b>" . $arFields["PROPERTY_SALON_TIME_VALUE"] . "</b><br>"; ?>", "<?= $arFields["PROPERTY_SALON_TYPE_2_VALUE"] ?>");
					<?}else{
						$searchphrase = str_replace(",", "", $arFields["PROPERTY_SALON_CITY_VALUE"] . " ");
						$searchphrase = str_replace(".", "", $searchphrase);
						$searchphrase = str_replace("ул.", "", $searchphrase);
						$searchphrase = str_replace("д.", "", $searchphrase);
						$searchphrase = str_replace("г.", "", $searchphrase);
						$searchphrase = str_replace("м.", "", $searchphrase);
						$searchphrase = str_replace("&quot;", "", $searchphrase);
						$searchphrase = str_replace('"', "", $searchphrase);
						$searchphrase = trim($searchphrase);
						?>
						showAddressMy("<?= $searchphrase ?>", "<? echo "<h1 class='on_map'>" . $arFields["NAME"] . "</h1> <br>Город: " . $arFields["PROPERTY_SALON_CITY_VALUE"] . "<br>Телефон: <b>" . $arFields["PROPERTY_SALON_PHONE_VALUE"] . "</b><br>Часы работы: <b>" . $arFields["PROPERTY_SALON_TIME_VALUE"] . "</b><br>"; ?>", "<?= $arFields["PROPERTY_SALON_TYPE_2_VALUE"] ?>");
					<?}
				} ?>
				}
			}  
		</script>
	</head>
	<body onload="initialize()" onunload="GUnload()">
		<div id="map_canvas" style="width: 100%; height: 100%"></div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>