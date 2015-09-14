<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Мягкая мебель купить магазин. Фото каталог диванов для дома, продажа");
$APPLICATION->SetTitle("Мягкая мебель купить магазин. Фото каталог диванов для дома, продажа");
?> 
<?
if($_REQUEST["SECTION_ID"]!='')
{
$mainFilter= array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
$APPLICATION->IncludeComponent("bitrix:catalog.section", "ava_shop_cat", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "31",
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "UF_COLLECTION",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "mainFilter",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "Y",
	"PAGE_ELEMENT_COUNT" => "40",
	"LINE_ELEMENT_COUNT" => "2",
	"PROPERTY_CODE" => array(
		0 => "MATERIAL",
		1 => "",
	),
	"OFFERS_LIMIT" => "0",
	"SECTION_URL" => "?SECTION_ID=#SECTION_ID#",
	"DETAIL_URL" => "#SITE_DIR#/shop/catalog/element.php?id=#ELEMENT_ID#",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "0",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "N",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);
}
else
{
### список разделов интернет-магазина
$res = CIBlockSection::GetList(
	Array("LEFT_MARGIN"=>"ASC"), 
	Array("IBLOCK_ID"=>31, "SECTION_ID"=>"false","ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
	true,
	Array("ID", "NAME", "PICTURE", "UF_COLLECTION")
);
$elements = array();  
while($arSection = $res->GetNext())
	$elements[] = $arSection;

### список коллекций сайта
$res_col = CIBlockElement::GetList(
	Array("SORT"=>"ASC"), 
	Array("IBLOCK_ID"=>9,"ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"), 
	false,
	false,
	Array("ID", "NAME")
);
$collection = array();
$collection_count = 0;
while($arSection_col = $res_col->GetNext())
{
	$collection_count++;
	$collection[$collection_count] = $arSection_col;
	foreach($elements as $el)
		if ($el[UF_COLLECTION] == $arSection_col[ID])
			$collection[$collection_count]["el"][] = $el;
}
?> 
<div class="gray_td">
	<h1>Каталог интернет магазина</h1>
</div>
<table width="738" align="center">
<?
$kol = 0;
foreach($collection as $co)
{
	if ($co[el]) 
	{
		$rows_count = count($co[el]); 
		$kol++;
		if(($kol > 1) && ($kol <= $collection_count))
			echo '<tr><td colspan="5" class="gray_line_small"></td></tr>';
		?>
		<tr>
			<td colspan="5"><b><?=$co[NAME] ?></b></td>
		</tr>
		<?$i=0;
		foreach($co[el] as $cur_elem)
		{
			$i++;
			$rows_count--;
			if($i == 1)
				//echo '<tr class="divan_row last_row">';
				echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
			$elem_picture = CFile::GetPath($cur_elem["PICTURE"]);
			?>
			<td class="catalog_td">			
				<a href="/shop/catalogue/?SECTION_ID=<?=$cur_elem['ID']?>"><img class="catalog_picture" src="<?=$elem_picture ?>" alt="<?=$cur_elem['NAME'] ?>"></a><br>
				<a class="catalog_name" href="/shop/catalogue/?SECTION_ID=<?=$cur_elem['ID']?>"><?=$cur_elem['NAME'] ?></a>
			</td>
			<?
			if($i < 3) echo '<td width="26"></td>'; 			 
			if($i == 3){
				$i = 0;
				echo "</tr>";
			}				
		}
	}
}
?>
</table>
<?}?>
<br />
<br /> 
<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>