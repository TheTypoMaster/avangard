<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<table width="738" align="center">
	<?
	$collection_id= (int)$_GET["collection"];
	$arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "DATE_ACTIVE_FROM");
	$arFilter = Array("IBLOCK_ID" => IntVal(9), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
	if($collection_id!=0){
		$arFilter["ID"]= $collection_id;
	}

	$res = CIBlockElement::GetList(Array("SORT" => "ASC", "PROPERTY_PRIORITY" => "ASC"), $arFilter, false, Array("nPageSize" => 50), $arSelect);
	$cat_array = array();
	while($ob = $res->GetNextElement()){
		$i++;
		$arFields = $ob->GetFields();
		$cat_array[$arFields["ID"]]["name"] = $arFields["NAME"];
		$cat_array[$arFields["ID"]]["text"] = $arFields["PREVIEW_TEXT"];
		$cat_array[$arFields["ID"]]["detail_text"] = $arFields["DETAIL_TEXT"];
		$cat_array[$arFields["ID"]]['id'] = $arFields["ID"];
	}
	$kolvo_elems = count($cat_array);
	$kol = 0;
	foreach($cat_array as $category){
		if(IntVal($category[id]) != 2761){
			$kol++;
			?>
			<?if(($kol > 1) && ($kol <= $kolvo_elems)): ?>
				<tr><td colspan="5" class="gray_line_small"></td></tr>
			<?endif ?>
			<tr>
				<td colspan="5"><b><?=$category["name"] ?></b></td>
			</tr>
			<?
			$arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_PRICE", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT", "PROPERTY_COLLECTION", "IBLOCK_ID");
			$arElementFilter = Array("IBLOCK_ID" => IntVal(5), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "!PROPERTY_IN_CATALOG" => false, "PROPERTY_COLLECTION" => IntVal($category["id"]));
			$resElement = CIBlockElement::GetList(Array("SORT" => "ASC", "PROPERTY_PRIORITY" => "ASC"), $arElementFilter, false, Array("nPageSize" => 50), $arElementSelect);
			$i = 0;
			$rows_count= $resElement->SelectedRowsCount();
			while($obElement = $resElement->GetNextElement()){
				$i++;
				$rows_count--;
				if($i == 1)
					echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
				$arElementFields = $obElement->GetFields();
				if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE'])
					$img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); else
					$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
				$size = getimagesize($_SERVER['DOCUMENT_ROOT'] . $img_path);
				if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE'])
					$img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); else
					$img_path_bl = $img_path;
			?>
				<td class="catalog_td">
					<a href="/catalog/divan<?=$arElementFields[ID] ?>.htm">
						<img onMouseOver="this.src='<?=$img_path ?>';" onMouseOut="this.src='<?=$img_path_bl ?>';" class="catalog_picture" src="<?=$img_path_bl ?>" alt="<?=$arElementFields[NAME] ?>">
					</a><br>
					<table width="100%">
						<tr>
							<td>
								<a class="catalog_name" href="/catalog/divan<?=$arElementFields[ID] ?>.htm"><?=$arElementFields[NAME] ?></a>
							</td>
							<td id="price_new1">
								<?=$arElementFields['PROPERTY_PRICE_VALUE'] ?>
							</td>
						</tr>
					</table>
				<?if($i < 3){ ?>
					<td width="26"></td> 
				<? } ?>
	<?
				if($i == 3){
					$i = 0;
					echo "</tr>";
				}
			}
		}
	}
	?>
</table>
<div class="seo_text">
<?if($collection_id!=0){
	echo $cat_array[$collection_id]["detail_text"];
}else{?>
	<p>�� 12 ��� �������������, ������� ������ ������ &laquo;��������&raquo; ������ ��������� ����� �� ������������� ��������� �����, ������������ ������� �������� ����� ������� � ������. ������� ������� � �������� ������ � ������ ���� �������� 70 ������� ������ (�� 200 ������������ ��������). � ��������-�������� ������� ����� ���������, �������� � ������ �������, ������ � ������� ������ ��� ������ ��������� ����, � ����������� �������� ���� �� ��� �����, �������� �� ������ ��� �������. ������ ������ &laquo;��������&raquo; �� ������ ���������� (��� ������������ ������������� ������������ ������������), �� � ��������� �������� ����������� �� ���������� ����� � ��������. ������������? ��� ��������������. </p>
	<p>� ������������ ����� ������� � ������ ������������ ����������� ��������� &ndash; ������ ���� ������������ ������� ��������� �������. ���� ��� ���������� ����� ������������� ��������, ���������� � ��������. ����������� ������ �������� ��� ������ ���� ������������� � ��������, � ��� ���������� ������� � ����� ���� ��������� ������������ � �����������, ������ ������, ��������, � ������, ������� ���������.</p>
	<p>� ����� ������ �������� � ����������, ������ ������ ������� � ������ ����������: &laquo;EKKA&raquo;, &laquo;��������� � ������&raquo;, &laquo;Le Roi&raquo;, &laquo;Mix Line&raquo; (�������� ���������� ����). ��������� ����������� ����� ���������������, �������.</p>
	<p>&laquo;��������� � ������&raquo; - ���� �� ����� �������������� ��������� �� ����������� ����. ����� �� ������������ � �������� ������� ����������, �� �������, ����� ������ ��� ��� ��������, ����������. ������ ��� � ��� ���, � ������ � ��, � �������� ������, � ��������� - �� ���� ������� ������ ������� � �������. ���, ��� ��������� ��������� �������, �������� ���� � ������� �� ����� �����.</p>
	<p>��������� &laquo;����&raquo; ������� �� ������� ����������� ����������. ���� ����� ��� ������. ������ ��������� ������� &laquo;�������&raquo; � ��������� ��������, ����������� ������ ����� ����� �������� � �������� � ���� �����. &laquo;�������&raquo; ������� ������������ ������� �������, ���������������. ��� �����������, ���������� � ������������ ������ �������� ��� �������� ������������.</p>
	<p>&laquo;Mix Line&raquo; - ��������� ����� ������������� ���� � ������ � �������� ������������� ��������. �������� ������������ ������ ������ �� ������ ��� ����, ����� �������� ����, �� ���� ������� � ����������. ��� ����� ���������� ����� � ����������� ����������.</p>
	<p>&laquo;L� Roi&raquo; . ������ ������ ����� ��������� �� ��������� ����. �� ���� ���������: ��, ��� ���� ������ �����, ������ ���������� ���� �������. ������ �������� ������� ����������. ���������������, ��� ����������� ����� ���������, ����, �������. ������������ ������ ������ &laquo;Le Roi&raquo; ������ ������� � �������� ���������� ��������� ������, � ���������� ���, ��� ��������������� �� ����������� ���������� � �������������� ����������� ����������, ��� ������ � ����� ����������� � ��������������� � ����� �����.</p>
<?}?>
</div>