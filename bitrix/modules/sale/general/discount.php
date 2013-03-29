<?
IncludeModuleLangFile(__FILE__);

class CAllSaleDiscount
{
	static function DoProcessOrder(&$arOrder, $arOptions, &$arErrors)
	{
		global $DB;

		if (!array_key_exists("COUNT_DISCOUNT_4_ALL_QUANTITY", $arOptions))
			$arOptions["COUNT_DISCOUNT_4_ALL_QUANTITY"] = COption::GetOptionString("sale", "COUNT_DISCOUNT_4_ALL_QUANTITY", "N");

		$arMinDiscount = null;
		$allSum = 0;
		foreach ($arOrder["BASKET_ITEMS"] as $arItem)
			$allSum += $arItem["PRICE"] * $arItem["QUANTITY"];
		$dblMinPrice = $allSum;

		$dbDiscount = CSaleDiscount::GetList(
			array("SORT" => "ASC"),
			array(
				"LID" => $arOrder["SITE_ID"],
				"ACTIVE" => "Y",
				"!>ACTIVE_FROM" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"!<ACTIVE_TO" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"<=PRICE_FROM" => $arOrder["ORDER_PRICE"],
				">=PRICE_TO" => $arOrder["ORDER_PRICE"],
				"USER_GROUPS" => CUser::GetUserGroup($arOrder["USER_ID"]),
			),
			false,
			false,
			array("*")
		);
		while ($arDiscount = $dbDiscount->Fetch())
		{
			$dblDiscount = 0;
			if ($arDiscount["DISCOUNT_TYPE"] == "P")
			{
				if ($arOptions["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
				{
					foreach ($arOrder["BASKET_ITEMS"] as $arItem)
						$dblDiscount += roundEx($arItem["PRICE"] * $arItem["QUANTITY"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
				}
				else
				{
					foreach ($arOrder["BASKET_ITEMS"] as $arItem)
						$dblDiscount += roundEx(roundEx($arItem["PRICE"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION) * $arItem["QUANTITY"], SALE_VALUE_PRECISION);
				}
			}
			else
			{
				$dblDiscount = roundEx(CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $arOrder["CURRENCY"]), SALE_VALUE_PRECISION);
			}

			if ($dblMinPrice > ($allSum - $dblDiscount))
			{
				$dblMinPrice = $allSum - $dblDiscount;
				$arMinDiscount = $arDiscount;
			}
		}

		if ($arMinDiscount != null)
		{
			if ($arMinDiscount["DISCOUNT_TYPE"] == "P")
			{
				$arOrder["DISCOUNT_PERCENT"] = $arMinDiscount["DISCOUNT_VALUE"];
				foreach ($arOrder["BASKET_ITEMS"] as &$arItem)
				{
					if ($arOptions["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
					{
						$curDiscount = roundEx($arItem["PRICE"] * $arItem["QUANTITY"] * $arMinDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arOrder["DISCOUNT_PRICE"] += $curDiscount;
					}
					else
					{
						$curDiscount = roundEx($arItem["PRICE"] * $arMinDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arOrder["DISCOUNT_PRICE"] += roundEx($curDiscount * $arItem["QUANTITY"], SALE_VALUE_PRECISION);

					}
					//$arItem["DISCOUNT_PRICE"] = $arItem["PRICE"] - $curDiscount;
				}
			}
			else
			{
				$arOrder["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arMinDiscount["DISCOUNT_VALUE"], $arMinDiscount["CURRENCY"], $arOrder["CURRENCY"]);
				$arOrder["DISCOUNT_PRICE"] = roundEx($arOrder["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
			}
		}
	}

	function PrepareCurrency4Where($val, $key, $operation, $negative, $field, &$arField, &$arFilter)
	{
		$val = DoubleVal($val);

		$baseSiteCurrency = "";
		if (isset($arFilter["LID"]) && strlen($arFilter["LID"]) > 0)
			$baseSiteCurrency = CSaleLang::GetLangCurrency($arFilter["LID"]);
		elseif (isset($arFilter["CURRENCY"]) && strlen($arFilter["CURRENCY"]) > 0)
			$baseSiteCurrency = $arFilter["CURRENCY"];

		if (strlen($baseSiteCurrency) <= 0)
			return False;

		$strSqlSearch = "";

		$dbCurrency = CCurrency::GetList(($by = "sort"), ($order = "asc"));
		while ($arCurrency = $dbCurrency->Fetch())
		{
			$val1 = roundEx(CCurrencyRates::ConvertCurrency($val, $baseSiteCurrency, $arCurrency["CURRENCY"]), SALE_VALUE_PRECISION);
			if (strlen($strSqlSearch) > 0)
				$strSqlSearch .= " OR ";

			$strSqlSearch .= "(D.CURRENCY = '".$arCurrency["CURRENCY"]."' AND ";
			if ($negative == "Y")
				$strSqlSearch .= "NOT";
			$strSqlSearch .= "(".$field." ".$operation." ".$val1." OR ".$field." IS NULL OR ".$field." = 0)";
			$strSqlSearch .= ")";
		}

		return "(".$strSqlSearch.")";
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = intval($ID);
		if (0 < $ID)
		{
			$rsDiscounts = CSaleDiscount::GetList(array(),array('ID' => $ID),false,false,array());
			if ($arDiscount = $rsDiscounts->Fetch())
			{
				return $arDiscount;
			}
		}
		return false;
	}

	function CheckFields($ACTION, &$arFields)
	{
		global $DB;
		global $APPLICATION;

		if ((is_set($arFields, "ACTIVE") || $ACTION=="ADD") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"] = "N";
		if ((is_set($arFields, "DISCOUNT_TYPE") || $ACTION=="ADD") && $arFields["DISCOUNT_TYPE"]!="P")
			$arFields["DISCOUNT_TYPE"] = "V";

		if ((is_set($arFields, "SORT") || $ACTION=="ADD") && IntVal($arFields["SORT"])<=0)
			$arFields["SORT"] = 100;

		if ((is_set($arFields, "LID") || $ACTION=="ADD") && strlen($arFields["LID"])<=0)
			return false;
		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && strlen($arFields["CURRENCY"])<=0)
			return false;

		if (is_set($arFields, "CURRENCY"))
		{
			if (!($arCurrency = CCurrency::GetByID($arFields["CURRENCY"])))
			{
				$APPLICATION->ThrowException(str_replace("#ID#", $arFields["CURRENCY"], GetMessage("SKGD_NO_CURRENCY")), "ERROR_NO_CURRENCY");
				return false;
			}
		}

		if (is_set($arFields, "LID"))
		{
			$dbSite = CSite::GetByID($arFields["LID"]);
			if (!$dbSite->Fetch())
			{
				$APPLICATION->ThrowException(str_replace("#ID#", $arFields["LID"], GetMessage("SKGD_NO_SITE")), "ERROR_NO_SITE");
				return false;
			}
		}

		if (is_set($arFields, "DISCOUNT_VALUE"))
		{
			$arFields["DISCOUNT_VALUE"] = str_replace(",", ".", $arFields["DISCOUNT_VALUE"]);
			$arFields["DISCOUNT_VALUE"] = DoubleVal($arFields["DISCOUNT_VALUE"]);
		}
		if ((is_set($arFields, "DISCOUNT_VALUE") || $ACTION=="ADD") && DoubleVal($arFields["DISCOUNT_VALUE"])<=0)
		{
			$APPLICATION->ThrowException(GetMessage("SKGD_EMPTY_DVAL"), "ERROR_NO_DISCOUNT_VALUE");
			return false;
		}

		if (is_set($arFields, "PRICE_FROM"))
		{
			$arFields["PRICE_FROM"] = str_replace(",", ".", $arFields["PRICE_FROM"]);
			$arFields["PRICE_FROM"] = DoubleVal($arFields["PRICE_FROM"]);
		}

		if (is_set($arFields, "PRICE_TO"))
		{
			$arFields["PRICE_TO"] = str_replace(",", ".", $arFields["PRICE_TO"]);
			$arFields["PRICE_TO"] = DoubleVal($arFields["PRICE_TO"]);
		}

		/*
		if ($ACTION=="ADD"
			&& (!is_set($arFields, "PRICE_FROM") && DoubleVal($arFields["PRICE_TO"])<=0
			|| !is_set($arFields, "PRICE_TO") && DoubleVal($arFields["PRICE_FROM"])<=0
			|| DoubleVal($arFields["PRICE_TO"])<=0 && DoubleVal($arFields["PRICE_FROM"])<=0))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGD_WRONG_DBOUND"), "ERROR_BAD_BORDER");
			return false;
		}
		*/

		if ((is_set($arFields, "ACTIVE_FROM") || $ACTION=="ADD") && (!$DB->IsDate($arFields["ACTIVE_FROM"], false, LANGUAGE_ID, "FULL")))
			$arFields["ACTIVE_FROM"] = false;
		if ((is_set($arFields, "ACTIVE_TO") || $ACTION=="ADD") && (!$DB->IsDate($arFields["ACTIVE_TO"], false, LANGUAGE_ID, "FULL")))
			$arFields["ACTIVE_TO"] = false;

		if ((is_set($arFields, 'USER_GROUPS') || $ACTION=="ADD") && (!is_array($arFields['USER_GROUPS']) || empty($arFields['USER_GROUPS'])))
		{
			$APPLICATION->ThrowException(GetMessage("BT_MOD_SALE_DISC_ERR_USER_GROUPS_ABSENT"), "USER_GROUPS");
			return false;
		}

		return true;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);
		if (!CSaleDiscount::CheckFields("UPDATE", $arFields))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_discount", $arFields);
		$strSql = "UPDATE b_sale_discount SET ".$strUpdate." WHERE ID = ".$ID."";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (array_key_exists('USER_GROUPS',$arFields) && is_array($arFields['USER_GROUPS']))
		{
			$DB->Query("DELETE FROM b_sale_discount_group WHERE DISCOUNT_ID = ".$ID." ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$arValid = array();
			foreach ($arFields['USER_GROUPS'] as &$value)
			{
				$value = intval($value);
				if (0 < $value)
					$arValid[] = $value;
			}
			$arFields['USER_GROUPS'] = array_unique($arValid);
			if (!empty($arFields['USER_GROUPS']))
			{
				foreach ($arFields['USER_GROUPS'] as &$value)
				{
					$strSql =
						"INSERT INTO b_sale_discount_group(DISCOUNT_ID, GROUP_ID) ".
						"VALUES(".$ID.", ".$value.")";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
		}

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$DB->Query("DELETE FROM b_sale_discount_group WHERE DISCOUNT_ID = ".$ID." ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $DB->Query("DELETE FROM b_sale_discount WHERE ID = ".$ID."", true);
	}

}
?>