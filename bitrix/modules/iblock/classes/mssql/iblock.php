<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblock.php");
//IncludeModuleLangFile(__FILE__);

class CIBlock extends CAllIBlock
{
	///////////////////////////////////////////////////////////////////
	// List of blocks
	///////////////////////////////////////////////////////////////////
	function GetList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $bIncCnt = false)
	{
		global $DB, $USER;
		$arSqlSearch = Array();
		$bAddSites = false;
		$filter_keys = array_keys($arFilter);
		for($i=0; $i<count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];
			$key = $filter_keys[$i];
			$res = CIBlock::MkOperationFilter($key);
			$key = $res["FIELD"];
			$cOperationType = $res["OPERATION"];

			$key = strtoupper($key);
			switch($key)
			{
				case "ACTIVE":
					$arSqlSearch[] = CIBlock::FilterCreate("B.ACTIVE", $val, "string_equal", $cOperationType);
					break;
				case "LID":
				case "SITE_ID":
					$str_res = CIBlock::FilterCreate("BS.SITE_ID", $val, "string_equal", $cOperationType);
					if(strlen($str_res)>0)
					{
						$arSqlSearch[] = $str_res;
						$bAddSites = true;
					}
					break;
				case "NAME":
				case "XML_ID":
					$arSqlSearch[] = CIBlock::FilterCreate("B.".$key, $val, "string", $cOperationType);
					break;
				case "EXTERNAL_ID":
					$arSqlSearch[] = CIBlock::FilterCreate("B.XML_ID", $val, "string", $cOperationType);
					break;
				case "TYPE":
					$arSqlSearch[] = CIBlock::FilterCreate("B.IBLOCK_TYPE_ID", $val, "string", $cOperationType);
					break;
				case "CODE":
					$arSqlSearch[] = CIBlock::FilterCreate("B.CODE", $val, "string", $cOperationType);
					break;
				case "ID":
					$arSqlSearch[] = CIBlock::FilterCreate("B.ID", $val, "number", $cOperationType);
					break;
				case "VERSION":
					$arSqlSearch[] = CIBlock::FilterCreate("B.VERSION", $val, "number", $cOperationType);
					break;
			}
		}

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
			if(strlen($arSqlSearch[$i])>0)
				$strSqlSearch .= " AND  (".$arSqlSearch[$i].") ";

		if (is_object($USER) && $USER->IsAdmin())
			$sqlPermissions = "";
		else
		{
			$strGroups = (is_object($USER)?$USER->GetGroups():"2");
			$min_permission = (strlen($arFilter["MIN_PERMISSION"])==1) ? $arFilter["MIN_PERMISSION"] : "R";
			$sqlPermissions = "
				AND EXISTS (
					SELECT	*
					FROM 	b_iblock_group IBG
					WHERE
						IBG.IBLOCK_ID=B.ID 
						AND IBG.GROUP_ID IN (".$strGroups.")
						AND IBG.PERMISSION >= '".$min_permission."'
						AND (IBG.PERMISSION='X' OR B.ACTIVE='Y')
				)
			";
		}

		if ($bAddSites)
			$sqlJoinSites = " LEFT JOIN b_iblock_site BS ON B.ID=BS.IBLOCK_ID ".
					" LEFT JOIN b_lang L ON L.LID=BS.SITE_ID ";
		else
			$sqlJoinSites = " INNER JOIN b_lang L ON L.LID=B.LID ";

		if(!$bIncCnt)
		{
			$strSql =
				"SELECT B.*, B.XML_ID as EXTERNAL_ID, ".$DB->DateToCharFunction("B.TIMESTAMP_X")." as TIMESTAMP_X, L.DIR as LANG_DIR, L.SERVER_NAME ".
				"FROM b_iblock B ".
				$sqlJoinSites.
				"WHERE 1 = 1 ".
				$sqlPermissions.
				$strSqlSearch;
		}
		else
		{
			$strSql =
				"SELECT B.*, B.XML_ID as EXTERNAL_ID, ".$DB->DateToCharFunction("B.TIMESTAMP_X")." as TIMESTAMP_X, L.DIR as LANG_DIR, L.SERVER_NAME, T.CNT as ELEMENT_CNT ".
				"FROM b_iblock B ".
				$sqlJoinSites.
				"	LEFT JOIN ".
				"	(".
				"	SELECT COUNT(DISTINCT BE.ID) as CNT, BE.IBLOCK_ID ".
				"	FROM b_iblock_element BE  ".
				"	WHERE ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL ) ".
					($arFilter["CNT_ALL"]=="Y"?" OR BE.WF_NEW='Y' ":"").") ".
					($arFilter["CNT_ACTIVE"]=="Y"?
						" AND BE.ACTIVE='Y' ".
						" AND (BE.ACTIVE_TO >= ".$DB->CurrentDateFunction()." OR BE.ACTIVE_TO IS NULL) ".
						" AND (BE.ACTIVE_FROM <= ".$DB->CurrentDateFunction()." OR BE.ACTIVE_FROM IS NULL) "
					:"").
				"	GROUP BY BE.IBLOCK_ID ".
				"	) T ON T.IBLOCK_ID=B.ID ".
				"WHERE 1=1 ".
				$sqlPermissions.
				$strSqlSearch;
		}

		$arSqlOrder = Array();
		foreach($arOrder as $by=>$order)
		{
			$by = strtolower($by);
			$order = strtolower($order);
			if ($order!="asc")
				$order = "desc";

			if ($by == "id")				$arSqlOrder[] = " B.ID ".$order." ";
			elseif ($by == "lid")			$arSqlOrder[] = " B.LID ".$order." ";
			elseif ($by == "iblock_type")	$arSqlOrder[] = " B.IBLOCK_TYPE_ID ".$order." ";
			elseif ($by == "name")			$arSqlOrder[] = " B.NAME ".$order." ";
			elseif ($by == "active")		$arSqlOrder[] = " B.ACTIVE ".$order." ";
			elseif ($by == "sort")			$arSqlOrder[] = " B.SORT ".$order." ";
			elseif ($bIncCnt && $by == "element_cnt")  $arSqlOrder[] = " ELEMENT_CNT ".$order." ";
			else
			{
				$arSqlOrder[] = " B.TIMESTAMP_X ".$order." ";
				$by = "timestamp_x";
			}
		}

		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder); for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ",";

			$strSqlOrder .= $arSqlOrder[$i];
		}
		$strSql .= $strSqlOrder;
		//echo htmlspecialchars($strSql);
		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $res;
	}

	function _Upper($str)
	{
		return "UPPER(".$str.")";
	}

	///////////////////////////////////////////////////////////////////
	// Block by ID
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CIBlock::GetList(Array(), Array("ID"=>$ID));
	}

	///////////////////////////////////////////////////////////////////
	// New block
	///////////////////////////////////////////////////////////////////
	function Add($arFields)
	{
		global $DB, $USER;

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if(is_set($arFields, "PICTURE") && strlen($arFields["PICTURE"]["name"])<=0 && strlen($arFields["PICTURE"]["del"])<=0)
			unset($arFields["PICTURE"]);
		else
			$arFields["PICTURE"]["MODULE_ID"] = "iblock";

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "INDEX_SECTION") && $arFields["INDEX_SECTION"]!="Y")
			$arFields["INDEX_SECTION"]="N";

		if(is_set($arFields, "INDEX_ELEMENT") && $arFields["INDEX_ELEMENT"]!="Y")
			$arFields["INDEX_ELEMENT"]="N";

		if(is_set($arFields, "DESCRIPTION_TYPE") && $arFields["DESCRIPTION_TYPE"]!="html")
			$arFields["DESCRIPTION_TYPE"]="text";

		if(is_set($arFields, "SITE_ID"))
			$arFields["LID"] = $arFields["SITE_ID"];

		if(!$this->CheckFields(&$arFields))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			$arLID = Array();
			if(is_set($arFields, "LID"))
			{
				if(is_array($arFields["LID"]))
					$arLID = $arFields["LID"];
				else
					$arLID[] = $arFields["LID"];

				$arFields["LID"] = false;
				$str_LID = "''";
				foreach($arLID as $v)
				{
					$arFields["LID"] = $v;
					$str_LID .= ", '".$DB->ForSql($v)."'";
				}
			}

			unset($arFields["ID"]);
			$ID = $DB->Add("b_iblock", $arFields, Array("DESCRIPTION"), "iblock");

			if(is_set($arFields, "GROUP_ID") && is_array($arFields["GROUP_ID"]))
				CIBlock::SetPermission($ID, $arFields["GROUP_ID"]);

			if(count($arLID)>0)
			{
				$strSql = "DELETE FROM b_iblock_site WHERE IBLOCK_ID=".$ID;
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

				$strSql =
					"INSERT INTO b_iblock_site(IBLOCK_ID, SITE_ID) ".
					"SELECT ".$ID.", LID ".
					"FROM b_lang ".
					"WHERE LID IN (".$str_LID.") ";
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}
			if($arFields["VERSION"]==2)
			{
			 	if($this->_Add($ID))
				{
					$Result = $ID;
					$arFields["ID"] = &$ID;
				}
				else
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_TABLE_CREATION_ERROR");
					$Result = false;
					$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
				}
			}
			else
			{
				$Result = $ID;
				$arFields["ID"] = &$ID;
			}
		}

		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("iblock", "OnAfterIBlockAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}

	///////////////////////////////////////////////////////////////////
	// Update
	///////////////////////////////////////////////////////////////////
	function Update($ID, $arFields)
	{
		global $DB;

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if(is_set($arFields, "PICTURE"))
		{
			if(strlen($arFields["PICTURE"]["name"])<=0 && strlen($arFields["PICTURE"]["del"])<=0)
				unset($arFields["PICTURE"]);
			else
			{
				$pic_res = $DB->Query("SELECT PICTURE FROM b_iblock WHERE ID=".$ID);
				if($pic_res = $pic_res->Fetch())
					$arFields["PICTURE"]["old_file"]=$pic_res["PICTURE"];
			}
		}

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "INDEX_SECTION") && $arFields["INDEX_SECTION"]!="Y")
			$arFields["INDEX_SECTION"]="N";

		if(is_set($arFields, "INDEX_ELEMENT") && $arFields["INDEX_ELEMENT"]!="Y")
			$arFields["INDEX_ELEMENT"]="N";

		if(is_set($arFields, "DESCRIPTION_TYPE") && $arFields["DESCRIPTION_TYPE"]!="html")
			$arFields["DESCRIPTION_TYPE"] = "text";

		if(!$this->CheckFields(&$arFields, $ID))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			$arLID = Array();
			if(is_set($arFields, "LID"))
			{
				if(is_array($arFields["LID"]))
					$arLID = $arFields["LID"];
				else
					$arLID[] = $arFields["LID"];

				$arFields["LID"] = false;
				$str_LID = "''";
				foreach($arLID as $v)
				{
					$arFields["LID"] = $v;
					$str_LID .= ", '".$DB->ForSql($v)."'";
				}
			}

			unset($arFields["ID"]);
			unset($arFields["VERSION"]);
			$strUpdate = $DB->PrepareUpdate("b_iblock", $arFields, "iblock");

			$arBinds=Array();
			if(is_set($arFields, "DESCRIPTION"))
				$arBinds["DESCRIPTION"] = $arFields["DESCRIPTION"];

			$strSql = "UPDATE b_iblock SET ".$strUpdate." WHERE ID=".$ID;
			$DB->QueryBind($strSql, $arBinds);

			if(is_set($arFields, "GROUP_ID") && is_array($arFields["GROUP_ID"]))
				CIBlock::SetPermission($ID, $arFields["GROUP_ID"]);

			if(count($arLID)>0)
			{
				$strSql = "DELETE FROM b_iblock_site WHERE IBLOCK_ID=".$ID;
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

				$strSql =
					"INSERT INTO b_iblock_site(IBLOCK_ID, SITE_ID) ".
					"SELECT ".$ID.", LID ".
					"FROM b_lang ".
					"WHERE LID IN (".$str_LID.") ";
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}

			if(CModule::IncludeModule("search"))
			{
				$dbafter = $DB->Query("SELECT ACTIVE, DETAIL_PAGE_URL, LID FROM b_iblock WHERE ID=".$ID);
				$arAfter = $dbafter->Fetch();

				if($arAfter["ACTIVE"]!="Y")
				{
					CSearch::DeleteIndex("iblock", false, false, $ID);
				}
				else if(is_set($arFields, "GROUP_ID"))
				{
					$arPerms = Array();
					$arGroupsPerm = $arFields["GROUP_ID"];
					$arGroups = array_keys($arGroupsPerm);

					for($i=0; $i<count($arGroups); $i++)
					{
						if($arGroupsPerm[$arGroups[$i]]>="R")
						{
							if($arGroups[$i]==2)
							{
								$arPerms = Array(2);
								break;
							}
							$arPerms[] = $arGroups[$i];
						}
					}
					CSearch::ChangePermission("iblock", $arPerms, false, false, $ID);
				}
			}
			$Result = true;
		}

		$arFields["ID"] = $ID;
		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("iblock", "OnAfterIBlockUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}

	function _Add($ID)
	{
		global $DB;
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		$ID=intval($ID);
		$strSql = "
			CREATE TABLE B_IBLOCK_ELEMENT_PROP_S".$ID." (
				IBLOCK_ELEMENT_ID 	int not null,
				CONSTRAINT PK_B_IBLOCK_ELEMENT_PROP_S".$ID." PRIMARY KEY (IBLOCK_ELEMENT_ID),
				CONSTRAINT FK_B_IBLOCK_ELEMENT_PROP_S".$ID." FOREIGN KEY (IBLOCK_ELEMENT_ID) REFERENCES B_IBLOCK_ELEMENT(ID)
			)
		";
		$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		$strSql = "
			CREATE TABLE B_IBLOCK_ELEMENT_PROP_M".$ID."
			(
				ID int NOT NULL IDENTITY (1, 1),
				IBLOCK_PROPERTY_ID int NOT NULL,
				IBLOCK_ELEMENT_ID int NOT NULL,
				VALUE varchar(2000) NOT NULL,
				VALUE_ENUM int NULL,
				VALUE_NUM numeric(18,4) NULL,
				DESCRIPTION varchar(255) NULL
				CONSTRAINT PK_B_IBLOCK_ELEM_PROP_M".$ID." PRIMARY KEY (ID),
				CONSTRAINT FK_B_IBLOCK_ELEM_PROP_M".$ID."_1 FOREIGN KEY (IBLOCK_PROPERTY_ID) REFERENCES B_IBLOCK_PROPERTY(ID),
				CONSTRAINT FK_B_IBLOCK_ELEM_PROP_M".$ID."_2 FOREIGN KEY (IBLOCK_ELEMENT_ID) REFERENCES B_IBLOCK_ELEMENT(ID)
			)
		";
		if($rs) $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		$strSql = "
			CREATE INDEX IX_IBLOCK_ELEM_PROP_M".$ID."_1 on B_IBLOCK_ELEMENT_PROP_M".$ID." (IBLOCK_ELEMENT_ID,IBLOCK_PROPERTY_ID)
		";
		if($rs) $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		$strSql = "
			CREATE INDEX IX_IBLOCK_ELEM_PROP_M".$ID."_2 on B_IBLOCK_ELEMENT_PROP_M".$ID." (IBLOCK_PROPERTY_ID)
		";
		if($rs) $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		$strSql = "
			CREATE INDEX IX_IBLOCK_ELEM_PROP_M".$ID."_3 on B_IBLOCK_ELEMENT_PROP_M".$ID." (VALUE_ENUM,IBLOCK_PROPERTY_ID)
		";
		if($rs) $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $rs;
	}
}
?>
