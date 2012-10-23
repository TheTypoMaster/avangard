<?
/**************************************************************************
Component "Top selected catalog items by groups".

This component is intended for displaying of the top selected catalog elements by groups in the table order. Mainly used on the catalog main page.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/sections_top.php", Array(
	"IBLOCK_TYPE"			=> "catalog",
	"IBLOCK_ID"				=> "21",
	"PARENT_SECTION_ID"		=> "0",
	"SECTION_SORT_FIELD"	=> "sort",
	"SECTION_SORT_ORDER"	=> "asc",
	"SECTION_COUNT"			=> "20",
	"SECTION_URL"			=> "/catalog/phone/section.php?",
	"ELEMENT_COUNT"			=> "6",
	"LINE_ELEMENT_COUNT"	=> "2",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"arrPROPERTY_CODE" => Array(
		"YEAR",
		"STANDBY_TIME",
		"TALKTIME",
		"WEIGHT",
		"STANDART",
		"SIZE",
		"BATTERY"
		),
	"PRICE_CODE"			=> "RETAIL",
	"BASKET_URL"			=> "/personal/basket.php",
	"FILTER_NAME"			=> "arFilter",
	"CACHE_FILTER"			=> "N",
	"CACHE_TIME"			=> "3600",
));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID
PARENT_SECTION_ID -  ID of the "parent" group
SECTION_SORT_FIELD - by which field the groups will be sorted, can be used the following values:

	sort - by sorting index
	timestamp_x - by modification date
	name - by group title
	id - by group ID
	depth_level - nesting lebvel of the group

SECTION_SORT_ORDER - Sorting order for information block groups, following values can be used:

		asc - in ascending order
		desc - in descending order
		
SECTION_COUNT - maximum number of displayed groups
SECTION_URL - URL to the page with the group contents
ELEMENT_COUNT - maximum number of elements displayed in eacg group
LINE_ELEMENT_COUNT - number of elements displayed in one table row
ELEMENT_SORT_FIELD - by which field the elements will be sorted, can be used the following values:

	shows - average number of element views
	sort - by sorting index
	timestamp_x - by modification date
	name - by title
	id - by element ID
	active_from - by activity date FROM
	active_to - by activity date TILL

ELEMENT_SORT_ORDER - Sorting order for information block elements, following values can be used:

		asc - in ascending order
		desc - in descending order	

arrPROPERTY_CODE - array of the selected mnemonic property codes
PRICE_CODE- array of price type mnemonic codes for the element
BASKET_URL - URL to the page with the customer's basket
FILTER_NAME - name of an array with the filter values
CACHE_FILTER - [Y|N] cache or not the values selected from the database if they are used for the filter
CACHE_TIME - (sec.) time to cache the values selected from database


***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):

	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Processing of the received parameters
	*************************************************************************/

	$arrPROPERTY_CODE = is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array();
	$LINE_ELEMENT_COUNT = intval($LINE_ELEMENT_COUNT);
	global $$FILTER_NAME;
	$arFilter = ${$FILTER_NAME};
	$filter_set = count($arFilter)>0 ? "Y" : "N";
	$CACHE_FILTER = ($CACHE_FILTER=="Y") ? "Y" : "N";
	if ($CACHE_FILTER=="N" && count($arFilter)>0) $CACHE_TIME = 0;
	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	if (!is_array($PRICE_CODE))
		$PRICE_CODE = array($PRICE_CODE);

	$arCatalogGroupCodesFilter = array();
	foreach ($PRICE_CODE as $key => $value)
	{
		$value = Trim($value);
		if (StrLen($value) > 0)
			$arCatalogGroupCodesFilter[] = $value;
	}

	$ACTION_VARIABLE = Trim($ACTION_VARIABLE);
	if (StrLen($ACTION_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $ACTION_VARIABLE))
		$ACTION_VARIABLE = "action";

	$PRODUCT_ID_VARIABLE = Trim($PRODUCT_ID_VARIABLE);
	if (StrLen($PRODUCT_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $PRODUCT_ID_VARIABLE))
		$PRODUCT_ID_VARIABLE = "id";

	$curPagePath = $APPLICATION->GetCurPageParam("aaa=", array($PRODUCT_ID_VARIABLE, $ACTION_VARIABLE));

	$BASKET_URL = Trim($BASKET_URL);
	if (StrLen($BASKET_URL) <= 0)
		$BASKET_URL = "basket.php";

	$SECTION_COUNT = IntVal($SECTION_COUNT);
	$SECTION_COUNT = (($SECTION_COUNT > 0) ? $SECTION_COUNT : 10);

	$LINE_ELEMENT_COUNT = IntVal($LINE_ELEMENT_COUNT);
	$LINE_ELEMENT_COUNT = (($LINE_ELEMENT_COUNT > 0) ? $LINE_ELEMENT_COUNT : 1);

	$SHOW_DESCRIPTION = (($SHOW_DESCRIPTION == "Y") ? "Y" : "N");

	$USE_PRICE_COUNT = (($USE_PRICE_COUNT == "Y") ? "Y" : "N");

	$SHOW_PRICE_COUNT = IntVal($SHOW_PRICE_COUNT);
	$SHOW_PRICE_COUNT = (($SHOW_PRICE_COUNT > 0) ? $SHOW_PRICE_COUNT : 1);

	if ($bDisplayPanel)
		CIBlock::ShowPanel($IBLOCK_ID, 0, 0, $IBLOCK_TYPE);

	/*************************************************************************
						Processing of the Buy button
	*************************************************************************/

	if (array_key_exists($ACTION_VARIABLE, $_REQUEST) && array_key_exists($PRODUCT_ID_VARIABLE, $_REQUEST))
	{
		$action = StrToUpper($_REQUEST[$ACTION_VARIABLE]);
		$productID = IntVal($_REQUEST[$PRODUCT_ID_VARIABLE]);
		if (($action == "ADD2BASKET" || $action == "BUY") && $productID > 0)
		{
			if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
			{
				if (Add2BasketByProductID($productID))
				{
					if ($action == "BUY")
						LocalRedirect($BASKET_URL);
					else
						LocalRedirect($curPagePath);
				}
				else
				{
					if ($ex = $GLOBALS["APPLICATION"]->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= GetMessage("CATALOG_ERROR2BASKET").".";
				}
			}
		}
	}

	/*************************************************************************
								Work with cache
	*************************************************************************/

	$CACHE_ID = __FILE__.md5(serialize($arParams).serialize($arFilter).$USER->GetGroups().CDBResult::NavStringForCache($ELEMENT_COUNT));
	$obCache = new CPHPCache;
	if($obCache->StartDataCache($CACHE_TIME, $CACHE_ID, "/")):

		/************************************
						Price
		************************************/

		if (CModule::IncludeModule("catalog"))
		{
			$arCatalogGroupsFilter = array();
			$arCatalogGroups = CCatalogGroup::GetListArray();
			if (count($arCatalogGroupCodesFilter) > 0)
			{
				foreach ($arCatalogGroups as $key => $value)
				{
					if (in_array($value["NAME"], $arCatalogGroupCodesFilter))
						$arCatalogGroupsFilter[] = $key;
				}
			}

			$arPriceGroups = CCatalogGroup::GetGroupsPerms($USER->GetUserGroupArray(), $arCatalogGroupsFilter);
			// Now in $arPriceGroups["view"] we've collected catalog group IDs that current user can view (filtered is nesessary)
			// and in $arPriceGroups["buy"] we've collected catalog group IDs that current user can buy (not filtered)
			// In $arCatalogGroups we have the array of all catalog groups
		}

		/************************************
						Groups
		************************************/
		$bFound = False;

		$dbSections = CIBlockSection::GetList(
			array($SECTION_SORT_FIELD => $SECTION_SORT_ORDER, "ID" => "ASC"),
			array(
				"IBLOCK_ID" => $IBLOCK_ID,
				"SECTION_ID" => $PARENT_SECTION_ID,
				"ACTIVE" => "Y",
				"IBLOCK_ACTIVE" => "Y"
			)
		);
		$dbSections->NavStart($SECTION_COUNT);
		while ($arSection = $dbSections->Fetch())
		{
			$arSelect = array(
				"ID",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"NAME", 
				"PREVIEW_PICTURE",
				"DETAIL_PICTURE",
				"DETAIL_PAGE_URL",
				"PROPERTY_*"
				);
			if ($SHOW_DESCRIPTION == "Y")
			{
				$arSelect[] = "PREVIEW_TEXT";
				$arSelect[] = "PREVIEW_TEXT_TYPE";
			}
			if ($USE_PRICE_COUNT == "N")
			{
				foreach ($arPriceGroups["view"] as $key => $value)
				{
					$arSelect[] = "CATALOG_GROUP_".$value;
					$arFilter["CATALOG_SHOP_QUANTITY_".$value] = $SHOW_PRICE_COUNT;
				}
			}

			// adding values to the filter
			$arFilter["ACTIVE"] = "Y";
			$arFilter["IBLOCK_ID"] = $IBLOCK_ID;
			$arFilter["SECTION_ID"] = $arSection["ID"];
			$arFilter["IBLOCK_LID"] = SITE_ID;
			$arFilter["IBLOCK_ACTIVE"] = "Y";
			$arFilter["ACTIVE_DATE"] = "Y";
			$arFilter["CHECK_PERMISSIONS"] = "Y";
			$arFilter["IBLOCK_TYPE"] = $IBLOCK_TYPE;

			$dbElementList = CIBlockElement::GetList(
				array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER),
				$arFilter,
				false,
				array("nTopCount" => $ELEMENT_COUNT),
				$arSelect
			);

			if ($dbElementList)
			{
				$dbElementList->NavStart($ELEMENT_COUNT);
				if ($element = $dbElementList->GetNextElement())
				{
					?>
					<a class="subtitletext" href="<?=$SECTION_URL?>SECTION_ID=<?=$arSection["ID"]?>"><?echo htmlspecialchars($arSection["NAME"])?></a>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<?
						$counter = -1;
						do
						{
							$bFound = True;
							$counter++;
							if (($LINE_ELEMENT_COUNT == 1) || ($counter % $LINE_ELEMENT_COUNT == 0))
								echo "<tr>";

							?>
							<td valign="top" width="<?=IntVal(100 / $LINE_ELEMENT_COUNT)?>%" style="padding-top:20px; padding-left:4px; padding-bottom:20px">
								<?
								$arElement = $element->GetFields();
							//echo "<pre><b>arElement:</b>\n";print_r($arElement);echo "</pre>";
								//echo "<pre>".print_r($arElement, True)."</pre>";
								if (count($arrPROPERTY_CODE) > 0)
									$arProperty = $element->GetProperties();
								?>
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td valign="top"><?
											$imageID = IntVal($arElement["PREVIEW_PICTURE"]) > 0 ? IntVal($arElement["PREVIEW_PICTURE"]) : IntVal($arElement["DETAIL_PICTURE"]);
											if ($imageID > 0)
												echo ShowImage($imageID, 150, 150, "align='left' hspace='0' vspace='0' border='0' alt='".$arElement["NAME"]."'", $arElement["DETAIL_PAGE_URL"]);
										?></td>
										<td valign="top"><img src="/bitrix/images/1.gif" width="1" height="140" border="0" alt=""></td>
										<td valign="top" style="padding-left:5px;"><font class="text"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></font><?
											if (count($arrPROPERTY_CODE) > 0)
											{
												$bNeedOpenTable = True;
												$bNeedCloseTable = False;
												foreach ($arrPROPERTY_CODE as $pid)
												{
													if (is_array($arProperty[$pid]["VALUE"]) && count($arProperty[$pid]["VALUE"]) > 0
														|| !is_array($arProperty[$pid]["VALUE"]) && strlen($arProperty[$pid]["VALUE"]) > 0)
													{
														if ($bNeedOpenTable)
														{
															?>
															<br><img src="/bitrix/images/1.gif" width="1" height="6" border="0" alt=""><br>
															<table cellpadding="1" cellspacing="0" border="0">
															<?
															$bNeedOpenTable = False;
														}
														?>
														<tr>
															<td valign="top" nowrap><font class="smalltext"><?=$arProperty[$pid]["NAME"]?>:&nbsp;</font></td>
															<td valign="top" nowrap><font class="smalltextblack"><?echo (is_array($arProperty[$pid]["VALUE"])) ? implode("<br>",$arProperty[$pid]["VALUE"]) : $arProperty[$pid]["VALUE"]?></font></td>
														</tr>
														<?
														$bNeedCloseTable = True;
													}
												}
												if ($bNeedCloseTable)
												{
													?>
													</table>
													<?
												}
											}

											if ($SHOW_DESCRIPTION == "Y")
											{
												?>
												<br><img src="/bitrix/images/1.gif" width="1" height="6" border="0" alt=""><br>
												<font class="smalltextblack"><?= ($arElement["PREVIEW_TEXT_TYPE"]=="html") ? $arElement["PREVIEW_TEXT"] : TxtToHTML($arElement["~PREVIEW_TEXT"]); ?></font>
												<?
											}
										?></td>
									</tr>
									<?
									if ($USE_PRICE_COUNT == "N")
									{
										//echo "<pre><b>arPriceGroups:</b>\n";print_r($arPriceGroups);echo "</pre>";
										if (count($arPriceGroups) > 0 && count($arPriceGroups["view"]) > 0)
										{
										//echo "<pre><b>arCatalogGroups:</b>\n";print_r($arCatalogGroups);echo "</pre>";
											foreach ($arCatalogGroups as $key => $value)
											{
										//echo "key=$key;<br>";
												if (in_array($key, $arPriceGroups["view"]))
												{
										//echo "arElement[\"CATALOG_PRICE_".$key."\"]=".$arElement["CATALOG_PRICE_".$key].";<br>";
													if (StrLen($arElement["CATALOG_PRICE_".$key]) > 0)
													{
														$arDiscounts = CCatalogDiscount::GetDiscount(
															$arElement["ID"],
															$IBLOCK_ID,
															array($key),
															$GLOBALS["USER"]->GetUserGroupArray(),
															"N",
															SITE_ID,
															false
														);
														$discountPrice = CCatalogProduct::CountPriceWithDiscount(
															$arElement["CATALOG_PRICE_".$key],
															$arElement["CATALOG_CURRENCY_".$key],
															$arDiscounts
														);
														?>
														<tr>
															<td colspan="3" style="padding-top:2px;"><font class="text"><?=$value["NAME_LANG"]?>:&nbsp;&nbsp;<b><?
															if ($discountPrice < $arElement["CATALOG_PRICE_".$key])
																echo '<s>'.FormatCurrency($arElement["CATALOG_PRICE_".$key], $arElement["CATALOG_CURRENCY_".$key]).'</s> <font color="red">'.FormatCurrency($discountPrice, $arElement["CATALOG_CURRENCY_".$key]).'</font>';
															else
																echo '<font color="red">'.FormatCurrency($arElement["CATALOG_PRICE_".$key], $arElement["CATALOG_CURRENCY_".$key]).'</font>'; ?></b></font></td>
														</tr>
														<?
													}
												}
											}
										}
									}
									else
									{
										$arPriceMatrix = CatalogGetPriceTableEx($arElement["ID"]);
										?>
										<tr>
											<td colspan="3">
												<br><img src="/bitrix/images/1.gif" width="1" height="6" border="0" alt=""><br>
												<table cellpadding="0" cellspacing="0" border="0"><tr><td class="tableborder">
												<table cellpadding="3" cellspacing="1" border="0" width="100%">
													<tr>
														<?
														if (count($arPriceMatrix["ROWS"]) > 1 || count($arPriceMatrix["ROWS"]) == 1 && ($arPriceMatrix["ROWS"][0]["QUANTITY_FROM"] > 0 || $arPriceMatrix["ROWS"][0]["QUANTITY_TO"] > 0))
														{
															?><td valign="top" nowrap class="tablebody"><font class="smalltext"><?= GetMessage("CATALOG_QUANTITY") ?></font></td><?
														}

														foreach ($arPriceMatrix["COLS"] as $typeID => $arType)
														{
															?><td valign="top" nowrap class="tablebody"><font class="smalltext"><?= $arType["NAME_LANG"] ?></font></td><?
														}
														?>
													</tr>
													<?
													foreach ($arPriceMatrix["ROWS"] as $ind => $arQuantity)
													{
														?>
														<tr>
															<?
															if (count($arPriceMatrix["ROWS"]) > 1 || count($arPriceMatrix["ROWS"]) == 1 && ($arPriceMatrix["ROWS"][0]["QUANTITY_FROM"] > 0 || $arPriceMatrix["ROWS"][0]["QUANTITY_TO"] > 0))
															{
																?>
																<td valign="top" nowrap class="tablebody"><font class="smalltext"><?
																	if (IntVal($arQuantity["QUANTITY_FROM"]) > 0 && IntVal($arQuantity["QUANTITY_TO"]) > 0)
																		echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_FROM_TO")));
																	elseif (IntVal($arQuantity["QUANTITY_FROM"]) > 0)
																		echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], GetMessage("CATALOG_QUANTITY_FROM"));
																	elseif (IntVal($arQuantity["QUANTITY_TO"]) > 0)
																		echo str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_TO"));
																?></font></td>
																<?
															}

															foreach ($arPriceMatrix["COLS"] as $typeID => $arType)
															{
																?><td valign="top" nowrap class="tablebody"><font class="smalltext"><?
																	if ($arPriceMatrix["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"] < $arPriceMatrix["MATRIX"][$typeID][$ind]["PRICE"])
																		echo '<s>'.FormatCurrency($arPriceMatrix["MATRIX"][$typeID][$ind]["PRICE"], $arPriceMatrix["MATRIX"][$typeID][$ind]["CURRENCY"]).'</s> <font color="red">'.FormatCurrency($arPriceMatrix["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arPriceMatrix["MATRIX"][$typeID][$ind]["CURRENCY"]).'</font>';
																	else
																		echo '<font color="red">'.FormatCurrency($arPriceMatrix["MATRIX"][$typeID][$ind]["PRICE"], $arPriceMatrix["MATRIX"][$typeID][$ind]["CURRENCY"]).'</font>';
																?></font></td><?
															}
															?>
														</tr>
														<?
													}
													?>
												</table>
												</td></tr></table>
											</td>
										</tr>
										<?
									}
									?>
									<tr>
										<td colspan="3" style="padding-top:5px;"><font class="text"><input class="inputbuttonflat" name="compary" type="button" value="<?=GetMessage("CATALOG_COMPARE")?>" OnClick="window.location='<?= $curPagePath ?>&amp;id=<?= $arElement["ID"] ?>&amp;action=ADD_TO_COMPARE_LIST'"><?
											if (count($arPriceGroups) > 0 && count($arPriceGroups["buy"]) > 0)
											{
												if ($arElement["CATALOG_QUANTITY_TRACE"] != "Y"
													|| ($arElement["CATALOG_QUANTITY_TRACE"] == "Y" && IntVal($arElement["CATALOG_QUANTITY"]) > 0))
												{
													?>&nbsp;<input class="inputbuttonflat" name="buy" type="button" value="<?= GetMessage("CATALOG_BUY") ?>" OnClick="window.location='<?= $curPagePath ?>&amp;<?= $PRODUCT_ID_VARIABLE ?>=<?= $arElement["ID"] ?>&amp;<?= $ACTION_VARIABLE ?>=BUY'"><?
												}
												else
												{
													?>&nbsp;&nbsp;<font class="smalltext"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></font><?
												}
											}
											?></font></td>
									</tr>
								</table>
							</td>
							<?

							if (($LINE_ELEMENT_COUNT == 1) || ($counter % $LINE_ELEMENT_COUNT == $LINE_ELEMENT_COUNT - 1))
								echo "</tr>";
						}
						while ($element = $dbElementList->GetNextElement());

						if (($LINE_ELEMENT_COUNT != 1) && ($counter % $LINE_ELEMENT_COUNT != $LINE_ELEMENT_COUNT - 1))
						{
							for ($i = $counter % $LINE_ELEMENT_COUNT; $i < $LINE_ELEMENT_COUNT - 1; $i++)
								echo "<td>&nbsp;</td>";
							echo "</tr>";
						}
						?>
					</table>
					<hr><img src="/bitrix/images/1.gif" width="1" height="10" border="0" alt=""><br>
					<?
				}
			}
		}

		if (!$bFound)
			echo ShowNote(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));

		$obCache->EndDataCache();
	endif;
endif;
?>