<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/user.php");

///////////////////////////////////////////////////////////////////
// Класс пользователей
///////////////////////////////////////////////////////////////////

class CUser extends CAllUser
{
	function err_mess()
	{
		return "<br>Class: CUser<br>File: ".__FILE__;
	}

	function CUser()
	{
		$this->SESS_AUTH=&$_SESSION["SESS_AUTH"];
	}

	function Add($arFields)
	{
		$err_mess = (CUser::err_mess())."<br>Function: Add<br>Line: ";
		global $DB;

		if(!$this->CheckFields(&$arFields))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			unset($arFields["ID"]);
			if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
				$arFields["ACTIVE"]="N";

			if($arFields["PERSONAL_GENDER"]=="NOT_REF" || ($arFields["PERSONAL_GENDER"]!="M" && $arFields["PERSONAL_GENDER"]!="F"))
				$arFields["PERSONAL_GENDER"] = "";

			$arFields["PASSWORD"] = md5($arFields["PASSWORD"]);
			//$arFields["STORED_HASH"] = CUser::GetPasswordHash($arFields["PASSWORD"]);
			unset($arFields["STORED_HASH"]);

			if(strlen($arFields["CHECKWORD"])<=0)
				$arFields["CHECKWORD"] = randString(8);

			$arFields["~CHECKWORD_TIME"] = $DB->CurrentTimeFunction();

			if(is_set($arFields,"EMAIL"))
				$arFields["EMAIL"] = strtolower($arFields["EMAIL"]);

			if(is_set($arFields, "WORK_COUNTRY"))
				$arFields["WORK_COUNTRY"] = intval($arFields["WORK_COUNTRY"]);

			if(is_set($arFields, "PERSONAL_COUNTRY"))
				$arFields["PERSONAL_COUNTRY"] = intval($arFields["PERSONAL_COUNTRY"]);

			$arInsert = $DB->PrepareInsert("b_user", $arFields, "main");

			if(!is_set($arFields, "DATE_REGISTER"))
			{
				$arInsert[0] .= ", DATE_REGISTER";
				$arInsert[1] .= ", ".$DB->GetNowFunction();
			}

			$strSql = "
				INSERT INTO b_user (
					".$arInsert[0]."
				) VALUES (
					".$arInsert[1]."
				)
				";
			$DB->Query($strSql, false, $err_mess.__LINE__);
			$ID = $DB->LastID();

			if(is_set($arFields, "GROUP_ID"))
				CUser::SetUserGroup($ID, $arFields["GROUP_ID"]);

			$Result = $ID;
			$arFields["ID"] = &$ID;
		}

		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("main", "OnAfterUserAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}

	function GetDropDownList($strSqlSearch="and ACTIVE='Y'", $strSqlOrder="ORDER BY ID, NAME, LAST_NAME")
	{
		global $DB;
		$err_mess = (CUser::err_mess())."<br>Function: GetDropDownList<br>Line: ";
		$strSql = "
			SELECT
				ID as REFERENCE_ID,
				'[' + ID + '] (' + LOGIN + ') ' + isnull(NAME,'') + ' ' + isnull(LAST_NAME,'') as REFERENCE
			FROM
				b_user
			WHERE
				1=1
			$strSqlSearch
			$strSqlOrder
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function GetList(&$by, &$order, $arFilter=Array())
	{
		$err_mess = (CUser::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $USER;
		$arFields_m = array("ID", "ACTIVE", "LAST_LOGIN", "LOGIN", "EMAIL", "NAME", "LAST_NAME", "SECOND_NAME", "TIMESTAMP_X", "PERSONAL_BIRTHDAY");
		$arFields = array("DATE_REGISTER", "PERSONAL_PROFESSION", "PERSONAL_WWW", "PERSONAL_ICQ", "PERSONAL_GENDER", "PERSONAL_PHOTO", "PERSONAL_PHONE", "PERSONAL_FAX", "PERSONAL_MOBILE", "PERSONAL_PAGER", "PERSONAL_STREET", "PERSONAL_MAILBOX", "PERSONAL_CITY", "PERSONAL_STATE", "PERSONAL_ZIP", "PERSONAL_COUNTRY", "PERSONAL_NOTES", "WORK_COMPANY", "WORK_DEPARTMENT", "WORK_POSITION", "WORK_WWW", "WORK_PHONE", "WORK_FAX", "WORK_PAGER", "WORK_STREET", "WORK_MAILBOX", "WORK_CITY", "WORK_STATE", "WORK_ZIP", "WORK_COUNTRY", "WORK_PROFILE", "WORK_NOTES", "ADMIN_NOTES");
		$arFields_all = array_merge($arFields_m, $arFields);
		$bGroupBy = false;
		$arSqlSearch = Array();
		$strSqlSearch = "";
		if(is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$key = $filter_keys[$i];
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || "$val"=="NOT_REF") continue;
				if (is_array($val) && count($val)<=0) continue;
				$match_value_set = (in_array($key."_EXACT_MATCH", $filter_keys)) ? true : false;
				$key = strtoupper($key);
				switch($key)
				{
					case "ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("U.ID", $val, $match);
						break;
					case "ID_EQUAL_EXACT":
						$arSqlSearch[] = "U.ID='".intval($val)."'";
						break;
					case "TIMESTAMP_1":
						$arSqlSearch[] = "U.TIMESTAMP_X >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "TIMESTAMP_2":
						$arSqlSearch[] = "U.TIMESTAMP_X < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "LAST_LOGIN_1":
						$arSqlSearch[] = "U.LAST_LOGIN >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "LAST_LOGIN_2":
						$arSqlSearch[] = "U.LAST_LOGIN < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "U.ACTIVE='Y'" : "U.ACTIVE='N'";
						break;
					case "LOGIN_EQUAL":
						$arSqlSearch[] = GetFilterQuery("U.LOGIN", $val, "N");
						break;
					case "LOGIN_EQUAL_EXACT":
						$arSqlSearch[] = "U.LOGIN='".$DB->ForSql($val)."'";
						break;
					case "LOGIN":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("U.LOGIN", $val, $match);
						break;
					case "EXTERNAL_AUTH_ID":
						$arSqlSearch[] = "U.EXTERNAL_AUTH_ID='".$DB->ForSQL($val, 255)."'";
						break;
					case "XML_ID":
						$arSqlSearch[] = "U.XML_ID='".$DB->ForSql($val)."'";
						break;
					case "COUNTRY_ID":
						$arSqlSearch[] = "U.WORK_COUNTRY=".intval($val);
						break;
					case "NAME":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("U.NAME, U.LAST_NAME, U.SECOND_NAME", $val, $match);
						break;
					case "EMAIL":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("U.EMAIL", $val, $match, array("@","_",".","-"));
						break;
					case "GROUP_MULTI":
					case "GROUPS_ID":
						if(is_numeric($val)>0 && intval($val)>0)
							$val = Array($val);

						if(is_array($val) && count($val)>0)
						{
							$str = GetFilterQuery("UG.GROUP_ID", implode(" | ", $val), "N");
							if(strlen($str)>0)
							{
								$strGroupFilter = "
									and exists(
										SELECT
											'x'
										FROM
											b_user_group UG
										WHERE
												UG.USER_ID=U.ID
											and $str
											and (
													UG.DATE_ACTIVE_FROM is null or
													UG.DATE_ACTIVE_FROM <= ".$DB->CurrentTimeFunction()."
												)
											and (
													UG.DATE_ACTIVE_TO is null or
													UG.DATE_ACTIVE_TO >= ".$DB->CurrentTimeFunction()."
												)
									)
								";
							}
						}
						break;
					case "PERSONAL_BIRTHDATE_1":
						$arSqlSearch[] = "U.PERSONAL_BIRTHDATE >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "PERSONAL_BIRTHDATE_2":
						$arSqlSearch[] = "U.PERSONAL_BIRTHDATE < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "PERSONAL_BIRTHDAY_1":
						$arSqlSearch[] = "U.PERSONAL_BIRTHDAY >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "PERSONAL_BIRTHDAY_2":
						$arSqlSearch[] = "U.PERSONAL_BIRTHDAY < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "KEYWORDS":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery(implode(",",$arFields), $val, $match);
						break;
					default:
						if (in_array(strtoupper($filter_keys[$i]), $arFields))
						{
							$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
							$arSqlSearch[] = GetFilterQuery(strtoupper($filter_keys[$i]), $val, $match);
						}
				}
			}
		}
		if(in_array(strtoupper($by),$arFields_all))
		{
			$strSqlOrder = " ORDER BY U.".strtoupper($by);
		}
		else
		{
			$strSqlOrder = " ORDER BY U.TIMESTAMP_X ";
			$by = "timestamp_x";
		}
		if($order!="asc")
		{
			$strSqlOrder .= " desc ";
			$order = "desc";
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				U.*,
				".$DB->DateToCharFunction("U.TIMESTAMP_X")."				TIMESTAMP_X,
				".$DB->DateToCharFunction("U.DATE_REGISTER")."				DATE_REGISTER,
				".$DB->DateToCharFunction("U.LAST_LOGIN")."					LAST_LOGIN,
				".$DB->DateToCharFunction("U.PERSONAL_BIRTHDAY", "SHORT")."	PERSONAL_BIRTHDAY
			FROM
				b_user U
			WHERE
				$strSqlSearch
				$strGroupFilter
			$strSqlOrder
			";
		//echo "<pre>".$strSql."</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$res->is_filtered = (IsFiltered($strSqlSearch) || strlen($strGroupFilter)>0);
		return $res;
	}
}

///////////////////////////////////////////////////////////////////
// Класс групп пользователей
///////////////////////////////////////////////////////////////////

class CGroup extends CAllGroup
{
	function err_mess()
	{
		return "<br>Class: CGroup<br>File: ".__FILE__;
	}

	function Add($arFields)
	{
		$err_mess = (CGroup::err_mess())."<br>Function: Add<br>Line: ";
		global $DB;

		if(!$this->CheckFields($arFields))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		$arInsert = $DB->PrepareInsert("b_group", $arFields);

		$strSql = "
			INSERT INTO b_group (
				".$arInsert[0]."
			) VALUES(
				".$arInsert[1]."
			)
			";
		//echo "<pre>".$strSql."</pre>";
		$DB->Query($strSql, false, $err_mess.__LINE__);
		$ID = $DB->LastID();

		if (count($arFields["USER_ID"]) > 0)
		{
			if (is_array($arFields["USER_ID"][0]) && count($arFields["USER_ID"][0]) > 0)
			{
				$arTmp = array();
				for ($i=0; $i<count($arFields["USER_ID"]); $i++)
				{
					if (intval($arFields["USER_ID"][$i]["USER_ID"]) > 0
						&& !in_array(intval($arFields["USER_ID"][$i]["USER_ID"]), $arTmp))
					{
						$arInsert = $DB->PrepareInsert("b_user_group", $arFields["USER_ID"][$i]);
						$strSql = "
							INSERT INTO b_user_group (
								GROUP_ID,
								".$arInsert[0]."
							) VALUES (
								$ID,
								".$arInsert[1]."
							)
							";
						$DB->Query($strSql, false, $err_mess.__LINE__);
						$arTmp[] = intval($arFields["USER_ID"][$i]["USER_ID"]);
					}
				}
			}
			else
			{
				$strUsers = "0";
				for($i=0; $i<count($arFields["USER_ID"]); $i++)
					$strUsers.=",".intval($arFields["USER_ID"][$i]);

				$strSql = "
					INSERT INTO b_user_group (
						GROUP_ID,
						USER_ID
					) SELECT
						$ID,
						ID
					FROM
						b_user
					WHERE
						ID in (".$strUsers.")
					";
				$DB->Query($strSql, false, $err_mess.__LINE__);
			}
		}
		return $ID;
	}

	function GetDropDownList($strSqlSearch="and ACTIVE='Y'", $strSqlOrder="ORDER BY C_SORT, NAME, ID")
	{
		global $DB;
		$err_mess = (CGroup::err_mess())."<br>Function: GetDropDownList<br>Line: ";
		$strSql = "
			SELECT
				ID as REFERENCE_ID,
				NAME + ' [' + convert(varchar(18), ID) + ']' as REFERENCE
			FROM
				b_group
			WHERE
				1=1
			$strSqlSearch
			$strSqlOrder
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function GetList(&$by, &$order, $arFilter=Array(), $SHOW_USERS_AMOUNT="N")
	{
		$err_mess = (CGroup::err_mess())."<br>Function: GetList<br>Line: ";
		global $DB, $USER;
		$arSqlSearch = $arSqlSearch_h = array();
		$strSqlSearch = $strSqlSearch_h = "";
		if(is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$key = $filter_keys[$i];
				$val = $arFilter[$filter_keys[$i]];
				if (strlen($val)<=0 || "$val"=="NOT_REF") continue;
				if (is_array($val) && count($val)<=0) continue;
				$match_value_set = (in_array($key."_EXACT_MATCH", $filter_keys)) ? true : false;
				$key = strtoupper($key);
				switch($key)
				{
					case "ID":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="N" && $match_value_set) ? "Y" : "N";
						$arSqlSearch[] = GetFilterQuery("G.ID", $val, $match);
						break;
					case "TIMESTAMP_1":
						$arSqlSearch[] = "G.TIMESTAMP_X >= ".$DB->CharToDateFunction($val, "SHORT");
						break;
					case "TIMESTAMP_2":
						$arSqlSearch[] = "G.TIMESTAMP_X < dateadd(day, 1, ".$DB->CharToDateFunction($val, "SHORT").")";
						break;
					case "ACTIVE":
						$arSqlSearch[] = ($val=="Y") ? "G.ACTIVE='Y'" : "G.ACTIVE='N'";
						break;
					case "ADMIN":
						$arSqlSearch[] = ($val=="Y") ? "G.ID=1" : "G.ID>1";
						break;
					case "NAME":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("G.NAME", $val, $match);
						break;
					case "DESCRIPTION":
						$match = ($arFilter[$key."_EXACT_MATCH"]=="Y" && $match_value_set) ? "N" : "Y";
						$arSqlSearch[] = GetFilterQuery("G.DESCRIPTION", $val, $match);
						break;
					case "USERS_1":
						$SHOW_USERS_AMOUNT="Y";
						$arSqlSearch_h[] = "count(distinct U.USER_ID) >= ".intval($val);
						break;
					case "USERS_2":
						$SHOW_USERS_AMOUNT="Y";
						$arSqlSearch_h[] = "count(distinct U.USER_ID) <= ".intval($val);
						break;
				}
			}
			for($i=0; $i<count($arSqlSearch_h); $i++) $strSqlSearch_h .= " and (".$arSqlSearch_h[$i].") ";
		}


		if(strtolower($by) == "id")					$strSqlOrder = " ORDER BY G.ID ";
		elseif(strtolower($by) == "active")			$strSqlOrder = " ORDER BY G.ACTIVE ";
		elseif(strtolower($by) == "timestamp_x")	$strSqlOrder = " ORDER BY G.TIMESTAMP_X ";
		elseif(strtolower($by) == "c_sort")			$strSqlOrder = " ORDER BY G.C_SORT ";
		elseif(strtolower($by) == "sort")			$strSqlOrder = " ORDER BY G.C_SORT, G.NAME, G.ID ";
		elseif(strtolower($by) == "name")			$strSqlOrder = " ORDER BY G.NAME ";
		elseif(strtolower($by) == "description")	$strSqlOrder = " ORDER BY G.DESCRIPTION ";
		elseif(strtolower($by) == "anonymous")		$strSqlOrder = " ORDER BY G.ANONYMOUS ";
		elseif(strtolower($by) == "dropdown")		$strSqlOrder = " ORDER BY C_SORT, NAME ";
		elseif(strtolower($by) == "users")
		{
			$strSqlOrder = " ORDER BY USERS ";
			$SHOW_USERS_AMOUNT="Y";
		}
		else
		{
			$strSqlOrder = " ORDER BY G.C_SORT ";
			$by = "c_sort";
		}

		if(strtolower($order)=="desc")
		{
			$strSqlOrder .= " desc ";
			$order = "desc";
		}
		else
		{
			$strSqlOrder .= " asc ";
			$order = "asc";
		}

		if($SHOW_USERS_AMOUNT=="Y")
		{
			$str_USERS = "count(distinct U.USER_ID)						USERS,";
			$str_TABLE = "LEFT JOIN b_user_group U ON (U.GROUP_ID=G.ID AND ((U.DATE_ACTIVE_FROM IS NULL) OR (U.DATE_ACTIVE_FROM <= ".$DB->CurrentTimeFunction().")) AND ((U.DATE_ACTIVE_TO IS NULL) OR (U.DATE_ACTIVE_TO >= ".$DB->CurrentTimeFunction().")))";
		}
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				G.ID, G.ACTIVE, G.C_SORT, G.ANONYMOUS, G.NAME, G.DESCRIPTION,
				$str_USERS
				G.ID											REFERENCE_ID,
				G.NAME + ' [' + convert(varchar(18), G.ID) + ']'		REFERENCE,
				".$DB->DateToCharFunction("G.TIMESTAMP_X")."	TIMESTAMP_X
			FROM
				b_group G
			$str_TABLE
			WHERE
			$strSqlSearch
			GROUP BY
				G.ID, G.ACTIVE, G.C_SORT, G.TIMESTAMP_X, G.ANONYMOUS, G.NAME, G.DESCRIPTION
			HAVING
				1=1
				$strSqlSearch_h
			$strSqlOrder
			";
		//echo "<pre>".$strSql."</pre>";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$res->is_filtered = (IsFiltered($strSqlSearch) || strlen($strSqlSearch_h)>0);
		return $res;
	}

	function GetFilterOperation($key)
	{
		$strNegative = "N";
		if (substr($key, 0, 1)=="!")
		{
			$key = substr($key, 1);
			$strNegative = "Y";
		}

		$strOrNull = "N";
		if (substr($key, 0, 1)=="+")
		{
			$key = substr($key, 1);
			$strOrNull = "Y";
		}

		if (substr($key, 0, 2)==">=")
		{
			$key = substr($key, 2);
			$strOperation = ">=";
		}
		elseif (substr($key, 0, 1)==">")
		{
			$key = substr($key, 1);
			$strOperation = ">";
		}
		elseif (substr($key, 0, 2)=="<=")
		{
			$key = substr($key, 2);
			$strOperation = "<=";
		}
		elseif (substr($key, 0, 1)=="<")
		{
			$key = substr($key, 1);
			$strOperation = "<";
		}
		elseif (substr($key, 0, 1)=="@")
		{
			$key = substr($key, 1);
			$strOperation = "IN";
		}
		elseif (substr($key, 0, 1)=="~")
		{
			$key = substr($key, 1);
			$strOperation = "LIKE";
		}
		elseif (substr($key, 0, 1)=="%")
		{
			$key = substr($key, 1);
			$strOperation = "QUERY";
		}
		else
		{
			$strOperation = "=";
		}

		return array("FIELD" => $key, "NEGATIVE" => $strNegative, "OPERATION" => $strOperation, "OR_NULL" => $strOrNull);
	}

	function PrepareSql(&$arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields)
	{
		global $DB;

		$strSqlSelect = "";
		$strSqlFrom = "";
		$strSqlWhere = "";
		$strSqlGroupBy = "";
		$strSqlOrderBy = "";

		$arGroupByFunct = array("COUNT", "AVG", "MIN", "MAX", "SUM");

		$arAlreadyJoined = array();

		// GROUP BY -->
		if (is_array($arGroupBy) && count($arGroupBy)>0)
		{
			$arSelectFields = $arGroupBy;
			foreach ($arGroupBy as $key => $val)
			{
				$val = strtoupper($val);
				$key = strtoupper($key);
				if (array_key_exists($val, $arFields) && !in_array($key, $arGroupByFunct))
				{
					if (strlen($strSqlGroupBy) > 0)
						$strSqlGroupBy .= ", ";
					$strSqlGroupBy .= $arFields[$val]["FIELD"];

					if (isset($arFields[$val]["FROM"])
						&& strlen($arFields[$val]["FROM"]) > 0
						&& !in_array($arFields[$val]["FROM"], $arAlreadyJoined))
					{
						if (strlen($strSqlFrom) > 0)
							$strSqlFrom .= " ";
						$strSqlFrom .= $arFields[$val]["FROM"];
						$arAlreadyJoined[] = $arFields[$val]["FROM"];
					}
				}
			}
		}
		// <-- GROUP BY

		// SELECT -->
		$arFieldsKeys = array_keys($arFields);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSqlSelect = "COUNT(%%_DISTINCT_%% ".$arFields[$arFieldsKeys[0]]["FIELD"].") as CNT ";
		}
		else
		{
			if (isset($arSelectFields) && !is_array($arSelectFields) && is_string($arSelectFields) && strlen($arSelectFields)>0 && array_key_exists($arSelectFields, $arFields))
				$arSelectFields = array($arSelectFields);

			if (!isset($arSelectFields)
				|| !is_array($arSelectFields)
				|| count($arSelectFields)<=0
				|| in_array("*", $arSelectFields))
			{
				for ($i = 0; $i < count($arFieldsKeys); $i++)
				{
					if (isset($arFields[$arFieldsKeys[$i]]["WHERE_ONLY"])
						&& $arFields[$arFieldsKeys[$i]]["WHERE_ONLY"] == "Y")
					{
						continue;
					}

					if (strlen($strSqlSelect) > 0)
						$strSqlSelect .= ", ";

					if ($arFields[$arFieldsKeys[$i]]["TYPE"] == "datetime")
						$strSqlSelect .= $DB->DateToCharFunction($arFields[$arFieldsKeys[$i]]["FIELD"], "FULL")." as ".$arFieldsKeys[$i];
					elseif ($arFields[$arFieldsKeys[$i]]["TYPE"] == "date")
						$strSqlSelect .= $DB->DateToCharFunction($arFields[$arFieldsKeys[$i]]["FIELD"], "SHORT")." as ".$arFieldsKeys[$i];
					else
						$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i];

					if (isset($arFields[$arFieldsKeys[$i]]["FROM"])
						&& strlen($arFields[$arFieldsKeys[$i]]["FROM"]) > 0
						&& !in_array($arFields[$arFieldsKeys[$i]]["FROM"], $arAlreadyJoined))
					{
						if (strlen($strSqlFrom) > 0)
							$strSqlFrom .= " ";
						$strSqlFrom .= $arFields[$arFieldsKeys[$i]]["FROM"];
						$arAlreadyJoined[] = $arFields[$arFieldsKeys[$i]]["FROM"];
					}
				}
			}
			else
			{
				foreach ($arSelectFields as $key => $val)
				{
					$val = strtoupper($val);
					$key = strtoupper($key);
					if (array_key_exists($val, $arFields))
					{
						if (strlen($strSqlSelect) > 0)
							$strSqlSelect .= ", ";

						if (in_array($key, $arGroupByFunct))
						{
							$strSqlSelect .= $key."(".$arFields[$val]["FIELD"].") as ".$val;
						}
						else
						{
							if ($arFields[$val]["TYPE"] == "datetime")
								$strSqlSelect .= $DB->DateToCharFunction($arFields[$val]["FIELD"], "FULL")." as ".$val;
							elseif ($arFields[$val]["TYPE"] == "date")
								$strSqlSelect .= $DB->DateToCharFunction($arFields[$val]["FIELD"], "SHORT")." as ".$val;
							else
								$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val;
						}

						if (isset($arFields[$val]["FROM"])
							&& strlen($arFields[$val]["FROM"]) > 0
							&& !in_array($arFields[$val]["FROM"], $arAlreadyJoined))
						{
							if (strlen($strSqlFrom) > 0)
								$strSqlFrom .= " ";
							$strSqlFrom .= $arFields[$val]["FROM"];
							$arAlreadyJoined[] = $arFields[$val]["FROM"];
						}
					}
				}
			}

			if (strlen($strSqlGroupBy) > 0)
			{
				if (strlen($strSqlSelect) > 0)
					$strSqlSelect .= ", ";
				$strSqlSelect .= "COUNT(%%_DISTINCT_%% ".$arFields[$arFieldsKeys[0]]["FIELD"].") as CNT";
			}
			else
				$strSqlSelect = "%%_DISTINCT_%% ".$strSqlSelect;
		}
		// <-- SELECT

