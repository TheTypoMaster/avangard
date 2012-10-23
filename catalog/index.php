<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "ћ€гка€ мебель купить магазин. ‘ото каталог диванов дл€ дома, продажа");
$APPLICATION->SetPageProperty("up_inc_file", "none");
$APPLICATION->SetPageProperty("right_inc_file", "right_nomain_inc_file.php");
$APPLICATION->SetTitle("ћ€гка€ мебель купить магазин. ‘ото каталог диванов дл€ дома, продажа");
?> 
<table cellspacing="0" cellpadding="0"> 
	<tbody> 
		<tr>
			<td width="100%"> 
				<div class="gray_td"> 
					<h1> аталог м€гкой мебели, диванов по коллекци€м</h1>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<?
				$mainFilter= array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "!PROPERTY_IN_CATALOG" => false);
				if($_REQUEST["collection"]!='')
					$mainFilter["PROPERTY_COLLECTION"]= (int)$_REQUEST["collection"];
				$APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_bigpics", Array(
					"AJAX_MODE" => "N",
					"IBLOCK_ID" => "5",
					"SHOW_ALL_WO_SECTION" => "Y",
					"ELEMENT_SORT_FIELD" => "sort",
					"ELEMENT_SORT_ORDER" => "asc",
					"FILTER_NAME" => "mainFilter",
					"INCLUDE_SUBSECTIONS" => "Y",
					"SECTION_URL" => "section.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#",
					"DETAIL_URL" => "element.php?IBLOCK_ID=#IBLOCK_ID#&SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
					"BASKET_URL" => "/personal/basket.php",
					"ACTION_VARIABLE" => "action",
					"PRODUCT_ID_VARIABLE" => "id",
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"META_KEYWORDS" => "-",
					"META_DESCRIPTION" => "-",
					"DISPLAY_PANEL" => "N",
					"DISPLAY_COMPARE" => "N",
					"SET_TITLE" => "Y",
					"PAGE_ELEMENT_COUNT" => "30",
					"LINE_ELEMENT_COUNT" => "3",
					"PROPERTY_CODE" => array("FULLCOLOR_PIC", "BLACKWHITE_PIC", "PRICE", "COLLECTION"),
					"PRICE_CODE" => array("BASE"),
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => "1",
					"PRICE_VAT_INCLUDE" => "Y",
					"CACHE_TYPE" => "N",
					"CACHE_TIME" => "0",
					"CACHE_FILTER" => "N",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "“овары",
					"PAGER_SHOW_ALWAYS" => "Y",
					"PAGER_TEMPLATE" => "",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"AJAX_OPTION_SHADOW" => "Y",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
				));?>
			</td>
		</tr>
	</tbody>
</table>
<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>