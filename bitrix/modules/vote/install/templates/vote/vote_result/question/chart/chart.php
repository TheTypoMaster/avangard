<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $arrSaveColor;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");


if (CModule::IncludeModule("vote")) :

$diameter = (intval($dm)>0) ? intval($dm) : 180;

// create an image
$ImageHandle = CreateImageHandle($diameter, $diameter);

// getting the data array
$arr = $arAnswers = array();
$total = 0;
$w = CVoteAnswer::GetList($qid,($by="s_counter"),($order="desc"));
while ($wr=$w->GetNext(true,false))	
{
	$total++;
	$arr[] = array("ORIGINAL_COLOR" => TrimEx($wr["COLOR"],"#"), "COUNTER"=> $wr["COUNTER"], "MESSAGE" => $wr["MESSAGE"]);
}
$color = "";
$arChart = array();
while(list($key,$sector)=each($arr))
{
	$color = GetNextRGB($color, $total);
	$show_color = (strlen($sector["ORIGINAL_COLOR"])<=0) ? $color : $sector["ORIGINAL_COLOR"];
	$arChart[] = array("COUNTER"=>$sector["COUNTER"], "COLOR"=>$show_color, "MESSAGE" => $sector["MESSAGE"]);
}
//echo $total; echo "<pre>"; print_r($arChart); echo "</pre>";

// drawing pie chart
Circular_Diagram($ImageHandle, $arChart, "FFFFFF", $diameter, $diameter/2, $diameter/2);

// displaying of the resulting image
ShowImageHeader($ImageHandle);

endif;
?>