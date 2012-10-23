<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("iblock")):
//*********************************************************

$IBLOCK_ID = IntVal($IBLOCK_ID);		// ID of current informational block
$ID = IntVal($ID);						// ID of current section

$bShowHeader = (($SHOW_HEADER=="Y") ? True : False);
$bShowSections = (($SHOW_SECTIONS=="Y") ? True : False);
$bShowSectionsExt = (($SHOW_SECTIONS_EXT=="Y") ? True : False);
$bShowItems = (($SHOW_ITEMS=="Y") ? True : False);

$SECTION_COLUMNS_COUNT = IntVal($SECTION_COLUMNS_COUNT);
if ($SECTION_COLUMNS_COUNT <= 0)
	$SECTION_COLUMNS_COUNT = 3;

$PRICE_TYPE_OLD = IntVal($PRICE_TYPE_OLD);
if ($PRICE_TYPE_OLD<=0)
	$PRICE_TYPE_OLD = 1;
$PRICE_TYPE_NEW = IntVal($PRICE_TYPE_NEW);
if ($PRICE_TYPE_NEW<=0)
	$PRICE_TYPE_NEW = 2;

if (strlen($LIST_PAGE_TEMPLATE)<=0)
	$LIST_PAGE_TEMPLATE = "catalog.php?BID=#IBLOCK_ID#&ID=#ID#";

$ITEMS_LIST_COUNT = IntVal($ITEMS_LIST_COUNT);
if ($ITEMS_LIST_COUNT <= 0)
	$ITEMS_LIST_COUNT = 5;

$ACTION_VALIABLE = Trim($ACTION_VALIABLE);
if (strlen($ACTION_VALIABLE)<=0)
	$ACTION_VALIABLE = "action";

$PRICE_ID_VALIABLE = Trim($PRICE_ID_VALIABLE);
if (strlen($PRICE_ID_VALIABLE)<=0)
	$PRICE_ID_VALIABLE = "PRICE_ID";

if (strlen($BASKET_PAGE_TEMPLATE)<=0)
	$BASKET_PAGE_TEMPLATE = "basket.php";

$CACHE_TIME = IntVal($CACHE_TIME);

$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

function UNI_SECTION_MakeRealPath($template, $ar)
{
	return
		str_replace("//", "/",
			str_replace("#SITE_DIR#", SITE_DIR,
				str_replace("#SERVER_NAME#", SITE_SERVER_NAME,
					str_replace("#ID#", $ar["ID"],
						str_replace("#IBLOCK_ID#", $ar["IBLOCK_ID"], $template)
					)
				)
			)
		);
}

if (($_REQUEST[$ACTION_VALIABLE] == "ADD2BASKET" || $_REQUEST[$ACTION_VALIABLE] == "BUY")
	&& IntVal($_REQUEST[$PRICE_ID_VALIABLE])>0)
{
	if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
	{
		Add2Basket($_REQUEST[$PRICE_ID_VALIABLE]);
		if ($_REQUEST[$ACTION_VALIABLE] == "BUY")
			LocalRedirect($BASKET_PAGE_TEMPLATE);
		else
			LocalRedirect(UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $ID)));
	}
}

$cache = new CPHPCache;
$cache_id = "iblock_uni_section_".$IBLOCK_ID."_".$ID."_".$SHOW_HEADER."_".$SHOW_SECTIONS."_".$SHOW_SECTIONS_EXT."_".$SHOW_ITEMS."_".$SECTION_COLUMNS_COUNT."_".$PRICE_TYPE_OLD."_".$PRICE_TYPE_NEW."_".$LIST_PAGE_TEMPLATE."_".$ITEMS_LIST_COUNT."_".$ACTION_VALIABLE."_".$PRICE_ID_VALIABLE."_".$BASKET_PAGE_TEMPLATE."_".$DETAIL_PAGE_TEMPLATE."_".SITE_ID."_".CDBResult::NavStringForCache($ITEMS_LIST_COUNT);


