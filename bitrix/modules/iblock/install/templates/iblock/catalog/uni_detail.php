<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("iblock")):
//*********************************************************

$ID = IntVal($ID);		// ID of current product
$GID = IntVal($GID);		// ID of current section

$LIST_PAGE_TEMPLATE = Trim($LIST_PAGE_TEMPLATE);
if (strlen($LIST_PAGE_TEMPLATE)<=0)
	$LIST_PAGE_TEMPLATE = "catalog.php?BID=#IBLOCK_ID#&ID=#ID#";

$PRICE_TYPE_OLD = IntVal($PRICE_TYPE_OLD);
if ($PRICE_TYPE_OLD<=0)
	$PRICE_TYPE_OLD = 1;
$PRICE_TYPE_NEW = IntVal($PRICE_TYPE_NEW);
if ($PRICE_TYPE_NEW<=0)
	$PRICE_TYPE_NEW = 2;

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

function UNI_DETAIL_MakeRealPath($template, $ar)
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
	}
}


$cache = new CPHPCache;
$cache_id = "iblock_uni_detail_".$ID."_".$GID."_".$LIST_PAGE_TEMPLATE."_".$PRICE_TYPE_OLD."_".$PRICE_TYPE_NEW."_".$ACTION_VALIABLE."_".$PRICE_ID_VALIABLE."_".$BASKET_PAGE_TEMPLATE."_".$DETAIL_PAGE_TEMPLATE."_".SITE_ID;

