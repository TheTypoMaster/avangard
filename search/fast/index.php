<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Быстрый поиск");
?>

<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<br />

<form action="" method="get">
<?if($arResult["REQUEST"]["HOW"]=="d"):?>
	<input type="hidden" name="how" value="d" />
<?endif;?>

	<table width="400" height="36" cellspacing="0" cellpadding="0">
	<tr>
		<td width="240" valign="middle" align="left"  style="BACKGROUND:
url(/bitrix/templates/avangard/images/search_bg.jpg) no-repeat left top">

	<input type="text" name="q" value="<?=$q?>" size="25" style="border: 0px; width: 220px; padding: 5px 5px 5px 5px" />

		</td>
		<td align="right">
	&nbsp;
	<input  type="image" src="/bitrix/templates/avangard/images/search_go.jpg" type="submit" value="искать" />
		</td>
	</tr>
	</table>
<br />

<?
$nnn=0;
  $arFilter = Array('IBLOCK_ID'=>5, 'ACTIVE'=>'Y');
  $db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter);

  while($ar_result = $db_list->GetNext())
  {
   $lname="list[".$nnn."]";

	$checked = "";

   if($_GET["list"][$nnn]!=""){   	$checked = "checked";
   }
       ?>
    <input type="checkbox"  name="<?=$lname?>" value="<?=$ar_result['ID']?>" <?echo($checked)?> />  <?=$ar_result['NAME']?><br />
    <?

    $nnn++;
  }

array_splice($list);
?>


</form><br />


<?
//print_r($_GET);
$q=$_GET["q"];
$list=$_GET["list"];


//print_r($list);

 if (($q!="") || ($list!="")){


if($list!=""):
else:

$sss=0;
  $arFilter = Array('IBLOCK_ID'=>5, 'ACTIVE'=>'Y');
  $db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter);

  while($ar_result = $db_list->GetNext())
  {
   $list["$sss"]=$ar_result['ID'];
     $sss++;
  }


endif;
//print_r($list);


$arOutput = Array(	"PREVIEW_PICTURE",
					"DETAIL_PAGE_URL",
					"NAME",
					"SECTION_ID",
					"IBLOCK_SECTION_ID",
		//			"PROPERTY_COMPLECT",
					"PROPERTY_F_TYPE",
					"PROPERTY_COLLECTION",
		//			"PROPERTY_TRANSFORMATION",
					Array("PROPERTY_W_SEARCH_PARAM"));



$sc = "%".$q."%";
$items = Array();

	foreach($list as $_list):


   $items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("SORT"=>"ASC"), 10000, Array("NAME" => $sc, "SECTION_ID" => $_list), $arOutput);




/*echo ("<br><br> <pre>");

   print_r($res_items);
   echo ("</pre>");
 */


?>
<a name="list">
<?
   // цикл по всем новостям
$cnt = 0;

  while($arItem = $items->GetNext())
  {

$listItem[$arItem["ID"]] = $arItem;

$cnt++;

  }

	endforeach;

	      foreach($listItem as $Item):
 ?>

      <table cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td valign="top" width="160" align="left">
				  <?   echo ShowImage($Item["PREVIEW_PICTURE"], 156, 97, "border='0'",
			  "/catalogue/".$Item["IBLOCK_SECTION_ID"]."/tov_".$Item["ID"].".html");?>

					</td>
					<td valign="top" class="bottext"><small>
					<b><a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$Item["ID"]?>.html"><?=$Item["NAME"]?></a></b><br /></br>

					<!--<b>	Тип мебели: </b>-->

					<b><?=$Item["PROPERTY_F_TYPE_VALUE"]?></b><br />


					 <?
   $arResEl=GetIBlockElement($Item["ID"]);

   $arResElU = array_unique($arResEl["PROPERTIES"]["COMPLECT"]["VALUE"]);

   foreach($arResElU as $compl):


   $arResCompl=GetIBlockElement($compl);
   echo($arResCompl["NAME"]."<br />");

   endforeach;
   ?>


						</small>
					</td>
				</tr>
			</table>

  <?
    endforeach;
  ?>



<script language="javascript" type="text/javascript">

	window.location.replace('#list');

</script>

<?

 }




?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>