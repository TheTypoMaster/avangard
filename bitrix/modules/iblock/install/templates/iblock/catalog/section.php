<?
/**************************************************************************
Component "Group elements in table order".

This component is intended for displaying the elements in table order. Mainly used on the page displaying contents of some of the catalog group.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/section.php", Array(
	"IBLOCK_TYPE"			=> "catalog",
	"IBLOCK_ID"				=> "21",
	"SECTION_ID"			=> $_REQUEST["SECTION_ID"],
	"PAGE_ELEMENT_COUNT"	=> "30",
	"LINE_ELEMENT_COUNT"	=> "2",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"arrPROPERTY_CODE"		=> Array(
		"YEAR",
		"STANDBY_TIME",
		"TALKTIME",
		"WEIGHT",
		"STANDART",
		"SIZE",
		"BATTERY"
		),
	"PRICE_CODE"			=> array("RETAIL"),
	"BASKET_URL"			=> "/personal/basket.php",
	"FILTER_NAME"			=> "arrFilter",
	"CACHE_FILTER"			=> "N",
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID 
SECTION_ID - Group ID
PAGE_ELEMENT_COUNT - number of elements on the page
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

IncludeTemplateLangFile(__FILE__);

if (CModule::IncludeModule("iblock"))
{
	global $USER, $APPLICATION;

	/******************************************************/
	$ACTION_VARIABLE = Trim($ACTION_VARIABLE);
	if (StrLen($ACTION_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $ACTION_VARIABLE))
		$ACTION_VARIABLE = "action";

	$PRODUCT_ID_VARIABLE = Trim($PRODUCT_ID_VARIABLE);
	if (StrLen($PRODUCT_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $PRODUCT_ID_VARIABLE))
		$PRODUCT_ID_VARIABLE = "id";

	$SECTION_ID_VARIABLE = Trim($SECTION_ID_VARIABLE);
	if (StrLen($SECTION_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $SECTION_ID_VARIABLE))
		$SECTION_ID_VARIABLE = "SECTION_ID";

	$SECTION_ID = IntVal($SECTION_ID);
	if ($SECTION_ID <= 0)
		if (array_key_exists($SECTION_ID_VARIABLE, $_REQUEST))
			$SECTION_ID = IntVal($_REQUEST[$SECTION_ID_VARIABLE]);

	$BASKET_URL = Trim($BASKET_URL);
	if (StrLen($BASKET_URL) <= 0)
		$BASKET_URL = "basket.php";

	global $$FILTER_NAME;
	$arFilter = ${$FILTER_NAME};
	$filterIsSet = count($arFilter) > 0 ? "Y" : "N";

	$PAGE_ELEMENT_COUNT = IntVal($PAGE_ELEMENT_COUNT);
	$PAGE_ELEMENT_COUNT = (($PAGE_ELEMENT_COUNT > 0) ? $PAGE_ELEMENT_COUNT : 10);

	$LINE_ELEMENT_COUNT = IntVal($LINE_ELEMENT_COUNT);
	$LINE_ELEMENT_COUNT = (($LINE_ELEMENT_COUNT > 0) ? $LINE_ELEMENT_COUNT : 1);

	$CACHE_TIME = IntVal($CACHE_TIME);
	$CACHE_FILTER = ($CACHE_FILTER=="Y") ? "Y" : "N";
	if ($CACHE_FILTER=="N" && count($arrFilter) > 0)
		$CACHE_TIME = 0;

	$DISPLAY_PANEL = (($DISPLAY_PANEL == "Y") ? "Y" : "N");

	$SHOW_DESCRIPTION = (($SHOW_DESCRIPTION == "Y") ? "Y" : "N");

	if (!is_array($PRICE_CODE))
		$PRICE_CODE = array($PRICE_CODE);

	$arCatalogGroupCodesFilter = array();
	foreach ($PRICE_CODE as $key => $value)
	{
		$value = Trim($value);
		if (StrLen($value) > 0)
			$arCatalogGroupCodesFilter[] = $value;
	}

	$arrPROPERTY_CODE = is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array();

	$curPagePath = $APPLICATION->GetCurPageParam($SECTION_ID_VARIABLE."=".$SECTION_ID, array($PRODUCT_ID_VARIABLE, $ACTION_VARIABLE, $SECTION_ID_VARIABLE));

	$USE_PRICE_COUNT = (($USE_PRICE_COUNT == "Y") ? "Y" : "N");

	$SHOW_PRICE_COUNT = IntVal($SHOW_PRICE_COUNT);
	$SHOW_PRICE_COUNT = (($SHOW_PRICE_COUNT > 0) ? $SHOW_PRICE_COUNT : 1);


	/******************************************************/
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

	/******************************************************/
	if ($SECTION_ID > 0)
	{
		$SECTION_NAME = "";
		$IBLOCK_ID = 0;
		$IBLOCK_TYPE = "";
		$ITEM_NAME = "";

		$cacheID = __FILE__.md5(serialize($arParams).serialize($arFilter).$USER->GetGroups().CDBResult::NavStringForCache($PAGE_ELEMENT_COUNT));
		$cachePath = "/iblock/section/product/";

		$cache = new CPHPCache;
		if ($cache->InitCache($CACHE_TIME, $cacheID, $cachePath))
		{
			extract($cache->GetVars());
		}
		else
		{
			$dbSection = CIBlockSection::GetList(
				array(),
				array("ID" => $SECTION_ID)
			);
			if ($arSection = $dbSection->Fetch())
			{
				$SECTION_NAME = $arSection["NAME"];
				$IBLOCK_ID = IntVal($arSection["IBLOCK_ID"]);

				$dbIBlock = CIBlock::GetList(
					array(),
					array("ID" => $IBLOCK_ID, "LID" => SITE_ID, "ACTIVE" => "Y")
				);
				if ($arIBlock = $dbIBlock->Fetch())
				{
					$IBLOCK_TYPE = $arIBlock["IBLOCK_TYPE_ID"];

					$arIBlockType = CIBlockType::GetByIDLang($IBLOCK_TYPE, LANGUAGE_ID);
					if ($arIBlockType)
						$ITEM_NAME = $arIBlockType["ELEMENT_NAME"];
				}
			}
			else
			{
				$SECTION_ID = 0;
			}
		}

		if ($SECTION_ID > 0)
		{
			if ($bDisplayPanel)
				CIBlock::ShowPanel($IBLOCK_ID, 0, $SECTION_ID, $IBLOCK_TYPE);

			$APPLICATION->SetTitle($SECTION_NAME);
			$APPLICATION->AddChainItem($SECTION_NAME, $curPagePath);

			if ($cache->StartDataCache())
			{
				$arPriceGroups = array();
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

				$arSelect = array(
					"ID",
					"NAME", 
					"PREVIEW_PICTURE",
					"DETAIL_PICTURE",
					"DETAIL_PAGE_URL",
					"IBLOCK_ID",
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

				$arFilter["ACTIVE"] = "Y";
				$arFilter["SECTION_ID"] = $SECTION_ID;
				$arFilter["IBLOCK_ID"] = $IBLOCK_ID;
				$arFilter["IBLOCK_LID"] = SITE_ID;
				$arFilter["IBLOCK_ACTIVE"] = "Y";
				$arFilter["ACTIVE_DATE"] = "Y";
				$arFilter["ACTIVE"] = "Y";
				$arFilter["CHECK_PERMISSIONS"] = "Y";
				$arFilter["IBLOCK_TYPE"] = $IBLOCK_TYPE;

				$dbElementList = CIBlockElement::GetList(
					array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "DESC"),
					$arFilter,
					false,
					false,
					$arSelect
				);

				if ($dbElementList)
				{
					$dbElementList->NavStart($PAGE_ELEMENT_COUNT);
					echo "<p>".$dbElementList->NavPrint($ELEMENT_NAME)."</p>";

					if ($element = $dbElementList->GetNextElement())
					{
						$counter = -1;
						?>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<?
							do
							{
								$counter++;
								if (($LINE_ELEMENT_COUNT == 1) || ($counter % $LINE_ELEMENT_COUNT == 0))
									echo "<tr>";
								?>
								<td valign="top" width="<?=IntVal(100 / $LINE_ELEMENT_COUNT)?>%" style="padding-top:20px; padding-left:4px; padding-bottom:20px">
									<?
									$arElement = $element->GetFields();
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
											?></font></td>
										</tr>
										<?
										if ($USE_PRICE_COUNT == "N")
										{
											if (count($arPriceGroups) > 0 && count($arPriceGroups["view"]) > 0)
											{
												foreach ($arCatalogGroups as $key => $value)
												{
													if (in_array($key, $arPriceGroups["view"]))
													{
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
																	echo '<s>'.FormatCurrency($arElement["CATALOG_PRICE_".$key], $arElement["CATALOG_CURRENCY_".$key]).'</s> <font color="red">'.FormatCurrency($discountPrice, $arElement["CATALOG_CURRENCY_".$key]);
																else
																	echo '<font color="red">'.FormatCurrency($arElement["CATALOG_PRICE_".$key], $arElement["CATALOG_CURRENCY_".$key]); ?></b></font></font></td>
															</tr>
															<?
														}
													}
												}
											}
										}
										else
										{
											if (CModule::IncludeModule("catalog"))
											{
												$arPriceMatrix = CatalogGetPriceTableEx($arElement["ID"], 0, $arPriceGroups["view"]);
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
																				echo '<s>'.FormatCurrency($arPriceMatrix["MATRIX"][$typeID][$ind]["PRICE"], $arPriceMatrix["MATRIX"][$typeID][$ind]["CURRENCY"]).'</s> <font color="red">'.FormatCurrency($arPriceMatrix["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arPriceMatrix["MATRIX"][$typeID][$ind]["CURRENCY"]);
																			else
																				echo '<font color="red">'.FormatCurrency($arPriceMatrix["MATRIX"][$typeID][$ind]["PRICE"], $arPriceMatrix["MATRIX"][$typeID][$ind]["CURRENCY"]);
																		?></font></font></td><?
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
										}
										?>
										<tr>
											<td colspan="3" style="padding-top:5px;"><font class="text"><input class="inputbuttonflat" name="compary" type="button" value="<?=GetMessage("CATALOG_COMPARE")?>" height="20" width="20" OnClick="window.location='<?= $curPagePath ?>&id=<?= $arElement["ID"] ?>&action=ADD_TO_COMPARE_LIST'"><?
												if (count($arPriceGroups) > 0 && count($arPriceGroups["buy"]) > 0)
												{
													if ($arElement["CATALOG_QUANTITY_TRACE"] != "Y"
														|| ($arElement["CATALOG_QUANTITY_TRACE"] == "Y" && IntVal($arElement["CATALOG_QUANTITY"]) > 0))
													{
														?>&nbsp;<input class="inputbuttonflat" name="buy" type="button" value="<?= GetMessage("CATALOG_BUY") ?>" height="20" width="20" OnClick="window.location='<?= $curPagePath ?>&<?= $PRODUCT_ID_VARIABLE ?>=<?= $arElement["ID"] ?>&<?= $ACTION_VARIABLE ?>=BUY'"><?
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
						<?
					}
					else
					{
						if ($filterIsSet == "Y")
							echo ShowNote(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
					}
				}


				$cache->EndDataCache(
					array(
						"SECTION_NAME" => $SECTION_NAME,
						"IBLOCK_ID" => $IBLOCK_ID,
						"IBLOCK_TYPE" => $IBLOCK_TYPE,
						"ITEM_NAME" => $ITEM_NAME
					)
				);
			}
		}
		else
		{
			ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
			if (!defined("ERROR_404"))
				define("ERROR_404", "Y");
		}
	}
	else
	{
		ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
		if (!defined("ERROR_404"))
			define("ERROR_404", "Y");
	}
}
else
{
	ShowError(GetMessage("CATALOG_NO_INLOCK_MODULE"));
}
?>