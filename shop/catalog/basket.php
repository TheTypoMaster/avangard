<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header("content-type: application/x-javascript; charset=windows-1251");

include_once($_SERVER['DOCUMENT_ROOT']."/classes/advs.php");
$adv = new advs();
$object_id=$_GET['id'];
$del_object_id=$_GET['del_id'];
$del_all=$_GET['del_all'];
$count=$_GET['count'];

if ($del_all==1){
	if (sizeof($APPLICATION->get_cookie("basket"))>0){ 
		foreach($APPLICATION->get_cookie("basket") as $key => $val){
			$APPLICATION->set_cookie("basket[".$key."]", "");
		}
	}
}

if ($del_object_id>0){
//	session_destroy(${"basket_".$del_object_id});
	$APPLICATION->set_cookie("basket[".$del_object_id."]", "");
}

if ($object_id>0) {
	$t=true;
	if (sizeof($APPLICATION->get_cookie("basket"))>0){ 
		foreach($APPLICATION->get_cookie("basket") as $key => $val){
			if ($key>0 && $key==$object_id){
				$t=false;
				break;
			}
		}
	}
	if ($t) $APPLICATION->set_cookie("basket[".$object_id."]", $object_id);
}

//echo "<!-- idRealty -";
if (sizeof($APPLICATION->get_cookie("basket"))>0)
foreach($APPLICATION->get_cookie("basket") as $key => $val){
	$idRealty=$adv->GetAdvById($val);
		//print_r($idRealty);
		if (strtotime(date("d.m.Y H:i:s"))>strtotime($idRealty[delete_date])) echo "".$APPLICATION->set_cookie("basket[".$key."]", "")."";
}
//echo "-->";

//if ($count==1){
	if (sizeof($APPLICATION->get_cookie("basket"))>=0 || $object_id>0) {
		//$c=1;
		if (sizeof($APPLICATION->get_cookie("basket"))>0 && is_array(sizeof($APPLICATION->get_cookie("basket")))) $c=sizeof($APPLICATION->get_cookie("basket"));
		else $c =0;
		if ($object_id>0) $c++; 
		if ($del_object_id>0) $c--; 
		if (!empty($c)) echo "(".$c.")";
		/*echo "<!-- ".sizeof($APPLICATION->get_cookie("basket"));
		print_r($APPLICATION->get_cookie("basket"));
		echo "-->";*/
	}
//}
/*$APPLICATION->set_cookie("basket_1", "");
$APPLICATION->set_cookie("basket_2", "");
$APPLICATION->set_cookie("basket[0]", "");
$APPLICATION->set_cookie("basket[]", "");
$APPLICATION->set_cookie("basket", "");*/
//print_r($APPLICATION->get_cookie("basket"));
//print_r($APPLICATION->get_cookie("basket"));
?>