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

	if (isset($_REQUEST["ADD_FEATURE"]) && strlen($_REQUEST["pr_code"]) > 0)
	{
		unset($_SESSION[$NAME][$IBLOCK_ID]["DELETE_PROP"][$_REQUEST["pr_code"]]);
	}
	elseif (is_array($_REQUEST["pr_code"]) && count($_REQUEST["pr_code"])>0)
	{
		foreach ($_REQUEST["pr_code"] as $code)
			$_SESSION[$NAME][$IBLOCK_ID]["DELETE_PROP"][$code] = $code;
	}


	if (isset($_REQUEST["DIFFERENT"]))
		$_SESSION[$NAME][$IBLOCK_ID]["DIFFERENT"] = ($_REQUEST["DIFFERENT"]=="Y" ? "Y" : "N");


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

	$arrCompareList = $_SESSION[$NAME][$IBLOCK_ID]; 
	unset($arrCompareList["DELETE_PROP"]);
	unset($arrCompareList["DIFFERENT"]);
	$arDeleteProp = $_SESSION[$NAME][$IBLOCK_ID]["DELETE_PROP"];


	if (is_array($arrCompareList) && count($arrCompareList)>0) :

		/*************************************************************************
									Work with cache
		*************************************************************************/

		$CACHE_ID = SITE_ID."|".__FILE__."|".md5(serialize($arParams)."|".serialize($arrCompareList)."|".serialize($arDeleteProp)."|".$USER->GetGroups());
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

			if (is_array($arDeleteProp) && count($arDeleteProp)>0)
			{
				$arrPROPERTY_CODE = array_diff($arrPROPERTY_CODE,$arDeleteProp);
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

				$cnt = 0;
				while ($obElement = $rsElements->GetNextElement())
				{
					$arElement[$cnt] = $obElement->GetFields();
					$arProperty[$cnt] = $obElement->GetProperties();
					$cnt++;
				}

				/****************************************************************
										HTML form
				****************************************************************/
				
				?><a name="compare_table"></a><?=ShowNote($strNote)?><?=ShowError($strError)?>
				<form action="<?=$APPLICATION->GetCurPage()?>" method="GET" style="margin-top: 0px;">
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td><font class="tablebodytext"><?if($_SESSION[$NAME][$IBLOCK_ID]["DIFFERENT"]=="Y"):?><a href="<?=$_SERVER["PHP_SELF"]?>?DIFFERENT=N"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
							<?else:?>
							<?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?>
							<?endif?></font>
							&nbsp;|&nbsp;
							<font class="tablebodytext"><?if($_SESSION[$NAME][$IBLOCK_ID]["DIFFERENT"]!="Y"):?>
							<a href="<?=$_SERVER["PHP_SELF"]?>?DIFFERENT=Y">
							<?=GetMessage("CATALOG_ONLY_DIFFERENT")?>
							</a>
							<?else:?>
							<?=GetMessage("CATALOG_ONLY_DIFFERENT")?>
							<?endif?></font>
						</td>
					</tr>
				</table><br>
				<?if (is_array($arDeleteProp) && ($arCNT = count($arDeleteProp))):?>
					<font class="tablebodytext"><?=GetMessage("CATALOG_REMOVED_FEATURES")?>: 
					<?$i = 0; foreach($arDeleteProp as $code): $i++;?>
						<a href="<?=$_SERVER["PHP_SELF"]?>?pr_code=<?=$code?>&ADD_FEATURE=Y"><?=$arrProp[$code]?></a><?if($i < $arCNT):?>, <?endif?>
					<?endforeach?>
					</font><br>
				<?endif?>

				<br><table cellspacing="0" cellpadding="0" border="0" class="tableborder">
					<tr>
						<td>
						<table cellspacing=1 cellpadding=4>
						<tr>
							<td class="tablehead" valign="top"></td>
							<?for($i = 0; $i < $cnt; $i++):?>
							<td class="tablenullbody" valign="top"><input type="checkbox" name="ID[]" value="<?=$arElement[$i]["ID"]?>"></td>
						<?endfor?>

						</tr>
						<?reset($arrFIELD_CODE);?>
						<?foreach($arrFIELD_CODE as $code):?>
						<tr>
							<td class="tablehead" valign="top"><font class="tablebodytext"><?echo GetMessage("CATALOG_COMPARE_".$code)?></font></td>
							<?for($i = 0; $i < $cnt; $i++):?>
							<td class="tablenullbody" valign="top"><font class="tablebodytext">
							<?switch($code):
								case "NAME":
									$first_code = reset($arrPRICE_CODE);
									$trace_quantity = $arElement[$i]["CATALOG_QUANTITY_TRACE"];
									$quantity = intval($arElement[$i]["CATALOG_QUANTITY"]);
									$price_id = $arElement[$i]["CATALOG_PRICE_ID_".$arrPrice[$first_code]["ID"]];
									$can_buy = $arElement[$i]["CATALOG_CAN_BUY_".$arrPrice[$first_code]["ID"]];
									
									?><a href="<?=$arElement[$i]["DETAIL_PAGE_URL"]?>"><?echo $arElement[$i][$code]?></a><?
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
									echo ShowImage($arElement[$i][$code], 150, 150, "align='left' hspace='0' vspace='0' border='0' alt='".$arElement[$i]["NAME"]."'", $arElement[$i]["DETAIL_PAGE_URL"]);
									break;
								default:
									echo $arElement[$i][$code];
									break;												
							endswitch;
							?>
							</font></td>
							<?endfor?>
						</tr>
						<?endforeach;?>


						<?reset($arrPRICE_CODE);?>
						<?foreach($arrPRICE_CODE as $code):?>
						<tr>
							<td class="tablehead" valign="top">
							<font class="tablebodytext"><?=htmlspecialchars($arrPrice[$code]["TITLE"])?></font></td>
							<?for($i = 0; $i < $cnt; $i++):?>
							<td class="tablenullbody" nowrap valign="top">
							<?
							$value = $arElement[$i]["CATALOG_PRICE_".$arrPrice[$code]["ID"]];
							$currency = $arElement[$i]["CATALOG_CURRENCY_".$arrPrice[$code]["ID"]];
							$price_id =$arElement[$i]["CATALOG_PRICE_ID_".$arrPrice[$code]["ID"]];
							$can_access = $arElement[$i]["CATALOG_CAN_ACCESS_".$arrPrice[$code]["ID"]];
							$can_buy = $arElement[$i]["CATALOG_CAN_BUY_".$arrPrice[$code]["ID"]];
							?>
							<nobr>
								<?if ($can_access=="Y"):?>
									<input type="hidden" name="PRICE_ID[]" value="<?=$price_id?>"><font class="tablebodytext"><font color="red"><b><? echo FormatCurrency($value, $currency); ?></b></font></font>
								<?endif;?>
							</nobr>
							</td>
							<?endfor?>
						</tr>
						<?endforeach;?>


						<?reset($arrPROPERTY_CODE);?>
						<?foreach($arrPROPERTY_CODE as $code):?>
						<?$arCompare = Array();
							for($i = 0; $i < $cnt; $i++)
							{
								if($arProperty[$i][$code]["VALUE"] != "")
									$arCompare[] = $arProperty[$i][$code]["VALUE"];
							}

						$diff = (count(array_unique($arCompare)) > 1 ? true : false);

						$view = (!$diff && $_SESSION[$NAME][$IBLOCK_ID]["DIFFERENT"]=="Y" ? false : true);
						?>
						<?if($view):?>
						<tr>
							<td class="tablehead" valign="top">
							<input type="checkbox" name="pr_code[]" value="<?=$code?>">
							<font class="tablebodytext"><?echo $arrProp[$code]?></font></td>
							<?for($i = 0; $i < $cnt; $i++):?>
							<td <?if($diff):?> class="tablehead"<?else:?> class="tablenullbody" <?endif?> valign="top">
							<font class="tablebodytext"><?echo (is_array($arProperty[$i][$code]["VALUE"])) ? implode("<br>",$arProperty[$i][$code]["VALUE"]) : $arProperty[$i][$code]["VALUE"]?></font>
							</td>
							<?endfor?>
						</tr>
						<?endif?>
						<?endforeach;?>
						<tr>
							<td class="tablenullbody" valign="top"><font class="tablebodytext"><input type="submit" class="inputbuttonflat" style="width:120px;" value="<?=GetMessage("CATALOG_REMOVE_FEATURES")?>"></font>
							</td>
							<td class="tablenullbody" colspan="<?=$cnt?>">
							<font class="tablebodytext"><input type="submit"  style="width:120px;" class="inputbuttonflat" value="<?=GetMessage("CATALOG_REMOVE_PRODUCTS")?>" name="DELETE_FROM_COMPARE_LIST"></font>
							</td>
						</tr>
						</table>
						</td>
					</tr>
				</table> 
				</form>

<?if($DISPLAY_ELEMENT_SELECT_BOX=="Y"):?>
				<form action="<?=$_SERVER["PHP_SELF"]?>" method="get">
					<input type="hidden" name="action" value="COMPARE">
					<select name="id">
<?
$aFilter = Array("IBLOCK_ID"=>$IBLOCK_ID);
foreach($arrCompareList as $eid) $aFilter["!ID"][] = $eid;

$items = CIBlockElement::GetList(Array($ELEMENT_SORT_FIELD_BOX => $ELEMENT_SORT_ORDER_BOX, "ID" => "DESC"),$aFilter, false, false, Array("ID","NAME"));
while($arItems = $items->GetNext()):?>
						<option value="<?=$arItems["ID"]?>"><?=$arItems["NAME"]?></option>
<?endwhile?>
					</select>
					<input type="submit" class="inputbuttonflat" style="width:200px" value="<?=GetMessage("CATALOG_ADD_TO_COMPARE_LIST")?>">
				</form>
<?endif?>
				<?
			endif;
			$obCache->EndDataCache();
		endif;
	else:
		ShowNote(GetMessage("CATALOG_COMPARE_LIST_EMPTY"));
	endif;
endif;
?>