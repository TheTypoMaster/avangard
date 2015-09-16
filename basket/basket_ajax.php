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

if ($del_all==1){
	if (sizeof($APPLICATION->get_cookie("basket"))>0){ 
		foreach($APPLICATION->get_cookie("basket") as $key => $val){
			$APPLICATION->set_cookie("basket[".$key."]", "");
		}
	}
}

if ($del_key>0){
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
}

if ($count==1){
	if ( (sizeof($APPLICATION->get_cookie("basket"))>=0 && is_array($APPLICATION->get_cookie("basket")) ) || $subj_id>0) {
		//$c=1;
		if (sizeof($APPLICATION->get_cookie("basket"))>0) $c=sizeof($APPLICATION->get_cookie("basket"));
		//if ($subj_id>0) $c++; 
		if ($del_subj_id>0) $c--; 
		if (!empty($c)) echo "".$c.""; else echo "0";
	}
}
?>
