<?
/**************************************************************************
Component "Table of comparance for the catalog elements"

This component is intended for displaying the table of catalog elements that are being compared. Enables to remove element from the list or go to order page fopr the chosen element.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/compare_table.php", Array(
	"IBLOCK_TYPE"			=> "catalog",
	"IBLOCK_ID"				=> "21",
	"NAME"					=> "CATALOG_COMPARE_LIST",
	"ELEMENT_SORT_FIELD"	=> "shows",
	"ELEMENT_SORT_ORDER"	=> "desc",
	"arrFIELD_CODE"			=> array(
		"NAME",
		"PREVIEW_PICTURE",
		),					
	"arrPROPERTY_CODE"		=> array(
		"WEIGHT",
		"SIZE",
		"STANDART", 
		"STANDBY_TIME",
		"TALKTIME",
		"BATTERY",
		"VIBRO",
		"VOICE",
		"PC",
		"WAP",
		),
	"arrPRICE_CODE"			=> array("RETAIL"),
	"BASKET_URL"			=> "/personal/basket.php",
	"CACHE_TIME"			=> "3600",
	));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID 
NAME - unique identifier of the Compare list
ELEMENT_SORT_FIELD - Field for sorting the elements, following values can be used:

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

arrFIELD_CODE - array of the field identifiers, the following values can be used:

	ID - Element ID
	NAME - Element name
	DATE_ACTIVE_FROM - activity date FROM
	DATE_ACTIVE_TO - activity date TILL
	DETAIL_PICTURE - picture for the element detailed description
	DETAIL_TEXT - text for the element detailed description
	PREVIEW_PICTURE - picture for the element anounce
	PREVIEW_TEXT - text for the element anounce

arrPROPERTY_CODE - array of selected proiperties mnemonic codes
arrPRICE_CODE - array of the price types mnemonic codes
BASKET_URL - URL that leads to the page with the customer's basket
	CACHE_TIME - (sec.) time for caching (0 - do not cache)


***************************************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):
	
	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Processing of the received parameters
	*************************************************************************/

	$arrFIELD_CODE = is_array($arrFIELD_CODE) ? $arrFIELD_CODE : array();
	$arrPROPERTY_CODE = is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array();

	if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")) 
		$arrPRICE_CODE = is_array($arrPRICE_CODE) ? $arrPRICE_CODE : array();
	else 
		$arrPRICE_CODE = array();

	/*************************************************************************
		Processing of the "Add to the Compare table", "Remove from the Compare table"
	*************************************************************************/

	if (intval($_REQUEST["id"])>0) $_REQUEST["ID"][] = $_REQUEST["id"];
	if (is_array($_REQUEST["ID"]) && count($_REQUEST["ID"])>0)
	{
		$action = $_REQUEST["action"];
		if (strlen($_REQUEST["DELETE_FROM_COMPARE_LIST"])>0) $action = "DELETE_FROM_COMPARE_LIST";		
		foreach($_REQUEST["ID"] as $id)
		{
			switch($action)
			{
				case "COMPARE":
					$_SESSION[$NAME][$IBLOCK_ID][$id] = $id;
					break;
				case "DELETE_FROM_COMPARE_LIST":
					unset($_SESSION[$NAME][$IBLOCK_ID][$id]);
					break;
			}
		}
	}

	/*************************************************************************
				Processing of the links "Buy" and "Add to basket"
	*************************************************************************/

	if (($_REQUEST["action"] == "ADD_TO_BASKET" || $_REQUEST["action"] == "BUY") && IntVal($_REQUEST["price_id"])>0)
	{
		Add2Basket($_REQUEST["price_id"]);
		if ($_REQUEST["action"] == "BUY")
			LocalRedirect($BASKET_URL);
		else
			LocalRedirect($APPLICATION->GetCurPageParam("", array("price_id", "action")));
	}

	$arrCompareList = $_SESSION[$NAME][$IBLOCK_ID]; // array for storing IDs of the lements taht are being compared 
	if (is_array($arrCompareList) && count($arrCompareList)>0) :

		/*************************************************************************
									Work with cache
		*************************************************************************/

		$CACHE_ID = SITE_ID."|".__FILE__."|".md5(serialize($arParams)."|".serialize($arrCompareList)."|".$USER->GetGroups());
		$obCache = new CPHPCache;
		if($obCache->StartDataCache($CACHE_TIME, $CACHE_ID, "/")):

			$arSelect = array_unique(array_merge(array("ID","IBLOCK_ID","NAME","DETAIL_PAGE_URL"), $arrFIELD_CODE));

			// initializing of the filter array
			foreach($arrCompareList as $eid) $aFilter["ID"][] = $eid;

			// properties
			if (count($arrPROPERTY_CODE)>0)
			{
				$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
				while ($arProp = $rsProp->Fetch())
				{
					if (in_array($arProp["CODE"],$arrPROPERTY_CODE) && in_array($arProp["PROPERTY_TYPE"], array("L", "N", "S")))
					{
						$arrProp[$arProp["CODE"]] = $arProp["NAME"];
					}
				}
			}

			// prices
			if (count($arrPRICE_CODE)>0 && CModule::IncludeModule("catalog"))
			{
				$rsPrice = CCatalogGroup::GetList($v1, $v2);
				while($arPrice = $rsPrice->Fetch())
				{
					if (in_array($arPrice["NAME"],$arrPRICE_CODE)) 
					{
						$arrPrice[$arPrice["NAME"]] = array("ID"=>$arPrice["ID"], "TITLE"=>$arPrice["NAME_LANG"]);
						$arSelect[] = "CATALOG_GROUP_".$arPrice["ID"];
					}
				}
			}
			// getting selection from elements
			if ($rsElements = GetIBlockElementListEx($IBLOCK_TYPE, false, false, array($ELEMENT_SORT_FIELD => $ELEMENT_SORT_ORDER, "ID" => "DESC"), false, $aFilter, $arSelect)) :

				/****************************************************************
										HTML form
				****************************************************************/
				
				?>
				<script language="JavaScript">
				<!--

				function checkUncheckAll(Element) 
				{
					var theForm = Element.form, i = 0;
					for(i=0; i<theForm.length; i++)
					{
						if(theForm[i].type == 'checkbox' && theForm[i].name != 'selectall')
							theForm[i].checked = Element.checked;
					}
				}
				//-->
				</script>
				<a name="compare_table"></a>
				<?=ShowNote($strNote)?>
				<?=ShowError($strError)?>
				<form action="<?=$APPLICATION->GetCurPage()?>" method="GET" name="form_compare_table">
				<font class="text"><input type="submit" class="inputbuttonflat" value="<?=GetMessage("CATALOG_COMPARE_DELETE")?>" name="DELETE_FROM_COMPARE_LIST"></font><br><br>
				<table cellspacing="0" cellpadding="0" class="tableborder">
					<tr>
						<td><table cellspacing=1 cellpadding=4 width="100%">
								<tr>
									<td class="tablehead" valign="top"><input type="checkbox" name="selectall" value="Y" onclick="checkUncheckAll(this)"></td>

									<?
									// fields
									reset($arrFIELD_CODE);
									foreach($arrFIELD_CODE as $code):
										?><td class="tablehead" valign="top"><font class="tablebodytext"><?echo GetMessage("CATALOG_COMPARE_".$code)?></font></td><?
									endforeach;

									// prices 
									reset($arrPRICE_CODE);
									foreach($arrPRICE_CODE as $code):
										?><td class="tablehead" valign="top"><font class="tablebodytext"><?=htmlspecialchars($arrPrice[$code]["TITLE"])?></font></td><?
									endforeach;

									// properties
									reset($arrPROPERTY_CODE);
									foreach($arrPROPERTY_CODE as $code):
										?><td class="tablehead" valign="top"><font class="tablebodytext"><?echo $arrProp[$code]?></font></td><?
									endforeach;
									?>
								</tr>
								<?
								// loop by elements
								while ($obElement = $rsElements->GetNextElement()):
									$arElement = $obElement->GetFields();
									$arProperty = $obElement->GetProperties();
								?>
								<tr>
									<td class="tablenullbody" valign="top"><input type="checkbox" name="ID[]" value="<?=$arElement["ID"]?>"></td>

									<?
									// fields
									reset($arrFIELD_CODE);
									foreach($arrFIELD_CODE as $code):
										?><td class="tablenullbody" valign="top"><font class="tablebodytext"><?
										switch($code):
											case "NAME":

												$first_code = reset($arrPRICE_CODE);
												$trace_quantity = $arElement["CATALOG_QUANTITY_TRACE"];
												$quantity = intval($arElement["CATALOG_QUANTITY"]);
												$price_id = $arElement["CATALOG_PRICE_ID_".$arrPrice[$first_code]["ID"]];
												$can_buy = $arElement["CATALOG_CAN_BUY_".$arrPrice[$first_code]["ID"]];
												
												?><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?echo $arElement[$code]?></a><?
													if (strlen($price_id)>0 && $can_buy=="Y"):
														if ($trace_quantity!="Y" || ($trace_quantity=="Y" && $quantity>0)):
															?><br><br><table cellspacing=0 cellpadding=2>
																<tr>
																	<td valign="top"><font class="tablebodytext"><b><a href="<?= $APPLICATION->GetCurPageParam("price_id=".$price_id."&action=BUY", array("price_id", "action")) ?>" class="text"><?=GetMessage("CATALOG_COMPARE_BUY"); ?></a></b></font></td>
																</tr>
															</table><?
														else:
															?><br><br><nobr><font class="smalltext"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></font></nobr><?
														endif;
													endif;
												break;
											case "PREVIEW_PICTURE":
											case "DETAIL_PICTURE":
												echo ShowImage($arElement[$code], 150, 150, "align='left' hspace='0' vspace='0' border='0' alt='".$arElement["NAME"]."'", $arElement["DETAIL_PAGE_URL"]);
												break;
											default:
												echo $arElement[$code];
												break;												
										endswitch;
										?></font></td><?
									endforeach;

									// prices
									reset($arrPRICE_CODE);
									foreach($arrPRICE_CODE as $code):
										
										$value			= $arElement["CATALOG_PRICE_".$arrPrice[$code]["ID"]];
										$currency		= $arElement["CATALOG_CURRENCY_".$arrPrice[$code]["ID"]];
										$price_id		= $arElement["CATALOG_PRICE_ID_".$arrPrice[$code]["ID"]];
										$can_access		= $arElement["CATALOG_CAN_ACCESS_".$arrPrice[$code]["ID"]];
										$can_buy		= $arElement["CATALOG_CAN_BUY_".$arrPrice[$code]["ID"]];

										?><td class="tablenullbody" valign="top" nowrap><nobr><?
											if ($can_access=="Y"):
												?><input type="hidden" name="PRICE_ID[]" value="<?=$price_id?>"><font class="tablebodytext"><font color="red"><b><? echo FormatCurrency($value, $currency); ?></b></font></font><?
											endif;
											?></nobr></td><?
									endforeach;

									// properties
									reset($arrPROPERTY_CODE);
									foreach($arrPROPERTY_CODE as $code):
										?><td class="tablenullbody" valign="top"><font class="tablebodytext"><?echo (is_array($arProperty[$code]["VALUE"])) ? implode("<br>",$arProperty[$code]["VALUE"]) : $arProperty[$code]["VALUE"]?></font></td><?
									endforeach;

									?>
								</tr>
								<?endwhile;?>
							</table>
						</td>
					</tr>
				</table><br><font class="text"><input type="submit" class="inputbuttonflat" value="<?=GetMessage("CATALOG_COMPARE_DELETE")?>" name="DELETE_FROM_COMPARE_LIST"></font>
				</form>
				<?
			endif;
			$obCache->EndDataCache();
		endif;
	else:
		ShowNote(GetMessage("CATALOG_COMPARE_LIST_EMPTY"));
	endif;
endif;
?>