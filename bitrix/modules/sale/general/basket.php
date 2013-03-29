<?
IncludeModuleLangFile(__FILE__);

class CAllSaleBasket
{
	/**
	* The function remove old subscribe product
	*
	* @param string $LID - site for cleaning
	* @return true false
	*/
	function ClearProductSubscribe($LID)
	{
		CSaleBasket::_ClearProductSubscribe($LID);

		return "CSaleBasket::ClearProductSubscribe(".$LID.");";
	}

	/**
	* The function send subscribe product letter
	*
	* @param integer $ID - code product
	* @param string $MODULE - module product
	* @return true false send letter
	*/
	function ProductSubscribe($ID, $MODULE)
	{
		$ID = IntVal($ID);
		$MODULE = trim($MODULE);

		if ($ID <= 0 || strlen($MODULE) <= 0)
			return false;

		$arSubscribeProd = array();
		$subscribeProd = COption::GetOptionString("sale", "subscribe_prod", "");
		if (strlen($subscribeProd) > 0)
			$arSubscribeProd = unserialize($subscribeProd);

		$rsItemsBasket = CSaleBasket::GetList(
				array("USER_ID" => "DESC", "LID" => "ASC"),
				array(
						"PRODUCT_ID" => $ID,
						"SUBSCRIBE" => "Y",
						"CAN_BUY" => "N",
						"ORDER_ID" => "NULL",
						">USER_ID" => "0",
						"MODULE" => $MODULE
				),
				false,
				false,
				array('ID', 'FUSER_ID', 'USER_ID', 'MODULE', 'PRODUCT_ID', 'CURRENCY', 'DATE_INSERT', 'QUANTITY', 'LID', 'DELAY', 'CALLBACK_FUNC', 'SUBSCRIBE')
		);
		while ($arItemsBasket = $rsItemsBasket->Fetch())
		{
			$bSend = false;
			$LID = $arItemsBasket["LID"];

			if (isset($arSubscribeProd[$LID]) && $arSubscribeProd[$LID]["use"] == "Y")
			{
				$USER_ID = $arItemsBasket['USER_ID'];
				$arMailProp = array();
				$arPayerProp = array();

				//select person type
				$dbPersonType = CSalePersonType::GetList(($by="SORT"), ($order="ASC"), array("LID" => $LID));
				while ($arPersonType = $dbPersonType->Fetch())
				{
					//select ID props is mail
					$dbProperties = CSaleOrderProps::GetList(
						array(),
						array("ACTIVE" => "Y", "IS_EMAIL" => "Y", "PERSON_TYPE_ID" => $arPersonType["ID"]),
						false,
						false,
						array('ID', 'PERSON_TYPE_ID')
					);
					while ($arProperties = $dbProperties->Fetch())
						$arMailProp[$arProperties["PERSON_TYPE_ID"]] = $arProperties["ID"];

					//select ID props is name
					$arPayerProp = array();
					$dbProperties = CSaleOrderProps::GetList(
						array(),
						array("ACTIVE" => "Y", "IS_PAYER" => "Y", "PERSON_TYPE_ID" => $arPersonType["ID"]),
						false,
						false,
						array('ID', 'PERSON_TYPE_ID')
					);
					while ($arProperties = $dbProperties->Fetch())
						$arPayerProp[$arProperties["PERSON_TYPE_ID"]] = $arProperties["ID"];
				}//end while

				//load user profiles
				$arUserProfiles = CSaleOrderUserProps::DoLoadProfiles($USER_ID);

				$rsUser = CUser::GetByID($USER_ID);
				$arUser = $rsUser->Fetch();
				$userName = $arUser["LAST_NAME"];
				if (strlen($userName) > 0)
					$userName .= " ";
				$userName .= $arUser["NAME"];

				//select of user name to be sent
				$arUserSendName = array();
				if (count($arUserProfiles) > 0 && count($arPayerProp) > 0)
				{
					foreach($arPayerProp as $personType => $namePropID)
					{
						if (isset($arUserProfiles[$personType]))
						{
							foreach($arUserProfiles[$personType] as $profiles)
							{
								if (isset($profiles["VALUES"][$namePropID]) && strlen($profiles["VALUES"][$namePropID]) > 0)
								{
									$arUserSendName[$personType] = trim($profiles["VALUES"][$namePropID]);
									break;
								}
							}
						}
					}
				}
				else
					$arUserSendName[] = $userName;

				//select of e-mail to be sent
				$arUserSendMail = array();
				if (count($arUserProfiles) > 0 && count($arMailProp) > 0)
				{
					foreach($arMailProp as $personType => $mailPropID)
					{
						if (isset($arUserProfiles[$personType]))
						{
							foreach($arUserProfiles[$personType] as $profiles)
							{
								if (isset($profiles["VALUES"][$mailPropID]) && strlen($profiles["VALUES"][$mailPropID]) > 0)
								{
									$arUserSendMail[$personType] = trim($profiles["VALUES"][$mailPropID]);
									break;
								}
							}
						}
					}
				}
				else
					$arUserSendMail[] = trim($arUser["EMAIL"]);

				if (array_key_exists("CALLBACK_FUNC", $arItemsBasket) && !empty($arItemsBasket["CALLBACK_FUNC"]))
				{
					$arCallback = CSaleBasket::ExecuteCallbackFunction(
						trim($arItemsBasket["CALLBACK_FUNC"]),
						$MODULE,
						$ID,
						1,
						"N",
						$USER_ID,
						$LID
					);
					if (count($arCallback) > 0)
					{
						$arCallback["QUANTITY"] = 1;
						$arCallback["DELAY"] = "N";
						$arCallback["SUBSCRIBE"] = "N";
						CSaleBasket::Update($arItemsBasket["ID"], $arCallback);
					}
				}

				$serverName = COption::GetOptionString("main", "server_name", "order@".$SERVER_NAME);
				//send mail

				if (count($arUserSendMail) > 0 && count($arCallback) > 0)
				{
					$eventName = "SALE_SUBSCRIBE_PRODUCT";
					$event = new CEvent;

					foreach ($arUserSendMail as $personType => $mail)
					{
						$sendName = $userName;
						if (isset($arUserSendName[$personType]) && strlen($arUserSendName[$personType]) > 0)
							$sendName = $arUserSendName[$personType];

						$arFields = Array(
								"EMAIL" => $mail,
								"USER_NAME" => $sendName,
								"NAME" => $arCallback["NAME"],
								"PAGE_URL" => "http://".$serverName.$arCallback["DETAIL_PAGE_URL"],
								"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
						);

						$event->Send($eventName, $LID, $arFields, "N");
					}
				}
			}//enf if bSend
		}//end while $arItemsBasket

		return true;
	}