		// WHERE -->
		$arSqlSearch = Array();

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$vals = $arFilter[$filter_keys[$i]];
			if (!is_array($vals))
				$vals = array($vals);

			$key = $filter_keys[$i];
			$key_res = CGroup::GetFilterOperation($key);
			$key = $key_res["FIELD"];
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];
			$strOrNull = $key_res["OR_NULL"];

			if (array_key_exists($key, $arFields))
			{
				$arSqlSearch_tmp = array();
				for ($j = 0; $j < count($vals); $j++)
				{
					$val = $vals[$j];

					if (isset($arFields[$key]["WHERE"]))
					{
						$arSqlSearch_tmp1 = call_user_func_array(
								$arFields[$key]["WHERE"],
								array($val, $key, $strOperation, $strNegative, $arFields[$key]["FIELD"], $arFields, $arFilter)
							);
						if ($arSqlSearch_tmp1 !== false)
							$arSqlSearch_tmp[] = $arSqlSearch_tmp1;
					}
					else
					{
						if ($arFields[$key]["TYPE"] == "int")
						{
							if (intval($val) <= 0)
								$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL OR ".$arFields[$key]["FIELD"]." <= 0)";
							else
								$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".intval($val)." )";
						}
						elseif ($arFields[$key]["TYPE"] == "double")
						{
							$val = str_replace(",", ".", $val);
							if (DoubleVal($val) <= 0)
								$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL OR ".$arFields[$key]["FIELD"]." <= 0)";
							else
								$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".DoubleVal($val)." )";
						}
						elseif ($arFields[$key]["TYPE"] == "string" || $arFields[$key]["TYPE"] == "char")
						{
							if ($strOperation == "QUERY")
							{
								$arSqlSearch_tmp[] = GetFilterQuery($arFields[$key]["FIELD"], $val, "Y");
							}
							else
							{
								if (strlen($val) <= 0)
									$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL OR LEN(".$arFields[$key]["FIELD"].")<=0)";
								else
									$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." '".$DB->ForSql($val)."' )";
							}
						}
						elseif ($arFields[$key]["TYPE"] == "datetime")
						{
							if (strlen($val) <= 0)
								$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL)";
							else
								$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "FULL").")";
						}
						elseif ($arFields[$key]["TYPE"] == "date")
						{
							if (strlen($val) <= 0)
								$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL)";
							else
								$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "SHORT").")";
						}
					}
				}

				if (isset($arFields[$key]["FROM"])
					&& strlen($arFields[$key]["FROM"]) > 0
					&& !in_array($arFields[$key]["FROM"], $arAlreadyJoined))
				{
					if (strlen($strSqlFrom) > 0)
						$strSqlFrom .= " ";
					$strSqlFrom .= $arFields[$key]["FROM"];
					$arAlreadyJoined[] = $arFields[$key]["FROM"];
				}

				$strSqlSearch_tmp = "";
				for ($j = 0; $j < count($arSqlSearch_tmp); $j++)
				{
					if ($j > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					$strSqlSearch_tmp .= "(".$arSqlSearch_tmp[$j].")";
				}
				if ($strOrNull == "Y")
				{
					if (strlen($strSqlSearch_tmp) > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." IS ".($strNegative=="Y" ? "NOT " : "")."NULL)";
				}

				if ($strSqlSearch_tmp != "")
					$arSqlSearch[] = "(".$strSqlSearch_tmp.")";
			}
		}

		for ($i = 0; $i < count($arSqlSearch); $i++)
		{
			if (strlen($strSqlWhere) > 0)
				$strSqlWhere .= " AND ";
			$strSqlWhere .= "(".$arSqlSearch[$i].")";
		}
		// <-- WHERE

		// ORDER BY -->
		$arSqlOrder = Array();
		foreach ($arOrder as $by => $order)
		{
			$by = strtoupper($by);
			$order = strtoupper($order);
			if ($order != "ASC")
				$order = "DESC";

			if (array_key_exists($by, $arFields))
			{
				$arSqlOrder[] = " ".$arFields[$by]["FIELD"]." ".$order." ";

				if (isset($arFields[$by]["FROM"])
					&& strlen($arFields[$by]["FROM"]) > 0
					&& !in_array($arFields[$by]["FROM"], $arAlreadyJoined))
				{
					if (strlen($strSqlFrom) > 0)
						$strSqlFrom .= " ";
					$strSqlFrom .= $arFields[$by]["FROM"];
					$arAlreadyJoined[] = $arFields[$by]["FROM"];
				}
			}
		}

		$strSqlOrderBy = "";
		for ($i = 0; $i < count($arSqlOrder); $i++)
		{
			if (strlen($strSqlOrderBy) > 0)
				$strSqlOrderBy .= ", ";
			$strSqlOrderBy .= $arSqlOrder[$i];
		}
		// <-- ORDER BY

		return array(
				"SELECT" => $strSqlSelect,
				"FROM" => $strSqlFrom,
				"WHERE" => $strSqlWhere,
				"GROUPBY" => $strSqlGroupBy,
				"ORDERBY" => $strSqlOrderBy
			);
	}

	function GetListEx($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "TIMESTAMP_X", "ACTIVE", "C_SORT", "ANONYMOUS", "NAME", "DESCRIPTION");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "G.ID", "TYPE" => "int"),
				"TIMESTAMP_X" => array("FIELD" => "G.TIMESTAMP_X", "TYPE" => "datetime"),
				"ACTIVE" => array("FIELD" => "G.ACTIVE", "TYPE" => "char"),
				"C_SORT" => array("FIELD" => "G.C_SORT", "TYPE" => "int"),
				"ANONYMOUS" => array("FIELD" => "G.ANONYMOUS", "TYPE" => "char"),
				"NAME" => array("FIELD" => "G.NAME", "TYPE" => "string"),
				"DESCRIPTION" => array("FIELD" => "G.DESCRIPTION", "TYPE" => "string"),
				"USER_USER_ID" => array("FIELD" => "UG.USER_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_user_group UG ON (G.ID = UG.GROUP_ID)"),
				"USER_GROUP_ID" => array("FIELD" => "UG.GROUP_ID", "TYPE" => "string", "FROM" => "INNER JOIN b_user_group UG ON (G.ID = UG.GROUP_ID)"),
				"USER_DATE_ACTIVE_FROM" => array("FIELD" => "UG.DATE_ACTIVE_FROM", "TYPE" => "datetime", "FROM" => "INNER JOIN b_user_group UG ON (G.ID = UG.GROUP_ID)"),
				"USER_DATE_ACTIVE_TO" => array("FIELD" => "UG.DATE_ACTIVE_TO", "TYPE" => "datetime", "FROM" => "INNER JOIN b_user_group UG ON (G.ID = UG.GROUP_ID)")
			);
		// <-- FIELDS

		$arSqls = CGroup::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_group G ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialchars($strSql)."<br>";

			$dbRes = $DB->Query($strSql);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT #TOP# ".$arSqls["SELECT"]." ".
			"FROM b_group G ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])<=0)
		{
			$strSql = str_replace("#TOP#", "", $strSql);

			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_group G ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialchars($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"])<=0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}
			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialchars($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])>0)
			{
				$strSql = str_replace("#TOP#", "TOP ".$arNavStartParams["nTopCount"], $strSql);
			}

			//echo "!3!=".htmlspecialchars($strSql)."<br>";

			$dbRes = $DB->Query($strSql);
		}

		return $dbRes;
	}

	function GetByID($ID, $SHOW_USERS_AMOUNT = "N")
	{
		global $DB;

		$err_mess = (CGroup::err_mess())."<br>Function: GetList<br>Line: ";
		$ID = intval($ID);

		$strSql = "SELECT G.ID, G.ACTIVE, G.C_SORT, G.ANONYMOUS, G.NAME, G.DESCRIPTION, ".$DB->DateToCharFunction("G.TIMESTAMP_X")." as TIMESTAMP_X ";

		if ($SHOW_USERS_AMOUNT == "Y")
			$strSql .= ", count(distinct U.USER_ID) USERS ";
		else
			$strSql .= ", G.SECURITY_POLICY ";

		$strSql .= "FROM b_group G ";

		if ($SHOW_USERS_AMOUNT == "Y")
			$strSql .= "LEFT JOIN b_user_group U ON (U.GROUP_ID=G.ID AND ((U.DATE_ACTIVE_FROM IS NULL) OR (U.DATE_ACTIVE_FROM <= ".$DB->CurrentTimeFunction().")) AND ((U.DATE_ACTIVE_TO IS NULL) OR (U.DATE_ACTIVE_TO >= ".$DB->CurrentTimeFunction()."))) ";

		$strSql .= "WHERE G.ID = ".$ID." ";

		if ($SHOW_USERS_AMOUNT == "Y")
			$strSql .= "GROUP BY G.ID, G.ACTIVE, G.C_SORT, G.TIMESTAMP_X, G.ANONYMOUS, G.NAME, G.DESCRIPTION";

		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $z;
	}
}
?>
