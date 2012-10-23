<?
/********************************************************************************
Component "Filter for the elements".

This component is intended for displaying filter form for elements.
 
Sample of usage:

$APPLICATION->IncludeFile("iblock/catalog/element_filter.php", Array(
	"IBLOCK_TYPE"		=> "catalog",
	"IBLOCK_ID"			=> "21",
	"arrFIELD_CODE"		=> Array("NAME"),
	"arrPROPERTY_CODE"	=> Array(
		"YEAR",
		"WEIGHT",
		"STANDART",
		"BATTERY",
		"WAP",
		"VIBRO",
		"VOICE",
		"PC"
		),
	"arrPRICE_CODE"		=> Array("RETAIL"),
	"CURRENCY_CODE"		=> "\$",
	"SAVE_IN_SESSION"	=> "N",
	"FILTER_NAME"		=> "arrFilter",
	"LIST_HEIGHT"		=> "5",
	"TEXT_WIDTH"		=> "24",
	"NUMBER_WIDTH"		=> "5",
	"CACHE_TIME"		=> "0",
));

Parameters:

IBLOCK_TYPE - Information block type
IBLOCK_ID - Information block ID
arrFIELD_CODE -  array of the field identifiers, the following values can be used:
	ID - Element ID (complex logic can be used for this field)
	NAME - element title (complex logic can be used for this field)
	PREVIEW_TEXT - announce text (complex logic can be used for this field)
	DETAIL_TEXT - detailed element description (complex logic can be used for this field)
	SEARCHABLE_CONTENT - title and description of an element (complex logic can be used for this field)
	SECTION_ID - drop-down list of the information block groups
	DATE_ACTIVE_FROM - interval for activity date FROM
	DATE_ACTIVE_TO - interval for activity date TILL
arrPROPERTY_CODE - array of property mnemonic codes
arrPRICE_CODE - array of price type mnemonic codes
CURRENCY_CODE - currency sign for the Prices related filter fields 
SAVE_IN_SESSION - [Y|N] Y - keep filter fields values in session
FILTER_NAME - name of the received array with filter values
LIST_HEIGHT - height of the multi-select list
TEXT_WIDTH - width of the single-line text input fields
NUMBER_WIDTH - width of the input fields for digits intervals
CACHE_TIME - (sec.) time to cache the values selected from database and used for filter form

************************************************/

