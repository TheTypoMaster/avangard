<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("iblock"))
{
	global $USER, $APPLICATION;

	$IBLOCK_ID_VARIABLE = Trim($IBLOCK_ID_VARIABLE);
	if (StrLen($IBLOCK_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $IBLOCK_ID_VARIABLE))
		$IBLOCK_ID_VARIABLE = "BID";

	$IBLOCK_ID = IntVal($IBLOCK_ID);
	if ($IBLOCK_ID <= 0)
		if (array_key_exists($IBLOCK_ID_VARIABLE, $_REQUEST))
			$IBLOCK_ID = IntVal($_REQUEST[$IBLOCK_ID_VARIABLE]);

	$SECTION_ID_VARIABLE = Trim($SECTION_ID_VARIABLE);
	if (StrLen($SECTION_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $SECTION_ID_VARIABLE))
		$SECTION_ID_VARIABLE = "ID";

	$ID = IntVal($ID);
	if ($ID <= 0)
		if (array_key_exists($SECTION_ID_VARIABLE, $_REQUEST))
			$ID = IntVal($_REQUEST[$SECTION_ID_VARIABLE]);

	$bShowHeader = (($SHOW_HEADER == "Y") ? True : False);
	$bShowSections = (($SHOW_SECTIONS == "Y") ? True : False);
	$bShowSectionsExt = (($SHOW_SECTIONS_EXT == "Y") ? True : False);
	$bShowItems = (($SHOW_ITEMS == "Y") ? True : False);

	$SECTION_COLUMNS_COUNT = IntVal($SECTION_COLUMNS_COUNT);
	if ($SECTION_COLUMNS_COUNT <= 0)
		$SECTION_COLUMNS_COUNT = 3;

	if (strlen($LIST_PAGE_TEMPLATE) <= 0)
		$LIST_PAGE_TEMPLATE = "catalog.php";

	$prefixListPageTemplate = ((strpos($LIST_PAGE_TEMPLATE, "?") !== false) ? "&" : "?");

	$ITEMS_LIST_COUNT = IntVal($ITEMS_LIST_COUNT);
	if ($ITEMS_LIST_COUNT <= 0)
		$ITEMS_LIST_COUNT = 5;

	$ACTION_VARIABLE = Trim($ACTION_VARIABLE);
	if (StrLen($ACTION_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $ACTION_VARIABLE))
		$ACTION_VARIABLE = "action";

	$PRODUCT_ID_VARIABLE = Trim($PRODUCT_ID_VARIABLE);
	if (StrLen($PRODUCT_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $PRODUCT_ID_VARIABLE))
		$PRODUCT_ID_VARIABLE = "PRODUCT_ID";

	if (strlen($BASKET_PAGE_TEMPLATE) <= 0)
		$BASKET_PAGE_TEMPLATE = "/personal/basket.php";

	$CACHE_TIME = IntVal($CACHE_TIME);

	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	$USE_PRICE_COUNT = (($USE_PRICE_COUNT == "Y") ? "Y" : "N");

	$SHOW_PRICE_COUNT = IntVal($SHOW_PRICE_COUNT);
	$SHOW_PRICE_COUNT = (($SHOW_PRICE_COUNT > 0) ? $SHOW_PRICE_COUNT : 1);

	if (!is_array($PRICE_CODE))
		$PRICE_CODE = array($PRICE_CODE);

	$arCatalogGroupCodesFilter = array();
	foreach ($PRICE_CODE as $key => $value)
	{
		$value = Trim($value);
		if (StrLen($value) > 0)
			$arCatalogGroupCodesFilter[] = $value;
	}

	$arrPROPERTY_CODE = (is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array());
	if (StrLen($strPROPERTY_CODE) > 0)
	{
		$arrPROPERTY_CODE1 = explode(",", $strPROPERTY_CODE);
		for ($i = 0, $cnt = count($arrPROPERTY_CODE1); $i < $cnt; $i++)
		{
			$arrPROPERTY_CODE1[$i] = Trim($arrPROPERTY_CODE1[$i]);
			if (StrLen($arrPROPERTY_CODE1[$i]) > 0)
				$arrPROPERTY_CODE[] = $arrPROPERTY_CODE1[$i];
		}
	}

	$curPagePath = $APPLICATION->GetCurPageParam($IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$ID, array($IBLOCK_ID_VARIABLE, $SECTION_ID_VARIABLE, $PRODUCT_ID_VARIABLE, $ACTION_VARIABLE));


	/***************************************************************************/
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
						LocalRedirect($BASKET_PAGE_TEMPLATE);
					else
						LocalRedirect($curPagePath);
				}
				else
				{
					if ($ex = $GLOBALS["APPLICATION"]->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= GetMessage("CTMP_CANT_BASKET").".";
				}
			}
		}
	}
	/***************************************************************************/

	if ($IBLOCK_ID > 0)
	{
		$SECTION_NAME = "";
		$IBLOCK_TYPE = "";
		$ITEM_NAME = "";
		$arSectionPath4Cache = array();

		$cacheID = "iblock_uni_section_".$IBLOCK_ID."_".$ID."_".$SHOW_HEADER."_".$SHOW_SECTIONS."_".$SHOW_SECTIONS_EXT."_".$SHOW_ITEMS."_".$SECTION_COLUMNS_COUNT."_".$LIST_PAGE_TEMPLATE."_".$ITEMS_LIST_COUNT."_".$ACTION_VARIABLE."_".$PRODUCT_ID_VARIABLE."_".$BASKET_PAGE_TEMPLATE."_".$DETAIL_PAGE_TEMPLATE."_".SITE_ID."_".CDBResult::NavStringForCache($ITEMS_LIST_COUNT);
		$cachePath = "/".SITE_ID."/iblock/uni/section/";

		$cache = new CPHPCache;
		if ($cache->InitCache($CACHE_TIME, $cacheID, $cachePath))
		{
			extract($cache->GetVars());
		}
		else
		{
			$arSection = false;
			if ($ID > 0)
			{
				$dbSection = CIBlockSection::GetList(
					array(),
					array("ID" => $ID)
				);
				if ($arSection = $dbSection->Fetch())
				{
					$SECTION_NAME = $arSection["NAME"];
					$IBLOCK_ID = IntVal($arSection["IBLOCK_ID"]);
				}
				else
				{
					$ID = 0;
				}
			}

			$dbIBlock = CIBlock::GetList(
				array(),
				array("ID" => $IBLOCK_ID, "LID" => SITE_ID, "ACTIVE" => "Y")
			);
			if ($arIBlock = $dbIBlock->Fetch())
			{
				$IBLOCK_TYPE = $arIBlock["IBLOCK_TYPE_ID"];
				if (StrLen($SECTION_NAME) <= 0)
					$SECTION_NAME = $arIBlock["NAME"];

				$arSectionPath4Cache[] = array(
					$arIBlock["NAME"],
					$LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID
				);

				$arIBlockType = CIBlockType::GetByIDLang($IBLOCK_TYPE, LANGUAGE_ID);
				if ($arIBlockType)
					$ITEM_NAME = $arIBlockType["ELEMENT_NAME"];
			}
			else
			{
				$IBLOCK_ID = 0;
			}

			if ($ID > 0)
			{
				$dbSectionPath = CIBlockSection::GetNavChain($IBLOCK_ID, $ID);
				while ($arSectionPath = $dbSectionPath->Fetch())
					$arSectionPath4Cache[] = array(
						$arSectionPath["NAME"],
						$LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$arSectionPath["ID"]
					);
			}
		}
	}

	if ($IBLOCK_ID > 0)
	{
		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, 0, $ID, $IBLOCK_TYPE);

		$APPLICATION->SetTitle($SECTION_NAME);

		for ($i = 0, $cnt = count($arSectionPath4Cache); $i < $cnt; $i++)
			$APPLICATION->AddChainItem($arSectionPath4Cache[$i][0], $arSectionPath4Cache[$i][1]);

		if ($cache->StartDataCache())
		{
			if ($bShowHeader)
			{
				if (($ID > 0) && (StrLen($arSection["DESCRIPTION"]) > 0 || IntVal($arSection["PICTURE"]) > 0)
					|| ($ID <= 0) && (StrLen($arIBlock["DESCRIPTION"])>0 || IntVal($arIBlock["PICTURE"]) > 0))
				{
					?>
					<table border="0" cellpadding="4" cellspacing="2" width="100%" style="border: 1px solid #C7DAE4;">
						<tr>
							<td width="100%" valign="top">
								<font class="text">
								<?= ShowImage((($ID > 0) ? $arSection["PICTURE"] : $arIBlock["PICTURE"]), 100, 120, "hspace='5' vspace='2' align='left' border='0'", "", true);?>
								<?echo (($ID > 0) ? $arSection["DESCRIPTION"] : $arIBlock["DESCRIPTION"]); ?>
								</font>
							</td>
						</tr>
					</table>
					<?
				}
			}

			if ($bShowSections || $bShowSectionsExt)
			{
				$arSubSections = array();

				$arFilter = array(
					"IBLOCK_ID" => $IBLOCK_ID,
					"ACTIVE" => "Y",
					"CNT_ACTIVE" => "Y",
					"SECTION_ID" => $ID
				);

				$dbSubSection = CIBlockSection::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter, true);
				while ($arSubSection = $dbSubSection->Fetch())
					$arSubSections[] = $arSubSection;
			}

			if ($bShowSections)
			{
				if (count($arSubSections)>0)
				{
					?>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%" colspan="<?echo $SECTION_COLUMNS_COUNT ?>"><img src="/images/1.gif" width="1" height="10"></td>
						</tr>
						<?
						$s_style = "style=\"padding-top:5px\"";

						for ($i = 0; $i < count($arSubSections); $i++)
						{
							if ((($i + 1) % $SECTION_COLUMNS_COUNT)==1) echo "<tr>";
							?>
							<td align="left" valign="top"><table border="0" cellspacing="5" cellpadding="0">
								<tr>
									<td valign="top"><a href="<?= $LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$arSubSections[$i]["ID"] ?>"><img src="/bitrix/templates/template1/images/left_bullet.gif" width="17" height="13" border="0" alt=""></a></td>
									<td valign="top"><font class="text"><a href="<?= $LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$arSubSections[$i]["ID"] ?>"><?echo $arSubSections[$i]["NAME"] ?></a>&nbsp;(<?echo $arSubSections[$i]["ELEMENT_CNT"] ?>)&nbsp;</font></td>
								</tr>
							</table></td>
							<?
							if ((($i + 1) % $SECTION_COLUMNS_COUNT)==0) echo "</tr>";
						}

						if ((count($arSubSections) % $SECTION_COLUMNS_COUNT) != 0)
						{
							for ($j = 0; $j < ($SECTION_COLUMNS_COUNT - (count($arSubSections) % $SECTION_COLUMNS_COUNT)); $j++)
							{
								?><td>&nbsp;</td><?
							}
							?></tr><?
						}
						?>
						<tr>
							<td width="100%" colspan="<?echo $SECTION_COLUMNS_COUNT ?>"><img src="/images/1.gif" width="1" height="10"></td>
						</tr>
					</table>
					<?
				}
			}
			?>

			<img src="/images/1.gif" width="1" height="10">

			<?
			if ($bShowSectionsExt)
			{
				if (count($arSubSections)>0)
				{
					?>
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<?
						for ($i = 0; $i < count($arSubSections); $i++)
						{
							?>
							<tr>
								<td valign="top">
									<font class="text">
									<?= ShowImage($arSubSections[$i]["PICTURE"], 100, 100, "border='0' align='left' hspace='5' vspace='2' alt='".$arSubSections[$i]["NAME"]."'", $LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$arSubSections[$i]["ID"]);?>
									<b><a href="<?= $LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$arSubSections[$i]["ID"] ?>"> <?= $arSubSections[$i]["NAME"]?></a></b><br><br>
									<?= $arSubSections[$i]["DESCRIPTION"];?>
									</font>
								</td>
							</tr>
							<tr><td><hr></td></tr>
							<?
						}
						?>
					</table>
					<br><br>
					<?
				}
			}
			?>

			<?
			if ($bShowItems)
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
					"PREVIEW_TEXT",
					"PREVIEW_TEXT_TYPE",
					"PROPERTY_*"
				);
				if ($USE_PRICE_COUNT == "N")
				{
					foreach ($arPriceGroups["view"] as $key => $value)
					{
						$arSelect[] = "CATALOG_GROUP_".$value;
						$arFilter["CATALOG_SHOP_QUANTITY_".$value] = $SHOW_PRICE_COUNT;
					}
				}

				$arFilter["ACTIVE"] = "Y";
				$arFilter["SECTION_ID"] = $ID;
				$arFilter["IBLOCK_ID"] = $IBLOCK_ID;
				$arFilter["IBLOCK_LID"] = SITE_ID;
				$arFilter["IBLOCK_ACTIVE"] = "Y";
				$arFilter["ACTIVE_DATE"] = "Y";
				$arFilter["ACTIVE"] = "Y";
				$arFilter["CHECK_PERMISSIONS"] = "Y";

				$dbElementList = CIBlockElement::GetList(
					array("SORT"=>"ASC", "NAME"=>"ASC"),
					$arFilter,
					false,
					false,
					$arSelect
				);
				if ($dbElementList)
				{
					$dbElementList->NavStart($ITEMS_LIST_COUNT);
					echo "<p>".$dbElementList->NavPrint($ELEMENT_NAME)."</p>";

					if ($element = $dbElementList->GetNextElement())
					{
						?>
						<table cellpadding="3" cellspacing="0" border="0" width="100%">
							<?
							do
							{
								$arElement = $element->GetFields();
								if (count($arrPROPERTY_CODE) > 0)
									$arProperty = $element->GetProperties();
								?>
								<tr>
									<td width="0%" valign="top" align="center">
										<?= ShowImage($arElement["PREVIEW_PICTURE"], 100, 100, "border='0' alt='".$arElement["NAME"]."'", $arElement["DETAIL_PAGE_URL"]);?>
									</td>
									<td width="100%">
										<?
										if (CModule::IncludeModule("catalog"))
										{
											?>
											<div align="right">
											<table cellpadding="0" width="150" cellspacing="0" border="0" align="right">
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
																	<td style="padding-top:2px;"><font class="text"><?=$value["NAME_LANG"]?>:&nbsp;&nbsp;<b><?
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
												$arPriceMatrix = CatalogGetPriceTableEx($arElement["ID"], 0, $arPriceGroups["view"]);
												?>
												<tr><td>
												<table cellpadding="0" cellspacing="0" border="0"><tr><td class="tableborder">
												<table cellpadding="3" cellspacing="1" border="0" width="100%">
												<tr>
													<?
													if (count($arPriceMatrix["ROWS"]) > 1 || count($arPriceMatrix["ROWS"]) == 1 && ($arPriceMatrix["ROWS"][0]["QUANTITY_FROM"] > 0 || $arPriceMatrix["ROWS"][0]["QUANTITY_TO"] > 0))
													{
														?><td valign="top" nowrap class="tablebody"><font class="smalltext"><?= GetMessage("CTMP_QUANTITY") ?></font></td><?
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
																	echo str_replace("#NUM_FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#NUM_TO#", $arQuantity["QUANTITY_TO"], GetMessage("CTMP_NUM_FROM_TO")));
																elseif (IntVal($arQuantity["QUANTITY_FROM"]) > 0)
																	echo str_replace("#NUM#", $arQuantity["QUANTITY_FROM"], GetMessage("CTMP_NUM_MORE"));
																elseif (IntVal($arQuantity["QUANTITY_TO"]) > 0)
																	echo str_replace("#NUM#", $arQuantity["QUANTITY_TO"], GetMessage("CTMP_TILL_NUM"));
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
												<?
											}

											if (CModule::IncludeModule("sale"))
											{
												?>
												<tr>
													<td style="padding-top:5px;"><font class="text"><?
														if (count($arPriceGroups) > 0 && count($arPriceGroups["buy"]) > 0)
														{
															if (
																($USE_PRICE_COUNT == "N") && ($arElement["CATALOG_QUANTITY_TRACE"] != "Y" || ($arElement["CATALOG_QUANTITY_TRACE"] == "Y" && IntVal($arElement["CATALOG_QUANTITY"]) > 0))
																||
																($USE_PRICE_COUNT != "N") && ($arPriceMatrix["AVAILABLE"] == "Y")
																)
															{
																?><input class="inputbuttonflat" name="buy" type="button" value="<?= GetMessage("UNI_S_ADD2BASKET") ?>" height="20" width="20" OnClick="window.location='<?= $curPagePath ?>&<?= $PRODUCT_ID_VARIABLE ?>=<?= $arElement["ID"] ?>&<?= $ACTION_VARIABLE ?>=ADD2BASKET'">&nbsp;<input class="inputbuttonflat" name="buy" type="button" value="<?= GetMessage("UNI_S_BUY") ?>" height="20" width="20" OnClick="window.location='<?= $curPagePath ?>&<?= $PRODUCT_ID_VARIABLE ?>=<?= $arElement["ID"] ?>&<?= $ACTION_VARIABLE ?>=BUY'"><?
															}
															else
															{
																?>&nbsp;&nbsp;<font class="smalltext"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></font><?
															}
														}
														?></font></td>
												</tr>
												<?
											}
											?>
											</table>
											</div>
											<?
										}
										?>

										<font class="text">
										<a href="<?= $arElement["DETAIL_PAGE_URL"] ?>"><b><?echo $arElement["NAME"]?></b></a><br>
										<?
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
										?>
										<br>
										<?echo $arElement["PREVIEW_TEXT"];?>
										<a href="<?= $arElement["DETAIL_PAGE_URL"] ?>"><?= GetMessage("UNI_S_MORE_INFO") ?></a><br>
										</font><br><br>

									</td>
								</tr>
								<tr><td colspan="2" valign="top"><hr></td></tr>
								<?
							}
							while ($element = $dbElementList->GetNextElement());
							?>
						</table>
						<?
					}
					echo "<p>".$dbElementList->NavPrint($ELEMENT_NAME)."</p>";
				}
			}

			$cache->EndDataCache(
				array(
					"SECTION_NAME" => $SECTION_NAME,
					"IBLOCK_TYPE" => $IBLOCK_TYPE,
					"ITEM_NAME" => $ITEM_NAME,
					"arSectionPath4Cache" => $arSectionPath4Cache
				)
			);
		}
	}
	else
	{
		ShowError(GetMessage("UNI_S_NO_BLOCK"));
	}
}
?>