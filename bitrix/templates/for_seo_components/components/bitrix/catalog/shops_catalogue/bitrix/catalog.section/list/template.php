<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section">
<p><?=$arResult["NAV_STRING"]?></p>
<table class="data-table" cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead>
	<tr>
		<td><?=GetMessage("CATALOG_TITLE")?></td>
		<?foreach($arResult["ITEMS"][0]["DISPLAY_PROPERTIES"] as $arProperty):?>
			<td><?=$arProperty["NAME"]?></td>
		<?endforeach;?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
			<td><?=$arPrice["TITLE"]?></td>
		<?endforeach?>
		<td>&nbsp;</td>
	</tr>
	</thead>
	<?foreach($arResult["ITEMS"] as $arElement):?>
	<tr>
		<td>
			<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
			<?if(count($arElement["SECTION"]["PATH"])>0):?>
				<br />
				<?foreach($arElement["SECTION"]["PATH"] as $arPath):?>
					/ <a href="<?=$arPath["SECTION_PAGE_URL"]?>"><?=$arPath["NAME"]?></a>
				<?endforeach?>
			<?endif?>
		</td>
		<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
		<td>
			<?if(is_array($arProperty["DISPLAY_VALUE"]))
				echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
			else
				echo $arProperty["DISPLAY_VALUE"];?>
		</td>
		<?endforeach?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
		<td>
			<?if($arPrice = $arElement["PRICES"][$code]):?>
				<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
					<s><?=$arPrice["PRINT_VALUE"]?></s><br /><span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
				<?else:?>
					<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
				<?endif?>
			<?else:?>
				&nbsp;
			<?endif;?>
		</td>
		<?endforeach;?>
		<td>
			<?if($arElement["CAN_BUY"]):?>
				<input name="buy" type="button" value="<?= GetMessage("CATALOG_BUY") ?>" OnClick="window.location='<?=$arElement["BUY_URL"]?>'" />
				&nbsp;<input name="add" type="button" value="<?= GetMessage("CATALOG_ADD") ?>" OnClick="window.location='<?=$arElement["ADD_URL"]?>'" />
			<?elseif(count($arResult["PRICES"])>0):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?endif?>&nbsp;
		</td>
	</tr>
	<?endforeach;?>
</table>
<p><?=$arResult["NAV_STRING"]?></p>
</div>