global $USER, $APPLICATION;
if (CModule::IncludeModule("iblock")):

	IncludeTemplateLangFile(__FILE__);

	/*************************************************************************
						Processing of the received parameters
	*************************************************************************/

	$arrFIELD_CODE = is_array($arrFIELD_CODE) ? $arrFIELD_CODE : array();
	$arrPROPERTY_CODE = is_array($arrPROPERTY_CODE) ? $arrPROPERTY_CODE : array();
	$arrPRICE_CODE = is_array($arrPRICE_CODE) ? $arrPRICE_CODE : array();

	global $$FILTER_NAME;
	$$FILTER_NAME = array();


	/*************************************************************************
				Processing the  "Filter" and "Reset" button actions
	*************************************************************************/

	if (in_array("ACTIVE_DATE", $arrFIELD_CODE))
	{
		$active_date_1 = $FILTER_NAME."_ACTIVE_DATE_1";
		$active_date_2 = $FILTER_NAME."_ACTIVE_DATE_2";
		$active_date_days_to_back = $active_date_1."_DAYS_TO_BACK";
		global $$active_date_days_to_back;
	}
	if (in_array("DATE_ACTIVE_FROM", $arrFIELD_CODE))
	{
		$date_active_from_1 = $FILTER_NAME."_DATE_ACTIVE_FROM_1";
		$date_active_from_2 = $FILTER_NAME."_DATE_ACTIVE_FROM_2";
		$date_active_from_days_to_back = $date_active_from_1."_DAYS_TO_BACK";
		global $$date_active_from_days_to_back;
	}
	if (in_array("DATE_ACTIVE_TO", $arrFIELD_CODE))
	{
		$date_active_to_1 = $FILTER_NAME."_DATE_ACTIVE_TO_1";
		$date_active_to_2 = $FILTER_NAME."_DATE_ACTIVE_TO_2";
		$date_active_to_days_to_back = $date_active_to_1."_DAYS_TO_BACK";
		global $$date_active_to_days_to_back;
	}

	if (strlen($_REQUEST["set_filter"])>0)
	{
		$arrPFV = $_REQUEST[$FILTER_NAME."_pf"];
		$arrCFV = $_REQUEST[$FILTER_NAME."_cf"];
		$arrFFV = $_REQUEST[$FILTER_NAME."_ff"];
		if (in_array("ACTIVE_DATE", $arrFIELD_CODE))
		{
			${$active_date_1} = $_REQUEST[$active_date_1];
			${$active_date_2} = $_REQUEST[$active_date_2];
			${$active_date_days_to_back} = $_REQUEST[$active_date_days_to_back];
			if (strlen(${$active_date_days_to_back})>0)
				${$active_date_1} = GetTime(time()-86400*intval(${$active_date_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_FROM", $arrFIELD_CODE))
		{
			${$date_active_from_1} = $_REQUEST[$date_active_from_1];
			${$date_active_from_2} = $_REQUEST[$date_active_from_2];
			${$date_active_from_days_to_back} = $_REQUEST[$date_active_from_days_to_back];
			if (strlen(${$date_active_from_days_to_back})>0)
				${$date_active_from_1} = GetTime(time()-86400*intval(${$date_active_from_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_TO", $arrFIELD_CODE))
		{
			${$date_active_to_1} = $_REQUEST[$date_active_to_1];
			${$date_active_to_2} = $_REQUEST[$date_active_to_2];
			${$date_active_to_days_to_back} = $_REQUEST[$date_active_to_days_to_back];
			if (strlen(${$date_active_to_days_to_back})>0)
				${$date_active_to_1} = GetTime(time()-86400*intval(${$date_active_to_days_to_back}));
		}

		if ($SAVE_IN_SESSION=="Y")
		{
			$_SESSION[$FILTER_NAME."arrPFV"] = $arrPFV;
			$_SESSION[$FILTER_NAME."arrCFV"] = $arrCFV;
			$_SESSION[$FILTER_NAME."arrFFV"] = $arrFFV;
			if (in_array("ACTIVE_DATE", $arrFIELD_CODE))
			{
				$_SESSION[$active_date_1] = $_REQUEST[$active_date_1];
				$_SESSION[$active_date_2] = $_REQUEST[$active_date_2];
				$_SESSION[$active_date_days_to_back] = $_REQUEST[$active_date_days_to_back];
			}
			if (in_array("DATE_ACTIVE_FROM", $arrFIELD_CODE))
			{
				$_SESSION[$date_active_from_1] = $_REQUEST[$date_active_from_1];
				$_SESSION[$date_active_from_2] = $_REQUEST[$date_active_from_2];
				$_SESSION[$date_active_from_days_to_back] = $_REQUEST[$date_active_from_days_to_back];
			}
			if (in_array("DATE_ACTIVE_TO", $arrFIELD_CODE))
			{
				$_SESSION[$date_active_to_1] = $_REQUEST[$date_active_to_1];
				$_SESSION[$date_active_to_2] = $_REQUEST[$date_active_to_2];
				$_SESSION[$date_active_to_days_to_back] = $_REQUEST[$date_active_to_days_to_back];
			}
		}
	}
	elseif ($SAVE_IN_SESSION=="Y")
	{
		$arrPFV = $_SESSION[$FILTER_NAME."arrPFV"];
		$arrCFV = $_SESSION[$FILTER_NAME."arrCFV"];
		$arrFFV = $_SESSION[$FILTER_NAME."arrFFV"];
		if (in_array("ACTIVE_DATE", $arrFIELD_CODE))
		{
			${$active_date_1} = $_SESSION[$active_date_1];
			${$active_date_2} = $_SESSION[$active_date_2];
			${$active_date_days_to_back} = $_SESSION[$active_date_days_to_back];
			if (strlen(${$active_date_days_to_back})>0)
				${$active_date_1} = GetTime(time()-86400*intval(${$active_date_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_FROM", $arrFIELD_CODE))
		{
			${$date_active_from_1} = $_SESSION[$date_active_from_1];
			${$date_active_from_2} = $_SESSION[$date_active_from_2];
			${$date_active_from_days_to_back} = $_SESSION[$date_active_from_days_to_back];
			if (strlen(${$date_active_from_days_to_back})>0)
				${$date_active_from_1} = GetTime(time()-86400*intval(${$date_active_from_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_TO", $arrFIELD_CODE))
		{
			${$date_active_to_1} = $_SESSION[$date_active_to_1];
			${$date_active_to_2} = $_SESSION[$date_active_to_2];
			${$date_active_to_days_to_back} = $_SESSION[$date_active_to_days_to_back];
			if (strlen(${$date_active_to_days_to_back})>0)
				${$date_active_to_1} = GetTime(time()-86400*intval(${$date_active_to_days_to_back}));
		}
	}
	if (strlen($_REQUEST["del_filter"])>0) 
	{
		$arrPFV = array();
		$arrCFV = array();
		$arrFFV = array();
		if (in_array("ACTIVE_DATE", $arrFIELD_CODE))
		{
			${$active_date_1} = "";
			${$active_date_2} = "";
			${$active_date_days_to_back} = "";
		}
		if (in_array("DATE_ACTIVE_FROM", $arrFIELD_CODE))
		{
			${$date_active_from_1} = "";
			${$date_active_from_2} = "";
			${$date_active_from_days_to_back} = "";
		}
		if (in_array("DATE_ACTIVE_TO", $arrFIELD_CODE))
		{
			${$date_active_to_1} = "";
			${$date_active_to_2} = "";
			${$date_active_to_days_to_back} = "";
		}
		if ($SAVE_IN_SESSION=="Y")
		{
			unset($_SESSION[$FILTER_NAME."arrPFV"]);
			unset($_SESSION[$FILTER_NAME."arrCFV"]);
			unset($_SESSION[$FILTER_NAME."arrFFV"]);
			if (in_array("ACTIVE_DATE", $arrFIELD_CODE))
			{
				unset($_SESSION[$active_date_1]);
				unset($_SESSION[$active_date_2]);
				unset($_SESSION[$active_date_days_to_back]);
			}
			if (in_array("DATE_ACTIVE_FROM", $arrFIELD_CODE))
			{
				unset($_SESSION[$date_active_from_1]);
				unset($_SESSION[$date_active_from_2]);
				unset($_SESSION[$date_active_from_days_to_back]);
			}
			if (in_array("DATE_ACTIVE_TO", $arrFIELD_CODE))
			{
				unset($_SESSION[$date_active_to_1]);
				unset($_SESSION[$date_active_to_2]);
				unset($_SESSION[$date_active_to_days_to_back]);
			}
		}
	}

	/*************************************************************************
								Work with cache
	*************************************************************************/

	$arrProp = array();
	$arrPrice = array();
	$arrSection = array();
	$obCache = new CPHPCache;
	$CACHE_ID = SITE_ID."|".__FILE__."|".md5(serialize($arParams)."|".$USER->GetGroups());
	if($obCache->InitCache($CACHE_TIME, $CACHE_ID, "/"))
	{
		$arVars = $obCache->GetVars();
		$arrProp = $arVars["arrProp"];
		$arrPrice = $arVars["arrPrice"];
		$arrSection = $arVars["arrSection"];
	}
	else
	{
		// простые поля
		if (in_array("SECTION_ID", $arrFIELD_CODE))
		{
			$arrSection[0] = GetMessage("IBLOCK_TOP_LEVEL");
			$rsSection = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID));
			while($arSection = $rsSection->Fetch())
			{
				$arrSection[$arSection["ID"]] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]).$arSection["NAME"];
			}
		}

		// цены
		if (CModule::IncludeModule("catalog"))
		{
			$rsPrice = CCatalogGroup::GetList($v1, $v2);
			while($arPrice = $rsPrice->Fetch())
			{
				if (in_array($arPrice["NAME"],$arrPRICE_CODE)) $arrPrice[$arPrice["NAME"]] = array("ID"=>$arPrice["ID"], "TITLE"=>$arPrice["NAME_LANG"]);
			}
		}

		// свойства
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
		while ($arProp = $rsProp->Fetch())
		{
			if (in_array($arProp["CODE"],$arrPROPERTY_CODE) && in_array($arProp["PROPERTY_TYPE"], array("L", "N", "S")))
			{
				$arrProp[$arProp["ID"]]["CODE"] = $arProp["CODE"];
				$arrProp[$arProp["ID"]]["NAME"] = $arProp["NAME"];
				$arrProp[$arProp["ID"]]["PROPERTY_TYPE"] = $arProp["PROPERTY_TYPE"];
				if ($arProp["MULTIPLE"]=="Y") $arrProp[$arProp["ID"]]["MULTIPLE"] = $arProp["MULTIPLE"];			
				if ($arProp["PROPERTY_TYPE"]=="L")
				{
					$arrEnum = array();
					$rsEnum = CIBlockProperty::GetPropertyEnum($arProp["ID"]);
					while($arEnum = $rsEnum->Fetch())
					{
						$arrEnum[$arEnum["ID"]] = $arEnum["VALUE"];
					}
					$arrProp[$arProp["ID"]]["VALUE_LIST"] = $arrEnum;
				}
			}
		}
	}
	if($obCache->StartDataCache()):
		$obCache->EndDataCache(Array("arrSection" => $arrSection, "arrProp" => $arrProp, "arrPrice" => $arrPrice));
	endif;


	/*************************************************************************
						Adding the titles and input fields
	*************************************************************************/
	
	$arrInputNames = array(); // array of the input field names; is being used in the functionи $APPLICATION->GetCurPageParam
	
	// simple fields
	$arrFIELDS = array();
	if (count($arrFIELD_CODE)>0):
		reset($arrFIELD_CODE);
		foreach($arrFIELD_CODE as $field_code):
			$field_name = GetMessage("IBLOCK_FILTER_FIELD_".$field_code);
			$arrInputNames[] = $FILTER_NAME."_ff";
			$name = $FILTER_NAME."_ff[".$field_code."]";			
			$value = $arrFFV[$field_code];
			switch ($field_code):
				case "ID":
				case "NAME":
				case "PREVIEW_TEXT":
				case "DETAIL_TEXT":
				case "SEARCHABLE_CONTENT":
					$field_res = "<input class=\"inputfield\" type=\"text\" name=\"".$name."\" size=\"".$TEXT_WIDTH."\" value=\"".htmlspecialchars($value)."\">";

					if (strlen($value)>0)
						${$FILTER_NAME}["?".$field_code] = $value;

					break;
				case "SECTION_ID":
					$arrRef = array("reference" => array_values($arrSection), "reference_id" => array_keys($arrSection));
					$field_res = SelectBoxFromArray($name, $arrRef, $value, " ", "class=\"inputselect\"");

					if ($value!="NOT_REF" && strlen($value)>0)
						${$FILTER_NAME}[$field_code] = intval($value);

					$_name = $FILTER_NAME."_ff[INCLUDE_SUBSECTIONS]";
					$_value = $arrFFV["INCLUDE_SUBSECTIONS"];
					$field_res .= "<br>".InputType("checkbox", $_name, "Y", $_value, false, "", "")."&nbsp;".GetMessage("IBLOCK_INCLUDE_SUBSECTIONS");

					if (strlen($value)>0 && $_value=="Y") ${$FILTER_NAME}["INCLUDE_SUBSECTIONS"] = "Y";

					break;

				case "ACTIVE_DATE":

					$arrInputNames[] = $FILTER_NAME."_ACTIVE_DATE_1";
					$arrInputNames[] = $FILTER_NAME."_ACTIVE_DATE_2";
					$arrInputNames[] = $FILTER_NAME."_ACTIVE_DATE_1_DAYS_TO_BACK";

					$field_name = GetMessage("IBLOCK_FILTER_FIELD_".$field_code);
					$field_res = CalendarPeriod($active_date_1, ${$active_date_1}, $active_date_2, ${$active_date_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

					if (strlen(${$active_date_1})>0)
						${$FILTER_NAME}[">=DATE_ACTIVE_FROM"] = ${$active_date_1};

					if (strlen(${$active_date_2})>0)
						${$FILTER_NAME}["<=DATE_ACTIVE_TO"] = ${$active_date_2};

					break;

				case "DATE_ACTIVE_FROM":

					$arrInputNames[] = $FILTER_NAME."_DATE_ACTIVE_FROM_1";
					$arrInputNames[] = $FILTER_NAME."_DATE_ACTIVE_FROM_2";
					$arrInputNames[] = $FILTER_NAME."_DATE_ACTIVE_FROM_1_DAYS_TO_BACK";

					$field_name = GetMessage("IBLOCK_FILTER_FIELD_".$field_code);
					$field_res = CalendarPeriod($date_active_from_1, ${$date_active_from_1}, $date_active_from_2, ${$date_active_from_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

					if (strlen(${$date_active_from_1})>0)
						${$FILTER_NAME}[">=".$field_code] = ${$date_active_from_1};

					if (strlen(${$date_active_from_2})>0)
						${$FILTER_NAME}["<=".$field_code] = ${$date_active_from_2};

					break;

				case "DATE_ACTIVE_TO":

					$arrInputNames[] = $FILTER_NAME."_DATE_ACTIVE_TO_1";
					$arrInputNames[] = $FILTER_NAME."_DATE_ACTIVE_TO_2";
					$arrInputNames[] = $FILTER_NAME."_DATE_ACTIVE_TO_1_DAYS_TO_BACK";

					$field_name = GetMessage("IBLOCK_FILTER_FIELD_".$field_code);
					$field_res = CalendarPeriod($date_active_to_1, ${$date_active_to_1}, $date_active_to_2, ${$date_active_to_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

					if (strlen(${$date_active_to_1})>0)
						${$FILTER_NAME}[">=".$field_code] = ${$date_active_to_1};

					if (strlen(${$date_active_to_2})>0)
						${$FILTER_NAME}["<=".$field_code] = ${$date_active_to_2};

					break;

			endswitch;
			$arrFIELDS[] = array("NAME" => htmlspecialchars($field_name), "INPUT" => $field_res);
		endforeach;
	endif;

	$arrPROPERTY = array();
	if (is_array($arrProp) && count($arrProp)>0):
		reset($arrProp);
		while(list($prop_id, $arProp) = each($arrProp)):
			$res = "";
			$arrInputNames[] = $FILTER_NAME."_pf";
			switch ($arProp["PROPERTY_TYPE"]):

				case "L":

					$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";
					$value = $arrPFV[$arProp["CODE"]];
					$arrRef = array("reference" => array_values($arProp["VALUE_LIST"]), "reference_id" => array_keys($arProp["VALUE_LIST"]));
					if (count($arrRef["reference"])>0)
					{
						if ($arProp["MULTIPLE"]=="Y")
						{												
							$res .= SelectBoxMFromArray($name."[]", $arrRef, $value, "", false, $LIST_HEIGHT, "class=\"inputselect\"");

							if (is_array($value) && count($value)>0) 
								${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $value;
						}
						else
						{
							$res .= SelectBoxFromArray($name, $arrRef, $value, " ", "class=\"inputselect\"");

							if (strlen($value)>0) 
								${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $value;
						}
					}
					break;

				case "N":

					$name = $FILTER_NAME."_pf[".$arProp["CODE"]."][LEFT]";
					$value = $arrPFV[$arProp["CODE"]]["LEFT"];
					$res .= "<input class=\"inputfield\" type=\"text\" name=\"".$name."\" size=\"".$NUMBER_WIDTH."\" value=\"".htmlspecialchars($value)."\"><font class=\"tablebodytext\">&nbsp;".GetMessage("IBLOCK_TILL")."&nbsp;</font>";

					if (strlen($value)>0)
						${$FILTER_NAME}["PROPERTY"][">=".$arProp["CODE"]] = intval($value);

					$name = $FILTER_NAME."_pf[".$arProp["CODE"]."][RIGHT]";
					$value = $arrPFV[$arProp["CODE"]]["RIGHT"];
					$res .= "<input class=\"inputfield\" type=\"text\" name=\"".$name."\" size=\"".$NUMBER_WIDTH."\" value=\"".htmlspecialchars($value)."\">";

					if (strlen($value)>0)
						${$FILTER_NAME}["PROPERTY"]["<=".$arProp["CODE"]] = intval($value);

					break;

				case "S":

					$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";
					$value = $arrPFV[$arProp["CODE"]];
					$res .= "<input class=\"inputfield\" type=\"text\" name=\"".$name."\" size=\"".$TEXT_WIDTH."\" value=\"".htmlspecialchars($value)."\">";

					if (strlen($value)>0)
						${$FILTER_NAME}["PROPERTY"]["?".$arProp["CODE"]] = $value;

					break;
			endswitch;
			$arrPROPERTY[] = array("NAME" => htmlspecialchars($arProp["NAME"]), "INPUT" => $res);
		endwhile;
	endif;

	$arrPRICE = array();
	if (is_array($arrPrice)):
		reset($arrPrice);
		while (list($price_code, $arPrice) = each($arrPrice)):

			$res_price = "";
			$arrInputNames[] = $FILTER_NAME."_cf";

			$name = $FILTER_NAME."_cf[".$arPrice["ID"]."][LEFT]";
			$value = $arrCFV[$arPrice["ID"]]["LEFT"];

			if (strlen($value)>0)
				${$FILTER_NAME}[">=CATALOG_PRICE_".$arPrice["ID"]] = $value;

			$res_price .= "<input class=\"inputfield\" type=\"text\" name=\"".$name."\" size=\"".$NUMBER_WIDTH."\" value=\"".htmlspecialchars($value)."\">&nbsp;".GetMessage("IBLOCK_TILL")."&nbsp;";

			$name = $FILTER_NAME."_cf[".$arPrice["ID"]."][RIGHT]";
			$value = $arrCFV[$arPrice["ID"]]["RIGHT"];

			if (strlen($value)>0)
				${$FILTER_NAME}["<=CATALOG_PRICE_".$arPrice["ID"]] = $value;

			$res_price .= "<input class=\"inputfield\" type=\"text\" name=\"".$name."\" size=\"".$NUMBER_WIDTH."\" value=\"".htmlspecialchars($value)."\">";

			$arrPRICE[] = array("NAME" => htmlspecialchars($arPrice["TITLE"])."&nbsp;(".$CURRENCY_CODE.")", "INPUT" => $res_price);

		endwhile;
	endif;
	$arrInputNames[] = "set_filter";
	$arrInputNames[] = "del_filter";
	$arrInputNames = array_unique($arrInputNames);

	/****************************************************************
							HTML form
	****************************************************************/

	?>
	<table cellspacing=0 cellpadding=1 class="tableborder">
	<form name="<?=$FILTER_NAME."_form"?>" action="<?=$APPLICATION->GetCurPage()?>" method="GET">
		<?
		$arrREQ = array_merge($_GET, $_POST);
		reset($arrREQ);
		if (is_array($arrREQ) && count($arrREQ)>0):
			while(list($key,$value)=each($arrREQ)):
				if (!in_array($key, $arrInputNames)):
					?><input type="hidden" name="<?=htmlspecialchars($key)?>" value="<?=htmlspecialchars($value)?>"><?
				endif;
			endwhile;
		endif;
		?>
		<tr>
			<td><table cellspacing="0" cellpadding="2" class="tablebody" width="100%">
					<tr>
						<td class="tablehead" colspan="2" align="center"><font class="tableheadtext"><?=GetMessage("IBLOCK_FILTER_TITLE")?></font></td>
					</tr>
					<?
					$arrFIELDS = array_merge($arrFIELDS, $arrPROPERTY);
					$arrFIELDS = array_merge($arrFIELDS, $arrPRICE);
					foreach($arrFIELDS as $arr):?>
					<tr>
						<td valign="top"><font class="tablebodytext"><?=$arr["NAME"]?>:</font></td>
						<td valign="top"><font class="tablebodytext"><?=$arr["INPUT"]?></font></td>
					</tr>
					<?endforeach;?>
					<tr>
						<td colspan="2"><font class="tablebodytext"><input class="inputbuttonflat" type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>"><input type="hidden" name="set_filter" value="Y">&nbsp;&nbsp;<input class="inputbuttonflat" type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>"></font></td>
					</tr>
				</table>
			</td>
		</tr>
	</form>
	</table>	
	<?
endif;
?>