	function DoGetUserShoppingCart($siteId, $userId, $shoppingCart, &$arErrors, $arCoupons = array())
	{
		$siteId = trim($siteId);
		if (empty($siteId))
		{
			$arErrors[] = array("CODE" => "PARAM", "TEXT" => GetMessage('SKGB_PARAM_SITE_ERROR'));
			return null;
		}

		$userId = intval($userId);

		if (!is_array($shoppingCart))
		{
			if (intval($shoppingCart)."|" != $shoppingCart."|")
			{
				$arErrors[] = array("CODE" => "PARAM", "TEXT" => GetMessage('SKGB_PARAM_SK_ERROR'));
				return null;
			}
			$shoppingCart = intval($shoppingCart);

			$dbShoppingCartItems = CSaleBasket::GetList(
				array("NAME" => "ASC"),
				array(
					"FUSER_ID" => $shoppingCart,
					"LID" => $siteId,
					"ORDER_ID" => "NULL",
					"DELAY" => "N",
				),
				false,
				false,
				array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE",
					"WEIGHT", "NAME", "CURRENCY", "CATALOG_XML_ID", "VAT_RATE", "NOTES", "DISCOUNT_PRICE", "DETAIL_PAGE_URL")
			);
			$arTmp = array();
			while ($arShoppingCartItem = $dbShoppingCartItems->Fetch())
				$arTmp[] = $arShoppingCartItem;

			$shoppingCart = $arTmp;
		}

		if (CSaleHelper::IsAssociativeArray($shoppingCart))
			$shoppingCart = array($shoppingCart);

		if (is_array($arCoupons) && (count($arCoupons) > 0))
		{
			$events = GetModuleEvents("sale", "OnSetCouponList");
			while($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($userId, $arCoupons, array()));
		}

		$arResult = array();

		foreach ($shoppingCart as &$arShoppingCartItem)
		{
			if (array_key_exists("CALLBACK_FUNC", $arShoppingCartItem) && !empty($arShoppingCartItem["CALLBACK_FUNC"]))
			{
				// USER CODE ->
				$arFieldsTmp = CSaleBasket::ExecuteCallbackFunction(
					$arShoppingCartItem["CALLBACK_FUNC"],
					$arShoppingCartItem["MODULE"],
					$arShoppingCartItem["PRODUCT_ID"],
					$arShoppingCartItem["QUANTITY"],
					"N",
					$userId,
					$siteId
				);
				if ($arFieldsTmp && is_array($arFieldsTmp) && (count($arFieldsTmp) > 0))
				{
					$arFieldsTmp["CAN_BUY"] = "Y";
					$arFieldsTmp["SUBSCRIBE"] = "N";
				}
				else
					$arFieldsTmp = array("CAN_BUY" => "N");

				if (array_key_exists("ID", $arShoppingCartItem) && (intval($arShoppingCartItem["ID"]) > 0))
				{
					$arFieldsTmp["IGNORE_CALLBACK_FUNC"] = "Y";

					CSaleBasket::Update($arShoppingCartItem["ID"], $arFieldsTmp);

					$dbTmp = CSaleBasket::GetList(
						array(),
						array("ID" => $arShoppingCartItem["ID"]),
						false,
						false,
						array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE",
							"WEIGHT", "NAME", "CURRENCY", "CATALOG_XML_ID", "VAT_RATE", "NOTES", "DISCOUNT_PRICE", "DETAIL_PAGE_URL")
					);
					$arTmp = $dbTmp->Fetch();

					foreach ($arTmp as $key => $val)
						$arShoppingCartItem[$key] = $val;
				}
				else
				{
					foreach ($arFieldsTmp as $key => $val)
						$arShoppingCartItem[$key] = $val;
				}
			}

