<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск по коллекциям");
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
        <input  type="image" src="/bitrix/templates/avangard/images/search_go.jpg" type="submit"
         value="искать" />
                </td>
        </tr>
        </table>

<br />


<?

$nnn=0;

//*правильно указать инфоблок с описаниями коллекций*//
$coll_iblock=9;

$arFilter = Array("IBLOCK_ID"=>$coll_iblock, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter);
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
 $cname="collection[".$nnn."]";


	$checked = "";

   if($_GET["collection"][$nnn]!=""){
   	$checked = "checked";
   }

    ?>
    <input type="checkbox"  name="<?=$cname?>" value="<?=$arFields["ID"]?>" <?echo($checked)?> />  <?=$arFields["NAME"]?><br />
    <?
$nnn++;
}
?>


</form>


<?
// print_r($_GET);

$q=$_GET["q"];
$coll=$_GET["collection"];
	if (($q!="") || ($coll!="")){
?>
  <a name="list">
<?


if ($coll!=""):
else:

$rrr=0;
$res = CIBlockElement::GetList(Array(), $arFilter);
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();

$coll["$rrr"] =$arFields["ID"];
$rrr++;
}

endif;



$sc = "%".$q."%";
$arOutput = Array(        "PREVIEW_PICTURE",
                                        "DETAIL_PAGE_URL",
                                        "ID",
                                        "NAME",
                                        "SECTION_ID",
                                        "IBLOCK_SECTION_ID",
                //                        "PROPERTY_COMPLECT",
                                        "PROPERTY_F_TYPE",
                                        "PROPERTY_COLLECTION",
                //                        "PROPERTY_TRANSFORMATION",
                                        Array("PROPERTY_W_SEARCH_PARAM"));



  foreach($coll as $coll_el):
  $show_coll=0;
   $items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("SORT"=>"ASC", "NAME" => "ASC", "DATE_ACTIVE_FROM"=>"DESC"),0, Array("NAME" => $sc, "PROPERTY_COLLECTION" => $coll_el), $arOutput);
   while($arItem = $items->GetNext())
   {
            if($arItem["ID"]!=""):
            $show_coll=1;
            else:
            $show_coll=0;
            endif;
   }

    if($show_coll!=0):
     ?> <hr class="hline"><br /> <?
        $collFilter = Array("IBLOCK_ID"=>$coll_iblock, "ACTIVE"=>"Y", "ID"=>"$coll_el");
        $collres = CIBlockElement::GetList(Array(), $collFilter);
        while($cob = $collres->GetNextElement())
{
  $collFields = $cob->GetFields();


?>
         <table cellpadding="0" cellspacing="5" border="0">
                                <tr>
                                <?if ($collFields["PREVIEW_PICTURE"]!=""):?>
                                        <td valign="top" width="210" align="left">
                                  <?   echo ShowImage($collFields["PREVIEW_PICTURE"], 200, 200, "border='0'");?>

                                        </td>
                                <?endif;?>
                                        <td valign="top" class="bottext">
                                        <h1><?=$collFields["NAME"]?></h1></br>
                                        <?=$collFields["PREVIEW_TEXT"]?>
                                  </td>
                                </tr>
                        </table>

<?

 }





   $items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("SORT"=>"ASC", "NAME" => "ASC", "DATE_ACTIVE_FROM"=>"DESC"),0, Array("NAME" => $sc, "PROPERTY_COLLECTION" => $coll_el), $arOutput);

   // цикл по всем новостям



   while($arItem = $items->GetNext())
   {
   ?>
   <pre>
   <?//print_r($arItem)?></pre>



      <table cellpadding="0" cellspacing="5" border="0">
                                <tr>
                                        <td valign="top" width="160" align="left">
                                  <?   echo ShowImage($arItem["PREVIEW_PICTURE"], 156, 97, "border='0'",
                          "/catalogue/".$arItem["IBLOCK_SECTION_ID"]."/tov_".$arItem["ID"].".html");?>

                                        </td>
                                        <td valign="top" class="bottext"><small>
                                        <b><a href="/catalogue/<?=$arItem["IBLOCK_SECTION_ID"]?>/tov_<?=$arItem["ID"]?>.html"><?=$arItem["NAME"]?></a></b><br /></br>

                                        <!--<b>        Тип мебели: </b>-->

                                        <b><?=$arItem["PROPERTY_F_TYPE_VALUE"]?></b><br />


                                         <?
   $arResEl=GetIBlockElement($arItem["ID"]);

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

   }
   else:
   endif;

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
