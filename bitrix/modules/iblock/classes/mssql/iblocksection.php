<?
//IncludeModuleLangFile(__FILE__);
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblocksection.php");

class CIBlockSection extends CAllIBlockSection
{
	///////////////////////////////////////////////////////////////////
	// List of sections
	///////////////////////////////////////////////////////////////////
	function GetList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $bIncCnt = false)
	{
		global $DB, $USER;

		$arSqlSearch = CIBlockSection::GetFilter($arFilter);
		$strSqlSearchProp = "";

		if(!$USER->IsAdmin())
		{
			$min_permission = strlen($arFilter["MIN_PERMISSION"])==1 ? $arFilter["MIN_PERMISSION"] : "R";
			$arSqlSearch[] = "
					EXISTS(
						SELECT *
						FROM b_iblock_group IBG
						WHERE IBG.IBLOCK_ID=BS.IBLOCK_ID
							AND IBG.GROUP_ID IN (".$USER->GetGroups().")
							AND IBG.PERMISSION >= '".$min_permission."'
							AND (IBG.PERMISSION = 'X' OR B.ACTIVE = 'Y')
					)
				";
		}

		if(array_key_exists("PROPERTY", $arFilter))
		{
			$val = $arFilter["PROPERTY"];
			foreach($val as $propID=>$propVAL)
			{
				$res = CIBlock::MkOperationFilter($propID);
				$propID = $res["FIELD"];
				$cOperationType = $res["OPERATION"];
				if($db_prop = CIBlockProperty::GetPropertyArray($propID, $arFilter["IBLOCK_ID"]))
				{

					if(!is_array($propVAL))
						$propVAL = Array($propVAL);

					if($db_prop["PROPERTY_TYPE"]=="N" || $db_prop["PROPERTY_TYPE"]=="G" || $db_prop["PROPERTY_TYPE"]=="E")
					{
						if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
						{
							$r = CIBlock::FilterCreate("FPV.PROPERTY_".$db_prop["ORIG_ID"], $propVAL, "number", $cOperationType);
						}
						else
							$r = CIBlock::FilterCreate("FPV.VALUE_NUM", $propVAL, "number", $cOperationType);
					}
					else
					{
						if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
						{
							$r = CIBlock::FilterCreate("FPV.PROPERTY_".$db_prop["ORIG_ID"], $propVAL, "string", $cOperationType);
						}
						else
							$r = CIBlock::FilterCreate("FPV.VALUE", $propVAL, "string", $cOperationType);
					}

					if(strlen($r)>0)
					{
						if(is_numeric(substr($propID, 0, 1)))
							$strPropsAdd = "FP".$iPropsAdd.".ID = ".IntVal($propID)." ";
						else
							$strPropsAdd = "FP".$iPropsAdd.".CODE = '".$DB->ForSql($propID, 100)."' ";

						if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
						{
							$strSqlSearchProp .= "
							AND EXISTS (
								SELECT *
								FROM b_iblock_element_prop_s".$db_prop["IBLOCK_ID"]." FPV
								WHERE FPV.IBLOCK_ELEMENT_ID=BE.ID
								AND ".$r."
							)
							";
						}
						else
						{
							if($db_prop["VERSION"]==2)
								$strTable = "b_iblock_element_prop_m".$db_prop["IBLOCK_ID"];
							else
								$strTable = "b_iblock_element_property";
							$strSqlSearchProp .= "
							AND EXISTS (
								SELECT *
								FROM b_iblock_property FP
									,".$strTable." FPV
								WHERE ".$strPropsAdd."
								AND FP.ID=FPV.IBLOCK_PROPERTY_ID
								AND FPV.IBLOCK_ELEMENT_ID=BE.ID
								AND ".$r."
							)
							";
						}

					}
				}
			}
		}

		$strSqlSearch = "";
		foreach($arSqlSearch as $r)
			if(strlen($r)>0)
				$strSqlSearch .= "\n\t\t\t\tAND  (".$r.") ";

		if(!$bIncCnt)
		{
			$strSql = "
				SELECT
					BS.*,
					B.LIST_PAGE_URL,
					B.SECTION_PAGE_URL,
					BS.XML_ID as EXTERNAL_ID,
					BS.TIMESTAMP_X, ".$DB->DateToCharFunction("BS.TIMESTAMP_X")." as TIMESTAMP_X
				FROM b_iblock_section BS
					,b_iblock B
				WHERE BS.IBLOCK_ID = B.ID
				".(strlen($strSqlSearchProp)>0?"
					AND EXISTS(
						SELECT *
						FROM b_iblock_element BE
							,b_iblock_section BSTEMP
							,b_iblock_section_element BSE
						WHERE BSE.IBLOCK_ELEMENT_ID=BE.ID
								AND BSTEMP.IBLOCK_ID = BS.IBLOCK_ID
								AND BSTEMP.LEFT_MARGIN >= BS.LEFT_MARGIN
								AND BSTEMP.RIGHT_MARGIN <= BS.RIGHT_MARGIN
								AND BSE.IBLOCK_SECTION_ID=BSTEMP.ID
								AND BE.IBLOCK_ID = BS.IBLOCK_ID
					 			AND ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL )
							".($arFilter["CNT_ALL"]=="Y"?" OR BE.WF_NEW='Y' ":"").")
							".($arFilter["CNT_ACTIVE"]=="Y"?
								" AND BE.ACTIVE='Y' ".
								" AND (BE.ACTIVE_TO >= ".$DB->CurrentDateFunction()." OR BE.ACTIVE_TO IS NULL) ".
								" AND (BE.ACTIVE_FROM <= ".$DB->CurrentDateFunction()." OR BE.ACTIVE_FROM IS NULL)"
							:"")."
							".$strSqlSearchProp.") "
				:""
				)."
				".$strSqlSearch;
		}
		else
		{
			$strSql = "
				SELECT
					BS.*,
					B.LIST_PAGE_URL,
					B.SECTION_PAGE_URL,
					BS.XML_ID as EXTERNAL_ID,
					BS.TIMESTAMP_X, ".$DB->DateToCharFunction("BS.TIMESTAMP_X")." as TIMESTAMP_X,
					BSC.ELEMENT_CNT
				FROM
					b_iblock B
					INNER JOIN b_iblock_section BS ON BS.IBLOCK_ID = B.ID
					LEFT JOIN
					(
						SELECT BS.ID,COUNT(DISTINCT BE.ID) ELEMENT_CNT
						FROM b_iblock B
							,b_iblock_section BS
							,b_iblock_element BE
							,b_iblock_section BSTEMP
							,b_iblock_section_element BSE
						WHERE BSE.IBLOCK_ELEMENT_ID=BE.ID
							AND BSTEMP.IBLOCK_ID=BS.IBLOCK_ID
							AND BSTEMP.LEFT_MARGIN >= BS.LEFT_MARGIN
							AND BSTEMP.RIGHT_MARGIN <= BS.RIGHT_MARGIN
							AND BSE.IBLOCK_SECTION_ID=BSTEMP.ID
							AND BE.IBLOCK_ID = BS.IBLOCK_ID
					 		AND ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL )
						".($arFilter["CNT_ALL"]=="Y"?" OR BE.WF_NEW='Y' ":"").")
						".($arFilter["CNT_ACTIVE"]=="Y"?
							" AND BE.ACTIVE='Y' ".
							" AND (BE.ACTIVE_TO >= ".$DB->CurrentDateFunction()." OR BE.ACTIVE_TO IS NULL) ".
							" AND (BE.ACTIVE_FROM <= ".$DB->CurrentDateFunction()." OR BE.ACTIVE_FROM IS NULL)"
						:"")."
						".$strSqlSearchProp."
						GROUP BY BS.ID
					) BSC ON BSC.ID = BS.ID
				WHERE 1=1
				".$strSqlSearch."
			";
		}

		$arSqlOrder = Array();
		foreach($arOrder as $by=>$order)
		{
			$by = strtolower($by);
			$order = strtolower($order);
			if($order!="asc")
				$order = "desc";

			if($by == "id")			$arSqlOrder[] = " BS.ID ".$order." ";
			elseif($by == "section")	$arSqlOrder[] = " BS.IBLOCK_SECTION_ID ".$order." ";
			elseif($by == "name")		$arSqlOrder[] = " BS.NAME ".$order." ";
			elseif($by == "active")		$arSqlOrder[] = " BS.ACTIVE ".$order." ";
			elseif($by == "left_margin")	$arSqlOrder[] = " BS.LEFT_MARGIN ".$order." ";
			elseif($by == "depth_level")	$arSqlOrder[] = " BS.DEPTH_LEVEL ".$order." ";
			elseif($by == "sort")		$arSqlOrder[] = " BS.SORT ".$order." ";
			elseif($bIncCnt && $by == "element_cnt")  $arSqlOrder[] = " ELEMENT_CNT ".$order." ";
			else
			{
				$arSqlOrder[] = " BS.TIMESTAMP_X ".$order." ";
				$by = "timestamp_x";
			}
		}

		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder);
		for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if($i==0)
				$strSqlOrder = "\n\t\t\t\tORDER BY ";
			else
				$strSqlOrder .= ",";

			$strSqlOrder .= $arSqlOrder[$i];
		}

		//echo "<pre>",htmlspecialchars($strSql),"</pre>";
		$res = $DB->Query($strSql.$strSqlOrder, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return new CIBlockResult($res);
	}

	///////////////////////////////////////////////////////////////////
	// New section
	///////////////////////////////////////////////////////////////////
	function Add($arFields, $bResort=true, $bUpdateSearch=true)
	{
		global $DB;

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];
		Unset($arFields["GLOBAL_ACTIVE"]);
		Unset($arFields["DEPTH_LEVEL"]);
		Unset($arFields["LEFT_MARGIN"]);
		Unset($arFields["RIGHT_MARGIN"]);

		if(is_set($arFields, "PICTURE") && strlen($arFields["PICTURE"]["name"])<=0 && strlen($arFields["PICTURE"]["del"])<=0)
			unset($arFields["PICTURE"]);

		if(is_set($arFields, "DETAIL_PICTURE") && strlen($arFields["DETAIL_PICTURE"]["name"])<=0 && strlen($arFields["DETAIL_PICTURE"]["del"])<=0)
			unset($arFields["DETAIL_PICTURE"]);

		if($arFields["IBLOCK_SECTION_ID"]=="0")
			$arFields["IBLOCK_SECTION_ID"]=false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DESCRIPTION_TYPE") && $arFields["DESCRIPTION_TYPE"]!="html")
			$arFields["DESCRIPTION_TYPE"]="text";

		$arFields["SEARCHABLE_CONTENT"] =
			ToUpper(
				$arFields["NAME"]."\r\n".
				($arFields["DESCRIPTION_TYPE"]=="html" ?
					HTMLToTxt($arFields["DESCRIPTION"]) :
					$arFields["DESCRIPTION"]
				)
			);

		if(!$this->CheckFields(&$arFields))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			unset($arFields["ID"]);
			$arInsert = $DB->PrepareInsert("b_iblock_section", $arFields, "iblock");

			$strSql = "
				INSERT INTO b_iblock_section (
					".$arInsert[0]."
				) VALUES (
					".$arInsert[1]."
				)
				";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			$ID = $DB->LastID();

			if($bResort)
				CIBlockSection::ReSort($arFields["IBLOCK_ID"]);

			if($bUpdateSearch)
				CIBlockSection::UpdateSearch($ID);

			$Result = $ID;
			$arFields["ID"] = &$ID;
		}

		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("iblock", "OnAfterIBlockSectionAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}

	///////////////////////////////////////////////////////////////////
	// Update
	///////////////////////////////////////////////////////////////////
	function Update($ID, $arFields, $bResort=true, $bUpdateSearch=true)
	{
		global $DB;

		$ID = intval($ID);

		$db_record = CIBlockSection::GetByID($ID);
		if(!($db_record = $db_record->Fetch()))
			return false;

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		Unset($arFields["GLOBAL_ACTIVE"]);
		Unset($arFields["DEPTH_LEVEL"]);
		Unset($arFields["LEFT_MARGIN"]);
		Unset($arFields["RIGHT_MARGIN"]);
		$SAVED_IBLOCK_ID = $arFields["IBLOCK_ID"];
		unset($arFields["IBLOCK_ID"]);

		if(is_set($arFields, "PICTURE") && strlen($arFields["PICTURE"]["name"])<=0 && strlen($arFields["PICTURE"]["del"])<=0)
			unset($arFields["PICTURE"]);

		if(is_set($arFields, "DETAIL_PICTURE") && strlen($arFields["DETAIL_PICTURE"]["name"])<=0 && strlen($arFields["DETAIL_PICTURE"]["del"])<=0)
			unset($arFields["DETAIL_PICTURE"]);

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DESCRIPTION_TYPE") && $arFields["DESCRIPTION_TYPE"]!="html")
			$arFields["DESCRIPTION_TYPE"] = "text";

		if($arFields["IBLOCK_SECTION_ID"]=="0")
			$arFields["IBLOCK_SECTION_ID"]=false;

		$DESC_tmp = is_set($arFields, "DESCRIPTION")?$arFields["DESCRIPTION"]:$db_record["DESCRIPTION"];
		$arFields["SEARCHABLE_CONTENT"] =
			ToUpper(
				(is_set($arFields, "NAME") ? $arFields["NAME"] : $db_record["NAME"])."\r\n".
				((is_set($arFields, "DESCRIPTION_TYPE") ? $arFields["DESCRIPTION_TYPE"] : $db_record["DESCRIPTION_TYPE"])=="html" ?
					HTMLToTxt($DESC_tmp) :
					$DESC_tmp
				)
			);


		if(!$this->CheckFields(&$arFields, $ID))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			unset($arFields["ID"]);
			$strUpdate = $DB->PrepareUpdate("b_iblock_section", $arFields, "iblock");
			$strSql = "
				UPDATE b_iblock_section SET 
					$strUpdate
				WHERE 
					ID= $ID
				";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

			if($bResort && (
					(isset($arFields["SORT"]) && $arFields["SORT"]!=$db_record["SORT"])
					|| (isset($arFields["NAME"]) && $arFields["NAME"]!=$db_record["NAME"])
					|| (isset($arFields["IBLOCK_SECTION_ID"]) && $arFields["IBLOCK_SECTION_ID"]!=$db_record["IBLOCK_SECTION_ID"])
					|| (isset($arFields["ACTIVE"]) && $arFields["ACTIVE"]!=$db_record["ACTIVE"])
				)
				)
				CIBlockSection::ReSort($db_record["IBLOCK_ID"]);

			if($bUpdateSearch)
				CIBlockSection::UpdateSearch($ID);

			$Result = true;
		}

		$arFields["ID"] = $ID;
		$arFields["IBLOCK_ID"] = $SAVED_IBLOCK_ID;
		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("iblock", "OnAfterIBlockSectionUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}
}
?>