			if ($arShoppingCartItem["CAN_BUY"] == "Y")
			{
				$baseLangCurrency = CSaleLang::GetLangCurrency($siteId);
				if ($baseLangCurrency != $arShoppingCartItem["CURRENCY"])
				{
					$arShoppingCartItem["PRICE"] = CCurrencyRates::ConvertCurrency($arShoppingCartItem["PRICE"], $arShoppingCartItem["CURRENCY"], $baseLangCurrency);
					if (is_set($arShoppingCartItem, "DISCOUNT_PRICE"))
						$arShoppingCartItem["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arShoppingCartItem["DISCOUNT_PRICE"], $arShoppingCartItem["CURRENCY"], $baseLangCurrency);
					$arShoppingCartItem["CURRENCY"] = $baseLangCurrency;
				}

				$arShoppingCartItem["PRICE"] = roundEx($arShoppingCartItem["PRICE"], SALE_VALUE_PRECISION);
				$arShoppingCartItem["QUANTITY"] = doubleval($arShoppingCartItem["QUANTITY"]);
				$arShoppingCartItem["WEIGHT"] = doubleval($arShoppingCartItem["WEIGHT"]);
				$arShoppingCartItem["VAT_RATE"] = doubleval($arShoppingCartItem["VAT_RATE"]);
				$arShoppingCartItem["DISCOUNT_PRICE"] = roundEx($arShoppingCartItem["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);

				if ($arShoppingCartItem["VAT_RATE"] > 0)
					$arShoppingCartItem["VAT_VALUE"] = (($arShoppingCartItem["PRICE"] / ($arShoppingCartItem["VAT_RATE"] + 1)) * $arShoppingCartItem["VAT_RATE"]);
					//$arShoppingCartItem["VAT_VALUE"] = roundEx((($arShoppingCartItem["PRICE"] / ($arShoppingCartItem["VAT_RATE"] + 1)) * $arShoppingCartItem["VAT_RATE"]), SALE_VALUE_PRECISION);

				if ($arShoppingCartItem["DISCOUNT_PRICE"] > 0)
					$arShoppingCartItem["DISCOUNT_PRICE_PERCENT"] = $arShoppingCartItem["DISCOUNT_PRICE"] * 100 / ($arShoppingCartItem["DISCOUNT_PRICE"] + $arShoppingCartItem["PRICE"]);

				$arResult[] = $arShoppingCartItem;
			}
		}
		unset($arShoppingCartItem);

		if (is_array($arCoupons) && (count($arCoupons) > 0))
		{
			$events = GetModuleEvents("sale", "OnClearCouponList");
			while($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($userId, $arCoupons, array()));
		}

		return $arResult;
	}

	static function DoSaveOrderBasket($orderId, $siteId, $userId, $arShoppingCart, &$arErrors, $arCoupons = array())
	{
		global $DB;

		$orderId = IntVal($orderId);
		if ($orderId <= 0)
			return false;

		if (!is_array($arShoppingCart) || (count($arShoppingCart) <= 0))
		{
			$arErrors[] = array("CODE" => "PARAM", "TEXT" => GetMessage('SKGB_SHOPPING_CART_EMPTY'));
			return false;
		}

		$arIDs = array();
		$dbResult = CSaleBasket::GetList(
			array(),
			array("ORDER_ID" => $orderId),
			false,
			false,
			array("ID", "QUANTITY", "CANCEL_CALLBACK_FUNC", "MODULE", "PRODUCT_ID")
		);
		while ($arResult = $dbResult->Fetch())
		{
			$arIDs[$arResult["ID"]] = array(
				"QUANTITY" => $arResult["QUANTITY"],
				"CANCEL_CALLBACK_FUNC" => $arResult["CANCEL_CALLBACK_FUNC"],
				"MODULE" => $arResult["MODULE"],
				"PRODUCT_ID" => $arResult["PRODUCT_ID"],
			);
		}

		if (is_array($arCoupons) && (count($arCoupons) > 0))
		{
			$events = GetModuleEvents("sale", "OnSetCouponList");
			while($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($userId, $arCoupons, array()));
		}

		$FUSER_ID = 0;
		$arFUserListTmp = CSaleUser::GetList(array("USER_ID" => $userId));
		if(empty($arFUserListTmp))
		{
			$arFields = array(
					"=DATE_INSERT" => $DB->GetNowFunction(),
					"=DATE_UPDATE" => $DB->GetNowFunction(),
					"USER_ID" => $userId
				);

			$FUSER_ID = CSaleUser::_Add($arFields);
		}
		else
			$FUSER_ID = $arFUserListTmp["ID"];

		foreach ($arShoppingCart as &$arItem)
		{
			if (array_key_exists("ID", $arItem) && intval($arItem["ID"]) > 0)
			{
				$arItem["ID"] = intval($arItem["ID"]);
				if (array_key_exists($arItem["ID"], $arIDs))
				{
					if ($arIDs[$arItem["ID"]]["QUANTITY"] > $arItem["QUANTITY"])
					{
						if (strlen($arItem["CANCEL_CALLBACK_FUNC"]) > 0)
						{
							CSaleBasket::ExecuteCallbackFunction(
								$arItem["CANCEL_CALLBACK_FUNC"],
								$arItem["MODULE"],
								$arItem["PRODUCT_ID"],
								($arIDs[$arItem["ID"]]["QUANTITY"] - $arItem["QUANTITY"]),
								true
							);
						}
					}
					elseif ($arIDs[$arItem["ID"]]["QUANTITY"] < $arItem["QUANTITY"])
					{
						if (strlen($arItem["ORDER_CALLBACK_FUNC"]) > 0)
						{
							CSaleBasket::ExecuteCallbackFunction(
								$arItem["ORDER_CALLBACK_FUNC"],
								$arItem["MODULE"],
								$arItem["PRODUCT_ID"],
								($arItem["QUANTITY"] - $arIDs[$arItem["ID"]]["QUANTITY"]),
								"N",
								$userId,
								$siteId
							);
						}
					}

					unset($arIDs[$arItem["ID"]]);
				}
				else
				{
					if (strlen($arItem["ORDER_CALLBACK_FUNC"]) > 0)
					{
						CSaleBasket::ExecuteCallbackFunction(
							$arItem["ORDER_CALLBACK_FUNC"],
							$arItem["MODULE"],
							$arItem["PRODUCT_ID"],
							$arItem["QUANTITY"],
							"N",
							$userId,
							$siteId
						);
					}
				}

				$arFuserItems = CSaleUser::GetList(array("USER_ID" => intval($userId)));
				$arItem["FUSER_ID"] = $arFuserItems["ID"];

				//CSaleBasket::Update($arItem["ID"], array("CALLBACK_FUNC" => false, "ORDER_ID" => $orderId, "IGNORE_CALLBACK_FUNC" => "Y") + $arItem);
				CSaleBasket::Update($arItem["ID"], array("ORDER_ID" => $orderId, "IGNORE_CALLBACK_FUNC" => "Y") + $arItem);
			}
			else
			{
				unset($arItem["ID"]);

				if (strlen($arItem["ORDER_CALLBACK_FUNC"]) > 0)
				{
					$arFieldsTmp = CSaleBasket::ExecuteCallbackFunction(
						$arItem["ORDER_CALLBACK_FUNC"],
						$arItem["MODULE"],
						$arItem["PRODUCT_ID"],
						$arItem["QUANTITY"],
						"N",
						$userId,
						$siteId
					);

					//$arItem = array_merge ($arItem, $arFieldsTmp);
				}
				
				if ($FUSER_ID > 0)
					$arItem["FUSER_ID"] = $FUSER_ID;
				
				//$arItem["ID"] = CSaleBasket::Add(array("CALLBACK_FUNC" => false, "ORDER_ID" => $orderId, "IGNORE_CALLBACK_FUNC" => "Y") + $arItem);
				$arItem["ID"] = CSaleBasket::Add(array("ORDER_ID" => $orderId, "IGNORE_CALLBACK_FUNC" => "Y") + $arItem);
			}
		}

		if (is_array($arCoupons) && (count($arCoupons) > 0))
		{
			$events = GetModuleEvents("sale", "OnClearCouponList");
			while($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($userId, $arCoupons, array()));
		}

		foreach ($arIDs as $key => $id)
		{
			if (strlen($id["CANCEL_CALLBACK_FUNC"]) > 0)
			{
				CSaleBasket::ExecuteCallbackFunction(
					$id["CANCEL_CALLBACK_FUNC"],
					$id["MODULE"],
					$id["PRODUCT_ID"],
					$id["QUANTITY"],
					true
				);
			}
			CSaleBasket::Delete($key);
		}

		$events = GetModuleEvents("sale", "OnDoBasketOrder");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($orderId));
	}

