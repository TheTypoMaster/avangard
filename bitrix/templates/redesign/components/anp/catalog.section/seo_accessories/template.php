<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?$sid = IntVal($arResult["ITEMS"][0][IBLOCK_SECTION_ID]); ?>

<?$_POST['design_tmp'] = 'seo'; ?>
<?if ($arResult["ITEMS"][0][IBLOCK_SECTION_ID]): ?>
<table align="right" cellspacing="0" cellpadding="0">
	<tbody>
		<tr><td width="100%">
				<div class="gray_td">
					<h1><?=$arResult[NAME]?></h1>
				</div>
			</td></tr>

		<tr><td>
				<table width="738" align="center">
					<?

						$arElementSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "PROPERTY_FULLCOLOR_PIC", "PROPERTY_NOVELTY", "PROPERTY_BLACKWHITE_PIC", "PROPERTY_HIT", "PROPERTY_COLLECTION", "IBLOCK_ID");

						$arElementFilter = Array("IBLOCK_ID" => IntVal(5), "SECTION_ID" => $sid, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");

						$resElement = CIBlockElement::GetList(Array("SORT" => "ASC", "PROPERTY_PRIORITY" => "ASC"), $arElementFilter, false, Array("nPageSize" => 50), $arElementSelect);

						$i = 0;
						$rows_count= $resElement->SelectedRowsCount();
						while($obElement = $resElement->GetNextElement()){
							$i++;
							$rows_count--;
							if($i==1) 
								echo "<tr class='divan_row ".($rows_count<3 ? 'last_row' : '')."'>";
							$arElementFields = $obElement->GetFields();

							if($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE'])
								$img_path = CFile::GetPath($arElementFields['PROPERTY_FULLCOLOR_PIC_VALUE']); 
							else
								$img_path = CFile::GetPath($arElementFields['PREVIEW_PICTURE']);
							$size = getimagesize($_SERVER['DOCUMENT_ROOT'] . $img_path);
							if($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE'])
								$img_path_bl = CFile::GetPath($arElementFields['PROPERTY_BLACKWHITE_PIC_VALUE']); 
							else
								$img_path_bl = $img_path;
							?>

							<td class="catalog_td">
								<a href="/catalog/divan<?=$arElementFields[ID] ?>.htm"><img onMouseOver="this.src='<?=$img_path ?>';" onMouseOut="this.src='<?=$img_path_bl ?>';" class="catalog_picture" src="<?=$img_path_bl ?>" alt="<?=$arElementFields[NAME] ?>"></a><br>
								<a class="catalog_name" href="/catalog/divan<?=$arElementFields[ID] ?>.htm"><?=$arElementFields[NAME] ?></a>
							</td>
							<?if($i < 3){ ?>
								<td width="26"></td> 
							<? } ?>
							<?if($i == 3){$i = 0;?>
								</tr>
							<?}
						}

					?>
				</table>
			</td></tr>
	</tbody>
</table>
<?else: ?>
<table align="right" cellspacing="0" cellpadding="0">
		<tr><td width="735">
				<div class="gray_td">
					<?if ($arResult[NAME]): ?>
						<h1><?=$arResult[NAME]?></h1>
					<?else: ?>
						<h1>Пустой раздел</h1>
					<?endif ?>
				</div>
			</td>
		</tr>
</table>
<?endif ?>