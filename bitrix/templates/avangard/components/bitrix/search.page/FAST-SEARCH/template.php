<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<br />
<div class="search-page">
<pre><?//*print_r($arResult)?></pre>
Введите название мебели, полностью или частично: <br /></br />
<form action="" method="get">
<?if($arResult["REQUEST"]["HOW"]=="d"):?>
	<input type="hidden" name="how" value="d" />
<?endif;?>
	<input type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="40" />

<?if($arParams["SHOW_WHERE"]):?>
	&nbsp;<select name="where" type="hidden">
	<option value=""><?=GetMessage("SEARCH_ALL")?></option>
	<?foreach($arResult["DROPDOWN"] as $key=>$value):?>
	<option value="<?=$key?>"<?if($arResult["REQUEST"]["WHERE"]==$key) echo " selected"?>><?=$value?></option>
	<?endforeach?>
	</select>
<?endif;?>

  &nbsp;<input type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
</form><br />

<?


if ($arResult["REQUEST"]["QUERY"]!=""):
$sc = "%".$arResult["REQUEST"]["QUERY"]."%";
   $items = GetIBlockElementListEx("catalogue", "furniture", Array(),
              Array("SORT"=>"ASC", "NAME" => "ASC", "DATE_ACTIVE_FROM"=>"DESC"),1000000, Array("NAME" => $sc));

   // цикл по всем новостям
   while($arItem = $items->GetNext())
   {
     // выведем ссылку на страницу с детальным просмотром
      echo "<a href='".$arItem["DETAIL_PAGE_URL"]."'>".$arItem["NAME"]."</a>";
     // выведем дату
      echo $arItem["DATE_ACTIVE_FROM"]."<br>";
     // выведем картинку для анонса, с ссылкой на детальный просмотр
      echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100,
                     "border='0'", $arItem["DETAIL_PAGE_URL"]);
     // выведем анонс
      echo $arItem["PREVIEW_TEXT"]."<hr>";
   }
else:
 	echo("Не введено название мебели. ");
endif;
?>





</div>