if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/".SITE_ID."/catalog/uni_section.php/"))
{
	extract($cache->GetVars());

	if ($bDisplayPanel)
		CIBlock::ShowPanel($IBLOCK_ID, 0, $ID, $IBLOCK_TYPE_ID);
	$GLOBALS["APPLICATION"]->SetTitle($NAME);
	for ($i = 0; $i < count($arSectionPath4Cache); $i++)
	{
		$GLOBALS["APPLICATION"]->AddChainItem($arSectionPath4Cache[$i][0], $arSectionPath4Cache[$i][1]);
	}

	$cache->Output();
}
else
{
	if ($CACHE_TIME>0)
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/".SITE_ID."/catalog/uni_section.php/");

	$arSectionPath4Cache = array();
	$arFolder = False;

	$dbIBlock = CIBlock::GetByID($IBLOCK_ID);
	if ($arIBlock = $dbIBlock->Fetch())
	{
		if (strlen($DETAIL_PAGE_TEMPLATE)<=0)
			$DETAIL_PAGE_TEMPLATE = $arIBlock["DETAIL_PAGE_URL"];

		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, 0, $ID, $arIBlock["IBLOCK_TYPE_ID"]);

		$strPath_tmp = UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => 0));
		$GLOBALS["APPLICATION"]->AddChainItem($arIBlock["NAME"], $strPath_tmp);
		$arSectionPath4Cache[] = array($arIBlock["NAME"], $strPath_tmp);

		if (IntVal($ID)>0)
		{
			$dbFolder = CIBlockSection::GetByID($ID);
			if ($arFolder = $dbFolder->Fetch())
			{
				$dbSectionPath = CIBlockSection::GetNavChain($IBLOCK_ID, $ID);
				while ($arSectionPath = $dbSectionPath->Fetch())
				{
					$strPath_tmp = UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $arSectionPath["ID"]));
					$GLOBALS["APPLICATION"]->AddChainItem($arSectionPath["NAME"], $strPath_tmp);
					$arSectionPath4Cache[] = array($arSectionPath["NAME"], $strPath_tmp);
				}
			}
		}

		$GLOBALS["APPLICATION"]->SetTitle($arFolder ? $arFolder["NAME"] : $arIBlock["NAME"]);

		if ($bShowHeader)
		{
			if ($arFolder && (strlen($arFolder["DESCRIPTION"])>0 || IntVal($arFolder["PICTURE"])>0)
				|| !$arFolder && (strlen($arIBlock["DESCRIPTION"])>0 || IntVal($arIBlock["PICTURE"])>0))
			{
				?>
				<table border="0" cellpadding="4" cellspacing="2" width="100%" style="border: 1px solid #C7DAE4;">
					<tr>
						<td width="100%" valign="top">
							<font class="text">
							<?= ShowImage(($arFolder ? $arFolder["PICTURE"] : $arIBlock["PICTURE"]), 100, 120, "hspace='5' vspace='2' align='left' border='0'", "", true);?>
							<?echo ($arFolder ? $arFolder["DESCRIPTION"] : $arIBlock["DESCRIPTION"]); ?>
							</font>
						</td>
					</tr>
				</table>
				<?
			}
		}

		if ($bShowSections || $bShowSectionsExt)
		{
			$arSections = array();

			$arFilter = array(
					"IBLOCK_ID" => $IBLOCK_ID,
					"ACTIVE" => "Y",
					"CNT_ACTIVE" => "Y",
					"SECTION_ID" => $ID
				);

			$dbSection = CIBlockSection::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter, true);
			while ($arSection = $dbSection->Fetch())
				$arSections[] = $arSection;
		}

		if ($bShowSections)
		{
			if (count($arSections)>0)
			{
				?>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" colspan="<?echo $SECTION_COLUMNS_COUNT ?>"><img src="/images/1.gif" width="1" height="10"></td>
					</tr>
					<?
					$s_style = "style=\"padding-top:5px\"";

					for ($i = 0; $i < count($arSections); $i++)
					{
						if ((($i + 1) % $SECTION_COLUMNS_COUNT)==1) echo "<tr>";
						?>
						<td align="left" valign="top"><table border="0" cellspacing="5" cellpadding="0">
							<tr>
								<td valign="top"><a href="<?= UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $arSections[$i]["ID"])) ?>"><img src="<?echo BX_PERSONAL_ROOT?>/templates/template1/images/left_bullet.gif" width="17" height="13" border="0" alt=""></a></td>
								<td valign="top"><font class="text"><a href="<?= UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $arSections[$i]["ID"])) ?>"><?echo $arSections[$i]["NAME"] ?></a>&nbsp;(<?echo $arSections[$i]["ELEMENT_CNT"] ?>)&nbsp;</font></td>
							</tr>
						</table></td>
						<?
						if ((($i + 1) % $SECTION_COLUMNS_COUNT)==0) echo "</tr>";
					}

					if ((count($arSections) % $SECTION_COLUMNS_COUNT) != 0)
					{
						for ($j = 0; $j < ($SECTION_COLUMNS_COUNT - (count($arSections) % $SECTION_COLUMNS_COUNT)); $j++)
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
			if (count($arSections)>0)
			{
				?>
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<?
					for ($i = 0; $i < count($arSections); $i++)
					{
						?>
						<tr>
							<td valign="top">
								<font class="text">
								<?= ShowImage($arSections[$i]["PICTURE"], 100, 100, "border='0' align='left' hspace='5' vspace='2' alt='".$arSections[$i]["NAME"]."'", UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $arSections[$i]["ID"])));?>
								<b><a href="<?= UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $arSections[$i]["ID"])) ?>"> <?= $arSections[$i]["NAME"]?></a></b><br><br>
								<?= $arSections[$i]["DESCRIPTION"];?>
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
			?>
			<table cellpadding="3" cellspacing="0" border="0" width="100%">
				<?
				$arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "CHECK_PERMISSIONS" => "Y", "SECTION_ID" => $ID);
				$dbItems = CIBlockElement::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
				$dbItems->NavStart($ITEMS_LIST_COUNT);
				?>
				<tr><td colspan="2" valign="top"><?= $dbItems->NavPrint(GetMessage("UNI_S_PRODUCTS")); ?></td></tr>
				<?
				while ($arItem = $dbItems->Fetch())
				{
					?>
					<tr>
						<td width="0%" valign="top" align="center">
							<?echo ShowImage($arItem["PREVIEW_PICTURE"], 100, 100, "border='0' alt='".$arItem["NAME"]."'", CIBlock::ReplaceDetailUrl($DETAIL_PAGE_TEMPLATE, $arItem, true));?>
						</td>
						<td width="100%">
							<?if (CModule::IncludeModule("catalog") && CCatalog::GetByID($IBLOCK_ID)):?>
							<div align="right">
								<table border="0" cellSpacing="1" width="150" cellPadding="0" style="border: 1px solid #C7DAE4" align="right">
									<tr>
										<td>
									<table border="0" class="tablebody" cellSpacing="0" width="100%" cellPadding="2">
										<?
										$arProduct = GetCatalogProduct($arItem["ID"]);
										$arPrice = GetCatalogProductPriceList($arItem["ID"], "SORT", "ASC");
										//print_r($arPrice);
										$bCanBuy = False;
										// Let's find indexes of #1 and #2 price types
										$indPT1 = -1;
										$indPT2 = -1;
										for ($ii = 0; $ii<count($arPrice); $ii++)
										{
											if (IntVal($arPrice[$ii]["CATALOG_GROUP_ID"])==$PRICE_TYPE_OLD)
												$indPT1 = $ii;
											if (IntVal($arPrice[$ii]["CATALOG_GROUP_ID"])==$PRICE_TYPE_NEW)
												$indPT2 = $ii;
											if ($indPT1>=0 && $indPT2>=0)
												break;
										}

										// Retail price
										if ($indPT2>=0)
										{
											if ($arPrice[$indPT2]["CAN_BUY"]=="Y" && (IntVal($arProduct["QUANTITY"])>0 || $arProduct["QUANTITY_TRACE"]!="Y")) $bCanBuy = True;
											if ($bCanBuy) $PRICE_ID = $arPrice[$indPT2]["ID"];
											if (($arPrice[$indPT1]["PRICE"]!=$arPrice[$indPT2]["PRICE"] || $arPrice[$indPT1]["CURRENCY"]!=$arPrice[$indPT2]["CURRENCY"]) && $arPrice[$indPT1]["PRICE"]>0)
											{
												?>
												<tr>
													<td><font class="text"><b><?= GetMessage("UNI_S_OLD_PRICE") ?>:</b></font></td>
													<td align="left"><font class="text">&nbsp;<s><?echo FormatCurrency($arPrice[$indPT1]["PRICE"], $arPrice[$indPT1]["CURRENCY"])?></s></font></td>
												</tr>
												<?
											}
											?>
											<tr>
												<td><font class="text"><b><?= GetMessage("UNI_S_PRICE") ?>:</b></font></td>
												<td align="left"><font class="text">&nbsp;<?echo FormatCurrency($arPrice[$indPT2]["PRICE"], $arPrice[$indPT2]["CURRENCY"])?></font></td>
											</tr>
											<?
											if (($arPrice[$indPT1]["PRICE"]!=$arPrice[$indPT2]["PRICE"] || $arPrice[$indPT1]["CURRENCY"]!=$arPrice[$indPT2]["CURRENCY"]) && $arPrice[$indPT1]["PRICE"]>0)
											{
												?>
												<tr>
													<td><font class="text"><b><?= GetMessage("UNI_S_YOU_SAVE") ?>:</b></font></td>
													<td align="left"><font class="text">&nbsp;<?echo FormatCurrency(($arPrice[$indPT1]["PRICE"]-$arPrice[$indPT2]["PRICE"]), $arPrice[$indPT2]["CURRENCY"])?></font></td>
												</tr>
												<?
											}
											?>
											<?
										}

										// Other price types
										for ($ii = 0; $ii<count($arPrice); $ii++)
										{
											if ($arPrice[$ii]["CAN_ACCESS"]=="Y" && $ii!=$indPT1 && $ii!=$indPT2)
											{
												if ($arPrice[$ii]["CAN_BUY"]=="Y" && (IntVal($arProduct["QUANTITY"])>0 || $arProduct["QUANTITY_TRACE"]!="Y")) $bCanBuy = True;
												if ($bCanBuy) $PRICE_ID = $arPrice[$ii]["ID"];
												?>
												<tr>
													<td colspan="2"><img src="/images/1.gif" alt="" width="1" height="1"></td>
												</tr>
												<tr>
													<td><font class="text"><b><?echo $arPrice[$ii]["CATALOG_GROUP_NAME"]?>:</b></font></td>
													<td align="left"><font class="text">&nbsp;<?echo FormatCurrency($arPrice[$ii]["PRICE"], $arPrice[$ii]["CURRENCY"])?></font></td>
												</tr>
												<tr>
													<td colspan="2"><img src="/images/1.gif" alt="" width="1" height="1"></td>
												</tr>
												<?
											}
										}?>
										</table>
										<table border="0" class="tablebody" cellSpacing="1" width="100%" cellPadding="0">
										<?// If current user can buy this product
										if ($bCanBuy && CModule::IncludeModule("sale"))
										{
											$strPath_tmp = UNI_SECTION_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $ID));
											?>
											<tr>
												<td valign="top"><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=ADD2BASKET"><img src="<?echo BX_PERSONAL_ROOT?>/templates/.default/images/icons/basket.gif" width="15" height="15" alt="<?= GetMessage("UNI_S_ADD2BASKET_ALT") ?>" border="0"></a></td>
												 <td valign="top"><font class="text"><nobr><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=ADD2BASKET"><?= GetMessage("UNI_S_ADD2BASKET") ?></a></nobr></font></td>
											</tr>
											<tr>
												<td><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=BUY">
														<img src="<?echo BX_PERSONAL_ROOT?>/templates/.default/images/icons/buy.gif" width="15" height="15" alt="<?= GetMessage("UNI_S_BUY_ALT") ?>" border="0"></a></td>
												 <td><font class="text"><nobr><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=BUY"><?= GetMessage("UNI_S_BUY") ?></a></nobr></font></td>
											</tr>
											<?
										}
										?>
									</table></td>
									</tr>
								</table>
							</div>
							<?endif;?>
							<font class="text">
							<a href="<?= CIBlock::ReplaceDetailUrl($DETAIL_PAGE_TEMPLATE, $arItem, true) ?>"><b><?echo $arItem["NAME"]?></b></a><br>
							<?echo $arItem["PREVIEW_TEXT"];?>
							<a href="<?= CIBlock::ReplaceDetailUrl($DETAIL_PAGE_TEMPLATE, $arItem, true) ?>"><?= GetMessage("UNI_S_MORE_INFO") ?></a><br>
							</font><br><br>
						</td>
					</tr>
					<tr><td colspan="2" valign="top"><hr></td></tr>
					<?
				}
				?>
				<tr><td colspan="2" valign="top"><?= $dbItems->NavPrint(GetMessage("UNI_S_PRODUCTS")); ?></td></tr>
			</table>
			<?
		}
	}
	else
	{
		ShowError(GetMessage("UNI_S_NO_BLOCK"));
	}

	if ($CACHE_TIME>0)
		$cache->EndDataCache(
				array(
						"IBLOCK_TYPE_ID" => ($arIBlock ? $arIBlock["IBLOCK_TYPE_ID"] : 0),
						"arSectionPath4Cache" => $arSectionPath4Cache,
						"NAME" => ($arFolder ? $arFolder["NAME"] : ($arIBlock ? $arIBlock["NAME"] : GetMessage("UNI_S_NO_BLOCK_S")))
					)
			);
}

//*********************************************************
endif;
?>