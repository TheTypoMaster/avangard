<?
/**************************************************************************
Component "List of linked catalog items".

This component is intended for displaying list of linked catalog items.

Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/link_element_list.php", Array(
	"LINK_IBLOCK_TYPE"			=> "catalog",
	"LINK_IBLOCK_ID"			=> "22",
	"ELEMENT_ID"				=> $_REQUEST["PARENT_ELEMENT_ID"],
	"LINK_PROPERTY_SID"			=> "PHONE_ID",
	"PAGE_LINK_ELEMENT_COUNT"	=> "50",
	"LINK_ELEMENT_SORT_FIELD"	=> "sort",
	"LINK_ELEMENT_SORT_ORDER"	=> "desc",
	"CATALOG_URL"				=> "/catalog/",
	"SECTION_URL"				=> "/catalog/accessory/section.php?",
	"LINK_PRICE_CODE"			=> "RETAIL",
	"arrPROPERTY_LINK_CODE"		=> array(),
	"BASKET_URL"				=> "/personal/basket.php"
	"CACHE_TIME"				=> "3600",
	));

Parameters:

LINK_IBLOCK_TYPE - Type of Information block of the linked catalog items
LINK_IBLOCK_ID - ID of Information block of the linked catalog items
ELEMENT_ID - ID of the "parent" element for the linked catalog items
LINK_PROPERTY_SID - mnemonic property code of the parent element which keeps linkage information
PAGE_LINK_ELEMENT_COUNT - number of linked items on one page (page navigation)
LINK_ELEMENT_SORT_FIELD - by which field the elements will be sorted, can be used the following values:

	shows - average number of element views
	sort - by sorting index
	timestamp_x - by modification date
	name - by title
	id - by element ID
	active_from - by activity date FROM
	active_to - by activity date TILL

LINK_ELEMENT_SORT_ORDER - Sorting order for information block elements, following values can be used:

		asc - in ascending order
		desc - in descending order

CATALOG_URL - starting catalog page
SECTION_URL - page with the groups of linked elements
LINK_PRICE_CODE - price type mnemonic code for the linked elements
arrPROPERTY_LINK_CODE - array of the selected mnemonic property codes for the linked elements
BASKET_URL - URL to the page with the customer's basket
CACHE_TIME - (sec.) time to cache the values selected from database

***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):

	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
					Processing of the received parameters
	*************************************************************************/

	$arrPROPERTY_LINK_CODE = is_array($arrPROPERTY_LINK_CODE) ? $arrPROPERTY_LINK_CODE : array();
	$PAGE_LINK_ELEMENT_COUNT = intval($PAGE_LINK_ELEMENT_COUNT);
	$bDisplayPanel = ($DISPLAY_PANEL == "Y") ? True : False;


	if (!(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))) $LINK_PRICE_CODE = "";

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

	$CACHE_ID = SITE_ID."|".__FILE__.md5(serialize($arParams).$USER->GetGroups().CDBResult::NavStringForCache($PAGE_LINK_ELEMENT_COUNT));
	$obCache = new CPHPCache;
	if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		$arVars = $obCache->GetVars();
		$ELEMENT_NAME	= $arVars["ELEMENT_NAME"];
	}
	else
	{
		$rsElement = CIBlockElement::GetList(Array(), Array("ID"=>IntVal($ELEMENT_ID), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CHECK_PERMISSIONS"=>"Y"), false, false, array("ID","IBLOCK_ID","NAME","DETAIL_PAGE_URL"));
		$arElement = $rsElement->GetNext();
		$ELEMENT_NAME = $arElement["NAME"];
	}

	if (strlen($ELEMENT_NAME)>0) :
		if ($bDisplayPanel)
			CIBlock::ShowPanel($LINK_IBLOCK_ID, 0, 0, $LINK_IBLOCK_TYPE);
		$APPLICATION->SetTitle(str_replace("#PHONE_NAME#", $ELEMENT_NAME, $APPLICATION->GetTitle()));

		if($obCache->StartDataCache()):

			$arrPrice = "";

			// if price type code specified
			if (strlen($LINK_PRICE_CODE)>0 && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
			{
				$arrPrice = array();
				$rsPrice = CCatalogGroup::GetList($v1, $v2, array("NAME"=>$LINK_PRICE_CODE));
				while($arPrice = $rsPrice->Fetch()):
					$arrPrice[$arPrice["NAME"]] = array("ID" => $arPrice["ID"], "TITLE" => $arPrice["NAME_LANG"]);
				endwhile;
			}

			/************************************
						Elements
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
			if (is_array($arrPrice) && count($arrPrice)>0) $arSelect[] = "CATALOG_GROUP_".$arrPrice[$LINK_PRICE_CODE]["ID"];

			// adding values to the filter
			$arrFilter["ACTIVE"] = "Y";
			$arrFilter["IBLOCK_ID"] = $LINK_IBLOCK_ID;
			$arrFilter["PROPERTY_".$LINK_PROPERTY_SID] = $ELEMENT_ID;

			$arIBlock_link = array();
			if ($rsElements_link = GetIBlockElementListEx($LINK_IBLOCK_TYPE, false, false, Array( $LINK_ELEMENT_SORT_FIELD=>$LINK_ELEMENT_SORT_ORDER, "ID"=>"DESC"), false, $arrFilter, $arSelect)):

				$rsElements_link->NavStart($PAGE_LINK_ELEMENT_COUNT);
				$count = intval($rsElements_link->SelectedRowsCount());

				/****************************************************************
										HTML form
				****************************************************************/

				?>
				<p><font class="text"><a href="<? echo $arElement["DETAIL_PAGE_URL"]; ?>"><?echo $ELEMENT_NAME?></a></font></p>
				<?

				if ($count>0):

					// element title from the information block type description, will be used for page navigation
					$arIblockType_link = CIBlockType::GetByIDLang($LINK_IBLOCK_TYPE, LANGUAGE_ID);
					$ELEMENT_LINK_NAME	= $arIblockType_link["ELEMENT_NAME"];
					?><p><?echo $rsElements_link->NavPrint($LINK_ELEMENT_NAME)?></p><?

					// information block of the linked elements
					if(!is_set($arIBlock_link, $LINK_IBLOCK_ID))
						$arIBlock_link[$LINK_IBLOCK_ID] = GetIBlock($LINK_IBLOCK_ID);

					$obElement = $rsElements_link->GetNextElement();
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
							if (is_array($arrPROPERTY_LINK_CODE) && count($arrPROPERTY_LINK_CODE)>0):
								foreach($arrPROPERTY_LINK_CODE as $pid):
									?>
									<td class="tablehead" align="center"><font class="tableheadtext"><?=$arProperty[$pid]["NAME"]?></font></td>
									<?
								endforeach;
							endif;

							// price
							if (strlen($LINK_PRICE_CODE)>0 && is_array($arrPrice) && count($arrPrice)>0):
								?>
								<td class="tablehead" align="center"><font class="tableheadtext"><?=$arrPrice[$LINK_PRICE_CODE]["TITLE"]?></font></td>
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

									?><a class="tablebodylink" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></font><br><?
								else:
									?><a class="tablebodylink" href="<?=$SECTION_URL."SECTION_ID=".$arElement["IBLOCK_SECTION_ID"]?>"><?=$arElement["NAME"]?></a></font><br><?
								endif;

								?><font class="smalltext"><?

								echo "<a href='".$CATALOG_URL."'>".$arIblockType_link["NAME"]."</a> / ";
								echo "<a href='".$arIBlock_link[$LINK_IBLOCK_ID]["LIST_PAGE_URL"]."'>".$arIBlock_link[$LINK_IBLOCK_ID]["NAME"]."</a>";

								$rsPath = GetIBlockSectionPath($arElement["IBLOCK_ID"], $arElement["IBLOCK_SECTION_ID"]);
								while($arPath = $rsPath->GetNext())
								{
									echo " / <a href='".$SECTION_URL."SECTION_ID=".$arPath["ID"]."'>".$arPath["NAME"]."</a>";
								}

								?></font></td>
								<?

								// properties
								if (is_array($arrPROPERTY_LINK_CODE) && count($arrPROPERTY_LINK_CODE)>0):
									reset($arrPROPERTY_LINK_CODE);
									foreach($arrPROPERTY_LINK_CODE as $pid):
										?>
										<td class="tablenullbody"><font class="tablebodytext"><?echo (is_array($arProperty[$pid]["VALUE"])) ? implode("<br>",$arProperty[$pid]["VALUE"]) : $arProperty[$pid]["VALUE"]?>&nbsp;</font></td>
										<?
									endforeach;
								endif;

								// price
								if (strlen($LINK_PRICE_CODE)>0 && is_array($arrPrice) && count($arrPrice)>0):

									// goods parameters
									$trace_quantity	= $arElement["CATALOG_QUANTITY_TRACE"];		// monitor quantity in stock
									$quantity		= intval($arElement["CATALOG_QUANTITY"]);	// quantity in stock
									$price_value	= $arElement["CATALOG_PRICE_".$arrPrice[$LINK_PRICE_CODE]["ID"]];		// price
									$price_currency	= $arElement["CATALOG_CURRENCY_".$arrPrice[$LINK_PRICE_CODE]["ID"]];	// currency
									$can_access		= $arElement["CATALOG_CAN_ACCESS_".$arrPrice[$LINK_PRICE_CODE]["ID"]];	// can be viewed?
									$can_buy		= $arElement["CATALOG_CAN_BUY_".$arrPrice[$LINK_PRICE_CODE]["ID"]];		// can be ordered?
									$price_id		= $arElement["CATALOG_PRICE_ID_".$arrPrice[$LINK_PRICE_CODE]["ID"]];	// goods price ID

									?>
									<td class="tablenullbody" align="right" nowrap><nobr><font class="tablebodytext"><?
									if ($can_access=="Y"):
										?><font color="red"><b><? echo FormatCurrency($price_value, $price_currency); ?></b></font><?
									endif;
									?>&nbsp;</font></nobr></td>
									<td class="tablenullbody" nowrap><font class="tablebodytext"><?
									if ($can_buy=="Y"):
										if ($trace_quantity!="Y" || ($trace_quantity=="Y" && $quantity>0)):
											?><b><a href="javascript: alert('<?=GetMessage("CATALOG_ADD_TO_BASKET_NOTIFY")?>'); window.location='<?=$APPLICATION->GetCurPageParam("price_id=".$price_id."&action=ADD_TO_BASKET&link=".$link, array("price_id", "action", "link"))?>'" class="text"><?=GetMessage("CATALOG_ADD_TO_BASKET"); ?></a></b><br>
											<b><a href="<?=$APPLICATION->GetCurPageParam("price_id=".$price_id."&action=BUY&link=".$link, array("price_id", "action", "link")) ?>" class="text"><?=GetMessage("CATALOG_BUY"); ?></a></b><?
										else:
											?><font class="smalltext"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></font><?
										endif;
									endif;
									?>&nbsp;</font></td>
								<?endif;?>
							</tr>
							<?
						} while ($obElement = $rsElements_link->GetNextElement());
									?>
								</table>
							</td>
						</tr>
					</table>
					<?

					?><p><?echo $rsElements_link->NavPrint($LINK_ELEMENT_NAME)?></p><?
				endif;

			endif; // if ($rsElements = GetIBlockElementListEx
			$obCache->EndDataCache(array(
				"ELEMENT_NAME"		=> $ELEMENT_NAME
				));
		endif; // if($obCache->StartDataCache()):
		?>
		<script language="JavaScript">
		<!--
		function OnButtonClick(id, action)
		{
			document.form_btn.id.value = id;
			document.form_btn.action.value = action;
			document.form_btn.submit();
		}
		//-->
		</script>
		<form action="<?echo $APPLICATION->GetCurPageParam("", Array("action", "id"))?>#compare_list" name="form_btn" method="POST"><input type="hidden" name="id"><input type="hidden" name="action"></form>
	<?
	else:
			ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
	endif;
endif;
?>