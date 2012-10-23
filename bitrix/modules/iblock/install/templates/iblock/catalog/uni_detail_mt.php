<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("iblock"))
{
	global $USER, $APPLICATION;

	$forumTopicPropCode = "FORUM_TOPIC_ID";

	$IBLOCK_ID_VARIABLE = Trim($IBLOCK_ID_VARIABLE);
	if (StrLen($IBLOCK_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $IBLOCK_ID_VARIABLE))
		$IBLOCK_ID_VARIABLE = "BID";

	$PRODUCT_ID_VARIABLE = Trim($PRODUCT_ID_VARIABLE);
	if (StrLen($PRODUCT_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $PRODUCT_ID_VARIABLE))
		$PRODUCT_ID_VARIABLE = "ID";

	$ID = IntVal($ID);
	if ($ID <= 0)
		if (array_key_exists($PRODUCT_ID_VARIABLE, $_REQUEST))
			$ID = IntVal($_REQUEST[$PRODUCT_ID_VARIABLE]);

	$SECTION_ID_VARIABLE = Trim($SECTION_ID_VARIABLE);
	if (StrLen($SECTION_ID_VARIABLE) <= 0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $SECTION_ID_VARIABLE))
		$SECTION_ID_VARIABLE = "GID";

	$GID = IntVal($GID);
	if ($GID <= 0)
		if (array_key_exists($SECTION_ID_VARIABLE, $_REQUEST))
			$GID = IntVal($_REQUEST[$SECTION_ID_VARIABLE]);

	$LIST_PAGE_TEMPLATE = Trim($LIST_PAGE_TEMPLATE);
	if (strlen($LIST_PAGE_TEMPLATE)<=0)
		$LIST_PAGE_TEMPLATE = "catalog.php";

	$prefixListPageTemplate = ((strpos($LIST_PAGE_TEMPLATE, "?") !== false) ? "&" : "?");

	$ACTION_VARIABLE = Trim($ACTION_VARIABLE);
	if (strlen($ACTION_VARIABLE)<=0)
		$ACTION_VARIABLE = "action";

	if (strlen($BASKET_PAGE_TEMPLATE)<=0)
		$BASKET_PAGE_TEMPLATE = "basket.php";

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

	$arrPROPERTY_CODE_EXCL = (is_array($arrPROPERTY_CODE_EXCL) ? $arrPROPERTY_CODE_EXCL : array());
	if (StrLen($strPROPERTY_CODE_EXCL) > 0)
	{
		$arrPROPERTY_CODE_EXCL1 = explode(",", $strPROPERTY_CODE_EXCL);
		for ($i = 0, $cnt = count($arrPROPERTY_CODE_EXCL1); $i < $cnt; $i++)
		{
			$arrPROPERTY_CODE_EXCL1[$i] = Trim($arrPROPERTY_CODE_EXCL1[$i]);
			if (StrLen($arrPROPERTY_CODE_EXCL1[$i]) > 0)
				$arrPROPERTY_CODE_EXCL[] = $arrPROPERTY_CODE_EXCL1[$i];
		}
	}

	$curPagePath = $APPLICATION->GetCurPageParam($SECTION_ID_VARIABLE."=".$GID."&".$PRODUCT_ID_VARIABLE."=".$ID, array($SECTION_ID_VARIABLE, $PRODUCT_ID_VARIABLE, $ACTION_VARIABLE));

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

	if ($ID > 0)
	{
		$IBLOCK_ID = 0;
		$IBLOCK_TYPE = "";
		$ELEMENT_NAME = "";
		$arSectionPath4Cache = array();
		$arElement = array();
		$arProperty = array();

		$cacheID = "iblock_uni_detail_".$ID."_".$GID."_".$LIST_PAGE_TEMPLATE."_".$ACTION_VARIABLE."_".$PRODUCT_ID_VARIABLE."_".$BASKET_PAGE_TEMPLATE."_".SITE_ID;
		$cachePath = "/".SITE_ID."/iblock/uni/detail/";

		$cache = new CPHPCache;
		if ($cache->InitCache($CACHE_TIME, $cacheID, $cachePath))
		{
			extract($cache->GetVars());
		}
		else
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

			$element = false;

			$arSelect = array(
				"ID",
				"NAME",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"DETAIL_TEXT_TYPE",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_PICTURE",
				"PREVIEW_PICTURE",
				"DETAIL_TEXT",
				"PREVIEW_TEXT",
				"PROPERTY_*"
			);

			$arFilter = array(
				"ID" => $ID,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y"
			);

			if ($USE_PRICE_COUNT == "N")
			{
				foreach ($arPriceGroups["view"] as $key => $value)
				{
					$arSelect[] = "CATALOG_GROUP_".$value;
					$arFilter["CATALOG_SHOP_QUANTITY_".$value] = $SHOW_PRICE_COUNT;
				}
			}

			$dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			if ($dbElement)
			{
				if ($element = $dbElement->GetNextElement())
				{
					$arElement = $element->GetFields();
					$arProperty = $element->GetProperties();

					$KEYWORDS = $arProperty["KEYWORDS"]["VALUE"];
					$DESCRIPTION = $arProperty["DESCRIPTION"]["VALUE"];
					$ELEMENT_NAME = $arElement["NAME"];
					$IBLOCK_ID = IntVal($arElement["IBLOCK_ID"]);

					$dbIBlock = CIBlock::GetList(
						array(),
						array("ID" => $IBLOCK_ID, "LID" => SITE_ID, "ACTIVE" => "Y")
					);
					if ($arIBlock = $dbIBlock->Fetch())
					{
						$IBLOCK_TYPE = $arIBlock["IBLOCK_TYPE_ID"];

						$arSectionPath4Cache[] = array(
							$arIBlock["NAME"],
							$LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID
						);
					}

					$dbSection = CIBlockElement::GetElementGroups($ID);
					$bCorrectSection = False;
					$bFirstSectionID = 0;
					while ($arSection = $dbSection->Fetch())
					{
						if (IntVal($arSection["IBLOCK_ID"]) == $IBLOCK_ID)
						{
							if ($GID <= 0)
							{
								$bCorrectSection = True;
								$GID = IntVal($arSection["ID"]);
								break;
							}
							if ($GID == IntVal($arSection["ID"]))
							{
								$bCorrectSection = True;
								break;
							}
							if ($bFirstSectionID <= 0)
								$bFirstSectionID = IntVal($arSection["ID"]);
						}
					}
					if (!$bCorrectSection)
						$GID = $bFirstSectionID;

					if ($GID >= 0)
					{
						$dbSectionPath = CIBlockSection::GetNavChain($IBLOCK_ID, $GID);
						while ($arSectionPath = $dbSectionPath->Fetch())
							$arSectionPath4Cache[] = array(
								$arSectionPath["NAME"],
								$LIST_PAGE_TEMPLATE.$prefixListPageTemplate.$IBLOCK_ID_VARIABLE."=".$IBLOCK_ID."&".$SECTION_ID_VARIABLE."=".$arSectionPath["ID"]
							);
					}
				}
			}

			if (!$element)
				$ID = 0;
		}
	}

	if ($ID > 0)
	{
		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, $ID, $GID, $IBLOCK_TYPE);

		$APPLICATION->SetTitle($ELEMENT_NAME);

		for ($i = 0, $cnt = count($arSectionPath4Cache); $i < $cnt; $i++)
			$APPLICATION->AddChainItem($arSectionPath4Cache[$i][0], $arSectionPath4Cache[$i][1]);

		if ($cache->StartDataCache())
		{
			?>
			<table border="0">
				<tr>
					<td valign="top"><?
						$imageID = IntVal($arElement["DETAIL_PICTURE"]);
						if ($imageID <= 0)
							$imageID = IntVal($arElement["PREVIEW_PICTURE"]);
						echo ShowImage($imageID, 200, 200, "hspace='5' vspace='2' border='0'", "", true);
						?></td>
					<td valign="top">
						<?
						if (count($arProperty) > 0)
						{
							?>
							<table border="0">
								<?
								$cntPropertyCode = count($arrPROPERTY_CODE);
								$cntPropertyCodeExcl = count($arrPROPERTY_CODE_EXCL);
								foreach ($arProperty as $propID => $arPropValue)
								{
									if ($propID == $forumTopicPropCode)
										$GLOBALS["FORUM_TOPIC_ID"] = IntVal($arPropValue["VALUE"]);

									if ($arPropValue["PROPERTY_TYPE"] == "F")
										continue;

									if ($cntPropertyCode > 0)
										if (!in_array($propID, $arrPROPERTY_CODE))
											continue;

									if ($cntPropertyCodeExcl > 0)
										if (in_array($propID, $arrPROPERTY_CODE_EXCL))
											continue;
									?>
									<tr>
										<td valign="top"><font class="text"><b><?= $arPropValue["NAME"] ?><?if (strlen($arPropValue["NAME"]) > 0) echo ":"?></b></font></td>
										<td valign="top" align="left"><font class="text">&nbsp;<?
											if (is_array($arPropValue["VALUE"]))
											{
												for ($i = 0, $cnt = count($arPropValue["VALUE"]); $i < $cnt; $i++)
												{
													if ($i > 0)
														echo ", ";
													echo $arPropValue["VALUE"][$i];
												}
											}
											else
											{
												echo $arPropValue["VALUE"];
											}
											?></font></td>
									</tr>
									<?
								}
								?>
							</table>
							<?
						}
						?>
					</td>
					<td valign="top">
						<?
						if (CModule::IncludeModule("catalog"))
						{
							?>
							<table cellpadding="0" width="150" cellspacing="0" border="0">
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
												?><input class="inputbuttonflat" name="buy" type="button" value="<?= GetMessage("UNI_S_ADD2BASKET") ?>" height="20" width="20" OnClick="window.location='<?= $curPagePath ?>&<?= $ACTION_VARIABLE ?>=ADD2BASKET'">&nbsp;<input class="inputbuttonflat" name="buy" type="button" value="<?= GetMessage("UNI_S_BUY") ?>" height="20" width="20" OnClick="window.location='<?= $curPagePath ?>&<?= $ACTION_VARIABLE ?>=BUY'"><?
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
					</td>
				</tr>			
				<tr>
					<td colspan="3">
						<font class="text">
						<?
						if (StrLen($arElement["DETAIL_TEXT"]) > 0)
							echo ($arElement["DETAIL_TEXT_TYPE"]=="html") ? $arElement["DETAIL_TEXT"] : TxtToHTML($arElement["~DETAIL_TEXT"]);
						else
							echo ($arElement["PREVIEW_TEXT_TYPE"]=="html") ? $arElement["PREVIEW_TEXT"] : TxtToHTML($arElement["~PREVIEW_TEXT"]);
	
						if (count($arProperty) > 0)
						{
							$bFirstTime = True;
							$cntPropertyCode = count($arrPROPERTY_CODE);
							$cntPropertyCodeExcl = count($arrPROPERTY_CODE_EXCL);
							foreach ($arProperty as $propID => $arPropValue)
							{
								if ($arPropValue["PROPERTY_TYPE"] != "F")
									continue;

								if ($cntPropertyCode > 0)
									if (!in_array($propID, $arrPROPERTY_CODE))
										continue;

								if ($cntPropertyCodeExcl > 0)
									if (in_array($propID, $arrPROPERTY_CODE_EXCL))
										continue;

								if ($bFirstTime)
									echo "<br><br>";
								$bFirstTime = False;

								if (is_array($arPropValue["VALUE"]))
								{
									for ($i = 0, $cnt = count($arPropValue["VALUE"]); $i < $cnt; $i++)
									{
										if ($i > 0)
											echo " &nbsp;&nbsp;&nbsp; ";
										echo ShowImage($arPropValue["VALUE"][$i], 200, 200, "hspace='5' vspace='2' border='0'", "", true);
									}
								}
								else
								{
									echo ShowImage($arPropValue["VALUE"], 200, 200, "hspace='5' vspace='2' border='0'", "", true);
								}
							}
						}
						?>
						</font>
					</td>
				</tr>
			</table>
			<?
			$cache->EndDataCache(
				array(
					"IBLOCK_ID" => $IBLOCK_ID,
					"IBLOCK_TYPE" => $IBLOCK_TYPE,
					"ELEMENT_NAME" => $ELEMENT_NAME,
					"arSectionPath4Cache" => $arSectionPath4Cache,
					"arElement" => $arElement,
					"arProperty" => $arProperty,
				)
			);
		}
	}
	else
	{
		ShowError(GetMessage("CTMP_NO_PRODUCT"));
	}
}
?>