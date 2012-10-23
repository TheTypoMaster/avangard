<?
/***************************************************************************************
Component "List of group elements".

This component is intended for displaying the list of group elements. There is a tree of subsections for the current sections above the list. Mainly used on the page displaying contents of some of the catalog group.

Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/section_2.php", Array(
	"IBLOCK_TYPE"			=> "catalog",
	"IBLOCK_ID"				=> "22",
	"SECTION_ID"			=> $_REQUEST["SECTION_ID"],
	"PAGE_ELEMENT_COUNT"	=> "50",
	"ELEMENT_SORT_FIELD"	=> "sort",
	"ELEMENT_SORT_ORDER"	=> "asc",
	"arrPROPERTY_CODE"		=> array(),
	"PRICE_CODE"			=> "RETAIL",
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
PRICE_CODE- price type mnemonic code for the element
BASKET_URL - URL to the page with the customer's basket
FILTER_NAME - name of an array with the filter values
CACHE_FILTER - [Y|N] cache or not the values selected from the database if they are used for the filter
CACHE_TIME - (sec.) time to cache the values selected from database

***************************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):

	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
								Functions
	*************************************************************************/

	// build tree of the groups
	function ShowSectionsTree($IBLOCK_ID, $IBlockSection)
	{
		global $ID, $APPLICATION;
		$HTML="";

		$arFilter = array (
			"IBLOCK_ID"=>$IBLOCK_ID,
			"GLOBAL_ACTIVE"=>"Y",
			"LEFT_MARGIN"=>$IBlockSection["LEFT_MARGIN"]+1,
			"RIGHT_MARGIN"=>$IBlockSection["RIGHT_MARGIN"],
		);

		$rsS = CIBlockSection::GetList(Array("left_margin"=>"asc"), $arFilter, true);
		while($arS = $rsS->GetNext())
		{
			$s_url = $APPLICATION->GetCurPage()."?SECTION_ID=".$arS["ID"];

			$HTML .= '<tr valign="middle"><td><table cellpadding="0" cellspacing="0" border="0"><tr>';
			for ($i=1;$i<$arS["DEPTH_LEVEL"]-intval($IBlockSection["DEPTH_LEVEL"]);$i++)
			{
				$HTML .= '<td style="width:17px"><font class=tablebodytext>&nbsp;</font></td>';
			}
			$HTML .= '<td><img height="13" src="'.BX_PERSONAL_ROOT.'/templates/demo/images/right_down_arrow.gif" width="17" border="0"></td>';
			if ($arS["ID"] == $ID)
				$HTML.= '<td><font class="text"><div style="padding-left:3px"><b><a class="tablebodylink" href="'.$s_url.'">'.$arS["NAME"].'</a></b>&nbsp;('.$arS["ELEMENT_CNT"].')</div></font></td></tr>';
			else
				$HTML.= '<td><font class="text"><div style="padding-left:3px"><a class="tablebodylink" href="'.$s_url.'">'.$arS["NAME"].'</a>&nbsp;('.$arS["ELEMENT_CNT"].')</div></font></td></tr>';
			$HTML .= '</table></td></tr>';
		}
		return $HTML;
	}

	/*************************************************************************
						Processing of the received parameters
	*************************************************************************/

	global $$FILTER_NAME;
	$arrFilter = ${$FILTER_NAME};
	$filter_set = count($arrFilter)>0 ? "Y" : "N";
	$CACHE_FILTER = ($CACHE_FILTER=="Y") ? "Y" : "N";
	if ($CACHE_FILTER=="N" && count($arrFilter)>0) $CACHE_TIME = 0;
	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;

	$arrPROPERTY_CODE = is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array();

	if (!(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))) $PRICE_CODE = "";

	/*************************************************************************
						Processing of the Buy link
	*************************************************************************/

	if (($_REQUEST["action"] == "ADD_TO_BASKET" || $_REQUEST["action"] == "BUY") && IntVal($_REQUEST["price_id"])>0)
	{
		if ($_REQUEST["link"]=="N") $arRewriteFields = array("DETAIL_PAGE_URL"=>"");
		Add2Basket($_REQUEST["price_id"], 1, $arRewriteFields);
		if ($_REQUEST["action"] == "BUY")
			LocalRedirect($BASKET_URL);
		else
			LocalRedirect($APPLICATION->GetCurPageParam("", array("price_id", "action", "link")));
	}


	/*************************************************************************
								Work with cache
	*************************************************************************/

	$obCache = new CPHPCache;
	$CACHE_ID = __FILE__.md5(serialize($arParams).$USER->GetGroups().CDBResult::NavStringForCache($PAGE_ELEMENT_COUNT));
	if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		$arVars = $obCache->GetVars();
		$SECTION_ID		= $arVars["SECTION_ID"];
		$IBLOCK_ID		= $arVars["IBLOCK_ID"];
		$ELEMENT_NAME	= $arVars["ELEMENT_NAME"];
		$IBLOCK_TYPE	= $arVars["IBLOCK_TYPE"];
		$arrPath		= $arVars["arrPath"];
	}
	else
	{
		$arSection = GetIBlockSection($SECTION_ID);
		$SECTION_ID = $arSection["ID"];
		$IBLOCK_ID = $arSection["IBLOCK_ID"];

		$arIBlock = GetIBlock($IBLOCK_ID);
		$IBLOCK_TYPE = $arIBlock["IBLOCK_TYPE_ID"];

		$arrPath = array();
		$rsPath = GetIBlockSectionPath($IBLOCK_ID, $SECTION_ID);
		while($arPath=$rsPath->GetNext()) $arrPath[] = array("ID" => $arPath["ID"], "NAME" => $arPath["NAME"]);

		$arIblockType = CIBlockType::GetByIDLang($IBLOCK_TYPE, LANGUAGE_ID);
		$ELEMENT_NAME = $arIblockType["ELEMENT_NAME"];
	}

	if (intval($SECTION_ID) > 0):

		if ($bDisplayPanel)
			CIBlock::ShowPanel($IBLOCK_ID, 0, $SECTION_ID, $IBLOCK_TYPE);
		if (is_array($arrPath))
		{
			while(list($key, $arS) = each($arrPath))
			{
				if ($SECTION_ID==$arS["ID"]) $SECTION_NAME = $arS["NAME"];
				$APPLICATION->AddChainItem($arS["NAME"], $APPLICATION->GetCurPage()."?SECTION_ID=".$arS["ID"]);
			}
		}
		$APPLICATION->SetTitle($SECTION_NAME);

		if($obCache->StartDataCache()):

			/****************************************************************
									HTML form
			****************************************************************/

				$arrPrice = "";
				// if price type code specified
				if (strlen($PRICE_CODE)>0 && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
				{
					$arrPrice = array();
					$rsPrice = CCatalogGroup::GetList($v1, $v2, array("NAME" => $PRICE_CODE));
					while($arPrice = $rsPrice->Fetch()) $arrPrice[$arPrice["NAME"]] = array("ID" => $arPrice["ID"], "TITLE" => $arPrice["NAME_LANG"]);
				}
				else $PRICE_CODE = "";

				/************************************
							Group tree
				************************************/
				$HTML = ShowSectionsTree($IBLOCK_ID, $arSection);
				if (strlen($HTML)>0):
					?><table border="0" cellpadding="1" cellspacing="0" width="100%"><?=$HTML?></table><?
				endif;

				/************************************
						Element table
				************************************/

				// list the element fields that will be used in selection
				$arSelect = array(
					"ID",
					"IBLOCK_ID",
					"IBLOCK_SECTION_ID",
					"NAME",
					"PREVIEW_TEXT",
					"PREVIEW_TEXT_TYPE",
					"PREVIEW_PICTURE",
					"DETAIL_TEXT",
					"DETAIL_TEXT_TYPE",
					"DETAIL_PICTURE",
					"DETAIL_PAGE_URL",
					"PROPERTY_*",
					);
				if (is_array($arrPrice) && count($arrPrice)>0) $arSelect[] = "CATALOG_GROUP_".$arrPrice[$PRICE_CODE]["ID"];

				// adding values to the filter
				$arrFilter["ACTIVE"] = "Y";
				$arrFilter["SECTION_ID"] = $SECTION_ID;
				$arrFilter["INCLUDE_SUBSECTIONS"] = "Y";

				if ($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, $IBLOCK_ID, false, array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "DESC"), false, $arrFilter, $arSelect)):
					$rsElements->NavStart($PAGE_ELEMENT_COUNT);
					$count = intval($rsElements->SelectedRowsCount());

					if ($count<=0 && $filter_set=="Y"):
						echo ShowNote(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
					else:

						?><p><?echo $rsElements->NavPrint($ELEMENT_NAME)?></p><?

						if ($obElement = $rsElements->GetNextElement()):

							$arElement = $obElement->GetFields();
							$arProperty = $obElement->GetProperties();
					?>
					<table cellspacing=0 cellpadding=0 border=0 class="tableborder" width="100%">
						<tr>
							<td width="100%">
								<table cellspacing=1 cellpadding=5 border=0 width="100%">
									<tr>
										<td class="tablehead"><font class="tableheadtext"><?=GetMessage("CATALOG_TITLE")?></font></td>
										<?
										// properties
										if (is_array($arrPROPERTY_CODE) && count($arrPROPERTY_CODE)>0):
											reset($arrPROPERTY_CODE);
											foreach($arrPROPERTY_CODE as $pid):
										?>
										<td class="tablehead" align="center"><font class="tableheadtext"><?=$arProperty[$pid]["NAME"]?></font></td>
										<?
											endforeach;
										endif;
										?>
										<?
										// price
										if (strlen($PRICE_CODE)>0 && is_array($arrPrice) && count($arrPrice)>0):
										?>
										<td class="tablehead" align="center"><font class="tableheadtext"><?=$arrPrice[$PRICE_CODE]["TITLE"]?></font></td>
										<td class="tablehead"><font class="tableheadtext">&nbsp;</font></td>
										<?
										endif;
										?>
									</tr>
									<?
									do{
										$arElement = $obElement->GetFields();
										$arProperty = $obElement->GetProperties();
										$link = "N"; // flag shows if element has or not detailed description
									?>
									<tr>
										<td class="tablenullbody" width="90%"><font class="tablebodytext"><?

											if (intval($arElement["PREVIEW_PICTURE"])>0 ||
												strlen($arElement["PREVIEW_TEXT"])>0 ||
												strlen($arElement["DETAIL_TEXT"])>0 ||
												intval($arElement["PREVIEW_PICTURE"])>0):

												$link = "Y";

												?><a class="tablebodylink" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a><?

											else:

												echo $arElement["NAME"];

											endif;
											?></font></td>
										<?
										// properties
										if (is_array($arrPROPERTY_CODE) && count($arrPROPERTY_CODE)>0):
											reset($arrPROPERTY_CODE);
											foreach($arrPROPERTY_CODE as $pid):
												?>
												<td class="tablenullbody"><font class="tablebodytext"><?echo (is_array($arProperty[$pid]["VALUE"])) ? implode("<br>",$arProperty[$pid]["VALUE"]) : $arProperty[$pid]["VALUE"]?>&nbsp;</font></td>
												<?
											endforeach;
										endif;
										?>

										<?
										// price
										if (strlen($PRICE_CODE)>0 && is_array($arrPrice) && count($arrPrice)>0):

											// goods parameters
											$trace_quantity	= $arElement["CATALOG_QUANTITY_TRACE"];		// monitor quantity in stock
											$quantity		= intval($arElement["CATALOG_QUANTITY"]);	// quantity in stock
											$price_value	= $arElement["CATALOG_PRICE_".$arrPrice[$PRICE_CODE]["ID"]];		// price
											$price_currency	= $arElement["CATALOG_CURRENCY_".$arrPrice[$PRICE_CODE]["ID"]];		// currency
											$can_access		= $arElement["CATALOG_CAN_ACCESS_".$arrPrice[$PRICE_CODE]["ID"]];	// if the price can be viewed?
											$can_buy		= $arElement["CATALOG_CAN_BUY_".$arrPrice[$PRICE_CODE]["ID"]];		// if the price can be used for ordering?
											$price_id		= $arElement["CATALOG_PRICE_ID_".$arrPrice[$PRICE_CODE]["ID"]];		// goods price ID

											$arDiscounts = CCatalogDiscount::GetDiscountByProduct(
													$arElement["ID"],
													$GLOBALS["USER"]->GetUserGroupArray(),
													"N"
												); // getting discount of product

											$discountPrice = CCatalogProduct::CountPriceWithDiscount($price_value, $price_currency, $arDiscounts); // calculate price with discount
											?>
											<td class="tablenullbody" align="center"><font class="tablebodytext" nowrap><nobr><?
											if ($can_access=="Y"):

												if($discountPrice < $price_value)
													echo '<b><s>'.FormatCurrency($price_value, $price_currency).'</s><br><font color="red">'.FormatCurrency($discountPrice, $price_currency);
												else
													echo '<font color="red"><b>'.FormatCurrency($price_value, $price_currency);

											?></b></font><?
											endif;
											?>&nbsp;</font></nobr></td>
											<td class="tablenullbody" nowrap><font class="tablebodytext"><?
												if ($can_buy=="Y"):
													if ($trace_quantity!="Y" || ($trace_quantity=="Y" && $quantity>0)):
														?><b><a href="javascript: alert('<?=GetMessage("CATALOG_ADD_TO_BASKET_NOTIFY")?>'); window.location='<?=$APPLICATION->GetCurPageParam("price_id=".$price_id."&action=ADD_TO_BASKET&link=".$link, array("price_id", "action", "link"))?>'" class="text"><?=GetMessage("CATALOG_ADD_TO_BASKET"); ?></a></b><br>
														<b><a href="<?=$APPLICATION->GetCurPageParam("price_id=".$price_id. "&action=BUY&link=".$link, array("price_id", "action", "link")) ?>" class="text"><?=GetMessage("CATALOG_BUY"); ?></a></b><?
													else:
														?><font class="smalltext"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></font><?
													endif;
												endif;
											?>&nbsp;</font></td>
										<?endif;?>
									</tr>
									<?
									} while ($obElement = $rsElements->GetNextElement());
									?>
								</table>
							</td>
						</tr>
					</table>
					<p><?echo $rsElements->NavPrint($ELEMENT_NAME)?></p><?
						endif; //if ($obElement = $rsElements->GetNextElement()):
					endif;//if ($count<=0 && $filter_set=="Y"):
				endif;
			$obCache->EndDataCache(array(
				"IBLOCK_ID"		=> $IBLOCK_ID,
				"IBLOCK_TYPE"	=> $IBLOCK_TYPE,
				"SECTION_ID"	=> $SECTION_ID,
				"ELEMENT_NAME"	=> $ELEMENT_NAME,
				"arrPath"		=> $arrPath,
				));
		endif;
	else:
		ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
		@define("ERROR_404", "Y");
	endif;
endif;
?>