<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Матрасы");
$APPLICATION->SetTitle("");
?> 
<? /*
$APPLICATION->IncludeComponent("bitrix:catalog.section.list", ".default", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => IntVal(5),
	"SECTION_ID" => IntVal(5),
	"SECTION_CODE" => "",
	"COUNT_ELEMENTS" => "Y",
	"TOP_DEPTH" => "3",
	"SECTION_FIELDS" => array(
		0 => "ID",
		1 => "CODE",
		2 => "XML_ID",
		3 => "NAME",
		4 => "DESCRIPTION",
		5 => "PICTURE",
		6 => "DETAIL_PICTURE",
		7 => "",
	),
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "/accessories/?SECTION_ID=#SECTION_ID#",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y"
	),
	false
);
*/ ?>
<div class="gray_td">
	<h1>Матрасы</h1>
</div>
<?
  $res = CIBlockSection::GetList(
     Array("LEFT_MARGIN"=>"ASC"), 
     Array("IBLOCK_ID"=>5, "SECTION_ID"=>110,"ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
     true,
     Array("ID", "NAME", "PICTURE")
  );
  
  while($arSection = $res->GetNext())
  {
	  if ($arSection["PICTURE"]) {
$big_picture = CFile::GetPath($arSection["PICTURE"]);
		  echo '<div style="float:left; overflow:hidden; width:230px; height:250px; margin:20px 20px 10px 0; text-align:center;"><a href="/partners/mattresses/?SECTION_ID='.$arSection['ID'].'"><img style="width:230px; margin-bottom:5px;" src="'.$big_picture.'"></a><br /><span style="font-size:11px; font-weight:600;">'.$arSection['NAME'].'</span></div>';
	  }

  }
?>
<div style="clear: left"></div>
<br />
<br /> 

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>