if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/".SITE_ID."/catalog/uni_detail.php/"))
{
	extract($cache->GetVars());

	if ($bDisplayPanel)
		CIBlock::ShowPanel($IBLOCK_ID, $ID, $GID, $IBLOCK_TYPE_ID);
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
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/".SITE_ID."/catalog/uni_detail.php/");

	$arSectionPath4Cache = array();
	$arIBlock = False;

	$arFilter = Array(
			"ID" => $ID,
			"ACTIVE_DATE" => "Y",
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y"
		);

	$dbElement = CIBlockElement::GetList(Array(), $arFilter);
	if ($arElement = $dbElement->Fetch())
	{
		$dbIBlock = CIBlock::GetByID($arElement["IBLOCK_ID"]);
		if ($arIBlock = $dbIBlock->Fetch())
		{
			if (strlen($DETAIL_PAGE_TEMPLATE)<=0)
				$DETAIL_PAGE_TEMPLATE = $arIBlock["DETAIL_PAGE_URL"];

			$GLOBALS["APPLICATION"]->SetTitle($arElement["NAME"]);

			$strPath_tmp = UNI_DETAIL_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $arElement["IBLOCK_ID"], "ID" => 0));
			$GLOBALS["APPLICATION"]->AddChainItem($arIBlock["NAME"], $strPath_tmp);
			$arSectionPath4Cache[] = array($arIBlock["NAME"], $strPath_tmp);


			$dbSection = CIBlockElement::GetElementGroups($ID);
			$bCorrectSection = False;
			$bFirstSectionID = 0;
			while ($arSection = $dbSection->Fetch())
			{
				if (IntVal($arSection["IBLOCK_ID"])==IntVal($arElement["IBLOCK_ID"]))
				{
					if ($GID<=0)
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

			if ($bDisplayPanel)
				CIBlock::ShowPanel($arElement["IBLOCK_ID"], $ID, $GID, $arIBlock["IBLOCK_TYPE_ID"]);

			$dbSectionPath = CIBlockSection::GetNavChain($arElement["IBLOCK_ID"], $GID);
			while ($arSectionPath = $dbSectionPath->Fetch())
			{
				$strPath_tmp = UNI_DETAIL_MakeRealPath($LIST_PAGE_TEMPLATE, array("IBLOCK_ID" => $arElement["IBLOCK_ID"], "ID" => $arSectionPath["ID"]));
				$GLOBALS["APPLICATION"]->AddChainItem($arSectionPath["NAME"], $strPath_tmp);
				$arSectionPath4Cache[] = array($arSectionPath["NAME"], $strPath_tmp);
			}

			// Generate array of all product properties
			$arProps = array();
			$dbProp = CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $ID, "sort", "asc", Array("ACTIVE"=>"Y", "EMPTY"=>"N"));
			while ($arProp = $dbProp->Fetch())
			{
				$ind = ((strlen($arProp["CODE"]) > 0) ? $arProp["CODE"] : $arProp["ID"]);
				if ($arProp["PROPERTY_TYPE"]=="L")
				{
					$arProp["VALUE_ENUM_ID"] = $arProp["VALUE"];
					$arProp["VALUE"] = $arProp["VALUE_ENUM"];
				}
				if ($arProp["MULTIPLE"]=="Y")
				{
					if (isset($arProps[$ind]) && is_array($arProps[$ind]))
					{
						$arProps[$ind]["VALUE"][] = $arProp["VALUE"];
					}
					else
					{
						$arProp["VALUE"] = Array($arProp["VALUE"]);
						$arProps[$ind] = $arProp;
					}
				}
				else
				{
					$arProps[$ind] = $arProp;
				}
			}
			// $arProps - Array of all product properties

			?>
			<table border="0">
				<tr>
					<td valign="top"><?
						echo ShowImage($arElement["DETAIL_PICTURE"], 200, 200, "hspace='5' vspace='2' border='0'", "", true);
						if (isset($arProps["MORE_PHOTO"]["VALUE"]) && is_array($arProps["MORE_PHOTO"]["VALUE"]) && count($arProps["MORE_PHOTO"]["VALUE"])>0)
						{
							?><br><font class="text"><a href="#more_photo"><?= GetMessage("UNI_D_MORE_PHOTO") ?></a></font><?
						}
						?></td>
					<td width="100%" valign="top">
						<?
						/* Let's show properties of product if there are any.
							Special properties:
								MORE_PHOTO - Additional photos
								FORUM_TOPIC_ID - associated forum topic
						*/
						?>
						<?if (count($arProps)>0):?>
							<table width="100%">
								<?
								foreach ($arProps as $id => $arValue):
									if (strlen($arValue["VALUE"])<=0) continue;
									if ($arValue["CODE"]=="MORE_PHOTO") continue;
									if ($arValue["CODE"]=="FORUM_TOPIC_ID")
									{
										$GLOBALS["FORUM_TOPIC_ID"] = IntVal($arValue["VALUE"]);
										continue;
									}
									?>
									<tr>
										<td valign="top"><font class="text"><b><? echo $arValue["NAME"]?><? if(strlen($arValue["NAME"])>0)echo ":"?></b></font></td>
										<td width="100%" valign="top" align="left"><font class="text">&nbsp;<?
											if (is_array($arValue["VALUE"]))
											{
												for ($i = 0; $i < count($arValue["VALUE"]); $i++)
												{
													if ($i > 0) echo ", ";
													echo $arValue["VALUE"][$i];
												}
											}
											else
											{
												echo $arValue["VALUE"];
											}
											?></font></td>
									</tr>
									<?
								endforeach;
								?>
							</table>
						<?endif;?>
					</td>
					<?// if catalog module is installed and this infoblock is catalog then let's show prices ?>
					<?if (CModule::IncludeModule("catalog") && CCatalog::GetByID($arElement["IBLOCK_ID"])):?>
						<td valign="top" width="150">
							<table border="0" cellSpacing="1" width="150" cellPadding="0" style="border: 1px solid #C7DAE4" align="right">
									<tr>
										<td>
									<table border="0" class="tablebody" cellSpacing="0" width="100%" cellPadding="2">
										<?
										$arProduct = GetCatalogProduct($arElement["ID"]);
										$arPrice = GetCatalogProductPriceList($arElement["ID"], "SORT", "ASC");
										$bCanBuy = False;

										// Let's find indexes of #1 and #2 price types
										$indPT1 = -1;
										$indPT2 = -1;
										for ($ii = 0; $ii<count($arPrice); $ii++)
										{
											if (IntVal($arPrice[$ii]["CATALOG_GROUP_ID"])==$PRICE_TYPE_OLD) $indPT1 = $ii;
											if (IntVal($arPrice[$ii]["CATALOG_GROUP_ID"])==$PRICE_TYPE_NEW) $indPT2 = $ii;
											if ($indPT1>=0 && $indPT2>=0) break;
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
													<td><font class="text"><b><?= GetMessage("UNI_D_OLD_PRICE") ?>:</b></font></td>
													<td align="left"><font class="text">&nbsp;<s><?echo FormatCurrency($arPrice[$indPT1]["PRICE"], $arPrice[$indPT1]["CURRENCY"])?></s></font></td>
												</tr>
												<?
											}
											?>
											<tr>
												<td><font class="text"><b><?= GetMessage("UNI_D_PRICE") ?>:</b></font></td>
												<td align="left"><font class="text">&nbsp;<?echo FormatCurrency($arPrice[$indPT2]["PRICE"], $arPrice[$indPT2]["CURRENCY"])?></font></td>
											</tr>
											<?
											if (($arPrice[$indPT1]["PRICE"]!=$arPrice[$indPT2]["PRICE"] || $arPrice[$indPT1]["CURRENCY"]!=$arPrice[$indPT2]["CURRENCY"]) && $arPrice[$indPT1]["PRICE"]>0)
											{
												?>
												<tr>
													<td><font class="text"><b><?= GetMessage("UNI_D_YOU_SAVE") ?>:</b></font></td>
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
											$strPath_tmp = CIBlock::ReplaceDetailUrl($DETAIL_PAGE_TEMPLATE, $arElement, true);
											?>
											<tr>
												<td valign="top"><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=ADD2BASKET"><img src="<?echo BX_PERSONAL_ROOT?>/templates/.default/images/icons/basket.gif" width="15" height="15" alt="<?= GetMessage("UNI_D_ADD2BASKET_ALT") ?>" border="0"></a></td>
												<td valign="top"><font class="text"><nobr><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=ADD2BASKET"><?= GetMessage("UNI_D_ADD2BASKET") ?></a></nobr></font></td>
											</tr>
											<tr>
												<td><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=BUY"><img src="<?echo BX_PERSONAL_ROOT?>/templates/.default/images/icons/buy.gif" width="15" height="15" alt="<?= GetMessage("UNI_D_BUY_ALT") ?>" border="0"></a></td>
												<td><font class="text"><nobr><a href="<?= $strPath_tmp ?>&<?= $PRICE_ID_VALIABLE ?>=<?echo $PRICE_ID ?>&<?= $ACTION_VALIABLE ?>=BUY"><?= GetMessage("UNI_D_BUY") ?></a></nobr></font></td>
											</tr>
											<?
										}
										?>
									</table></td></tr>
							</table>
						</td>
					<?endif;?>
				</tr>
				<tr>
					<td colspan="3">
						<font class="text">
						<?
						echo $arElement["DETAIL_TEXT"];
						if (isset($arProps["MORE_PHOTO"]["VALUE"]) && is_array($arProps["MORE_PHOTO"]["VALUE"]))
						{
							echo "<br><br>";
							echo "<a name=\"more_photo\"></a><br>";
							foreach ($arProps["MORE_PHOTO"]["VALUE"] as $key_t => $val_t)
							{
								echo ShowImage($val_t, 200, 200, "hspace='5' vspace='2' border='0'", "", true);
								echo " &nbsp;&nbsp;&nbsp; ";
							}
						}
						?>
						</font>
					</td>
				</tr>
			</table>
			<?
		}
	}

	if ($CACHE_TIME>0)
		$cache->EndDataCache(
				array(
						"NAME" => ($arElement ? $arElement["NAME"] : GetMessage("UNI_D_NOT_FOUND")),
						"IBLOCK_TYPE_ID" => ($arIBlock ? $arIBlock["IBLOCK_TYPE_ID"] : 0),
						"arSectionPath4Cache" => $arSectionPath4Cache,
						"IBLOCK_ID" => $arElement["IBLOCK_ID"],
						"ID" => $ID,
						"GID" => $GID
					)
			);
}
//*********************************************************
endif;
?>