	//************** ADD, UPDATE, DELETE ********************//
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $DB, $USER, $APPLICATION;

		if (is_set($arFields, "PRODUCT_ID"))
			$arFields["PRODUCT_ID"] = IntVal($arFields["PRODUCT_ID"]);
		if ((is_set($arFields, "PRODUCT_ID") || $ACTION=="ADD") && IntVal($arFields["PRODUCT_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty product field", "EMPTY_PRODUCT_ID");
			return false;
		}

		if (is_set($arFields, "CALLBACK_FUNC")
			&& strlen($arFields["CALLBACK_FUNC"]) > 0
			&& (!is_set($arFields, "IGNORE_CALLBACK_FUNC") || $arFields["IGNORE_CALLBACK_FUNC"] != "Y"))
		{
			$arPrice = CSaleBasket::ExecuteCallbackFunction(
					$arFields["CALLBACK_FUNC"],
					$arFields["MODULE"],
					$arFields["PRODUCT_ID"],
					$arFields["QUANTITY"],
					$arFields["RENEWAL"],
					$arFields["USER_ID"],
					$arFields["LID"]
				);
			if (is_array($arPrice) && count($arPrice)>0)
			{
				$arFields["PRICE"] = $arPrice["PRICE"];
				$arFields["CURRENCY"] = $arPrice["CURRENCY"];
				$arFields["CAN_BUY"] = "Y";
				$arFields["PRODUCT_PRICE_ID"] = $arPrice["PRODUCT_PRICE_ID"];
				$arFields["NOTES"] = $arPrice["NOTES"];
			}
			else
			{
				$arFields["CAN_BUY"] = "N";
			}
		}

		if (is_set($arFields, "PRICE") || $ACTION=="ADD")
		{
			$arFields["PRICE"] = str_replace(",", ".", $arFields["PRICE"]);
			$arFields["PRICE"] = DoubleVal($arFields["PRICE"]);
		}

		if (is_set($arFields, "DISCOUNT_PRICE") || $ACTION=="ADD")
		{
			$arFields["DISCOUNT_PRICE"] = str_replace(",", ".", $arFields["DISCOUNT_PRICE"]);
			$arFields["DISCOUNT_PRICE"] = DoubleVal($arFields["DISCOUNT_PRICE"]);
		}

		if (is_set($arFields, "VAT_RATE") || $ACTION=="ADD")
		{
			$arFields["VAT_RATE"] = str_replace(",", ".", $arFields["VAT_RATE"]);
			$arFields["VAT_RATE"] = DoubleVal($arFields["VAT_RATE"]);
		}

		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && strlen($arFields["CURRENCY"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty currency field", "EMPTY_CURRENCY");
			return false;
		}

		if ((is_set($arFields, "LID") || $ACTION=="ADD") && strlen($arFields["LID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty site field", "EMPTY_SITE");
			return false;
		}

		if ($ACTION!="ADD" && IntVal($ID)<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty ID field", "EMPTY_ID");
			return false;
		}

		if (is_set($arFields, "ORDER_ID"))
		{
			if (!($arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["ORDER_ID"], GetMessage("SKGB_NO_ORDER")), "ERROR_NO_ORDER");
				return false;
			}
		}

		if (is_set($arFields, "CURRENCY"))
		{
			if (!($arCurrency = CCurrency::GetByID($arFields["CURRENCY"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["CURRENCY"], GetMessage("SKGB_NO_CURRENCY")), "ERROR_NO_CURRENCY");
				return false;
			}
		}

		if (is_set($arFields, "LID"))
		{
			$dbSite = CSite::GetByID($arFields["LID"]);
			if (!$dbSite->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["LID"], GetMessage("SKGB_NO_SITE")), "ERROR_NO_SITE");
				return false;
			}
		}

		if ($ACTION!="ADD"
			&& (strlen($arFields["LID"])<=0
					&& (is_set($arFields, "PRICE") || is_set($arFields, "CURRENCY"))
				|| (is_set($arFields, "PRICE") && !is_set($arFields, "CURRENCY"))
				|| (!is_set($arFields, "PRICE") && is_set($arFields, "CURRENCY"))
				)
			)
		{
			$tmp_res = CSaleBasket::GetByID($ID);
			if (strlen($arFields["LID"])<=0)
				$arFields["LID"] = $tmp_res["LID"];
			if (!is_set($arFields, "PRICE"))
				$arFields["PRICE"] = $tmp_res["PRICE"];
			if (!is_set($arFields, "CURRENCY") || strlen($arFields["CURRENCY"])<=0)
				$arFields["CURRENCY"] = $tmp_res["CURRENCY"];
		}

		if (strlen($arFields["LID"])>0 && strlen($arFields["CURRENCY"])>0)
		{
			$BASE_LANG_CURR = CSaleLang::GetLangCurrency($arFields["LID"]);
			if ($BASE_LANG_CURR != $arFields["CURRENCY"])
			{
				$arFields["PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arFields["PRICE"], $arFields["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				if (is_set($arFields, "DISCOUNT_PRICE"))
					$arFields["DISCOUNT_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arFields["DISCOUNT_PRICE"], $arFields["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$arFields["CURRENCY"] = $BASE_LANG_CURR;
			}
		}


		// Changed by Sigurd, 2007-08-16
		if (is_set($arFields, "QUANTITY"))
			$arFields["QUANTITY"] = DoubleVal($arFields["QUANTITY"]);
		if ((is_set($arFields, "QUANTITY") || $ACTION=="ADD") && DoubleVal($arFields["QUANTITY"]) <= 0)
			$arFields["QUANTITY"] = 1;

		if (is_set($arFields, "DELAY") && $arFields["DELAY"]!="Y")
			$arFields["DELAY"]="N";
		if (is_set($arFields, "CAN_BUY") && $arFields["CAN_BUY"]!="Y")
			$arFields["CAN_BUY"]="N";

		if ((is_set($arFields, "NAME") || $ACTION=="ADD") && strlen($arFields["NAME"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty name field", "EMPTY_NAME");
			return false;
		}

		if ($ACTION=="ADD" && !is_set($arFields, "FUSER_ID"))
			$arFields["FUSER_ID"] = CSaleBasket::GetBasketUserID();

		if ((is_set($arFields, "FUSER_ID") || $ACTION=="ADD") && IntVal($arFields["FUSER_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty basket user field", "EMPTY_FUSER_ID");
			return false;
		}

		return true;
	}

	function _Update($ID, &$arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		//CSaleBasket::Init();

		if (!CSaleBasket::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$db_events = GetModuleEvents("sale", "OnBeforeBasketUpdateAfterCheck");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, Array($ID, &$arFields))===false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_basket", $arFields);
		if(strlen($strUpdate) > 0)
		{
			$strSql = "UPDATE b_sale_basket SET ".
						"	".$strUpdate.", ".
						"	DATE_UPDATE = ".$DB->GetNowFunction()." ".
						"WHERE ID = ".$ID." ";

			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		if (is_array($arFields["PROPS"]) && count($arFields["PROPS"])>0)
		{
			$sql = "DELETE FROM b_sale_basket_props WHERE BASKET_ID = ".$ID;

			$bProductXml = false;
			$bCatalogXml = false;
			foreach($arFields["PROPS"] as $prop)
			{
				if ($prop["CODE"] == "PRODUCT.XML_ID")
					$bProductXml = true;

				if ($prop["CODE"] == "CATALOG.XML_ID")
					$bCatalogXml = true;

				if ($bProductXml && $bCatalogXml)
					break;
			}
			
			if (!$bProductXml)
				$sql .= " AND CODE <> 'PRODUCT.XML_ID'";

			if (!$bCatalogXml)
				$sql .= " AND CODE <> 'CATALOG.XML_ID'";

			$DB->Query($sql);

			foreach($arFields["PROPS"] as $prop)
			{
				if(strlen($prop["NAME"]) > 0)
				{
					$arInsert = $DB->PrepareInsert("b_sale_basket_props", $prop);
					$strSql =
						"INSERT INTO b_sale_basket_props(BASKET_ID, ".$arInsert[0].") ".
						"VALUES(".$ID.", ".$arInsert[1].")";

					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
		}

		$events = GetModuleEvents("sale", "OnBasketUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, Array($ID, $arFields));

		return True;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		if (isset($arFields["ID"]))
			unset($arFields["ID"]);

		$ID = IntVal($ID);
		CSaleBasket::Init();

		$db_events = GetModuleEvents("sale", "OnBeforeBasketUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, Array($ID, &$arFields))===false)
				return false;

		if (is_set($arFields, "QUANTITY") && DoubleVal($arFields["QUANTITY"])<=0)
		{
			return CSaleBasket::Delete($ID);
		}
		else
		{
			return CSaleBasket::_Update($ID, $arFields);
		}
	}


	//************** BASKET USER ********************//
	function Init($bVar = False, $bSkipFUserInit = False)
	{
		$bSkipFUserInit = ($bSkipFUserInit ? True : False);

		$_SESSION["SALE_USER_ID"] = IntVal($_SESSION["SALE_USER_ID"]);
		if ($_SESSION["SALE_USER_ID"] <= 0)
			$_SESSION["SALE_USER_ID"] = CSaleUser::GetID($bSkipFUserInit);
	}

	function GetBasketUserID($bSkipFUserInit = False)
	{
		$bSkipFUserInit = ($bSkipFUserInit ? True : False);

		if (!array_key_exists("SALE_USER_ID", $_SESSION))
			$_SESSION["SALE_USER_ID"] = 0;

		CSaleBasket::Init(false, $bSkipFUserInit);

		return $_SESSION["SALE_USER_ID"];
	}


	//************** SELECT ********************//
	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql =
			"SELECT * ".
			"FROM b_sale_basket ".
			"WHERE ID = ".$ID."";
		$dbBasket = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($arBasket = $dbBasket->Fetch())
			return $arBasket;

		return False;
	}

	//************** CALLBACK FUNCTIONS ********************//
	static function ExecuteCallbackFunction($callbackFunc = "", $module = "", $productID = 0)
	{
		$callbackFunc = trim($callbackFunc);
		$module = trim($module);
		$productID = IntVal($productID);

		$result = False;

		if (strlen($callbackFunc) > 0)
		{
			if (strlen($module)>0 && $module != "main")
				CModule::IncludeModule($module);

			$arArgs = array($productID);
			$numArgs = func_num_args();
			if ($numArgs > 3)
				for ($i = 3; $i < $numArgs; $i++)
					$arArgs[] = func_get_arg($i);

			$result = call_user_func_array($callbackFunc, $arArgs);
		}

		return $result;

		/*
		$callbackFunc = trim($callbackFunc);
		$productID = IntVal($productID);
		$module = Trim($module);
		$quantity = IntVal($quantity);

		$result = False;
		if (strlen($callbackFunc) > 0)
		{
			if (strlen($module)>0 && $module != "main")
				CModule::IncludeModule($module);

			$result = $callbackFunc($PRODUCT_ID, $QUANTITY, $arParams);
		}
		return $result;
		*/
	}

	function ReReadPrice($callbackFunc = "", $module = "", $productID = 0, $quantity = 0, $renewal = "N")
	{
		return CSaleBasket::ExecuteCallbackFunction($callbackFunc, $module, $productID, $quantity, $renewal);
	}

	function OnOrderProduct($callbackFunc = "", $module = "", $productID = 0, $quantity = 0)
	{
		CSaleBasket::ExecuteCallbackFunction($callbackFunc, $module, $productID, $quantity);
		return True;
	}

	function UpdatePrice($ID, $callbackFunc = "", $module = "", $productID = 0, $quantity = 0, $renewal = "N")
	{
		$callbackFunc = trim($callbackFunc);
		$productID = IntVal($productID);
		$module = Trim($module);
		$quantity = DoubleVal($quantity); // Changed by Sigurd, 2007-08-16
		$renewal = (($renewal == "Y") ? "Y" : "N");

		if (strlen($callbackFunc) <= 0 || $productID <= 0)
		{
			$arBasket = CSaleBasket::GetByID($ID);

			$callbackFunc = Trim($arBasket["CALLBACK_FUNC"]);
			$module = Trim($arBasket["MODULE"]);
			$productID = IntVal($arBasket["PRODUCT_ID"]);
			$quantity = DoubleVal($arBasket["QUANTITY"]); // Changed by Sigurd, 2007-08-16
		}

		$arFields = CSaleBasket::ExecuteCallbackFunction($callbackFunc, $module, $productID, $quantity, $renewal);
		if ($arFields && is_array($arFields) && count($arFields) > 0)
		{
			$arFields["CAN_BUY"] = "Y";
			CSaleBasket::Update($ID, $arFields);
		}
		else
		{
			UnSet($arFields);
			$arFields["CAN_BUY"] = "N";
			CSaleBasket::Update($ID, $arFields);
		}
	}

	function OrderBasket($orderID, $fuserID = 0, $strLang = LANG, $arDiscounts = False)
	{
		global $DB, $APPLICATION;

		$orderID = IntVal($orderID);
		if ($orderID <= 0)
			return False;

		$fuserID = IntVal($fuserID);
		if ($fuserID <= 0/* || $APPLICATION->GetGroupRight("sale") < "W"*/)
			$fuserID = CSaleBasket::GetBasketUserID();

		$dbBasketList = CSaleBasket::GetList(
				array("PRICE" => "DESC"),
				array("FUSER_ID" => $fuserID, "LID" => $strLang, "ORDER_ID" => 0)
			);
		while ($arBasket = $dbBasketList->Fetch())
		{
			/*
			if (strlen($arBasket["CALLBACK_FUNC"]) > 0)
			{
				CSaleBasket::UpdatePrice($arBasket["ID"], $arBasket["CALLBACK_FUNC"], $arBasket["MODULE"], $arBasket["PRODUCT_ID"], $arBasket["QUANTITY"]);
				$arBasket = CSaleBasket::GetByID($arBasket["ID"]);
			}
			*/
			$arFields = Array();
			if (/*$arBasket && */$arBasket["DELAY"]=="N" && $arBasket["CAN_BUY"]=="Y")
			{
				if (strlen($arBasket["ORDER_CALLBACK_FUNC"]) > 0)
				{
					$arFields = CSaleBasket::ExecuteCallbackFunction($arBasket["ORDER_CALLBACK_FUNC"], $arBasket["MODULE"], $arBasket["PRODUCT_ID"], $arBasket["QUANTITY"], $orderID);
					if ($arFields && is_array($arFields) && count($arFields) > 0)
					{
						$arFields["CAN_BUY"] = "Y";
					}
					elseif(is_array($arFields) && count($arFields) <= 0)
					{
						UnSet($arFields);
						$arFields["CAN_BUY"] = "N";
					}
				}

				if($arFields["CAN_BUY"] == "Y" || (empty($arFields) && $arBasket["CAN_BUY"]=="Y"))
				{
					$arFields["ORDER_ID"] = $orderID;
					//if ($arDiscounts && is_array($arDiscounts) && count($arDiscounts) > 0)
						//$arFields["DISCOUNT_PRICE"] = $arDiscounts[IntVal($arBasket["ID"])];
				}

				if(!empty($arFields))
				{
					if (CSaleBasket::Update($arBasket["ID"], $arFields))
						$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]--;
				}
			}
		}
		if ($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] < 0)
			$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] = 0;

		//$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] = 0;
		$events = GetModuleEvents("sale", "OnBasketOrder");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($orderID, $fuserID, $strLang, $arDiscounts));
	}

	function OrderPayment($orderID, $bPaid, $recurringID = 0)
	{
		CSaleBasket::OrderDelivery($orderID, $bPaid, $recurringID);
	}

	function OrderDelivery($orderID, $bPaid, $recurringID = 0)
	{
		global $DB, $APPLICATION;

		$orderID = IntVal($orderID);
		if ($orderID <= 0)
			return False;

		$bPaid = ($bPaid ? True : False);

		$recurringID = IntVal($recurringID);

		$arOrder = CSaleOrder::GetByID($orderID);
		if ($arOrder)
		{
			$dbBasketList = CSaleBasket::GetList(
					array("NAME" => "ASC"),
					array("ORDER_ID" => $orderID)
				);

			while ($arBasket = $dbBasketList->Fetch())
			{
				if (strlen($arBasket["PAY_CALLBACK_FUNC"]) > 0)
				{
					if ($bPaid)
					{
						$arFields = CSaleBasket::ExecuteCallbackFunction(
								$arBasket["PAY_CALLBACK_FUNC"],
								$arBasket["MODULE"],
								$arBasket["PRODUCT_ID"],
								$arOrder["USER_ID"],
								$bPaid,
								$orderID,
								$arBasket["QUANTITY"]
							);

						if ($arFields && is_array($arFields) && count($arFields) > 0)
						{
							$arFields["ORDER_ID"] = $orderID;
							$arFields["REMAINING_ATTEMPTS"] = (Defined("SALE_PROC_REC_ATTEMPTS") ? SALE_PROC_REC_ATTEMPTS : 3);
							$arFields["SUCCESS_PAYMENT"] = "Y";

							if ($recurringID > 0)
								CSaleRecurring::Update($recurringID, $arFields);
							else
								CSaleRecurring::Add($arFields);
						}
						elseif ($recurringID > 0)
						{
							CSaleRecurring::Delete($recurringID);
						}
					}
					else
					{
						CSaleBasket::ExecuteCallbackFunction(
								$arBasket["PAY_CALLBACK_FUNC"],
								$arBasket["MODULE"],
								$arBasket["PRODUCT_ID"],
								$arOrder["USER_ID"],
								$bPaid,
								$orderID,
								$arBasket["QUANTITY"]
							);

						$dbRecur = CSaleRecurring::GetList(
								array(),
								array(
										"USER_ID" => $arOrder["USER_ID"],
										"PRODUCT_ID" => $arBasket["PRODUCT_ID"],
										"MODULE" => $arBasket["MODULE"]
									)
							);
						while ($arRecur = $dbRecur->Fetch())
						{
							CSaleRecurring::Delete($arRecur["ID"]);
						}
					}
				}
			}
		}
	}

	function OrderCanceled($orderID, $bCancel)
	{
		global $DB;

		$orderID = IntVal($orderID);
		if ($orderID <= 0)
			return False;

		$bCancel = ($bCancel ? True : False);

		$arOrder = CSaleOrder::GetByID($orderID);
		if ($arOrder)
		{
			$dbBasketList = CSaleBasket::GetList(
					array("NAME" => "ASC"),
					array("ORDER_ID" => $orderID)
				);
			while ($arBasket = $dbBasketList->Fetch())
			{
				if (strlen($arBasket["CANCEL_CALLBACK_FUNC"]) > 0)
				{
					$arFields = CSaleBasket::ExecuteCallbackFunction(
							$arBasket["CANCEL_CALLBACK_FUNC"],
							$arBasket["MODULE"],
							$arBasket["PRODUCT_ID"],
							$arBasket["QUANTITY"],
							$bCancel
						);
				}
			}
		}
	}

	function TransferBasket($FROM_FUSER_ID, $TO_FUSER_ID)
	{
		$FROM_FUSER_ID = IntVal($FROM_FUSER_ID);
		$TO_FUSER_ID = IntVal($TO_FUSER_ID);

		if ($TO_FUSER_ID>0 && $FROM_FUSER_ID > 0)
		{
			$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] = 0;
			$dbTmp = CSaleUser::GetList(array("ID"=>$TO_FUSER_ID));
			if(!empty($dbTmp))
			{
				$arOldBasket = Array();
				$dbBasket = CSaleBasket::GetList(Array(), Array("FUSER_ID" => $TO_FUSER_ID, "ORDER_ID" => false));
				while($arBasket = $dbBasket->Fetch())
				{
					$arOldBasket[$arBasket["PRODUCT_ID"]] = $arBasket;
					$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]++;
				}

				$dbBasket = CSaleBasket::GetList(Array(), Array("FUSER_ID" => $FROM_FUSER_ID, "ORDER_ID" => false));
				while($arBasket = $dbBasket->Fetch())
				{
					$arUpdate = Array("FUSER_ID" => $TO_FUSER_ID);
					if(!empty($arOldBasket[$arBasket["PRODUCT_ID"]]))
					{
						$arUpdate["QUANTITY"] = $arBasket["QUANTITY"] + $arOldBasket[$arBasket["PRODUCT_ID"]]["QUANTITY"];
						CSaleBasket::Delete($arBasket["ID"]);
						CSaleBasket::_Update($arOldBasket[$arBasket["PRODUCT_ID"]]["ID"], $arUpdate);
					}
					else
					{
						$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]++;
						CSaleBasket::_Update($arBasket["ID"], $arUpdate);
					}
				}
				return true;
			}
		}
		return false;
	}

	function UpdateBasketPrices($fuserID, $siteID)
	{
		if(IntVal($fuserID) <= 0)
			return false;
		if(strlen($siteID) <= 0)
			$siteID = SITE_ID;

		$dbBasketItems = CSaleBasket::GetList(
				array(
						"ALL_PRICE" => "DESC",
					),
				array(
						"FUSER_ID" => $fuserID,
						"LID" => $siteID,
						"ORDER_ID" => "NULL",
						//"CAN_BUY" => "Y",
						"SUBSCRIBE" => "N",
					),
				false,
				false,
				array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY")
			);
		while ($arItems = $dbBasketItems->Fetch())
		{
			if (strlen($arItems["CALLBACK_FUNC"]) > 0)
			{
				CSaleBasket::UpdatePrice($arItems["ID"], $arItems["CALLBACK_FUNC"], $arItems["MODULE"], $arItems["PRODUCT_ID"], $arItems["QUANTITY"]);
			}
		}
	}
}

function TmpDumpToFile($txt)
{
	if (strlen($txt)>0)
	{
		$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/!!!!!.txt", "ab+");
		fputs($fp, $txt);
		@fclose($fp);
	}
}

class CAllSaleUser
{
	public static function DoAutoRegisterUser($autoEmail, $payerName, $siteId, &$arErrors, $arOtherFields = null)
	{
		$autoEmail = trim($autoEmail);
		if (empty($autoEmail))
			return null;

		if ($siteId == null)
			$siteId = SITE_ID;

		$autoName = "";
		$autoLastName = "";
		if (!is_array($payerName) && (strlen($payerName) > 0))
		{
			$arNames = explode(" ", $payerName);
			$autoName = $arNames[1];
			$autoLastName = $arNames[0];
		}
		elseif (is_array($payerName))
		{
			$autoName = $payerName["NAME"];
			$autoLastName = $payerName["LAST_NAME"];
		}

		$autoLogin = $autoEmail;

		$pos = strpos($autoLogin, "@");
		if ($pos !== false)
			$autoLogin = substr($autoLogin, 0, $pos);

		if (strlen($autoLogin) > 47)
			$autoLogin = substr($autoLogin, 0, 47);

		while (strlen($autoLogin) < 3)
			$autoLogin .= "_";

		$idx = 0;
		$loginTmp = $autoLogin;
		$dbUserLogin = CUser::GetByLogin($autoLogin);
		while ($arUserLogin = $dbUserLogin->Fetch())
		{
			$idx++;
			if ($idx == 10)
			{
				$autoLogin = $autoEmail;
			}
			elseif ($idx > 10)
			{
				$autoLogin = "buyer".time().GetRandomCode(2);
				break;
			}
			else
			{
				$autoLogin = $loginTmp.$idx;
			}
			$dbUserLogin = CUser::GetByLogin($autoLogin);
		}

		$defaultGroup = COption::GetOptionString("main", "new_user_registration_def_group", "");
		if ($defaultGroup != "")
		{
			$arDefaultGroup = explode(",", $defaultGroup);
			$arPolicy = CUser::GetGroupPolicy($arDefaultGroup);
		}
		else
		{
			$arPolicy = CUser::GetGroupPolicy(array());
		}

		$passwordMinLength = intval($arPolicy["PASSWORD_LENGTH"]);
		if ($passwordMinLength <= 0)
			$passwordMinLength = 6;
		$passwordChars = array(
			"abcdefghijklnmopqrstuvwxyz",
			"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			"0123456789",
		);
		if ($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
			$passwordChars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";

		$autoPassword = randString($passwordMinLength + 2, $passwordChars);

		$arFields = array(
			"LOGIN" => $autoLogin,
			"NAME" => $autoName,
			"LAST_NAME" => $autoLastName,
			"PASSWORD" => $autoPassword,
			"PASSWORD_CONFIRM" => $autoPassword,
			"EMAIL" => $autoEmail,
			"GROUP_ID" => $arDefaultGroup,
			"ACTIVE" => "Y",
			"LID" => $siteId,
		);
		if (is_array($arOtherFields))
		{
			foreach ($arOtherFields as $key => $value)
			{
				if (!array_key_exists($key, $arFields))
					$arFields[$key] = $value;
			}
		}

		$user = new CUser;
		$userId = $user->Add($arFields);

		if (intval($userId) <= 0)
		{
			$arErrors[] = array("TEXT" => GetMessage("STOF_ERROR_REG").((strlen($user->LAST_ERROR) > 0) ? ": ".$user->LAST_ERROR : ""));
			return 0;
		}

		return $userId;
	}

	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		return True;
	}

	function GetID($bSkipFUserInit = False)
	{
		global $USER;

		$bSkipFUserInit = ($bSkipFUserInit ? True : False);

		$cookie_name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");
		$ID = IntVal($_COOKIE[$cookie_name."_SALE_UID"]);

		$dbFUserList = False;
		if ($ID > 0)
		{
			$dbFUserListTmp = CSaleUser::GetList(array("ID" => $ID));
			if(!empty($dbFUserListTmp))
				$dbFUserList = true;
		}

		if ($ID > 0)
		{
			if ($dbFUserList)
			{
				CSaleUser::Update($ID);
			}
			else
			{
				$newID = CSaleUser::Add();
				CSaleBasket::TransferBasket($ID, $newID);
				$ID = $newID;
			}
		}
		elseif (!$bSkipFUserInit)
		{
			$ID = CSaleUser::Add();
		}

		return IntVal($ID);
	}

	function Update($ID)
	{
		global $DB, $USER;

		if (!is_object($USER))
			$USER = new CUser;

		$ID = IntVal($ID);

		$arFields = array(
				"=DATE_UPDATE" => $DB->GetNowFunction(),
			);
		if ($USER->IsAuthorized())
			$arFields["USER_ID"] = IntVal($USER->GetID());

		CSaleUser::_Update($ID, $arFields);

		$secure = false;
		if(COption::GetOptionString("sale", "use_secure_cookies", "N") == "Y" && CMain::IsHTTPS())
			$secure=1;
		$GLOBALS["APPLICATION"]->set_cookie("SALE_UID", $ID, false, "/", false, $secure, "Y", false);


		return true;
	}

	function _Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1)=="=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSaleUser::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_fuser", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate)>0) $strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		$strSql = "UPDATE b_sale_fuser SET ".$strUpdate." WHERE ID = ".$ID." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}

	function GetList($arFilter)
	{
		global $DB;
		$arSqlSearch = Array();

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		$countarFilter = count($filter_keys);
		for ($i=0; $i < $countarFilter; $i++)
		{
			$val = $DB->ForSql($arFilter[$filter_keys[$i]]);
			if (strlen($val)<=0) continue;

			$key = $filter_keys[$i];
			if ($key[0]=="!")
			{
				$key = substr($key, 1);
				$bInvert = true;
			}
			else
				$bInvert = false;

			switch(ToUpper($key))
			{
			case "ID":
				$arSqlSearch[] = "ID ".($bInvert?"<>":"=")." ".IntVal($val)." ";
				break;
			case "USER_ID":
				$arSqlSearch[] = "USER_ID ".($bInvert?"<>":"=")." ".IntVal($val)." ";
				break;
			}
		}

		$strSqlSearch = "";
		$countSqlSearch = count($arSqlSearch);
		for($i=0; $i < $countSqlSearch; $i++)
		{
			$strSqlSearch .= " AND ";
			$strSqlSearch .= " (".$arSqlSearch[$i].") ";
		}

		$strSql =
			"SELECT ID, DATE_INSERT, DATE_UPDATE, USER_ID ".
			"FROM b_sale_fuser ".
			"WHERE 1 = 1 ".$strSqlSearch;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res->Fetch();
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$events = GetModuleEvents("sale", "OnSaleUserDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, Array($ID));

		$DB->Query("DELETE FROM b_sale_fuser WHERE ID = ".$ID." ", true);

		return true;
	}

	function OnUserLogin($new_user_id)
	{
		$cookie_name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");
		$ID = IntVal($_SESSION["SALE_USER_ID"]);
		if ($ID <= 0)
			$ID = IntVal($_COOKIE[$cookie_name."_SALE_UID"]);
		$res = CSaleUser::GetList(array("!ID" => IntVal($ID), "USER_ID" => IntVal($new_user_id)));
		if (!empty($res))
		{
			if ($ID>0)
			{
				if (CSaleBasket::TransferBasket($ID, $res["ID"]))
				{
					CSaleUser::Delete($ID);
				}
			}
			$ID = IntVal($res["ID"]);
		}
		CSaleUser::Update($ID);

		$_SESSION["SALE_USER_ID"] = $ID;
		$_SESSION["SALE_BASKET_NUM_PRODUCTS"] = Array();

		return true;
	}

	function OnUserLogout($userID)
	{
		$_SESSION["SALE_USER_ID"] = 0;
		$_SESSION["SALE_BASKET_NUM_PRODUCTS"] = Array();;

		$secure = false;
		if(COption::GetOptionString("sale", "use_secure_cookies", "N") == "Y" && CMain::IsHTTPS())
			$secure=1;
		$GLOBALS["APPLICATION"]->set_cookie("SALE_UID", 0, false, "/", false, $secure, "Y", false);


		$cookie_name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");
		$_COOKIE[$cookie_name."_SALE_UID"] = 0;
	}

	function DeleteOldAgent($nDays)
	{
		if (!isset($GLOBALS["USER"]) || !is_object($GLOBALS["USER"]))
		{
			$bTmpUser = True;
			$GLOBALS["USER"] = new CUser;
		}
		CSaleUser::DeleteOld($nDays);

		global $pPERIOD;
		$pPERIOD = 8*60*60;

		if ($bTmpUser)
		{
			unset($GLOBALS["USER"]);
		}

		return "CSaleUser::DeleteOldAgent(".IntVal(COption::GetOptionString("sale", "delete_after", "360")).");";
	}

	function OnUserDelete($userID)
	{
		if($userID<=0)
			return false;
		$arSUser = CSaleUser::GetList(array("USER_ID" => $userID));
		if(!empty($arSUser))
		{
			if(!(CSaleBasket::DeleteAll($arSUser["ID"])))
				return false;
			if(!(CSaleUser::Delete($arSUser["ID"])))
				return false;
		}
		return true;
	}
}
?>