<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->RestartBuffer();
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header("content-type: application/x-javascript; charset=windows-1251");

$subj_id=$_GET['subj_id'];
$combinac_id=$_GET['combinac_id'];
$recommend_id=$_GET['recommend_id'];
$del_key=$_GET['del_key'];
$del_all=$_GET['del_all'];
$count=$_GET['count'];

//print_r($_GET);
if ($del_all==1){
	if (sizeof($APPLICATION->get_cookie("basket"))>0){ 
		foreach($APPLICATION->get_cookie("basket") as $key => $val){
			$APPLICATION->set_cookie("basket[".$key."]", "");
		}
	}
}

if ($del_key>0){
//	session_destroy(${"basket_".$del_key});
	$APPLICATION->set_cookie("basket[".$del_key."]", "");
	$count = 1;
}

if ($subj_id>0) { 
	$t=true;
	if (sizeof($APPLICATION->get_cookie("basket"))>0){ 
		foreach($APPLICATION->get_cookie("basket") as $key => $val){
			if ($key>0 && $val[subj_id]==$subj_id){
				$t=false;
				break;
			}
		}
	}
	//echo "t - $t ";
	$all_param = array("subj_id" => $subj_id, "combinac_id" => $combinac_id, "recommend_id" => $recommend_id);
	if ($t) {
		$key = 0;
		if (sizeof($APPLICATION->get_cookie("basket"))>0){ 
//			$key = 10;
			$key = 1;
			foreach($APPLICATION->get_cookie("basket") as $key_new => $val_new){
				if ($key_new>=$key) 
					$key = $key_new+1;
			}
		}
		$APPLICATION->set_cookie("basket[".$key."]", serialize($all_param));
	}
	$count = 1;
	//header("Location: http://".$_SERVER['SERVER_NAME']."/basket"); 
	//print_r($APPLICATION->get_cookie("basket"));
}

//echo "<!-- idRealty -";
//if (sizeof($APPLICATION->get_cookie("basket"))>0)
//foreach($APPLICATION->get_cookie("basket") as $key => $val){
	//$idRealty=$adv->GetAdvById($val);
		//print_r($idRealty);
	//if (strtotime(date("d.m.Y H:i:s"))>strtotime($idRealty[delete_date])) 
		//echo "".$APPLICATION->set_cookie("basket[".$key."]", "")."";
		//print_r($APPLICATION->get_cookie("basket"));
//}
//echo "-->";

if ($count==1){
	if ( (sizeof($APPLICATION->get_cookie("basket"))>=0 && is_array($APPLICATION->get_cookie("basket")) ) || $subj_id>0) {
		//$c=1;
		if (sizeof($APPLICATION->get_cookie("basket"))>0) $c=sizeof($APPLICATION->get_cookie("basket"));
		//if ($subj_id>0) $c++; 
		if ($del_subj_id>0) $c--; 
		if (!empty($c)) echo "".$c.""; else echo "0";
		/*echo "<!-- ".sizeof($APPLICATION->get_cookie("basket"));
		print_r($APPLICATION->get_cookie("basket"));
		echo "-->";*/
	}
}
/*$APPLICATION->set_cookie("basket_1", "");
$APPLICATION->set_cookie("basket_2", "");
$APPLICATION->set_cookie("basket[0]", "");
$APPLICATION->set_cookie("basket[]", "");
$APPLICATION->set_cookie("basket", "");*/
//print_r($APPLICATION->get_cookie("basket"));
//print_r($APPLICATION->get_cookie("basket"));
?>
